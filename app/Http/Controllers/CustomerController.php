<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Http\Requests\CustomerRequest;
use App\Imports\CustomersImport;
use App\Models\Customer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{

    public function index()
    {
        $customers = Customer::all();

        return response([
            'customers' => $customers
        ], 200);
    }


    public function store(CustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return response()->json($customer, 201);
    }


    public function update(CustomerRequest $request, string $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->update($request->validated());

        return response()->json($customer, 200);
    }


    public function destroy(string $id)
    {
        $customer = Customer::find($id);

        $customer->delete();

        return response()->json('Delete successfully!', 200);
    }

    public function importCustomers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        try {
            Excel::import(new CustomersImport, $request->file('file')->store('temp'));
            return response()->json(['message' => 'Customers imported successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was an error importing the file.'], 500);
        }
    }

    public function exportCustomers()
    {
        return Excel::download(new CustomersExport, 'customers.csv');
    }
}
