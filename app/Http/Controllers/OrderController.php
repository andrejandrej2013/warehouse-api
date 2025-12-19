<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Order::with('products')->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['products'] as $item) {
                $product = Product::find($item['id']);

                if ($product->quantity < $item['quantity']) {
                    return response()->json([
                        'message' => "Not enough stock for product \"{$product->code}\""
                    ], 422);
                }
            }

            $order = Order::create([
                'user_id' => $validated['user_id'],
                'name' => 'Order #' . now()->timestamp,
            ]);

            foreach ($validated['products'] as $item) {
                $product = Product::find($item['id']);

                $order->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $product->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return response()->json(
                $order->load('products'),
                201
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Order creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(
            Order::with('products')->findOrFail($id)
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::with('products')->findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($order->products as $product) {
                $product->increment(
                    'quantity',
                    $product->pivot->quantity
                );
            }

            foreach ($validated['products'] as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->quantity < $item['quantity']) {
                    return response()->json([
                        'message' => "Not enough stock for product {$product->name}"
                    ], 422);
                }
            }

            $order->products()->detach();

            foreach ($validated['products'] as $item) {
                $product = Product::findOrFail($item['id']);

                $order->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $product->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return response()->json(
                $order->load('products')
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Order update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Order::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
