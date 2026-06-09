# Internal Developer Portal — MON Logistics (Knowledge Hub)

This file guides Claude when working in this repository. It is the project's operating
manual (SOP) — **instructions to follow, not documentation to read**. Read it fully
before writing any code.

> **Stack note:** This is a *Laravel rebuild*. The legacy MON `sample/` folder is
> **PHP 5.6 + vanilla JS + plain-text JSON auth** and is referenced for **visual identity
> and business domain only** — **never** as a code pattern. All code follows the
> Laravel / Blade / SQLite stack defined below.

> **Project nature:** This is an **academic / portfolio mini-project**, **localhost-first**,
> built and graded on a developer laptop. It is *architected as if* it were a real MON
> internal tool, but production deployment is out of scope (see §5). Optimise for: clean
> relational schema, real RBAC, and security hygiene — those are what evaluators reward.

---

## 📑 Table of Contents

1. [Project Context](#1-project-context)
2. [Folder Structure](#2-folder-structure)
3. [Hard Constraints (Never Violate)](#3-hard-constraints--never-violate)
4. [Reference Patterns](#4-reference-patterns)
5. [Workflow Rules](#5-workflow-rules)
6. [Custom Commands](#6-custom-commands)
7. [Output Strategy](#7-output-strategy)
8. [Pinned Dependencies](#8-pinned-dependencies)
9. [Delivery Checklist](#9-delivery-checklist)

---

## 1. Project Context

- **Company (context):** MON Logistics Group — 3PL logistics.
- **What it is:** An **Internal Developer Portal (IDP) / Corporate Knowledge Base** — one
  place where employees find onboarding material, company knowledge, coding standards, and
  registered internal projects/repos.
- **Dual audience (the core idea):**
  - **Freshers (new hires):** guided onboarding — "where do I find X", checklists, culture,
    standards. Not overwhelmed with deep architecture docs.
  - **Seniors:** a place to *store and find* project READMEs, tech specs, and repo links.
  - The app stays useful across the **whole employee lifecycle**.
- **Languages:** Bilingual UI — **Thai and English**. Content (titles, articles) may be in
  Thai. Use the **Prompt** font (renders Thai + Latin cleanly); UTF-8 end to end
  (DB, app, views).
- **Environment:** **Localhost-first.** Runs on a laptop (Laragon / XAMPP / `php artisan
  serve`). Do not assume or write production config.

### Core Modules

| Module | Description |
|---|---|
| **Onboarding Hub** | Role-filtered onboarding articles + checklists for freshers (culture, "where to find things", first-week tasks). |
| **Knowledge Base** | Markdown articles organised by Department → Category → Tags. The searchable core. |
| **Project Registry** | Seniors register internal projects: repo link, tech stack, owner, and a Markdown README/spec. |
| **Admin** | Manage users, roles, departments, categories, and content visibility. |

### User Roles (3) + Department

Authorization has **two axes**: a **role** (seniority/permission level) and a
**department** (content scope).

| Role | Can do |
|---|---|
| **Fresher** | Read content marked for their level + department. Tick onboarding checklists. |
| **Senior** | Everything a Fresher can, **plus** create/edit articles and register projects. |
| **Admin** | Full access — manage users, roles, departments, and all content. |

> **Content visibility model (the interesting RBAC part):** every Article/Project carries a
> `department_id`, an `audience_level` (`onboarding` / `general` / `advanced`), and a
> `min_role`. A user sees an item only when **department matches (or item is global)** **AND**
> **their role ≥ `min_role`** **AND** their level can see `audience_level`. Implement this as
> a reusable **Eloquent query scope** (e.g. `Article::visibleTo($user)`), never as ad-hoc
> `if` checks scattered across controllers.

> Enforce role/visibility checks on the **server** (authoritative, via Policies/Gates +
> middleware) **and** in Blade (for display only). **Never trust the client for
> authorization.**

---

## 2. Folder Structure

> Standard Laravel layout. The legacy MON PHP lives in `sample/` for **theme + domain
> reference only**.

```
[root]
├── CLAUDE.md                 ← this file (project memory / SOP)
├── .env.example              ← documented env vars (NO real secrets)
├── .env                      ← real local secrets — git-ignored, never commit
├── composer.json / composer.lock   ← PHP deps (lockfile committed)
├── package.json              ← Vite + Tailwind toolchain
├── sample/                   ← legacy MON PHP — THEME & DOMAIN reference only
│   ├── common.css            ← MON theme source (extract tokens, do NOT copy code)
│   ├── theme.css             ← secondary theme reference
│   ├── MONLogo.png           ← brand logo asset (reuse)
│   └── ...                   ← (admin_users.json = forbidden auth pattern; see §3)
├── app/
│   ├── Models/               ← Eloquent models (User, Role, Department, Article, …)
│   ├── Http/
│   │   ├── Controllers/      ← thin controllers
│   │   ├── Requests/         ← Form Requests (validation at the boundary)
│   │   └── Middleware/       ← role / auth guards
│   ├── Policies/             ← per-model authorization (the RBAC enforcement layer)
│   └── Services/             ← business logic (Markdown render, content filtering, OAuth)
├── database/
│   ├── migrations/           ← schema, versioned (NEVER edit the DB directly)
│   ├── seeders/              ← realistic Thai + English demo data
│   └── factories/
├── resources/
│   ├── views/                ← Blade templates + components (MON-themed)
│   ├── css/ + js/            ← Tailwind entry (consumes MON tokens, see §4)
├── routes/web.php            ← routes (role middleware applied here)
├── sample-stack/             ← canonical SAME-STACK Laravel examples (see §4)
└── tests/                    ← Pest/PHPUnit (Feature + Unit)
```

---

## 3. Hard Constraints — Never Violate

> Every **NO** is paired with what to use instead. These keep the codebase consistent,
> secure, and worth full marks.

### Stack & Tooling (pinned)

- **PHP 8.2+ ONLY.** Do not use features unavailable in 8.2.
- **Laravel 12.x ONLY** (current stable). No mixing major versions.
- **Composer ONLY** for PHP deps; **npm** for the Vite/Tailwind toolchain. Commit
  `composer.lock` and `package-lock.json`.
- **Server-rendered Blade** is the frontend (PHP's strength — serving content). Light
  vanilla JS / Alpine is fine; **do not** introduce a React/Vue SPA.
- **NO `@latest`, no `dev-master`, no `"*"` constraints.** Pin exact versions (§8).

### Database Rules

- **SQLite (local dev)** as the single source of truth. **NO JSON flat-files for data** — the
  legacy `*.json` data approach is **forbidden**.
- **NEVER edit the database directly.** Change a **migration**, run
  `php artisan migrate`. Schema lives in `database/migrations`.
- **NEVER build SQL with string concatenation** (the legacy pattern). Use **Eloquent / the
  query builder** with bound parameters. Raw SQL only if unavoidable, and then with
  bindings + a justifying comment.
- **Relational, not flat.** Use real relations: `users ↔ roles`, `users ↔ departments`,
  `articles ↔ categories`, `articles ↔ tags` (many-to-many). Foreign keys + indexes on
  every relation. This schema is the star of the project — make it clean.

### Auth & Session

- **Primary auth: Microsoft Entra ID (Azure AD) OAuth via Laravel Socialite.** Restrict
  logins to the company domain (`AUTH_ALLOWED_DOMAIN=company.com`) — reject anyone else
  server-side.
- **Dev/demo fallback: a mock login driver** gated behind `AUTH_DRIVER=mock` (local only).
  It logs in seeded users of each role/department **without** contacting Azure, so the app
  always runs at a demo. `AUTH_DRIVER=oauth` switches to the real flow. The mock path must
  be **impossible to reach when `APP_ENV=production`**.
- **NEVER store plain-text passwords.** The legacy `admin_users.json` plain-text pattern is
  **forbidden**. If a local email+password fallback is ever added, hash with **bcrypt**.
- **Session timeout: 8 hours** (`config/session.php` lifetime). Provide working logout.
- RBAC is **independent of the login method** — roles/departments come from the DB and are
  assigned on first login (default `Fresher`) or by an Admin.

### Authorization (RBAC) — STRICT

- **Every protected route** carries auth + role middleware in `routes/web.php`.
- **Every write/sensitive action** is gated by a **Policy** (`ArticlePolicy`,
  `ProjectPolicy`, …) — not inline `if ($user->role == ...)`.
- **Content reads go through the `visibleTo($user)` query scope** (§1). A Fresher must never
  receive an `advanced` or wrong-department item, even via a direct URL/ID. Test this.
- Blade may **hide** UI the user can't use, but the server is the source of truth.

### Markdown Rendering — XSS-SAFE

- Authors write in **Markdown**; store the **raw Markdown** string in the DB.
- Render with **`league/commonmark`** (via `Str::markdown()`), configured to **escape or
  strip raw HTML input** (`html_input => 'strip'`, `allow_unsafe_links => false`).
- **Then sanitize** the rendered HTML with **HTMLPurifier (`mews/purifier`)** before display.
  Never `{!! $html !!}` un-sanitized user content — that is a direct XSS hole and an
  automatic mark-down. Cache the rendered HTML if you like, but the raw Markdown stays the
  source of truth.

### File Uploads (attachments / images)

- Stored on the **Laravel filesystem** (`storage/app`), **not** in the DB. The DB stores
  only **path + metadata** (type, size, uploaded_at, uploader).
- **Validate type by MIME + extension** (not extension alone) and enforce a **size cap**
  (e.g. ≤10 MB). Generate a **sanitized, unique filename** — never trust the original.
- Reject anything outside the allowlist server-side.

### Security & Secrets

- **CSRF on every form** (`@csrf`). It's on by default — do not disable it.
- **Mass-assignment safe:** define `$fillable` (or use Form Requests `->validated()`).
  Never `Model::create($request->all())` with `$guarded = []`.
- **Validate all input** at the boundary via **Form Requests**.
- **NEVER commit secrets.** All keys/credentials live in `.env` (git-ignored). Ship
  `.env.example` with keys but **no values** (incl. `AZURE_CLIENT_ID`, `AZURE_CLIENT_SECRET`,
  `AZURE_REDIRECT_URI`, `AUTH_ALLOWED_DOMAIN`, `AUTH_DRIVER`).
- Escape output in Blade with `{{ }}` (default). Reserve `{!! !!}` for sanitized Markdown
  HTML only.

---

## 4. Reference Patterns

> **Do not write from memory. Read the relevant reference before writing.**

This project uses a **two-source** reference model.

### A. MON Visual Identity — from legacy `sample/` (theme only)

The Blade UI must **keep the MON look**. Extract design intent from `sample/common.css` /
`sample/theme.css` and express it as **Tailwind tokens** in a CSS-first `@theme` block in
`resources/css/app.css` (Tailwind v4 — there is no `tailwind.config.js`) — do **not**
copy the PHP/CSS files wholesale. Reuse `sample/MONLogo.png`. Canonical MON tokens:

| Token | Value | Use |
|---|---|---|
| `mon-primary` | `#0066a1` | Primary brand blue (buttons, links, headers, accents) |
| `mon-primary-hover` | `#004d7a` | Hover / active state |
| `mon-primary-dark` | `#003d66` | Gradient end / dark headers |
| `mon-gradient` | `linear-gradient(135deg, #0066a1 0%, #003d66 100%)` | Login / page background |
| `mon-success` | `#28a745` | Success states |
| `mon-danger` | `#dc3545` | Errors / required marks |
| `mon-surface` | `#f8f9fa` | Section background |
| `mon-tint` | `#f0f8ff` / `#e6f2ff` | Light blue panels, hovered table rows |
| Card | radius `10px`, shadow `0 5px 15px rgba(0,0,0,0.1)` | Standard card |
| Font | **Prompt** (Google Fonts) | All UI — Thai + English |

> Put these in the `@theme` block of `resources/css/app.css` as the single source of truth.
> Blade components reference tokens (`bg-mon-primary`, `font-sans`, …), **never** hard-coded
> hex values. (If you prefer fewer build steps, you *may* import `sample/common.css` directly
> into a Blade layout — but the token set above is still the canonical reference.)

### B. Same-stack code patterns — `sample-stack/` (added as we build)

Because the legacy PHP can't show how to write this stack, we keep a small set of
**canonical Laravel examples**. Once present, Claude **copies these patterns** for new
features:

| File (sample-stack/) | What to learn |
|---|---|
| `migration.example.php` | Table + FK + index + enum-style column conventions. |
| `Model.example.php` | Relationships, `$fillable`, `scopeVisibleTo()` query scope. |
| `Policy.example.php` | Per-model authorization (the RBAC enforcement layer). |
| `Controller.example.php` | Thin controller → Form Request → Service → Blade view. |
| `view.example.blade.php` | MON-themed Blade page/component using Tailwind tokens. |
| `markdown.example.php` | Safe Markdown render (CommonMark strip-HTML + Purifier). |
| `upload.example.php` | Validated file upload (MIME + size, sanitized name, DB stores path). |

> These don't all exist yet. When asked to build a feature whose pattern is missing,
> **create the canonical example first, confirm it, then reuse it.** If `sample-stack/` is
> empty for a pattern, follow §2–§3 and ask if unsure.

---

## 5. Workflow Rules

### 1. Version control: git
- Use **git** as the safety net (no manual backup folders).
- Commit (or branch) **before** risky changes. Small, reviewable commits.
- Never commit `.env`, secrets, `vendor/`, `node_modules/`, or `storage` uploads.

### 2. Database changes: migrations only
- **Never edit the DB directly.** `php artisan make:migration` → edit → `php artisan migrate`.
  Commit migration files. Use `migrate:fresh --seed` to reset local data.

### 3. Local run (localhost-first)
- `composer install` → `npm install` → copy `.env.example` to `.env` → `php artisan key:generate`.
- `php artisan migrate --seed` → `npm run dev` (Vite) → `php artisan serve`.
- Default `AUTH_DRIVER=mock` for instant local demo; set `oauth` only when Azure creds exist.

### 4. Definition of Done (run before every handoff)
A task is **not done** until all pass:
- [ ] `php artisan test` green (Feature + Unit).
- [ ] **Laravel Pint** clean (code style).
- [ ] Migrations run cleanly on a fresh DB (`migrate:fresh --seed`).
- [ ] Manual smoke test of the flow: **onboarding → read article → register project**.
- [ ] RBAC verified: a Fresher **cannot** reach an `advanced`/other-department item by URL.

### 5. Deploy
- **Out of scope — localhost only.** Do not write production/server config. `#deploy` only
  runs the DoD checks and reminds that hosting + real Azure registration are future work.

---

## 6. Custom Commands

> Prefix `#`. One command → multiple steps → complete output. Labels in English.

### `#help`
List all commands with a one-line description each.

### `#file [instruction]`
Write the file(s) directly to disk and **confirm the path**. Do **not** paste full file
contents into chat — keep chat clean.

### `#scaffold [feature]`
Generate a full vertical slice in the Laravel stack, following `sample-stack/`:
migration → model (+`visibleTo` scope where relevant) → policy → Form Request →
controller → routes (with role middleware) → MON-themed Blade views.

### `#article`
Define/seed an **Article**: `title`, `body_markdown`, `department_id`, `category_id`,
`audience_level` (onboarding / general / advanced), `min_role`, `tags[]`, `author_id`,
`created_at`. Renders via the safe Markdown pipeline (§3).

### `#project`
Register a **Project**: `name`, `repo_url`, `tech_stack`, `owner_id`, `department_id`,
`readme_markdown`, `audience_level` (default `advanced`), `min_role` (default `Senior`).

### `#migrate [desc]`
Safe migration flow only: `php artisan make:migration [desc]` → edit → `php artisan migrate`.
**Never** touches the DB directly.

### `#seed`
Populate the local DB with realistic demo data: users across all roles/departments
(Thai + English names), categories, tags, onboarding articles, and sample projects.
Local/dev only.

### `#rbac-check`
Scan a feature or diff for authorization/security gaps: routes missing auth/role middleware,
controllers without a Policy check, content reads bypassing `visibleTo()`, `{!! !!}` on
un-sanitized input, mass-assignment risks, committed secrets, unvalidated uploads.

### `#report [period]`
Build a simple Blade dashboard: content counts by department/category, most-viewed articles,
onboarding-checklist completion. Period filter: today / this week / this month.

### `#deploy`
Localhost-first. Runs the **Definition of Done** (§5.4) and reminds that hosting + real
Azure app registration are future work. Does **not** deploy.

---

## 7. Output Strategy

> **The right format for the job.** Blade for UI, Eloquent/SQLite for data, the filesystem
> for binaries, Markdown for content + docs.

| Output | Format | Why |
|---|---|---|
| App screens (onboarding / KB / projects / admin) | **Blade + Tailwind (MON tokens)** | The live product; PHP serving content |
| Business logic | **Services + Policies + Form Requests** | Thin controllers, testable logic |
| Data | **SQLite via Eloquent** | Source of truth (no JSON files) |
| Article / README content | **Markdown in DB**, rendered safely | Authors' preferred format; XSS-safe on render |
| File attachments | **Laravel filesystem**; DB stores path + metadata | Binaries out of the DB |
| Schema changes | **Migration files** | Versioned, reviewable |
| `#report` dashboard | **Blade page** (counts + simple charts) | Quick admin insight |
| Config / docs / this file | **Markdown** | Read by AI & humans |

**Primary deliverables:** the Onboarding Hub, the role-filtered Knowledge Base, and the
Project Registry — all MON-themed (§4), all gated by real RBAC (§3).

---

## 8. Pinned Dependencies

> `@latest` = time bomb. Verify the latest stable on install, then **pin exact versions** —
> do not float. Starting points for Laravel 12 / PHP 8.2+:

| Package | Pin | Use |
|---|---|---|
| `php` | `8.2.x` | Runtime (Laravel 12 minimum) |
| `laravel/framework` | `12.x` (pin patch on install, e.g. `12.61.x`) | Framework |
| `laravel/socialite` | `5.27.0` | OAuth client |
| `socialiteproviders/microsoft-azure` | `5.2.0` | Microsoft Entra ID provider |
| `league/commonmark` | `2.8.2` | Markdown → HTML (via `Str::markdown()`) |
| `mews/purifier` | `3.4.4` | HTML sanitization (XSS defence) |
| `tailwindcss` + `@tailwindcss/vite` | `4.3.0` (CSS-first `@theme`, no `tailwind.config.js`) | Styling (MON tokens) |
| `vite` + `laravel-vite-plugin` | `vite 6.4.3` + `laravel-vite-plugin 1.3.0` | Asset build |
| `laravel/pint` (dev) | pin on install | Code style |
| `pestphp/pest` *(optional, else PHPUnit)* | `3.x` | Tests |

**Fonts:** Prompt via Google Fonts, pinned weights.
**RBAC:** hand-rolled (`roles`/`departments` tables + Policies) to demonstrate DB skill.
`spatie/laravel-permission` (`6.x`) is an acceptable alternative if you prefer a library —
pick one, don't mix.

> When adding any dependency: pin the exact version here first, then install.

---

## 9. Delivery Checklist

Run before every handoff — do not skip:

- [ ] `php artisan test` green; **Pint** clean.
- [ ] `migrate:fresh --seed` runs cleanly on an empty DB; `#seed` produces Thai + English data.
- [ ] Login works for **mock driver** (demo) and the **OAuth driver** is wired for Entra ID,
      domain-restricted to `company.com`.
- [ ] Mock login is unreachable when `APP_ENV=production`.
- [ ] Session timeout (8 h) works; logout works.
- [ ] **No plain-text passwords** anywhere (legacy `admin_users.json` pattern absent).
- [ ] RBAC enforced on the **server**: routes have role middleware; writes have Policies.
- [ ] Content reads use `visibleTo($user)`; a Fresher **cannot** open an `advanced` /
      other-department item by direct URL/ID — verified by a test.
- [ ] Markdown renders **XSS-safe** (CommonMark strip-HTML + Purifier; no raw `{!! !!}` on
      user input).
- [ ] Uploads: MIME + extension + size validated, sanitized filename, DB stores path +
      metadata only.
- [ ] CSRF on all forms; mass-assignment guarded; all input validated via Form Requests.
- [ ] No secrets committed; `.env` git-ignored; `.env.example` present and current.
- [ ] All dependencies pinned (no `@latest`) and listed in §8.
- [ ] Bilingual UI renders Thai + English correctly (UTF-8 end to end, Prompt font); MON
      theme applied via Tailwind tokens.
- [ ] Smoke test of the full flow: **onboarding → read article → register project**.
- [ ] Changes committed to git.
