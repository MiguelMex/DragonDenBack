<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');
        return [
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($userId, 'user_id'),
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'admin' => 'sometimes|boolean',
        ];
    }
}
