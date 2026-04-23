<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkspaceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $workspace = $this->route('workspace');
        return $workspace && $this->user()->can('update', $workspace);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workspace = $this->route('workspace');
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('workspaces', 'slug')->ignore($workspace->id),
            ],
        ];
    }
}
