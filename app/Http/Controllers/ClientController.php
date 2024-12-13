<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get a list of all the clients
        $clients = Client::get();

        // Return the clients
        return ClientResource::collection($clients);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // Find the client
        $client = Client::findOrFail($request->id);

        // Return the client
        return new ClientResource($client);
    }
}
