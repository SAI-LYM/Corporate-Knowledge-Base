<?php

/*
 |----------------------------------------------------------------------------
 | CANONICAL SAFE MARKDOWN RENDER  (reference only — see App\Services\MarkdownRenderer)
 |----------------------------------------------------------------------------
 | The XSS-safe pipeline required by CLAUDE.md §3. TWO layers, in order:
 |
 |   1. CommonMark (via Str::markdown) with raw HTML STRIPPED and unsafe links
 |      disabled — so author Markdown can't smuggle <script> or javascript: URLs.
 |   2. HTMLPurifier (mews/purifier) sanitises the resulting HTML as defence in
 |      depth, even though step 1 already stripped raw HTML.
 |
 | Only the output of this pipeline may be echoed with {!! !!} in Blade. The raw
 | Markdown string stays the source of truth in the DB; never render it any other way.
 */

use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

function render_safe_markdown(string $markdown): string
{
    // 1) Markdown → HTML, stripping any raw HTML the author typed.
    $html = Str::markdown($markdown, [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ]);

    // 2) Sanitise the HTML (whitelist of safe tags/attributes).
    return Purifier::clean($html);
}

// In Blade:  {!! render_safe_markdown($article->body_markdown) !!}
// NEVER:     {!! $article->body_markdown !!}   ← raw, unsanitised = XSS hole
