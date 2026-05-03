<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvitationUpdateRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $invitation = $this->route('invitation');
        return $invitation && $this->user()->can('update', $invitation);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:accepted,declined'],
        ];
    }
}
