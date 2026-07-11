<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,

            'amount' => $this->amount,

            'status' => $this->status,

            'provider' => $this->provider,

            'created_at' => $this->created_at,

        ];
    }
}