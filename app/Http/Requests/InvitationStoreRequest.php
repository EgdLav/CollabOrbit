<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvitationStoreRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $workspace = $this->route('workspace');
        return $workspace && $this->user()->can('createInvitation', $workspace);
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
            'invitee_id' => ['required', 'exists:users,id', ],
        ];
    }
}
