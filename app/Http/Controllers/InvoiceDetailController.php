<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Models\UserDetail;
use App\Models\RoomtypeDetail;
use App\Models\CurrentUtilityUsage;
use App\Models\PreviousUtilityUsage;
use Illuminate\Support\Facades\Validator;

class InvoiceDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Check if 'paid' filter is provided in the request
            $paidFilter = $request->query('paid'); // Get the 'paid' query parameter

            // Build the query with filtering if needed
            $query = InvoiceDetail::with(['room', 'user']);

            if ($paidFilter !== null) {
                // Apply the 'paid' filter, converting string to boolean
                $query->where('paid', filter_var($paidFilter, FILTER_VALIDATE_BOOLEAN));
            }

            // Execute the query and get results
            $invoiceDetails = $query->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice details retrieved successfully',
                'data' => $invoiceDetails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving invoice details',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user_details,id',
            'current_usage_id' => 'required|exists:current_utility_usages,id',
            'previous_usage_id' => 'required|exists:previous_utility_usages,id',
            'amount_due' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'paid' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $invoice = InvoiceDetail::create($validator->validated());
            return response()->json([
                'message' => 'Invoice created successfully',
                'data' => $invoice
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
