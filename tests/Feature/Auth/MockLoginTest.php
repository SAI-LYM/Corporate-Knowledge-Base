<?php

namespace Tests\Feature\Auth;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MockLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, DepartmentSeeder::class]);
    }

    private function makeUser(string $roleName): User
    {
        return User::create([
            'name' => 'Test '.$roleName,
            'email' => fake()->unique()->safeEmail(),
            'role_id' => Role::where('name', $roleName)->value('id'),
            'department_id' => Department::where('code', 'ISD')->value('id'),
        ]);
    }

    public function test_login_page_shows_mock_picker(): void
    {
        $this->makeUser(Role::FRESHER);

        $this->get('/login')->assertOk()->assertSee('Demo user');
    }

    public function test_mock_login_authenticates_and_redirects_to_dashboard(): void
    {
        $user = $this->makeUser(Role::FRESHER);

        $this->post('/login/mock', ['user_id' => $user->id])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_ends_the_session(): void
    {
        $user = $this->makeUser(Role::ADMIN);

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_mock_login_is_blocked_when_driver_is_not_mock(): void
    {
        config(['portal.auth_driver' => 'oauth']);
        $user = $this->makeUser(Role::FRESHER);

        $this->post('/login/mock', ['user_id' => $user->id])->assertNotFound();
        $this->assertGuest();
    }

    public function test_mock_login_is_blocked_in_production(): void
    {
        // Driver stays "mock" but the environment guard must still refuse.
        // (Flipping the env disables runningUnitTests()'s CSRF bypass, so skip CSRF
        // explicitly to reach the controller's production guard.)
        $this->app->detectEnvironment(fn () => 'production');
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $user = $this->makeUser(Role::FRESHER);

        $this->post('/login/mock', ['user_id' => $user->id])->assertNotFound();
        $this->assertGuest();
    }
}
