<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\Department;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin panel (BUILD_GUIDE Prompt 9). Proves the whole area is Admin-only and
 * that every write goes through a Policy: a Senior/Fresher is 403 everywhere,
 * an Admin can manage users + CRUD articles & projects.
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', 'somchai.jaidee@company.com')->first();
    }

    private function senior(): User
    {
        return User::where('email', 'anan.srisuk@company.com')->first();
    }

    private function fresher(): User
    {
        return User::where('email', 'nattapong.rakthai@company.com')->first();
    }

    /** @return array<string> */
    private static function adminUrls(): array
    {
        return [
            '/admin',
            '/admin/users',
            '/admin/articles',
            '/admin/articles/create',
            '/admin/projects',
            '/admin/projects/create',
        ];
    }

    public function test_fresher_is_forbidden_from_every_admin_url(): void
    {
        foreach (self::adminUrls() as $url) {
            $this->actingAs($this->fresher())->get($url)->assertForbidden();
        }
    }

    public function test_senior_is_forbidden_from_every_admin_url(): void
    {
        foreach (self::adminUrls() as $url) {
            $this->actingAs($this->senior())->get($url)->assertForbidden();
        }
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_admin_can_open_every_admin_url(): void
    {
        foreach (self::adminUrls() as $url) {
            $this->actingAs($this->admin())->get($url)->assertOk();
        }
    }

    public function test_admin_can_assign_a_users_role_and_department(): void
    {
        $fresher = $this->fresher();
        $seniorRole = Role::where('name', Role::SENIOR)->first();
        $department = Department::where('is_global', false)->first();

        $this->actingAs($this->admin())
            ->put(route('admin.users.update', $fresher), [
                'role_id' => $seniorRole->id,
                'department_id' => $department->id,
            ])
            ->assertRedirect(route('admin.users.index'));

        $fresher->refresh();
        $this->assertSame($seniorRole->id, $fresher->role_id);
        $this->assertSame($department->id, $fresher->department_id);
    }

    public function test_non_admin_cannot_update_a_user(): void
    {
        $target = $this->fresher();
        $adminRole = Role::where('name', Role::ADMIN)->first();

        $this->actingAs($this->senior())
            ->put(route('admin.users.update', $target), [
                'role_id' => $adminRole->id,
                'department_id' => Department::first()->id,
            ])
            ->assertForbidden();

        $this->assertNotSame($adminRole->id, $target->fresh()->role_id);
    }

    public function test_admin_can_create_edit_and_delete_an_article(): void
    {
        $department = Department::first();
        $category = Category::first();

        // Create
        $this->actingAs($this->admin())
            ->post(route('admin.articles.store'), [
                'title' => 'Admin Created Article',
                'body_markdown' => '# Hello from admin',
                'department_id' => $department->id,
                'category_id' => $category->id,
                'audience_level' => 'general',
                'min_role' => Role::RANK_FRESHER,
            ])
            ->assertRedirect(route('admin.articles.index'));

        $article = Article::where('title', 'Admin Created Article')->firstOrFail();
        $this->assertSame('admin-created-article', $article->slug);
        $this->assertSame($this->admin()->id, $article->author_id);

        // Edit
        $this->actingAs($this->admin())
            ->put(route('admin.articles.update', $article), [
                'title' => 'Admin Edited Article',
                'body_markdown' => 'updated body',
                'department_id' => $department->id,
                'category_id' => $category->id,
                'audience_level' => 'advanced',
                'min_role' => Role::RANK_SENIOR,
            ])
            ->assertRedirect(route('admin.articles.index'));

        $this->assertSame('Admin Edited Article', $article->fresh()->title);
        $this->assertSame('advanced', $article->fresh()->audience_level);

        // Delete
        $this->actingAs($this->admin())
            ->delete(route('admin.articles.destroy', $article->fresh()))
            ->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_admin_can_create_and_delete_a_project(): void
    {
        $department = Department::first();
        $owner = $this->senior();

        $this->actingAs($this->admin())
            ->post(route('admin.projects.store'), [
                'name' => 'Admin Created Project',
                'repo_url' => 'https://git.example.com/admin/proj',
                'tech_stack' => 'PHP, SQL',
                'status' => 'Active',
                'readme_markdown' => '# Readme',
                'owner_id' => $owner->id,
                'department_id' => $department->id,
                'audience_level' => 'advanced',
                'min_role' => Role::RANK_SENIOR,
            ])
            ->assertRedirect(route('admin.projects.index'));

        $project = Project::where('name', 'Admin Created Project')->firstOrFail();
        $this->assertSame('admin-created-project', $project->slug);

        $this->actingAs($this->admin())
            ->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_non_admin_cannot_create_content(): void
    {
        $department = Department::first();
        $category = Category::first();

        $this->actingAs($this->senior())
            ->post(route('admin.articles.store'), [
                'title' => 'Sneaky',
                'body_markdown' => 'x',
                'department_id' => $department->id,
                'category_id' => $category->id,
                'audience_level' => 'general',
                'min_role' => Role::RANK_FRESHER,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('articles', ['title' => 'Sneaky']);
    }

    public function test_admin_create_article_validation_fails_on_bad_input(): void
    {
        $this->actingAs($this->admin())
            ->from(route('admin.articles.create'))
            ->post(route('admin.articles.store'), [
                'title' => '',
                'body_markdown' => '',
                'department_id' => 999999,
                'category_id' => 999999,
                'audience_level' => 'nonsense',
                'min_role' => 7,
            ])
            ->assertRedirect(route('admin.articles.create'))
            ->assertSessionHasErrors(['title', 'body_markdown', 'department_id', 'category_id', 'audience_level', 'min_role']);
    }
}
