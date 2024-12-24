<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserPreference;

class UserPreferenceResource extends JsonResource
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
            'id'         => $this->id,
            'type'       => $this->type,
            'master'     => MasterResource::make($this->master),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
    // Add status attribute as Ok when the request is ok.
    public function with($request)
    {
        return [ 'status' => 'ok' ];
    }
}
