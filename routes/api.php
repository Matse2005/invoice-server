<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Middleware\JsonResponse;
use App\Http\Middleware\KeyValidation;
use App\Models\Invoice;
use App\Models\InvoiceLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::middleware([JsonResponse::class, KeyValidation::class])->group(function () {
    Route::prefix('/invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::get('/{id}', [InvoiceController::class, 'show']);
        Route::post('/', [InvoiceController::class, 'store']);
        Route::put('/{id}', [InvoiceController::class, 'update']);
        Route::delete('/{id}', [InvoiceController::class, 'destroy']);
        Route::post('/request-link/{id}', function (int $id) {
            $invoice = Invoice::findOrFail($id);

            // Generate a unique token
            $token = Str::random(32);
            $expiresAt = now()->addMinutes(10); // Link is valid for 10 minutes

            // Store the token and expiration in the database
            InvoiceLink::create([
                'invoice_id' => $invoice->id,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

            return response()->json(['link' => url("/invoice/view/{$token}")]);
        });
    });

    Route::prefix('/clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::get('/{id}', [ClientController::class, 'show']);
    });
});
