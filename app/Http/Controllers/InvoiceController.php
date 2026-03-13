<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceSent;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with([
            'client' => function ($query) {
                $query->withTrashed();
            },
            'project' => function ($query) {
                $query->withTrashed();
            },
        ])->latest()->paginate(10);

        $projects = Project::where('is_active', true)->get();

        return view('invoice', compact('invoices', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1', // Must have at least 1 item
            'items.*.desc' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);
        try {
            DB::transaction(function () use ($validated) {
                $invoice = Invoice::create([
                    'project_id' => $validated['project_id'],
                    'client_id' => $validated['client_id'],
                    'issue_date' => $validated['issue_date'],
                    'due_date' => $validated['due_date'],
                    'total_amount' => 0,
                    'status' => 'draft',
                ]);

                $grandTotal = 0;

                foreach ($validated['items'] as $item) {
                    $lineTotal = $item['qty'] * $item['price'];
                    $grandTotal += $lineTotal;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item['desc'],
                        'quantity' => $item['qty'],
                        'price' => $item['price'],
                        'total' => $lineTotal,
                    ]);
                }

                $invoice->update(['total_amount' => $grandTotal]);
            });

            return response()->json(['message' => 'Invoice created successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // 'project_id'    => 'required|exists:projects,id',
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.desc' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);
        // Update Invoice Parent Details
        try {
            $changesDetected = false;
            DB::transaction(function () use ($validated, $id, &$changesDetected) {
                $invoice = Invoice::findOrFail($id);

                // Check if basic invoice details changed
                if ($invoice->client_id != $validated['client_id'] ||
                    $invoice->issue_date != $validated['issue_date'] ||
                    $invoice->due_date != $validated['due_date']) {
                    $changesDetected = true;
                }

                $invoice->update([
                    'project_id' => $invoice['project_id'],
                    'client_id' => $validated['client_id'],
                    'issue_date' => $validated['issue_date'],
                    'due_date' => $validated['due_date'],
                ]);

                // Check if items changed
                $existingItems = $invoice->items()->get();
                if (count($existingItems) != count($validated['items'])) {
                    $changesDetected = true;
                } else {
                    foreach ($existingItems as $index => $existingItem) {
                        $newItem = $validated['items'][$index] ?? null;
                        if (! $newItem ||
                            $existingItem->description != $newItem['desc'] ||
                            $existingItem->quantity != $newItem['qty'] ||
                            $existingItem->price != $newItem['price']) {
                            $changesDetected = true;
                            break;
                        }
                    }
                }

                $invoice->items()->delete();

                $grandTotal = 0;

                foreach ($validated['items'] as $item) {
                    $lineTotal = $item['qty'] * $item['price'];
                    $grandTotal += $lineTotal;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item['desc'],
                        'quantity' => $item['qty'],
                        'price' => $item['price'],
                        'total' => $lineTotal,
                    ]);
                }

                $invoice->update(['total_amount' => $grandTotal]);
            });

            $message = $changesDetected ? 'Invoice updated successfully' : 'No Changes were made';

            return response()->json(['message' => $message, 'changesDetected' => $changesDetected]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            if (! $invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }

            $invoice->items()->delete();

            $invoice->delete();

            return response()->json(['message' => 'Invoice deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function sendEmail($id)
    {
        $invoice = Invoice::with(['client', 'project'])->findOrFail($id);

        if (! $invoice->client->email) {
            return response()->json(['message' => 'Client has no email address linked.'], 400);
        }

        try {
            Mail::to($invoice->client->email)->send(new InvoiceSent($invoice));

            if ($invoice->status === 'draft') {
                $invoice->update(['status' => 'sent']);
            }

            return response()->json(['message' => 'Invoice sent successfully via Brevo!']);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Email Error: '.$e->getMessage());

            return response()->json(['message' => 'Failed to send email. Check logs.'], 500);
        }
    }

    public function markAsPaid($id)
    {
        try {
            // Find the invoice or fail with 404
            $invoice = Invoice::findOrFail($id);

            if (! $invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }

            // Update the status
            $invoice->update(['status' => 'paid']);
            // Find the connected quote and change status to archived
            if ($invoice->project && $invoice->project->quote) {
                $invoice->project->quote->update([
                    'status' => 'archived',
                ]);
            }

            // Return success to the frontend
            return response()->json(['message' => 'Invoice marked as paid']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: '.$e->getMessage(),
            ], 500);
        }
    }
}
