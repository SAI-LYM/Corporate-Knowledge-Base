<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates an Admin assigning a user's role + department (CLAUDE.md §3 —
 * validate all input at the boundary). The Policy gate lives in the controller
 * (UserPolicy::update); this class only validates the two assignable axes, so a
 * crafted request can never touch name/email/password via mass assignment.
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // controller calls $this->authorize('update', $user)
    }

    public function rules(): array
    {
        return [
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ];
    }
}
