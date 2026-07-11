<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'book_id'=>$this->book_id,

            'last_page'=>$this->last_page,

            'progress'=>$this->progress_percent,

            'reading_time'=>$this->reading_time_seconds,

            'updated_at'=>$this->updated_at,

        ];
    }
}
