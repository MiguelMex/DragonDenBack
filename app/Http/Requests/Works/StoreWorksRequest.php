<?php

namespace App\Http\Requests\Works;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreWorksRequest extends FormRequest
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
            'title'=>'required|string|max:50',
            'description'=>'required|string|max:2000',
            'status'=>'required|boolean',
            'cover'=>'required|string',
            'age_restriction'=>['required',Rule::exists('age_restrictions','age_restriction_id')],
            'work_in_progress'=>['required',Rule::exists('wips','work_in_progress_id')],
        ];
    }
}
