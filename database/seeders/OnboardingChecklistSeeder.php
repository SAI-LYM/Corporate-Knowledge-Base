<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\OnboardingChecklistItem;
use App\Models\Role;
use App\Models\User;
use App\Models\UserChecklistProgress;
use Illuminate\Database\Seeder;

/**
 * The 8-item onboarding checklist template (BUILD_GUIDE.md Part A): items 1–7
 * are global, item 8 is department-specific (ISD / Warehouse). Each Fresher is
 * seeded with a few items already ticked so the progress bar shows a real %.
 */
class OnboardingChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $isd = Department::where('code', 'ISD')->value('id');
        $wh = Department::where('code', 'WH')->value('id');

        // 1–7: global (department_id = null); 8: department-specific.
        $items = [
            [1, null, 'Activate your Microsoft 365 account', 'Email, Teams and OneDrive access.'],
            [2, null, "Join your team's Microsoft Teams channel", null],
            [3, null, 'Set up VPN access', 'For working off-site.'],
            [4, null, 'Read "Welcome to MON" + the First-Week Guide', null],
            [5, null, 'Acknowledge the PDPA & Data Handling Policy', null],
            [6, null, 'Meet your manager and team', null],
            [7, null, 'Request access to the systems you need (e.g. WMS)', null],
            [8, $isd, 'Read the ISD Coding Standards', 'Department-specific (ISD).'],
            [8, $wh, 'Complete the Warehouse Safety briefing', 'Department-specific (Warehouse).'],
        ];

        foreach ($items as [$position, $deptId, $title, $description]) {
            OnboardingChecklistItem::updateOrCreate(
                ['position' => $position, 'department_id' => $deptId],
                ['title' => $title, 'description' => $description],
            );
        }

        // Partially complete each Fresher's checklist (BUILD_GUIDE: first 2–3 ticked).
        $completedCounts = [
            'nattapong.rakthai@company.com' => 3, // ISD
            'malee.suwan@company.com' => 2,        // HR
            'wichai.charoenporn@company.com' => 3, // Warehouse
            'suda.meechai@company.com' => 2,       // Transportation
        ];

        $freshers = User::whereHas('role', fn ($q) => $q->where('name', Role::FRESHER))
            ->whereIn('email', array_keys($completedCounts))
            ->get();

        foreach ($freshers as $fresher) {
            $visibleItems = OnboardingChecklistItem::forUser($fresher)->get();
            $toComplete = $visibleItems->take($completedCounts[$fresher->email]);

            foreach ($toComplete as $i => $item) {
                UserChecklistProgress::updateOrCreate(
                    ['user_id' => $fresher->id, 'checklist_item_id' => $item->id],
                    ['completed_at' => now()->subDays(count($toComplete) - $i)],
                );
            }
        }
    }
}
