<?php

namespace App\Http\Requests\Reading;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'last_page' => 'required|integer|min:1',
            
            'reading_time_seconds' => 'required|integer|min:0',

        ];
    }
}