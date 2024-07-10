<?php

namespace App\Imports;

use App\Models\SummaryOfTransaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SummaryOfTransactionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new SummaryOfTransaction([
            'transaction_id' => $row['transaction_id'],
            'commission' => $row['commission'],
            'gross_commission' => $row['gross_commission'],
            'net_commission' => $row['net_commission'],
            'vatable' => $row['vatable'],
        ]);
    }
}