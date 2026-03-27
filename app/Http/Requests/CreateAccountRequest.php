<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAccountRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'type' => 'required|in:Checking,Saving,Minor',
        'overdraft_limit' => 'required_if:type,Saving|nullable|numeric|min:0',
        'interest_rate' => 'required_if:type,Minor|nullable|numeric|min:0|max:100',
        'guardian_id' => 'required_if:type,Minor|nullable|exists:users,id',
        'child_id' => 'required_if:type,Minor|exists:users,id'
        ];
    }
}

