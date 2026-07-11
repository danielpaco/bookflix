<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [

            'id'=>$this->id,
            
            'page'=>$this->page_number,

            'width'=>$this->width,

            'height'=>$this->height

        ];
    }
}
