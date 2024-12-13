<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'date' => date('Y-m-d', strtotime($this->date)),
            'client' => new ClientResource($this->client),
            'port' => $this->port,
            'btw' => $this->btw,
            'items' => ItemResource::collection($this->items)
        ];
    }
}
