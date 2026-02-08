<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventory = Inventory::with('category')->orderBy('created_at', 'desc')->get();
        $categories = InventoryCategory::all();

        $transactions = InventoryTransaction::with('inventory')
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limit to the last 50 transactions
            ->get();

        return view('inventory', compact('categories', 'inventory', 'transactions'));
    }

    public function store(Request $request)
    {
        try {


            // Validate inputs matching the Modal Form
            $validated = $request->validate([
                'name'        => 'required|string|unique:inventory,item_name|max:255',
                'sku'         => 'required|string|unique:inventory,sku',
                'category_id' => 'required|exists:inventory_categories,id',
                'price'       => 'required|numeric|min:0',
                'stock'       => 'required|integer|min:0',
            ], [
                'name.unique' => 'This item name is already in use.',
                'sku.unique' => 'This SKU is already in use.',
                'stock.min'  => 'Stock must be at least 0.',
                'price.min'  => 'Price must be at least 0.',
            ]);

            // Create the item
            $item = Inventory::create([
                'item_name'    => $validated['name'],
                'sku'          => $validated['sku'],
                'category_id'  => $validated['category_id'],
                'price'        => $validated['price'],
                'stock_level'  => $validated['stock'],
            ]);

            // Log the transaction
            if ($item->stock_level > 0) {
                InventoryTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'IN',
                    'quantity'          => $item->stock_level,
                    'reason'            => 'Initial Stock (Item Created)',
                ]);
            }
            return response()->json([
                'status'   => 'success',
                'message'  => 'New Item added to inventory',
                'redirect' => route('inventory')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {


        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:255',
            'sku'         => 'sometimes|string|unique:inventory,sku,' . $id,
            'category_id' => 'sometimes|exists:inventory_categories,id',
            'price'       => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $item = Inventory::find($id);

            if (!$item) {
                return response()->json(['message' => 'Item not found'], 404);
            }


            $item->update($request->all());

            return response()->json([
                'status'   => 'success',
                'message'  => 'Item updated successfully',
                'redirect' => route('inventory'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stockIn(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'reason'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {

            $item = Inventory::find($id);

            if (!$item) {
                return response()->json(['message' => 'Item not found'], 404);
            }

            //Update the actual stock level
            $item->increment('stock_level', $request->quantity);

            // Create the History Log (Transaction)
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type'              => 'IN',       // Label this as Stock In
                'quantity'          => $request->quantity,
                'reason'            => $request->reason,
            ]);

            return response()->json([
                'status'   => 'success',
                'message'  => 'Stock added successfully',
                'redirect' => route('inventory'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stockOut(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'reason'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $item = Inventory::find($id);

            if (!$item) {
                return response()->json(['message' => 'Item not found'], 404);
            }

            if ($item->stock_level < $request->quantity) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Insufficient stock level'
                ], 400);
            }

            // Update the actual stock level
            $item->decrement('stock_level', $request->quantity);

            // Create the History Log (Transaction)
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type'              => 'OUT',      // Label this as Stock Out
                'quantity'          => $request->quantity,
                'reason'            => $request->reason,
            ]);

            return response()->json([
                'status'   => 'success',
                'message'  => 'Stock removed successfully',
                'redirect' => route('inventory'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Handle Delete
    public function destroy(String $id)
    {
        try {
            $item = Inventory::find($id);

            if ($item) {
                if ($item->stock_level > 0) {
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'type'              => 'OUT',
                        'quantity'          => $item->stock_level,
                        'reason'            => 'Item Archived',
                    ]);
                }
                $item->delete();
                return response()->json([
                    'status'   => 'success',
                    'message'  => 'Item deleted successfully',
                    'redirect' => route('inventory'),
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Item not found',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
