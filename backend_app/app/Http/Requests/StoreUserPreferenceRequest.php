<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\UserPreference;

class StoreUserPreferenceRequest extends FormRequest
{
    // preference types
    const PREFERENCE_TYPES = [
        UserPreference::TYPE_ARTICLES,
        UserPreference::TYPE_AUTHORS,
        UserPreference::TYPE_CATEGORIES,
        UserPreference::TYPE_SOURCES,
    ];
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
            'type' => [Rule::in(self::PREFERENCE_TYPES)],
            'preference_master_id' => 'numeric|required',
        ];
    }
}
