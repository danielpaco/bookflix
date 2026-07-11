<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReadingSessionResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id'=>$this->id,

            'book_id'=>$this->book_id,

            'start_page'=>$this->start_page,

            'end_page'=>$this->end_page,

            'duration_seconds'=>$this->duration_seconds,

            'created_at'=>$this->created_at,

        ];
    }
}