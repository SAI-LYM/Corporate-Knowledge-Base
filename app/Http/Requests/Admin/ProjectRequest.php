<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates create/edit of a Project in the Admin panel (CLAUDE.md §3).
 * Mirrors ArticleRequest; the Policy gate (ProjectPolicy) lives in the
 * controller. readme_markdown is stored raw and rendered XSS-safe on read.
 */
class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // controller calls $this->authorize() against ProjectPolicy
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'repo_url' => ['required', 'url', 'max:255'],
            'tech_stack' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'readme_markdown' => ['required', 'string'],
            'owner_id' => ['required', 'integer', 'exists:users,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'audience_level' => ['required', Rule::in(Article::AUDIENCE_LEVELS)],
            'min_role' => ['required', 'integer', Rule::in([
                Role::RANK_FRESHER, Role::RANK_SENIOR, Role::RANK_ADMIN,
            ])],
        ];
    }
}
