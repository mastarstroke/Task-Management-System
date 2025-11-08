<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user?->name,
            'action' => $this->action,
            'ip_address' => $this->ip_address,
            'details' => $this->details,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
