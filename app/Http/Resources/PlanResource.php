<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'description' => $this->description,

            'price' => $this->price,

            'duration_days' => $this->duration_days,

            'active' => (bool) $this->is_active,

        ];
    }
}