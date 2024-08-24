<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Http\Requests\OrderRequest;
use App\Imports\OrdersImport;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('customer', 'orderItems')->get();

        return response([
            'orders' => $orders
        ], 200);
    }


    public function  create()
    {
        $customers = Customer::get(['first_name', 'last_name', 'id']);

        $products = Product::all();

        return response([
            'customers' => $customers,
            'products' => $products
        ], 200);
    }

    public function store(OrderRequest $request)
    {

        $validated = $request->validated();


        DB::beginTransaction();

        try {

            $order = Order::create($validated);

            $items = $request->items;

            foreach ($items as $item) {
                if (is_string($item)) {
                    $item = json_decode($item, true);
                }

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            // Commit the transaction
            DB::commit();

            return response()->json($order, 201);
        } catch (\Exception $e) {
            // Rollback the transaction if there is an error
            DB::rollBack();
            return response()->json(['error' => 'Failed to create order.'], 500);
        }
    }

    public function edit($id)
    {
        $order = Order::find($id);

        return response([
            'order' => $order
        ], 200);
    }


    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'customer_id'    => 'sometimes|required',
            'total_amount'   => 'sometimes|required|numeric',
            'status'         => 'sometimes|nullable|boolean',
            'items'          => 'sometimes|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity'   => 'required_with:items|numeric|min:1',
            'items.*.price'      => 'required_with:items|numeric|min:0',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::beginTransaction();

        try {

            $dataToUpdate = array_filter($validatedData, function ($value) {
                return !is_null($value);
            });

            $order->update($dataToUpdate);

            if (isset($validatedData['items'])) {
                OrderItem::where('order_id', $order->id)->delete();

                foreach ($validatedData['items'] as $item) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json($order, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update order.'], 500);
        }
    }



    public function destroy(string $id)
    {

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->orderItems()->delete();

        $order->delete();

        return response()->json(['message' => 'Delete successfully!'], 200);
    }

    public function importOrders(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        try {
            Excel::import(new OrdersImport, $request->file('file')->store('temp'));
            return response()->json(['message' => 'Orders imported successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was an error importing the file.'], 500);
        }
    }

    public function exportOrders()
    {
        return Excel::download(new OrdersExport, 'orders.csv');
    }
}
