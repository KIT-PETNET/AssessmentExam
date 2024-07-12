<?php

namespace App\Imports;

use App\Models\Data;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SummaryOfTransactionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Data([
            'Type' => $row['type'],
            'Name' => $row['name'],
            'Description' => $row['description'],
            'Charges' => $row['charges'],
            'Amount' => $row['amount'],
        ]);
    }
}
