<?php

namespace App\Http\Requests\Reading;

use Illuminate\Foundation\Http\FormRequest;

class ProcessBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'book_id'=>'required|exists:books,id',
            
            'pdf_path'=>'required|string'

        ];
    }
}