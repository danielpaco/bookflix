<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'status' => $this->status,

            'starts_at' => $this->starts_at,

            'expires_at' => $this->expires_at,

            'provider' => $this->provider,

            'plan' => new PlanResource(
                $this->whenLoaded('plan')
            ),

        ];
    }
}