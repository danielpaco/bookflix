<?php

namespace App\Http\Requests\Reading;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'title'=>'sometimes|string|max:255',

            'description'=>'nullable|string',

            'author'=>'sometimes|string|max:255',
            
            'is_premium'=>'boolean'

        ];
    }
}