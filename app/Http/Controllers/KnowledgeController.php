<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Department;
use App\Models\Tag;
use App\Services\MarkdownRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * The Knowledge Base (CLAUDE.md §1 core). Every read goes through
 * Article::visibleTo($user) — a Fresher can never receive an advanced or
 * wrong-department item, even by guessing a slug (detail uses the same scope
 * and 404s on anything not visible).
 */
class KnowledgeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'department' => ['nullable', 'string', 'exists:departments,code'],
            'category' => ['nullable', 'string', 'exists:categories,slug'],
            'tag' => ['nullable', 'string', 'exists:tags,slug'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $articles = Article::visibleTo($request->user())
            ->with(['department', 'category', 'author', 'tags'])
            ->when($filters['department'] ?? null, fn ($query, $code) =>
                $query->whereHas('department', fn ($q) => $q->where('code', $code)))
            ->when($filters['category'] ?? null, fn ($query, $slug) =>
                $query->whereHas('category', fn ($q) => $q->where('slug', $slug)))
            ->when($filters['tag'] ?? null, fn ($query, $slug) =>
                $query->whereHas('tags', fn ($q) => $q->where('slug', $slug)))
            ->when($filters['q'] ?? null, fn ($query, $term) =>
                $query->where(fn ($q) => $q
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('body_markdown', 'like', "%{$term}%")))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('knowledge.index', [
            'articles' => $articles,
            'departments' => Department::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, string $slug, MarkdownRenderer $markdown): View
    {
        // visibleTo scope = 404 for hidden items (don't reveal their existence).
        $article = Article::visibleTo($request->user())
            ->with(['department', 'category', 'author', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $article->increment('view_count');

        return view('knowledge.show', [
            'article' => $article,
            'html' => $markdown->toSafeHtml($article->body_markdown),
        ]);
    }
}
