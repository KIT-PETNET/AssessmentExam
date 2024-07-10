<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\SummaryOfTransaction;
use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // Function to calculate commission per transaction
    public function calculateCommissionPerTransaction()
    {
        $transactions = Data::all(); 

        $n_com_rate = 18;
        $commission_rate = 15;

        foreach ($transactions as $transaction) {
            $charges = $transaction->charges;
            
            $gross_commission = $charges * ($commission_rate / 100);
            $commission = $gross_commission * (($commission_rate / 100) / ($n_com_rate / 100));

            // Save the calculated commission
            SummaryOfTransaction::create([
                'transaction_id' => $transaction->id,
                'commission' => $commission,
                'gross_commission' => $gross_commission,
                'net_commission' => $commission,
                'vatable' => $transaction->vatable ?? false 
            ]);
        }

        return response()->json(['message' => 'Commissions calculated and saved'], 200);
    }

    // Function to save commissions to the database
    public function saveCommissionsToDatabase()
    {
        return $this->calculateCommissionPerTransaction();
    }

    // Function to compute gross and net commission
    public function computeGrossAndNetCommission(Request $request)
    {
        $charge_rate = $request->input('charge_rate', 10); 
        $vat = $request->input('vat', 12); 

        $summary = DB::table('summary_of_transactions')
            ->select(
                DB::raw('SUM(commission) as sum_of_commission_per_type'),
                DB::raw('SUM(CASE WHEN vatable THEN commission ELSE 0 END) as sum_of_vatable_commission'),
                DB::raw('SUM(CASE WHEN NOT vatable THEN commission ELSE 0 END) as sum_of_non_vatable_commission')
            )
            ->first();

        $charge_rate = $charge_rate / 100;
        $vat = $vat / 100;

        // Vatable
        $gross_commission_vatable = $summary->sum_of_vatable_commission / (1 + $vat);
        $expanded_withholding_tax_vatable = $summary->sum_of_vatable_commission * $charge_rate;
        $net_commission_vatable = $gross_commission_vatable - $expanded_withholding_tax_vatable;

        // Not Vatable
        $gross_commission_non_vatable = $summary->sum_of_non_vatable_commission;
        $expanded_withholding_tax_non_vatable = $summary->sum_of_non_vatable_commission * $charge_rate;
        $net_commission_non_vatable = $gross_commission_non_vatable - $expanded_withholding_tax_non_vatable;

        return response()->json([
            'total_gross_commission_vatable' => $gross_commission_vatable,
            'total_net_commission_vatable' => $net_commission_vatable,
            'total_gross_commission_non_vatable' => $gross_commission_non_vatable,
            'total_net_commission_non_vatable' => $net_commission_non_vatable
        ], 200);
    }

    // Function to generate and download the report as an Excel file
    public function generateReport()
    {
        $transactions = SummaryOfTransaction::all();

        // Use Maatwebsite Excel to create an Excel file
        return Excel::download(new TransactionsExport($transactions), 'transactions_report.xlsx');
    }
}
