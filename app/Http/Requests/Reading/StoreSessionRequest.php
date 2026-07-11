<?php

namespace App\Http\Requests\Reading;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'book_id' => 'required|exists:books,id',

            'start_page' => 'required|integer|min:1',

            'end_page' => 'required|integer|min:1',
            
            'duration_seconds' => 'required|integer|min:1',

        ];
    }
}