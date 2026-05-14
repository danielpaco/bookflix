<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'title' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'author' => [
                'nullable',
                'string'
            ],

            'cover' => [
                'nullable',
                'image',
                'max:2048'
            ],

            'pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'max:51200'
            ],

            'is_premium' => [
                'boolean'
            ]
        ];
    }
}