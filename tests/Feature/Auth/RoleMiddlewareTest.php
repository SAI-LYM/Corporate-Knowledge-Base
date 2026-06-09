<?php

namespace Tests\Feature\Auth;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, DepartmentSeeder::class]);

        // A throwaway route guarded by the role middleware under test.
        Route::middleware(['web', 'auth', 'role:Senior'])
            ->get('/_test/senior-area', fn () => 'ok');
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => $role,
            'email' => fake()->unique()->safeEmail(),
            'role_id' => Role::where('name', $role)->value('id'),
            'department_id' => Department::where('code', 'ISD')->value('id'),
        ]);
    }

    public function test_fresher_is_forbidden_from_senior_area(): void
    {
        $this->actingAs($this->user(Role::FRESHER))
            ->get('/_test/senior-area')
            ->assertForbidden();
    }

    public function test_senior_and_admin_may_enter_senior_area(): void
    {
        $this->actingAs($this->user(Role::SENIOR))->get('/_test/senior-area')->assertOk();
        $this->actingAs($this->user(Role::ADMIN))->get('/_test/senior-area')->assertOk();
    }
}
