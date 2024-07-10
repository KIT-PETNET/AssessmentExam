<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SummaryOfTransactionsImport;

class CsvUploadController extends Controller
{
    public function uploadCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $file = $request->file('file');
        Excel::import(new SummaryOfTransactionsImport, $file);

        return response(['message' => 'CSV file uploaded successfully'], 200);
    }
}