<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRegistryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function fresherIsd(): User
    {
        return User::where('email', 'nattapong.rakthai@company.com')->first();
    }

    private function seniorIsd(): User
    {
        return User::where('email', 'anan.srisuk@company.com')->first();
    }

    private function admin(): User
    {
        return User::where('email', 'somchai.jaidee@company.com')->first();
    }

    public function test_fresher_is_forbidden_from_the_project_registry(): void
    {
        // Project Registry is Senior+ only — a Fresher gets 403 (not an empty list).
        $this->actingAs($this->fresherIsd())
            ->get(route('projects.index'))
            ->assertForbidden();
    }

    public function test_fresher_cannot_open_a_project_by_url(): void
    {
        // Blocked by role:Senior middleware before reaching the visibleTo scope.
        $this->actingAs($this->fresherIsd())
            ->get(route('projects.show', 'wms-infor-integration-layer'))
            ->assertForbidden();
    }

    public function test_senior_sees_and_can_open_projects(): void
    {
        $this->actingAs($this->seniorIsd())
            ->get(route('projects.index'))
            ->assertOk()
            ->assertSee('WMS (Infor) Integration Layer');

        $this->actingAs($this->seniorIsd())
            ->get(route('projects.show', 'wms-infor-integration-layer'))
            ->assertOk()
            ->assertSee('Integration Layer')
            ->assertSee('PHP, REST, SQL');           // tech stack meta
    }

    public function test_admin_can_open_a_project_in_another_department(): void
    {
        // Nestlé project belongs to Warehouse; Admin (ISD) bypasses department.
        $this->actingAs($this->admin())
            ->get(route('projects.show', 'nestle-material-serving-project'))
            ->assertOk();
    }

    public function test_readme_is_rendered_xss_safe(): void
    {
        $project = Project::where('slug', 'wms-infor-integration-layer')->first();
        $project->update([
            'readme_markdown' => "# Readme\n\n<script>alert('proj-pwned')</script>\n\n[x](javascript:alert(1))",
        ]);

        $response = $this->actingAs($this->seniorIsd())
            ->get(route('projects.show', 'wms-infor-integration-layer'))
            ->assertOk()
            ->assertSee('Readme');

        $response->assertDontSee('proj-pwned');
        $response->assertDontSee('javascript:', false);
    }
}
