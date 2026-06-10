<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Department;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

/**
 * Admin CRUD for Articles (CLAUDE.md §1 Admin module). Every action is gated by
 * ArticlePolicy (Admin always passes; the same Policy also lets a Senior author
 * edit their own work elsewhere). Input is validated by ArticleRequest, tags are
 * synced through the pivot, and the slug is generated/kept unique server-side.
 */
class ArticleController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Article::class);

        return view('admin.articles.index', [
            'articles' => Article::with(['department', 'category', 'author'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Article::class);

        return view('admin.articles.form', $this->formData(new Article));
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        $this->authorize('create', Article::class);

        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['author_id'] = $request->user()->id;

        $article = Article::create($data);
        $article->tags()->sync($request->validated('tags', []));

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "Article “{$article->title}” created.");
    }

    public function edit(Article $article): View
    {
        $this->authorize('update', $article);

        return view('admin.articles.form', $this->formData($article));
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['title'], $article->id);

        $article->update($data);
        $article->tags()->sync($request->validated('tags', []));

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "Article “{$article->title}” updated.");
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        $title = $article->title;
        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "Article “{$title}” deleted.");
    }

    /** Shared select options + current model for the create/edit form. */
    private function formData(Article $article): array
    {
        return [
            'article' => $article,
            'departments' => Department::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'selectedTags' => $article->exists ? $article->tags->pluck('id')->all() : [],
        ];
    }

    /** Slugify the title, keeping it unique (ignoring the row being updated). */
    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $n = 2;

        while (Article::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }
}
