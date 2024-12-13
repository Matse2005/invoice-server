<?php

use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use App\Models\InvoiceLink;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

// use Barryvdh\DomPDF\Facade\Pdf;

use function Spatie\LaravelPdf\Support\pdf;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invoice/{id}', function (int $id) {
    $invoice = Invoice::findOrFail($id);
    // return pdf()
    //     ->view('invoice', ['invoice' => $invoice])
    //     ->name('pdf.pdf');
    // return view('invoice', ['invoice' => $invoice]);

    $pdf = Pdf::loadView('invoice', ['invoice' => $invoice]);
    return $pdf->stream('factuur-' . $invoice->number . '.pdf');
});

Route::get('/invoice/view/{token}', function (string $token) {
    $invoiceLink = InvoiceLink::where('token', $token)
        ->where('expires_at', '>', now())
        ->firstOrFail();

    $invoice = Invoice::findOrFail($invoiceLink->invoice_id);
    $invoiceLink->delete();

    $pdf = Pdf()
        ->view('invoice', ['invoice' => $invoice]);

    return $pdf->name('pdf.pdf');

    // $pdf = Pdf::loadView('invoice', ['invoice' => $invoice]);
    // return $pdf->stream('factuur-' . $invoice->number . '.pdf');
});
