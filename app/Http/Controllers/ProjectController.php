<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function Symfony\Component\Clock\now;

class ProjectController extends Controller
{
    public function index(Request $request)
    {

        $query = Project::query();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('project_name', 'like', "%{$searchTerm}%")
                    ->orWhere('project_budget', 'like', "%{$searchTerm}%")
                    ->orWhereHas('client', function ($clientQuery) use ($searchTerm) {
                        $clientQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }
        $projects = $query->paginate(20)->withQueryString();
        $clients = Client::all();
        return view('project', compact('projects', 'clients'));
    }

    public function create(Request $request)
    {

        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_budget' => 'required|numeric|min:0',
            'project_end_date' => 'required|date|after:today',
            'client_id' => 'required|exists:clients,id',
        ]);
        Project::create([
            'project_name' => $validated['project_name'],
            'project_budget' => $validated['project_budget'],
            'is_active' => true,
            'project_start_date' => now(),
            'project_end_date' => $validated['project_end_date'],
            'client_id' => $validated['client_id'],
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'redirect' => route('projects')
        ], 200);
    }


    public function update(Request $request, string $id)
    {
        $project = Project::find($id);
        if ($project) {
            $project->update($request->all());
            return response()->json([
                'message' => 'Project updated successfully',
                'redirect' => route('projects')
            ], 200);
        } else {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }
    }

    public function destroy(string $id)
    {
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

        return redirect()->route('projects.panel', ['id' => $id])
            ->with('success', 'Photos uploaded successfully!');
    }
}