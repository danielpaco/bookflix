<?php

namespace App\Http\Resources;
use App\Http\Resources\ProgressResource;
use App\Http\Resources\BookListResource;
use App\Http\Resources\CategoryResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'continue_reading' =>
                ProgressResource::collection(
                    $this['continue_reading']
                ),

            'popular' =>
                BookListResource::collection(
                    $this['popular']
                ),

            'new' =>
                BookListResource::collection(
                    $this['new']
                ),

            'premium' =>
                BookListResource::collection(
                    $this['premium']
                ),

            'categories' =>
                CategoryResource::collection(
                    $this['categories']
                ),

            'recommended' =>
                BookListResource::collection(
                    $this['recommended']
                ),
        ];
    }
}
