<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromCollection, WithHeadings
{
    protected $transactions;
    protected $totals;

    public function __construct($transactions, $totals)
    {
        $this->transactions = $transactions;
        $this->totals = $totals;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->transactions->map(function ($transaction) {
            return [
                'Type' => $transaction->Type,
                'Name' => $transaction->Name,
                'Description' => $transaction->Description,
                'Commission' => $transaction->commission,
                'Gross Commission' => $transaction->gross_commission,
                'Net Commission' => $transaction->net_commission,
                'Vatable' => $transaction->vatable ? 'Yes' : 'No',
                'Total Gross Commission Vatable' => $this->totals[$transaction->Type]['total_gross_commission_vatable'] ?? 0,
                'Total Net Commission Vatable' => $this->totals[$transaction->Type]['total_net_commission_vatable'] ?? 0,
                'Total Gross Commission Non-Vatable' => $this->totals[$transaction->Type]['total_gross_commission_non_vatable'] ?? 0,
                'Total Net Commission Non-Vatable' => $this->totals[$transaction->Type]['total_net_commission_non_vatable'] ?? 0,
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Type',
            'Name',
            'Description',
            'Commission',
            'Gross Commission',
            'Net Commission',
            'Vatable [Y/N]',
            'Total Gross Commission Vatable',
            'Total Net Commission Vatable',
            'Total Gross Commission Non-Vatable',
            'Total Net Commission Non-Vatable',
        ];
    }
}