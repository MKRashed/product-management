<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\ToModel;

class OrdersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Order([
            'customer_id'   => $row['0'],
            'total_amount'  => floatval($row['1']),
            'status'        => $row['2'],
        ]);
    }
}
