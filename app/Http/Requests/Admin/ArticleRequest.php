<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates create/edit of an Article in the Admin panel (CLAUDE.md §3 —
 * Form Request at the boundary). Drives both store and update; the Policy gate
 * (ArticlePolicy) is applied in the controller. Only the raw Markdown is stored;
 * it is rendered XSS-safe on read via MarkdownRenderer.
 */
class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // controller calls $this->authorize() against ArticlePolicy
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'body_markdown' => ['required', 'string'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'audience_level' => ['required', Rule::in(Article::AUDIENCE_LEVELS)],
            'min_role' => ['required', 'integer', Rule::in([
                Role::RANK_FRESHER, Role::RANK_SENIOR, Role::RANK_ADMIN,
            ])],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }
}
