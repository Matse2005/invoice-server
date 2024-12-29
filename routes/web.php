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

Route::get('/status', function () {
    echo "Server is up and running...";
    return;
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

    $browsershot = Browsershot::html(view('invoice', ['invoice' => $invoice])->render())
        ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
        ->setOption('executablePath', env('BROWSERLESS_URL', 'https://chrome.browserless.io?token=YOUR_BROWSERLESS_API_TOKEN'));


    // $pdf = Pdf()->withBrowsershot($browsershot)->view('invoice', ['invoice' => $invoice]);
    $pdf = Pdf::loadView('invoice', ['invoice' => $invoice])
        ->withBrowsershot($browsershot);

    return $pdf->name('pdf.pdf');

    // $pdf = Pdf::loadView('invoice', ['invoice' => $invoice]);
    // return $pdf->stream('factuur-' . $invoice->number . '.pdf');
});
