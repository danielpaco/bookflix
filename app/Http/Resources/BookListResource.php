<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookListResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id'=>$this->id,

            'title'=>$this->title,

            'description' => $this->description,

            'author'=>$this->author,

            'cover'=>$this->cover,

            'pages'=>$this->pages_count,

            'premium'=>$this->is_premium,

            'categories'=>CategoryResource::collection(
                $this->whenLoaded('categories')
            ),

            'tags'=>TagResource::collection(
                $this->whenLoaded('tags')
            )

        ];
    }
}
