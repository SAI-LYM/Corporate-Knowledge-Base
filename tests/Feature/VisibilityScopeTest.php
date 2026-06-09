<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The content-visibility RBAC (CLAUDE.md §1) — the heart of the project.
 * A Fresher must never receive an advanced or wrong-department item.
 */
class VisibilityScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, DepartmentSeeder::class]);
        $this->category = Category::create(['name' => 'Eng', 'slug' => 'eng']);
    }

    private Category $category;

    private function user(string $role, string $deptCode): User
    {
        return User::create([
            'name' => "$role $deptCode",
            'email' => fake()->unique()->safeEmail(),
            'role_id' => Role::where('name', $role)->value('id'),
            'department_id' => Department::where('code', $deptCode)->value('id'),
        ]);
    }

    private function article(string $slug, string $deptCode, string $level, int $minRole, int $authorId): Article
    {
        return Article::create([
            'title' => $slug,
            'slug' => $slug,
            'body_markdown' => '...',
            'department_id' => Department::where('code', $deptCode)->value('id'),
            'category_id' => $this->category->id,
            'audience_level' => $level,
            'min_role' => $minRole,
            'author_id' => $authorId,
        ]);
    }

    public function test_fresher_cannot_see_advanced_senior_item_but_senior_and_admin_can(): void
    {
        $seniorIsd = $this->user(Role::SENIOR, 'ISD');
        $this->article('isd-advanced', 'ISD', 'advanced', Role::RANK_SENIOR, $seniorIsd->id);

        $fresherIsd = $this->user(Role::FRESHER, 'ISD');
        $adminIsd = $this->user(Role::ADMIN, 'ISD');

        $this->assertSame(0, Article::visibleTo($fresherIsd)->count(), 'Fresher must not see advanced item');
        $this->assertSame(1, Article::visibleTo($seniorIsd)->count());
        $this->assertSame(1, Article::visibleTo($adminIsd)->count(), 'Admin sees everything');
    }

    public function test_global_department_content_is_visible_across_departments(): void
    {
        $author = $this->user(Role::SENIOR, 'ISD');
        $this->article('welcome', 'ALL', 'onboarding', Role::RANK_FRESHER, $author->id);

        $fresherHr = $this->user(Role::FRESHER, 'HR');

        $this->assertSame(1, Article::visibleTo($fresherHr)->count(), 'Global content visible to all depts');
    }

    public function test_wrong_department_content_is_hidden(): void
    {
        $author = $this->user(Role::SENIOR, 'ISD');
        $this->article('isd-general', 'ISD', 'general', Role::RANK_FRESHER, $author->id);

        $fresherHr = $this->user(Role::FRESHER, 'HR');

        $this->assertSame(0, Article::visibleTo($fresherHr)->count(), 'Other-dept content is hidden');
    }

    public function test_policy_blocks_direct_view_of_hidden_article(): void
    {
        $seniorIsd = $this->user(Role::SENIOR, 'ISD');
        $advanced = $this->article('isd-secret', 'ISD', 'advanced', Role::RANK_SENIOR, $seniorIsd->id);

        $fresherIsd = $this->user(Role::FRESHER, 'ISD');

        $this->assertFalse($fresherIsd->can('view', $advanced), 'Fresher cannot view advanced item by direct access');
        $this->assertTrue($seniorIsd->can('view', $advanced));
    }
}
