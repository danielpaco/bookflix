<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'book_id'=>$this->book_id,

            'reaction'=>$this->reaction,

        ];
    }
}