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

            'book_id' => 'required|exists:books,id',
            
            'file'=>[
                'required',
                'file',
                'mimes:pdf',
                'max:102400' //100 MB
            ]

        ];
    }
}