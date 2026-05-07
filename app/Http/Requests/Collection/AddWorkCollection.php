<?php

namespace App\Http\Requests\Collection;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddWorkCollection extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "work_id"=>['required',Rule::exists('works','work_id')],
            "collection_id"=>['required', Rule::exists('collections','collection_id')]
        ];
    }
}
