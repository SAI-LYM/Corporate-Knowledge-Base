<?php

namespace App\Http\Controllers;

use App\Models\OnboardingChecklistItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Onboarding Hub checklist for the logged-in user. Items are the shared template
 * filtered to the user's department (global + their dept); completion is stored
 * per-user in user_checklist_progress (CLAUDE.md §1 / BUILD_GUIDE Part A).
 */
class OnboardingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $items = OnboardingChecklistItem::forUser($user)->get();

        $completedIds = $user->checklistProgress()
            ->whereNotNull('completed_at')
            ->pluck('checklist_item_id')
            ->all();

        $total = $items->count();
        $done = count($completedIds);

        return view('onboarding.index', [
            'items' => $items,
            'completedIds' => $completedIds,
            'total' => $total,
            'done' => $done,
            'percent' => $total > 0 ? (int) round($done / $total * 100) : 0,
        ]);
    }

    /** Toggle one checklist item's completion for the current user. */
    public function toggle(Request $request, OnboardingChecklistItem $item): RedirectResponse
    {
        $user = $request->user();

        // Only items in the user's own checklist (global or their department).
        abort_unless(
            $item->department_id === null || $item->department_id === $user->department_id,
            403,
        );

        $progress = $user->checklistProgress()
            ->where('checklist_item_id', $item->id)
            ->first();

        if ($progress && $progress->completed_at) {
            $progress->delete(); // un-tick
        } else {
            $user->checklistProgress()->updateOrCreate(
                ['checklist_item_id' => $item->id],
                ['completed_at' => now()],
            );
        }

        return redirect()->route('onboarding.index');
    }
}
