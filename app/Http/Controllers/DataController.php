<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function index()
    {
        $dataCommissions = Data::all();
        return response()->json($dataCommissions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Type' => 'required|string|max:255',
            'Name' => 'required|string|max:255',
            'Description' => 'required|string',
            'Charges' => 'required|integer',
            'Amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dataCommission = Data::create($request->all());
        return response()->json($dataCommission, 201);
    }

    public function show($id)
    {
        $dataCommission = Data::find($id);

        if (!$dataCommission) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return response()->json($dataCommission);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'Type' => 'string|max:255',
            'Name' => 'string|max:255',
            'Description' => 'string',
            'Charges' => 'integer',
            'Amount' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dataCommission = Data::find($id);

        if (!$dataCommission) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $dataCommission->update($request->all());
        return response()->json($dataCommission);
    }

    public function destroy($id)
    {
        $dataCommission = Data::find($id);

        if (!$dataCommission) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $dataCommission->delete();
        return response()->json(['message' => 'Data deleted successfully']);
    }
}