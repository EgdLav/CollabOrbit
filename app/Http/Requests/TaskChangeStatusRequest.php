<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskChangeStatusRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $workspace = $this->route('workspace');
        $task = $this->route('task');
        return $task && $this->user()->can('changeStatus', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:new,in progress,on edge,completed',
        ];
    }
}
