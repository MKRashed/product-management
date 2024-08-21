<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Customer([
            'first_name'        => $row['0'],
            'last_name'         => $row['1'],
            'phone'             => $row['2'],
            'email'             => $row['3'],
            'address'           => $row['4'],
        ]);
    }
}
