<?php

namespace App\Services;

use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

/**
 * The single XSS-safe Markdown pipeline for all user content (CLAUDE.md §3).
 *
 * 1. CommonMark renders Markdown → HTML with raw HTML stripped and unsafe links
 *    disabled (so authors can't inject <script> or javascript: URLs).
 * 2. HTMLPurifier sanitises the output as defence in depth.
 *
 * The raw Markdown stays the source of truth; only this method's result is ever
 * emitted with {!! !!}. Used by both Articles and Projects.
 */
class MarkdownRenderer
{
    public function toSafeHtml(?string $markdown): string
    {
        if (blank($markdown)) {
            return '';
        }

        $html = Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return Purifier::clean($html);
    }
}
