<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get a list of all the invoices
        $invoices = Invoice::get();

        // Return the invoices
        return InvoiceResource::collection($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'date' => ['required', 'date'],
            'client' => [
                'name' => ['required', 'string'],
                'address_one' => ['required', 'string'],
                'address_two' => ['nullable', 'string'],
                'address_three' => ['nullable', 'string'],
                'btw' => ['required', 'string']
            ],
            'items' => [
                '*.description' => ['required', 'string'],
                '*.price' => ['required', 'numeric'],
                '*.amount' => ['required', 'integer'],
                '*.btw' => ['required', 'integer', Rule::in([0, 6, 12, 21])]
            ],
            'port' => ['required', 'numeric'],
            'btw' => ['required', 'boolean']
        ]);

        // Check if the client already exists
        $client = Client::firstOrCreate(
            [
                'name' => $validatedData['client']['name'],
                'address_one' => $validatedData['client']['address_one'],
                'address_two' => $validatedData['client']['address_two'],
                'address_three' => $validatedData['client']['address_three'],
                'btw' => $validatedData['client']['btw']
            ]
        );

        // Latest number
        $number = Invoice::max('number');

        // Create the invoice
        $invoice = $client->invoices()->create([
            'number' => $number + 1,
            'date' => $validatedData['date'],
            'port' => $validatedData['port'],
            'btw' => $validatedData['btw']
        ]);

        // Create the invoice items
        foreach ($validatedData['items'] as $item) {
            $invoice->items()->create($item);
        }

        return new InvoiceResource($invoice);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // Find the invoice
        $invoice = Invoice::findOrFail($request->id);

        // Return the invoice
        return new InvoiceResource($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the invoice
        $invoice = Invoice::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'date' => ['required', 'date'],
            'client' => [
                'name' => ['required', 'string'],
                'address_one' => ['required', 'string'],
                'address_two' => ['nullable', 'string'],
                'address_three' => ['nullable', 'string'],
                'btw' => ['required', 'string']
            ],
            'items' => [
                '*.description' => ['required', 'string'],
                '*.price' => ['required', 'numeric'],
                '*.amount' => ['required', 'integer'],
                '*.btw' => ['required', 'integer', Rule::in([0, 6, 12, 21])]
            ],
            'port' => ['required', 'numeric'],
            'btw' => ['required', 'boolean']
        ]);

        // Check if the client already exists
        $client = Client::firstOrCreate(
            [
                'name' => $validatedData['client']['name'],
                'address_one' => $validatedData['client']['address_one'],
                'address_two' => $validatedData['client']['address_two'],
                'address_three' => $validatedData['client']['address_three'],
                'btw' => $validatedData['client']['btw']
            ]
        );

        // Update the invoice
        $invoice->update([
            'date' => $validatedData['date'],
            'port' => $validatedData['port'],
            'btw' => $validatedData['btw']
        ]);

        // Update the invoice items
        $invoice->items()->delete();
        foreach ($validatedData['items'] as $item) {
            $invoice->items()->create($item);
        }

        // Update the client
        $client->invoices()->save($invoice);

        return new InvoiceResource($invoice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // Find the invoice
        $invoice = Invoice::findOrFail($request->id);
        $returnInvoice = $invoice;

        // Delete the invoice
        $invoice->delete();

        // Return the invoice
        return new InvoiceResource($returnInvoice);
    }
}
