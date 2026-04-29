<?php

namespace App\Http\Requests;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class TaskStoreRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $workspace = $this->route('workspace');
        return $this->user()->can('createTask', $workspace);
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'preview' => 'nullable|image|max:10240',
            'files' => 'nullable|array|max:20',
            'files.*' => 'file|max:10240',
            'executor_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::exists('user_workspace', 'user_id')->where(function ($q) use ($workspace) {
                    $q->where('workspace_id', $workspace->id);
                }),
            ],
            'due_date' => 'required|date|after_or_equal:today',
        ];
    }
}
