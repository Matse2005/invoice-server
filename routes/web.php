<?php

use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use App\Models\InvoiceLink;
use Illuminate\Support\Facades\Http;
use Limenet\LaravelPdf\Pdf;
use Spatie\Browsershot\Browsershot;

function generateInvoicePdf(int $invoiceId)
{
    $invoice = Invoice::findOrFail($invoiceId);
    $view = view('invoice', ['invoice' => $invoice])->render();

    $response = Http::withHeaders([
        'Content-Type' => 'application/json'
    ])->post(env('BROWSERLESS_URL', 'https://production-sfo.browserless.io/pdf?token=YOUR_API_TOKEN_HERE'), [
        'html' => $view,
        'options' => [
            'landscape' => false,
            'format' => 'A4',
            'printBackground' => true
        ]
    ]);

    if ($response->failed()) {
        throw new \Exception('PDF generation failed: ' . $response->body());
    }

    return $response->body();
}

Route::get('/', function () {
    return view('welcome');
});

Route::get('/status', function () {
    echo "Server is up and running...";
    return;
});

// Route::get('/invoice/{id}', function (int $id) {
//     $invoice = Invoice::findOrFail($id);
//     $view = view('invoice', ['invoice' => $invoice])->render();

//     $curl = curl_init();

//     curl_setopt_array($curl, [
//         CURLOPT_URL => env('BROWSERLESS_URL', 'https://production-sfo.browserless.io/pdf?token=YOUR_API_TOKEN_HERE'),
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING => "",
//         CURLOPT_MAXREDIRS => 10,
//         CURLOPT_TIMEOUT => 30,
//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//         CURLOPT_CUSTOMREQUEST => "POST",
//         CURLOPT_POSTFIELDS => json_encode([
//             'url' => 'data:text/html;base64,' . base64_encode($view),
//             'options' => [
//                 'fullPage' => true,
//                 'encoding' => 'base64'
//             ]
//         ]),
//         CURLOPT_HTTPHEADER => [
//             "Content-Type: application/json"
//         ],
//     ]);

//     $response = curl_exec($curl);
//     $err = curl_error($curl);

//     curl_close($curl);

//     if ($err) {
//         return response()->json(['error' => 'cURL Error: ' . $err], 500);
//     } else {
//         $data = json_decode($response, true);
//         if (isset($data['base64'])) {
//             $screenshot = base64_decode($data['base64']);
//             return response($screenshot, 200)
//                 ->header('Content-Type', 'image/png')
//                 ->header('Content-Disposition', 'inline; filename="invoice.png"');
//         } else {
//             return response()->json(['error' => 'Invalid response from screenshot API'], 500);
//         }
//     }
// });

Route::get('/invoice/{id}', function (int $id) {
    try {
        $pdf = generateInvoicePdf($id);
        return response($pdf, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice.pdf"');
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/invoice/view/{token}', function (string $token) {
    $invoiceLink = InvoiceLink::where('token', $token)
        ->where('expires_at', '>', now())
        ->firstOrFail();

    $invoice = Invoice::findOrFail($invoiceLink->invoice_id);
    $invoiceLink->delete();

    try {
        $pdf = generateInvoicePdf($invoice->id);
        return response($pdf, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice.pdf"');
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
