<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category'     => 'string|nullable|max:255',
            'keywords'     => 'string|nullable|max:255',
            'page'         => 'numeric|nullable',
            'published_at' => 'string|nullable|max:255',
            'source'       => 'string|nullable|max:255',
        ];
    }
}
