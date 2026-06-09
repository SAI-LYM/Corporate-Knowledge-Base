<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\OnboardingChecklistItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
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

    private function fresherTransport(): User
    {
        return User::where('email', 'suda.meechai@company.com')->first();
    }

    private function isdSpecificItem(): OnboardingChecklistItem
    {
        return OnboardingChecklistItem::where('department_id', Department::where('code', 'ISD')->value('id'))
            ->firstOrFail();
    }

    public function test_index_shows_progress_for_the_user(): void
    {
        // Nattapong (ISD) is seeded with 3 of 8 items completed → 38%.
        $this->actingAs($this->fresherIsd())
            ->get(route('onboarding.index'))
            ->assertOk()
            ->assertSee('Activate your Microsoft 365 account')
            ->assertSee('Read the ISD Coding Standards') // department-specific item
            ->assertSee('38%');
    }

    public function test_department_specific_items_are_scoped_to_department(): void
    {
        // Transportation fresher must NOT see the ISD-specific item.
        $this->actingAs($this->fresherTransport())
            ->get(route('onboarding.index'))
            ->assertOk()
            ->assertDontSee('Read the ISD Coding Standards');
    }

    public function test_toggling_persists_and_can_be_undone(): void
    {
        $user = $this->fresherIsd();
        $item = $this->isdSpecificItem(); // not completed in the seed

        // Tick it.
        $this->actingAs($user)->post(route('onboarding.toggle', $item))
            ->assertRedirect(route('onboarding.index'));
        $this->assertDatabaseHas('user_checklist_progress', [
            'user_id' => $user->id,
            'checklist_item_id' => $item->id,
        ]);
        $this->assertNotNull(
            $user->checklistProgress()->where('checklist_item_id', $item->id)->first()->completed_at
        );

        // Un-tick it.
        $this->actingAs($user)->post(route('onboarding.toggle', $item));
        $this->assertDatabaseMissing('user_checklist_progress', [
            'user_id' => $user->id,
            'checklist_item_id' => $item->id,
        ]);
    }

    public function test_cannot_toggle_another_departments_item(): void
    {
        // Transportation fresher tries to tick the ISD-specific item → forbidden.
        $this->actingAs($this->fresherTransport())
            ->post(route('onboarding.toggle', $this->isdSpecificItem()))
            ->assertForbidden();
    }
}
