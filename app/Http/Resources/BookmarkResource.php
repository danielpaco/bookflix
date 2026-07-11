<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id'=>$this->id,

            'book_id'=>$this->book_id,

            'page'=>$this->page_number,

            'created_at'=>$this->created_at,

        ];
    }
}