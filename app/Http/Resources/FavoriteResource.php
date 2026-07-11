<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id'=>$this->id,

            'book'=>new BookResource(
                $this->whenLoaded('book')
            ),

            'created_at'=>$this->created_at,

        ];
    }
}