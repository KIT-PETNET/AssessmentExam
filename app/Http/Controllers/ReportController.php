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
    // Function to calculate commission per transaction and save to database
    public function calculateAndSaveCommissions()
    {
        $transactions = Data::all();

        $n_com_rate = 18;
        $commission_rate = 15;

        foreach ($transactions as $transaction) {
            $charges = floatval($transaction->Charges);

            if (is_nan($charges)) {
                continue;
            }

            // Calculate gross commission
            $gross_commission = $this->roundToTwo($charges * ($commission_rate / 100));
            // Calculate commission
            $commission = $this->roundToTwo($gross_commission * (($commission_rate / 100) / ($n_com_rate / 100)));

            // Add calculated values to the transaction object
            $transaction->gross_commission = $gross_commission;
            $transaction->commission = $commission;
            $transaction->net_commission = $commission;
            $transaction->vatable = $this->isVatable($transaction->Type);

            // Save to database
            SummaryOfTransaction::create([
                'Type' => $transaction->Type,
                'Name' => $transaction->Name,
                'Description' => $transaction->Description,
                'commission' => $transaction->commission,
                'gross_commission' => $transaction->gross_commission,
                'net_commission' => $transaction->net_commission,
                'vatable' => $transaction->vatable
            ]);
        }

        return response()->json(['message' => 'Commissions calculated and saved'], 200);
    }

    // Function to compute gross and net commission and generate report
    public function computeAndGenerateReport(Request $request)
    {
        // Hardcoded charge rates and VAT values based on type
        $chargeRates = [
            'MTS' => 0.12,
            'RS' => 0.19,
            'DR' => 0.15,
            'IT' => 0.18,
            'MMT' => 0.13,
        ];

        $vatValues = [
            'MTS' => 0.12,
            'RS' => 0.12,
            'DR' => 0.12,
            'IT' => 0.12,
            'MMT' => 0.12,
        ];

        $summary = DB::table('summary_of_transactions')
            ->select(
                'Type',
                DB::raw('SUM(commission) as sum_of_commission_per_type'),
                DB::raw('SUM(CASE WHEN vatable THEN commission ELSE 0 END) as sum_of_vatable_commission'),
                DB::raw('SUM(CASE WHEN NOT vatable THEN commission ELSE 0 END) as sum_of_non_vatable_commission')
            )
            ->groupBy('Type')
            ->get();

        $results = [];
        $categorizedTotals = [];
        $totalGrossCommissionVatable = 0;
        $totalNetCommissionVatable = 0;
        $totalGrossCommissionNonVatable = 0;
        $totalNetCommissionNonVatable = 0;

        foreach ($summary as $transaction) {
            $type = $transaction->Type;
            $charge_rate = $chargeRates[$type];
            $vat = $vatValues[$type];

            // Vatable
            if ($transaction->sum_of_vatable_commission > 0) {
                $gross_commission_vatable = $this->roundToTwo($transaction->sum_of_vatable_commission / (1 + $vat));
                $expanded_withholding_tax_vatable = $this->roundToTwo($transaction->sum_of_vatable_commission * $charge_rate);
                $net_commission_vatable = $this->roundToTwo($gross_commission_vatable - $expanded_withholding_tax_vatable);

                $results[$type]['total_gross_commission_vatable'] = $gross_commission_vatable;
                $results[$type]['total_net_commission_vatable'] = $net_commission_vatable;

                // Add to total
                $totalGrossCommissionVatable += $gross_commission_vatable;
                $totalNetCommissionVatable += $net_commission_vatable;
            }

            // Not Vatable
            if ($transaction->sum_of_non_vatable_commission > 0) {
                $gross_commission_non_vatable = $this->roundToTwo($transaction->sum_of_non_vatable_commission);
                $expanded_withholding_tax_non_vatable = $this->roundToTwo($transaction->sum_of_non_vatable_commission * $charge_rate);
                $net_commission_non_vatable = $this->roundToTwo($gross_commission_non_vatable - $expanded_withholding_tax_non_vatable);

                $results[$type]['total_gross_commission_non_vatable'] = $gross_commission_non_vatable;
                $results[$type]['total_net_commission_non_vatable'] = $net_commission_non_vatable;

                // Add to total
                $totalGrossCommissionNonVatable += $gross_commission_non_vatable;
                $totalNetCommissionNonVatable += $net_commission_non_vatable;
            }

            // Save categorized totals to the database
            $categorizedTotals[$type] = [
                'total_gross_commission_vatable' => $results[$type]['total_gross_commission_vatable'] ?? 0,
                'total_net_commission_vatable' => $results[$type]['total_net_commission_vatable'] ?? 0,
                'total_gross_commission_non_vatable' => $results[$type]['total_gross_commission_non_vatable'] ?? 0,
                'total_net_commission_non_vatable' => $results[$type]['total_net_commission_non_vatable'] ?? 0,
            ];

            // Update only the totals for the specific type
            DB::table('summary_of_transactions')
                ->where('Type', $type)
                ->update($categorizedTotals[$type]);
        }

        // Add totals for all transaction types
        $results['total_all_types'] = [
            'total_gross_commission_vatable' => $this->roundToTwo($totalGrossCommissionVatable),
            'total_net_commission_vatable' => $this->roundToTwo($totalNetCommissionVatable),
            'total_gross_commission_non_vatable' => $this->roundToTwo($totalGrossCommissionNonVatable),
            'total_net_commission_non_vatable' => $this->roundToTwo($totalNetCommissionNonVatable),
        ];

        // Generate and download the report as an Excel file
        $transactions = SummaryOfTransaction::all();
        return Excel::download(new TransactionsExport($transactions, $results), 'transactions_report.xlsx');
    }

    // Helper function to determine if a transaction type is vatable
    private function isVatable($type)
    {
        $vatableTypes = ['MTS', 'IT', 'MMT'];
        return in_array($type, $vatableTypes);
    }

    // Helper function to round numbers to two decimal places
    private function roundToTwo($num)
    {
        return round($num * 100) / 100;
    }
}