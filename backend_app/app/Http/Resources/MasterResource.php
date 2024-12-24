<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // Data resource structure.
    public function toArray(Request $request): array
    {   
        return [
            'name' => isset($this->name) ? $this->name : $this->title,
            'url'  => $this->url,
        ];
    }
}
