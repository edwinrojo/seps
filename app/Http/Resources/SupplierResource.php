<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'business_name' => $this->business_name,
            'supplier_type' => $this->supplier_type,
            'lobs' => SupplierLobResource::collection($this->whenLoaded('supplierLobs')),
        ];
    }
}
