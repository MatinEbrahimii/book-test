<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PostBookRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:64',
            'description' => 'required|string|max:255',
            'isbn' => 'required|numeric|min:10|max:13',
            'authors' => 'required|array',
            'authors.*' => 'required|array',
            'authors.*.id' => 'nullable|integer',
            'authors.*.name' => 'nullable|string|max:64',
            'authors.*.surname' => 'nullable|string|max:64',
        ];
    }
}
