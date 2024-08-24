<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Http\Requests\ProductRequest;
use App\Imports\ProductsImport;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        return response([
            'products' => $products
        ], 200);
    }


    public function store(ProductRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image,
            'price' => $request->price,
        ]);

        return response()->json($product, 201);
    }

    public function edit($id)
    {
        $product = Product::find($id);

        return response([
            'product' => $product
        ], 200);
    }


    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $dataToUpdate = array_filter($validatedData, function ($value) {
            return !is_null($value);
        });

        $product->update($dataToUpdate);

        return response()->json($product, 200);
    }


    public function destroy(string $id)
    {

        $product = Product::find($id);

        $product->delete();
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file')->store('temp'));
            return response()->json(['message' => 'Products imported successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was an error importing the file.'], 500);
        }
    }

    public function exportProducts()
    {
        return Excel::download(new ProductsExport, 'products.csv');
    }
}
