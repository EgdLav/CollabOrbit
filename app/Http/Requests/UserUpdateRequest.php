<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name'  => ['sometimes', 'string', 'max:255'],
            'bio'        => ['nullable', 'string'],
            'department' => ['sometimes', 'in:Backend Development,Frontend Development,Engineering,Mobile Development,DevOps,Quality Assurance,Data Engineering,Data Science,Product Management,UI/UX Design,Graphic Design,Research & Analytics,Marketing,Sales,Business Development,Human Resources,Finance,Legal,Operations,Public Relations,Copywriting'],
            'avatar'     => ['sometimes', 'image', 'max:2048'],
        ];
    }
}
