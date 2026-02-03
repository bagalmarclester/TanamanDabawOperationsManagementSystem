<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    public function index()
    {
        // Update quotes that have expired. Simple way no cron job setup
        Quote::where('status', 'pending')
            ->whereDate('valid_until', '<', now())
            ->update(['status' => 'rejected']);
        $quotes = Quote::with(['client', 'items'])->latest()->get();
        $clients = Client::all();
        return view('quotes', compact('quotes', 'clients'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id'   => 'required|exists:clients,id',
            'subject'     => 'required|string|max:255', // <--- NEW: Subject Validation
            'quote_date'  => 'required|date',
            'valid_until' => 'required|date|after_or_equal:quote_date',
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.price'       => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                // Calculate Grand Total
                $grandTotal = 0;
                foreach ($request->items as $item) {
                    $grandTotal += ($item['quantity'] * $item['price']);
                }

                // Create Quote
                $quote = Quote::create([
                    'client_id'    => $request->client_id,
                    'subject'      => $request->subject, // <--- NEW: Saving Subject
                    'quote_date'   => $request->quote_date,
                    'valid_until'  => $request->valid_until,
                    'total_amount' => $grandTotal,
                    'status'       => 'pending',
                ]);

                // Create Items
                foreach ($request->items as $item) {
                    $quote->items()->create([
                        'description' => $item['description'],
                        'quantity'    => $item['quantity'],
                        'price'       => $item['price'],
                        'subtotal'    => $item['quantity'] * $item['price'],
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Quote created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_id'   => 'required|exists:clients,id',
            'subject'     => 'required|string|max:255', // <--- NEW
            'quote_date'  => 'required|date',
            'valid_until' => 'required|date|after_or_equal:quote_date',
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.price'       => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request, $quote) {
                // Recalculate Total
                $grandTotal = 0;
                foreach ($request->items as $item) {
                    $grandTotal += ($item['quantity'] * $item['price']);
                }

                // Update Quote Details
                $quote->update([
                    'client_id'    => $request->client_id,
                    'subject'      => $request->subject, // <--- NEW
                    'quote_date'   => $request->quote_date,
                    'valid_until'  => $request->valid_until,
                    'total_amount' => $grandTotal,
                ]);

                // Replace Items
                $quote->items()->delete();

                foreach ($request->items as $item) {
                    $quote->items()->create([
                        'description' => $item['description'],
                        'quantity'    => $item['quantity'],
                        'price'       => $item['price'],
                        'subtotal'    => $item['quantity'] * $item['price'],
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Quote updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $quote = Quote::find($id);
        if ($quote) {
            $quote->delete();
            return response()->json(['message' => 'Quote moved to trash']);
        }
        return response()->json(['message' => 'Quote not found'], 404);
    }
}
