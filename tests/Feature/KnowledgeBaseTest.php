<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeBaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // full Part A dataset
    }

    private function fresherIsd(): User
    {
        return User::where('email', 'nattapong.rakthai@company.com')->first();
    }

    private function seniorIsd(): User
    {
        return User::where('email', 'anan.srisuk@company.com')->first();
    }

    public function test_fresher_index_hides_advanced_senior_articles(): void
    {
        $this->actingAs($this->fresherIsd())
            ->get(route('knowledge.index'))
            ->assertOk()
            ->assertSee('Welcome to MON Logistics')          // onboarding, visible
            ->assertDontSee('ISD Coding Standards &amp; Git Workflow')  // advanced/Senior
            ->assertDontSee('Internal Deployment Process');
    }

    public function test_senior_index_shows_advanced_articles(): void
    {
        $this->actingAs($this->seniorIsd())
            ->get(route('knowledge.index'))
            ->assertOk()
            ->assertSee('ISD Coding Standards');
    }

    public function test_fresher_cannot_open_advanced_article_by_url(): void
    {
        $this->actingAs($this->fresherIsd())
            ->get(route('knowledge.show', 'isd-coding-standards-git-workflow'))
            ->assertNotFound();
    }

    public function test_senior_can_open_advanced_article(): void
    {
        $this->actingAs($this->seniorIsd())
            ->get(route('knowledge.show', 'isd-coding-standards-git-workflow'))
            ->assertOk()
            ->assertSee('Branching');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('knowledge.index'))->assertRedirect(route('login'));
    }

    public function test_markdown_is_rendered_xss_safe(): void
    {
        $author = $this->seniorIsd();
        Article::create([
            'title' => 'XSS Probe',
            'slug' => 'xss-probe',
            'body_markdown' => "# Safe Heading\n\nNormal text.\n\n"
                ."<script>alert('xss-pwned')</script>\n\n"
                ."[click](javascript:alert('xss-pwned'))",
            'department_id' => Department::where('code', 'ISD')->value('id'),
            'category_id' => Category::first()->id,
            'audience_level' => 'onboarding',
            'min_role' => Role::RANK_FRESHER,
            'author_id' => $author->id,
        ]);

        $response = $this->actingAs($this->fresherIsd())
            ->get(route('knowledge.show', 'xss-probe'))
            ->assertOk()
            ->assertSee('Safe Heading');          // benign Markdown still renders

        // The script payload and javascript: URL must be gone.
        $response->assertDontSee('xss-pwned');
        $response->assertDontSee('javascript:', false);
    }

    public function test_category_filter_narrows_results(): void
    {
        $this->actingAs($this->seniorIsd())
            ->get(route('knowledge.index', ['category' => 'engineering-standards']))
            ->assertOk()
            ->assertSee('ISD Coding Standards')
            ->assertDontSee('Welcome to MON Logistics');
    }
}
