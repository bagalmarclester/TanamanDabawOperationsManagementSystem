<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Quote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use function Symfony\Component\Clock\now;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role, ['Head Landscaper', 'Field Crew'])) {
            $projects = Project::with('client')
                ->where('is_active', true)

                ->where(function ($query) use ($user) {
                    $query->where('head_landscaper_id', $user->id)
                        ->orWhereHas('fieldCrew', function ($subQuery) use ($user) {
                            $subQuery->where('users.id', $user->id);
                        });
                })

                ->orderBy('project_start_date', 'asc')
                ->get();
            return view('projectcrew', compact('projects'));
        }
        $query = Project::with(['client', 'headLandscaper', 'fieldCrew']);

        $projects = $query->latest()->paginate(20)->withQueryString();

        $pendingQuotes = Quote::where('status', 'pending')->get();
        $clients = Client::all();

        $workers = User::whereIn('role', ['Head Landscaper', 'Field Crew'])->get();

        return view('project', compact('projects', 'clients', 'pendingQuotes', 'workers'));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_budget' => 'required|numeric|min:0',
            'project_end_date' => 'required|date|after:today',
            'client_id' => 'required|exists:clients,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'project_description' => 'nullable|string',
            'project_location' => 'required|string|max:255',
            'head_landscaper_id' => 'nullable|exists:users,id',
            'crew_ids' => 'nullable|array',
            'crew_ids.*' => 'exists:users,id',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $project = Project::create([
                    'project_name' => $validated['project_name'],
                    'project_budget' => $validated['project_budget'],
                    'is_active' => true,
                    'project_start_date' => now(),
                    'project_end_date' => $validated['project_end_date'],
                    'client_id' => $validated['client_id'],
                    'quote_id' => $validated['quote_id'] ?? null,
                    'project_description' => $validated['project_description'],
                    'project_location' => $validated['project_location'],
                    'head_landscaper_id' => $validated['head_landscaper_id'],
                ]);

                if (!empty($validated['crew_ids'])) {
                    $project->fieldCrew()->sync($validated['crew_ids']);
                }

                if (!empty($validated['quote_id'])) {
                    Quote::where('id', $validated['quote_id'])->update(['status' => 'accepted']);
                }
            });
            return response()->json([
                'message' => 'Project created successfully',
                'redirect' => route('projects')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_budget' => 'required|numeric|min:0',
            'project_end_date' => 'required|date|after:today',
            'client_id' => 'required|exists:clients,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'project_description' => 'nullable|string',
            'project_location' => 'required|string|max:255',
            'head_landscaper_id' => 'nullable|exists:users,id',
            'crew_ids' => 'nullable|array',
            'crew_ids.*' => 'exists:users,id',
        ]);
        try {
            $project = Project::findOrFail($id);

            // Update the main project details
            $project->update([
                'project_name' => $validated['project_name'],
                'project_budget' => $validated['project_budget'],
                'project_end_date' => $validated['project_end_date'],
                'client_id' => $validated['client_id'],
                'quote_id' => $validated['quote_id'],
                'project_description' => $validated['project_description'],
                'project_location' => $validated['project_location'],
                'head_landscaper_id' => $validated['head_landscaper_id'],
            ]);

            // Update the Field Crew
            $project->fieldCrew()->sync($validated['crew_ids'] ?? []);

            return response()->json([
                'message' => 'Project updated successfully',
                'redirect' => route('projects')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $project = Project::find($id);
            if ($project) {
                $project->delete();
                return response()->json([
                    'message' => 'Project deleted successfully',
                    'redirect' => route('projects')
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Project not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $project = Project::findOrFail($id);
        $signedImages = [];
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        foreach ($project->images ?? [] as $image) {
            $signedImages[] = $disk->temporaryUrl(
                $image->image_path,
                \Illuminate\Support\Carbon::now()->addMinutes(15)
            );
        };

        return view("projectpanel", compact('project', 'signedImages'));
    }

    public function uploadImage(Request $request, string $id)
    {
        try {
            $project = Project::findOrFail($id);

            $files = $request->file('progression_images');

            // Check file upload errors BEFORE touching the files
            if ($files) {
                foreach ($files as $file) {
                    if (!$file->isValid()) {

                        if ($file->getError() === UPLOAD_ERR_INI_SIZE || $file->getError() === UPLOAD_ERR_FORM_SIZE) {
                            return back()->withErrors([
                                'progression_images' => 'One of the files is too large. Max upload size exceeded.'
                            ]);
                        }

                        return back()->withErrors([
                            'progression_images' => 'Upload failed: ' . $file->getErrorMessage()
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'message' => 'No files uploaded'
                ], 500);
            }

            // Upload valid files
            $destinationPath = 'projects/' . $project->id;

            foreach ($files as $file) {
                $s3Path = Storage::disk('s3')->putFile($destinationPath, $file);

                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $s3Path,
                ]);
            }

            return back()->with('success', 'Photos uploaded successfully!');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getInvoiceData($id)
    {
        try {
            $project = \App\Models\Project::with(['client', 'quote.items'])->findOrFail($id);

            $items = [];

            if ($project->quote) {
                foreach ($project->quote->items as $item) {
                    $items[] = [
                        'description' => $item->description,
                        'quantity'    => $item->quantity,
                        'price'       => $item->price,
                    ];
                }
            } else {
                $items[] = [
                    'description' => 'Project Service: ' . $project->project_name,
                    'quantity'    => 1,
                    'price'       => $project->project_budget,
                ];
            }

            return response()->json([
                'client_id'   => $project->client_id,
                'client_name' => $project->client->name,
                'items'       => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function complete(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                // 1. Load project with quote items
                $project = Project::with(['quote.items', 'client'])->withCount('images')->findOrFail($id);

                // 2. Validation: Ensure images exist before completing
                if ($project->images_count === 0) {
                    throw new \Exception('At least one project image is required to complete the project.');
                }

                $project->update(['is_active' => false]);

                $invoice = \App\Models\Invoice::create([
                    'project_id'   => $project->id,
                    'client_id'    => $project->client_id,
                    'issue_date'   => now(),
                    'due_date'     => Carbon::now()->addDays(14), // Default 14-day due date
                    'total_amount' => 0,
                    'status'       => 'draft'
                ]);

                $grandTotal = 0;

                if ($project->quote && $project->quote->items->count() > 0) {
                    foreach ($project->quote->items as $item) {
                        $lineTotal = $item->quantity * $item->price;
                        $grandTotal += $lineTotal;

                        \App\Models\InvoiceItem::create([
                            'invoice_id'  => $invoice->id,
                            'description' => $item->description,
                            'quantity'    => $item->quantity,
                            'price'       => $item->price,
                            'total'       => $lineTotal
                        ]);
                    }
                } else {
                    $grandTotal = $project->project_budget;
                    \App\Models\InvoiceItem::create([
                        'invoice_id'  => $invoice->id,
                        'description' => 'Project Completion: ' . $project->project_name,
                        'quantity'    => 1,
                        'price'       => $project->project_budget,
                        'total'       => $project->project_budget
                    ]);
                }

                $invoice->update(['total_amount' => $grandTotal]);
            });

            return response()->json([
                'message' => 'Project completed and Invoice generated successfully',
                'redirect' => route('projects')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
