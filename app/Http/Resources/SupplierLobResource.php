<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierLobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'lob_category' => [
                'title' => $this->lobCategory?->title,
                'description' => $this->lobCategory?->description,
            ],
            'lob_subcategory' => $this->lobSubcategory ? [
                'title' => $this->lobSubcategory->title,
                'description' => $this->lobSubcategory->description,
            ] : null,
        ];
    }
}
