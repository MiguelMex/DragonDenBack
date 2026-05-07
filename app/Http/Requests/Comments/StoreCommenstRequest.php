<?php

namespace App\Http\Requests\Comments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommenstRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'text' => 'required|string|max:10000',
            'chapter' => ['required',Rule::exists('chapters','chapter_id')],
            'reponse' => ['nullable',Rule::exists('comments','comment_id')],
        ];
    }
}
