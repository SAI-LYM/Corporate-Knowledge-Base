# MON Knowledge Hub — Internal Developer Portal

An **Internal Developer Portal / Corporate Knowledge Base** for **MON Logistics Group**
(a 3PL logistics company). One place where employees find onboarding material, company
knowledge, coding standards, and registered internal projects — useful across the whole
employee lifecycle, for new hires (**Freshers**) and **Seniors** alike.

> Academic / portfolio mini-project. **Localhost-first** — built and graded on a laptop.
> Bilingual UI (Thai + English), rendered with the **Prompt** font, UTF-8 end to end.

---

## ✨ What's inside

| Module | Description |
|---|---|
| **Onboarding Hub** | Role- & department-filtered checklist with per-user progress tracking. |
| **Knowledge Base** | Markdown articles organised by Department → Category → Tags, with search & filters. |
| **Project Registry** | Seniors' internal projects: repo link, tech stack, owner, and a Markdown README. |
| **Admin panel** | Admin-only: manage users (assign role + department) and full CRUD for articles & projects. |

### The interesting part — content-visibility RBAC

Authorization has **two axes**: a **role** (Fresher < Senior < Admin) and a **department**.
Every article/project carries a `department_id`, an `audience_level`
(`onboarding` / `general` / `advanced`), and a `min_role`. A user sees an item only when:

```
(department matches  OR  item is global)
  AND  user's role rank ≥ item's min_role
  AND  user's level may see the item's audience_level
```

This is enforced **on the server** as a reusable Eloquent scope (`visibleTo($user)`) plus
**Policies** — never trusting the client. A Fresher can't reach an `advanced` or
other-department item even by guessing its URL (it returns 404). Admins bypass all three axes.

---

## 🧰 Tech stack

- **PHP 8.2+** · **Laravel 12** · server-rendered **Blade** (no SPA)
- **SQLite** (single-file DB — zero setup)
- **Tailwind CSS v4** (CSS-first `@theme`, MON brand tokens) + **Vite**
- **Auth:** mock driver for local demo · Microsoft Entra ID (Azure AD) OAuth via Socialite
- **Markdown:** `league/commonmark` (raw HTML stripped) → **HTMLPurifier** (XSS-safe)
- **Tests:** PHPUnit (Feature + Unit) · **Laravel Pint** for code style

---

## 🚀 Quick start (localhost)

### Prerequisites

Install these once (standard for any Laravel project):

| Tool | Version | Check |
|---|---|---|
| **PHP** | 8.2 or newer (with `pdo_sqlite`, `mbstring`, `openssl` — on by default) | `php -v` |
| **Composer** | 2.x | `composer -V` |
| **Node.js + npm** | Node 18+ (20/22 fine) | `node -v` |

> Windows users: [Laragon](https://laragon.org/) bundles PHP + Composer + Node and is the
> easiest way to get all three. macOS/Linux: use Homebrew / your package manager.

### Setup — 6 steps

```bash
# 1. Clone and enter the project
git clone git@github.com:SAI-LYM/Corporate-Knowledge-Base.git
cd Corporate-Knowledge-Base

# 2. Install dependencies
composer install
npm install

# 3. Create your local env file + app key
cp .env.example .env          # Windows PowerShell:  Copy-Item .env.example .env
php artisan key:generate

# 4. Create the SQLite database file, then migrate + seed demo data
#    (bash/macOS/Linux)
touch database/database.sqlite
#    (Windows PowerShell)
#    New-Item -ItemType File database/database.sqlite
php artisan migrate --seed

# 5. Build front-end assets
npm run build                 # one-off production build
#    — or, for live-reloading during development, run in a second terminal:
#    npm run dev

# 6. Serve the app
php artisan serve
```

Open **http://127.0.0.1:8000** — the login page lists seeded demo users (mock driver).

> **Even faster:** with `npm run dev` and `php artisan serve` running, the project also
> exposes `composer run dev`, which launches the server, queue listener, and Vite together.

---

## 👤 Demo logins (mock driver)

`AUTH_DRIVER=mock` is the default, so **no password is needed** — just pick a user on the
login page. Suggested accounts to show off the RBAC:

| Role | Department | Email | Good for demoing |
|---|---|---|---|
| **Admin** | Information Systems | `somchai.jaidee@company.com` | Admin panel, user management, content CRUD |
| **Senior** | Information Systems | `anan.srisuk@company.com` | Project Registry, advanced articles |
| **Fresher** | Information Systems | `nattapong.rakthai@company.com` | Onboarding checklist; blocked from advanced/admin |
| **Fresher** | Warehouse | `wichai.charoenporn@company.com` | Department-scoped content visibility |

Other seeded users: `kanya.phongphan@` (Senior, Warehouse), `pranee.wongsuk@` (Senior, HR),
`malee.suwan@` (Fresher, HR), `suda.meechai@` (Fresher, Transportation).

### Try the RBAC (the headline feature)

1. Log in as the **Fresher** (Nattapong) → tick onboarding items, read a Knowledge Base article.
2. Manually visit `http://127.0.0.1:8000/admin` → you get **403 Forbidden**.
3. Try opening a Senior-only project URL directly, e.g. `/projects/wms-infor-integration-layer`
   → **404** (the scope hides it; it doesn't even reveal it exists).
4. Log out, log in as the **Admin** (Somchai) → the **⚙ Admin panel** appears; you can
   register projects, edit any content, and reassign a user's role/department.

---

## 🔐 Switching to real Microsoft Entra ID (optional)

The OAuth flow is wired but off by default. To use it, register an app in Entra ID and set
these in `.env` (then `AUTH_DRIVER=oauth`):

```env
AUTH_DRIVER=oauth
AUTH_ALLOWED_DOMAIN=company.com        # logins restricted to this domain, server-side
AZURE_CLIENT_ID=...
AZURE_CLIENT_SECRET=...
AZURE_TENANT_ID=...                    # or "common"
AZURE_REDIRECT_URI="${APP_URL}/auth/azure/callback"
```

Secrets live only in `.env` (git-ignored). The mock login is **automatically unreachable**
when `APP_ENV=production`.

---

## ✅ Verify it (Definition of Done)

```bash
php artisan test          # full Feature + Unit suite (41 tests)
vendor/bin/pint --test    # code-style check (should report PASS)
php artisan migrate:fresh --seed   # reset the DB to clean demo data
```

---

## 🗂️ Project layout

```
app/
├── Http/Controllers/         thin controllers (+ Admin/ for the admin panel)
├── Http/Requests/            Form Requests — validation at the boundary
├── Models/                   Eloquent models (+ Concerns/VisibleToUser scope)
├── Policies/                 per-model authorization (RBAC enforcement)
├── Services/                 MarkdownRenderer (XSS-safe pipeline), Azure provisioning
database/
├── migrations/               versioned schema (relational, FK + indexes)
└── seeders/                  realistic Thai + English demo data
resources/views/              MON-themed Blade (Onboarding / Knowledge / Projects / Admin)
sample-stack/                 canonical same-stack reference patterns
tests/Feature/                RBAC, auth, knowledge, onboarding, projects, admin
```

---

## 📝 Notes for evaluators

- **No setup beyond the 6 steps above** — SQLite needs no server; the mock driver needs no
  Azure account. The DB file and seed data are created by step 4.
- **Bilingual content** (Thai + English) is intentional and renders via the Prompt font.
- Production deployment is **out of scope** (localhost-first); real Azure registration and
  hosting are future work.
