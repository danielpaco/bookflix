<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Services\MediaService;

class BookResource extends JsonResource
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

            'title'=>$this->title,

            'description'=>$this->description,

            'author'=>$this->author,

            'cover' => MediaService::cover($this->cover),

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
