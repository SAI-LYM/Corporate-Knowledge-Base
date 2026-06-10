<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class OAuthDomainTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, DepartmentSeeder::class]);
        config([
            'portal.auth_driver' => 'oauth',
            'portal.allowed_domain' => 'company.com',
        ]);
    }

    private function fakeAzureUser(string $email): void
    {
        $socialiteUser = new SocialiteUser;
        $socialiteUser->id = 'oid-'.md5($email);
        $socialiteUser->name = 'External Person';
        $socialiteUser->email = $email;

        $provider = new class($socialiteUser)
        {
            public function __construct(private SocialiteUser $user) {}

            public function user(): SocialiteUser
            {
                return $this->user;
            }
        };

        Socialite::shouldReceive('driver')->with('azure')->andReturn($provider);
    }

    public function test_user_outside_allowed_domain_is_rejected(): void
    {
        $this->fakeAzureUser('intruder@evil.com');

        $this->get('/auth/azure/callback')->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'intruder@evil.com']);
    }

    public function test_company_user_is_provisioned_as_fresher_and_logged_in(): void
    {
        $this->fakeAzureUser('new.hire@company.com');

        $this->get('/auth/azure/callback')->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $user = User::where('email', 'new.hire@company.com')->first();
        $this->assertNotNull($user);
        $this->assertSame(Role::FRESHER, $user->role->name); // default role on first login
        $this->assertNull($user->password);                  // no plain-text password
    }
}
