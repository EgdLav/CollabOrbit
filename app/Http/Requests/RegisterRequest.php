<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends ApiFormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'avatar' => 'nullable|image|max:10240',
            'department' => 'required|string|in:Backend Development,Frontend Development,Engineering,Mobile Development,DevOps,Quality Assurance,Data Engineering,Data Science,Product Management,UI/UX Design,Graphic Design,Research & Analytics,Marketing,Sales,Business Development,Human Resources,Finance,Legal,Operations,Public Relations,Copywriting'
        ];
    }
}
