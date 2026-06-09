# MON Internal Developer Portal — Build Guide

Companion to `CLAUDE.md`. Two parts:
- **Part A — Seed data plan** (what `#seed` should create).
- **Part B — Ordered prompts** for Claude Code (copy-paste, one at a time).

Keep this file in the repo root next to `CLAUDE.md` so Claude Code can read it.

---

## Part A — Seed Data Plan

> Goal: realistic Thai + English demo data that exercises every RBAC path
> (role × department × audience level) and gives the search/filter features real content.

### Roles (3)
| Role | Notes |
|---|---|
| Fresher | Default for any new login. Read-only, sees onboarding + general content for their dept. |
| Senior | Can author articles and register projects. Sees advanced content. |
| Admin | Manages users, roles, departments, and all content. |

### Departments (6)
| Department | Purpose |
|---|---|
| Company-wide | Global content — visible to **everyone**, regardless of department. |
| Information Systems (ISD) | The MIS/ISD tech team (engineering standards, project docs). |
| Human Resources | Leave, payroll, workplace basics. |
| Warehouse | Warehouse operations + safety. |
| Transportation | Transport operations. |
| Container Depot | Depot operations. |

### Audience levels (3)
`onboarding` (first-week / new-hire) · `general` (everyday reference) · `advanced` (deep / senior).

### Users (8) — used by the mock login
| Name (EN) | Name (TH) | Role | Department |
|---|---|---|---|
| Somchai Jaidee | สมชาย ใจดี | Admin | Information Systems |
| Anan Srisuk | อนันต์ ศรีสุข | Senior | Information Systems |
| Pranee Wongsuk | ปราณี วงศ์สุข | Senior | Human Resources |
| Kanya Phongphan | กัญญา พงษ์พันธ์ | Senior | Warehouse |
| Nattapong Rakthai | ณัฐพงษ์ รักษ์ไทย | Fresher | Information Systems |
| Malee Suwan | มาลี สุวรรณ | Fresher | Human Resources |
| Wichai Charoenporn | วิชัย เจริญพร | Fresher | Warehouse |
| Suda Meechai | สุดา มีชัย | Fresher | Transportation |

### Categories (9)
Getting Started · Company & Culture · IT & Accounts · Systems & Tools · HR & Workplace ·
Policies & Compliance · Glossary & Acronyms · Engineering Standards · Project Docs

### Tags (free, many-to-many)
`onboarding`, `microsoft-365`, `vpn`, `wms`, `infor`, `pdpa`, `safety`, `git`,
`deployment`, `leave`, `payroll`, `glossary`, `access-request`

### Articles (12) — spread across every dept/level
| # | Title | Dept | Category | Level | Min role |
|---|---|---|---|---|---|
| 1 | Welcome to MON Logistics / ยินดีต้อนรับสู่ MON Logistics | Company-wide | Company & Culture | onboarding | Fresher |
| 2 | New Employee First-Week Guide / คู่มือสัปดาห์แรกของพนักงานใหม่ | Company-wide | Getting Started | onboarding | Fresher |
| 3 | MON's Four Business Divisions Explained | Company-wide | Company & Culture | general | Fresher |
| 4 | Setting Up Your Microsoft 365 Account (Teams & Outlook) | Information Systems | IT & Accounts | onboarding | Fresher |
| 5 | Wi-Fi, VPN & Password Resets | Information Systems | IT & Accounts | onboarding | Fresher |
| 6 | Logistics & WMS Glossary | Company-wide | Glossary & Acronyms | general | Fresher |
| 7 | Leave, Holidays & Payroll Basics | Human Resources | HR & Workplace | onboarding | Fresher |
| 8 | PDPA & Data Handling Policy | Company-wide | Policies & Compliance | general | Fresher |
| 9 | Warehouse Safety Essentials | Warehouse | Policies & Compliance | onboarding | Fresher |
| 10 | WMS (Infor) — Overview & Access Request | Information Systems | Systems & Tools | general | Senior |
| 11 | ISD Coding Standards & Git Workflow | Information Systems | Engineering Standards | advanced | Senior |
| 12 | Internal Deployment Process | Information Systems | Engineering Standards | advanced | Senior |

> Article bodies: a few short paragraphs of realistic Markdown each (headings, a list, a
> link). Items 1, 2, 4, 7 should include some **Thai** text to prove UTF-8 + Prompt font.
> Items 11–12 are `advanced`/Senior so you can demo that a **Fresher cannot see them**.

### Projects (4) — Project Registry, all `advanced` / min role Senior
| Name | Department | Tech | Status |
|---|---|---|---|
| WMS (Infor) Integration Layer | Information Systems | PHP, REST, SQL | Active |
| MON UOB Payment Project | Information Systems | PHP, UOB API | Active |
| Truck & Trailer Validation | Information Systems | PHP, MySQL | Active |
| Nestlé Material Serving Project | Warehouse | WMS, integration | Active |

> Each project: `repo_url` (placeholder internal URL), `owner_id` (a Senior),
> `readme_markdown` (a short overview + setup section).

### Onboarding checklist template (8 items, per-user progress)
1. Activate your Microsoft 365 account
2. Join your team's Microsoft Teams channel
3. Set up VPN access
4. Read "Welcome to MON" + the First-Week Guide
5. Acknowledge the PDPA & Data Handling Policy
6. Meet your manager and team
7. Request access to the systems you need (e.g. WMS)
8. Department-specific: ISD → read Coding Standards · Warehouse → complete Safety briefing

> Seed each Fresher with this checklist, **partially completed** (e.g. first 2–3 ticked) so
> the progress bar shows a real percentage in the demo.

---

## Part B — Ordered Prompts for Claude Code

> Feed these **one at a time**. Review each result before sending the next. Don't paste them
> all at once. Where a `#` command exists in `CLAUDE.md`, the prompt uses it.

### Prompt 1 — Align before building (no code yet)
```
Read CLAUDE.md and BUILD_GUIDE.md fully. Do NOT write any code yet.
Summarize back to me: the stack, the RBAC model (role x department x audience_level
and the visibleTo scope), and the hard constraints you must follow. Then list the order
you plan to build the features. Flag anything ambiguous or that you'd push back on.
```
*Check: does its summary match the files? Fix misunderstandings here — it's free.*

### Prompt 2 — Theme & base layout foundation
```
Set up the visual foundation only (no features yet):
- Configure tailwind.config.js with the MON tokens from sample/common.css and the Prompt
  font (Thai + Latin).
- Build a base Blade layout: blue gradient background, a white header card with
  sample/MONLogo.png, and a content container — matching the MON look.
- Add a simple placeholder home page using the layout so I can see the theme render.
```
*Check: run `npm run dev` + `php artisan serve` — does it look like MON (blue, Prompt font, card)?*

### Prompt 3 — Database schema
```
Set up the database schema with migrations and Eloquent models + relations:
users, roles, departments, articles, categories, tags (article_tag pivot), projects,
and an onboarding_checklist_items + user_checklist_progress structure.
Include the article fields department_id, category_id, audience_level, min_role.
Add the Article::visibleTo($user) query scope described in CLAUDE.md.
Run the migrations. Do not seed yet.
```
*Check: `php artisan migrate:fresh` runs clean; relations and FKs look right.*

### Prompt 4 — Auth + RBAC layer
```
Build the auth and RBAC layer per CLAUDE.md:
- The mock login driver (AUTH_DRIVER=mock) — pick a seeded user by role/department, no Azure.
- Wire Laravel Socialite + the Microsoft Entra ID provider for the AUTH_DRIVER=oauth path,
  domain-restricted to AUTH_ALLOWED_DOMAIN. Make mock unreachable when APP_ENV=production.
- Role middleware on routes + Policies for write actions.
- 8-hour session lifetime and a working logout.
```
*Check: log in as each role via mock; confirm logout works.*

### Prompt 5 — Seed the data
```
#seed
Use the exact dataset in BUILD_GUIDE.md Part A: 8 users, 6 departments, 3 roles,
9 categories, the tag list, 12 articles (include Thai text in items 1,2,4,7), 4 projects,
and the 8-item onboarding checklist with each Fresher partially completed.
```
*Check: `migrate:fresh --seed` populates everything; Thai renders correctly.*

### Prompt 6 — Knowledge Base (the core)
```
Build the Knowledge Base:
- An article index with filters by department, category, and tag, plus keyword search.
- All reads go through Article::visibleTo(auth user) — no bypassing it.
- An article detail page that renders body_markdown through the XSS-safe pipeline
  (CommonMark strip-HTML + Purifier), showing author, last-updated, and tags.
```
*Check: log in as Fresher (ISD) — you must NOT see articles 11 & 12 (advanced/Senior),
even by guessing their URL. Log in as Anan (Senior ISD) — you should see them.*

### Prompt 7 — Onboarding checklist
```
Build the onboarding checklist feature for the logged-in user: show their items with
checkboxes, save progress to user_checklist_progress, and show a progress bar/percentage.
Department-specific items appear based on the user's department.
```
*Check: ticking an item persists across reload; percentage updates.*

### Prompt 8 — Project Registry
```
Build the Project Registry: a list of projects (owner, tech stack, status, repo link) and
a detail page rendering readme_markdown via the same safe Markdown pipeline. Projects are
advanced/Senior — enforce visibility via Policy + visibleTo, same as articles.
```
*Check: Freshers can't open projects; Seniors/Admin can.*

### Prompt 9 — Admin panel (optional but impressive)
```
Build a minimal Admin area (Admin role only): manage users (assign role + department),
and create/edit/delete articles and projects. Enforce the Admin policy on every action.
```
*Check: a Senior or Fresher hitting an admin URL gets 403.*

### Prompt 10 — Security pass + Definition of Done
```
#rbac-check
Then run the full Definition of Done from CLAUDE.md: php artisan test, Laravel Pint,
migrate:fresh --seed on an empty DB, and confirm the smoke test path works:
onboarding -> read article -> register project. Report anything that fails.
```
*Check: this is your pre-submission gate. Everything green = ready to demo.*

---

### Tips
- If a step's output drifts from `CLAUDE.md`, just say: *"Re-read CLAUDE.md §X and fix this
  to follow it."*
- Commit to git after each green step (Prompt 10 of each feature) so you can roll back.
- The very first time Claude Code needs a pattern, it will create a `sample-stack/` example
  first (per CLAUDE.md §4) — let it; that becomes the template for later features.
