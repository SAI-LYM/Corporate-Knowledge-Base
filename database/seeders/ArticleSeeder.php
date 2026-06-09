<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 12 demo articles spread across every department / audience level
 * (BUILD_GUIDE.md Part A). Items 1, 2, 4, 7 include Thai to prove UTF-8 +
 * the Prompt font; items 11–12 are advanced/Senior to demo that a Fresher
 * cannot see them.
 */
class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $depts = Department::pluck('id', 'code');
        $cats = Category::pluck('id', 'slug');
        $authors = User::pluck('id', 'email');
        $tags = Tag::pluck('id', 'slug');

        foreach ($this->articles() as $a) {
            $article = Article::updateOrCreate(
                ['slug' => $a['slug']],
                [
                    'title' => $a['title'],
                    'body_markdown' => $a['body'],
                    'department_id' => $depts[$a['dept']],
                    'category_id' => $cats[$a['cat']],
                    'audience_level' => $a['level'],
                    'min_role' => $a['min_role'],
                    'author_id' => $authors[$a['author']],
                ],
            );

            $article->tags()->sync(
                collect($a['tags'])->map(fn ($slug) => $tags[$slug])->all(),
            );
        }
    }

    private function articles(): array
    {
        $admin = 'somchai.jaidee@company.com';   // Admin, ISD
        $isd = 'anan.srisuk@company.com';        // Senior, ISD
        $hr = 'pranee.wongsuk@company.com';      // Senior, HR
        $wh = 'kanya.phongphan@company.com';     // Senior, Warehouse

        return [
            [
                'slug' => 'welcome-to-mon-logistics',
                'title' => 'Welcome to MON Logistics / ยินดีต้อนรับสู่ MON Logistics',
                'dept' => 'ALL', 'cat' => 'company-culture', 'level' => 'onboarding',
                'min_role' => Role::RANK_FRESHER, 'author' => $admin, 'tags' => ['onboarding'],
                'body' => <<<'MD'
# Welcome to MON Logistics / ยินดีต้อนรับ

We are glad to have you on board. MON Logistics Group is a leading **3PL logistics**
provider in Thailand, serving warehousing, transportation, and container-depot customers.

ยินดีต้อนรับสู่ครอบครัว MON Logistics 🎉 เราดีใจที่คุณมาร่วมงานกับเรา
คู่มือนี้จะช่วยให้คุณเริ่มต้นได้อย่างราบรื่นในสัปดาห์แรก

## What you'll find here
- Onboarding guides for your first week
- Company knowledge and policies
- Where to find people, tools, and systems

Start with the [First-Week Guide](/knowledge/new-employee-first-week-guide).
MD,
            ],
            [
                'slug' => 'new-employee-first-week-guide',
                'title' => 'New Employee First-Week Guide / คู่มือสัปดาห์แรกของพนักงานใหม่',
                'dept' => 'ALL', 'cat' => 'getting-started', 'level' => 'onboarding',
                'min_role' => Role::RANK_FRESHER, 'author' => $admin, 'tags' => ['onboarding'],
                'body' => <<<'MD'
# Your First Week / สัปดาห์แรกของคุณ

A simple checklist to get productive quickly.

## Day 1
1. Activate your Microsoft 365 account
2. Join your team's Microsoft Teams channel
3. Meet your manager

## สัปดาห์แรก (ภาษาไทย)
- ตั้งค่าบัญชีอีเมลและ Microsoft Teams
- ขอสิทธิ์เข้าใช้งานระบบที่จำเป็น
- อ่านนโยบาย PDPA และความปลอดภัยข้อมูล

See the onboarding checklist on your dashboard to track progress.
MD,
            ],
            [
                'slug' => 'mon-four-business-divisions',
                'title' => "MON's Four Business Divisions Explained",
                'dept' => 'ALL', 'cat' => 'company-culture', 'level' => 'general',
                'min_role' => Role::RANK_FRESHER, 'author' => $isd, 'tags' => ['glossary'],
                'body' => <<<'MD'
# MON's Four Business Divisions

MON operates across four complementary divisions:

- **Warehouse** — storage, picking, and value-added services.
- **Transportation** — domestic trucking and distribution.
- **Container Depot** — empty-container handling and maintenance.
- **Information Systems (ISD)** — the technology backbone for all of the above.

Together they deliver an integrated 3PL service to our customers.
MD,
            ],
            [
                'slug' => 'microsoft-365-account-setup',
                'title' => 'Setting Up Your Microsoft 365 Account (Teams & Outlook)',
                'dept' => 'ISD', 'cat' => 'it-accounts', 'level' => 'onboarding',
                'min_role' => Role::RANK_FRESHER, 'author' => $isd, 'tags' => ['microsoft-365'],
                'body' => <<<'MD'
# Microsoft 365 Setup / ตั้งค่า Microsoft 365

Your company account gives you Outlook (email), Teams (chat & calls), and OneDrive.

## Steps
1. Go to [portal.office.com](https://portal.office.com).
2. Sign in with `firstname.lastname@company.com`.
3. Set a strong password and enable MFA.

## ภาษาไทย
1. เข้าสู่เว็บไซต์ Microsoft 365 แล้วลงชื่อเข้าใช้ด้วยอีเมลบริษัท
2. ตั้งรหัสผ่านที่ปลอดภัยและเปิดใช้งานการยืนยันตัวตนสองขั้นตอน (MFA)

If you have trouble signing in, contact the ISD help desk.
MD,
            ],
            [
                'slug' => 'wifi-vpn-password-resets',
                'title' => 'Wi-Fi, VPN & Password Resets',
                'dept' => 'ISD', 'cat' => 'it-accounts', 'level' => 'onboarding',
                'min_role' => Role::RANK_FRESHER, 'author' => $isd, 'tags' => ['vpn', 'access-request'],
                'body' => <<<'MD'
# Wi-Fi, VPN & Passwords

## Office Wi-Fi
Connect to **MON-Staff** using your Microsoft 365 credentials.

## VPN (remote access)
1. Install the VPN client from the ISD portal.
2. Sign in with your company account.
3. Request access via the [access form](/knowledge/wms-infor-overview-access-request).

## Password resets
Self-service reset is available from the Microsoft 365 sign-in page.
MD,
            ],
            [
                'slug' => 'logistics-wms-glossary',
                'title' => 'Logistics & WMS Glossary',
                'dept' => 'ALL', 'cat' => 'glossary-acronyms', 'level' => 'general',
                'min_role' => Role::RANK_FRESHER, 'author' => $isd, 'tags' => ['glossary', 'wms'],
                'body' => <<<'MD'
# Logistics & WMS Glossary

- **WMS** — Warehouse Management System.
- **3PL** — Third-Party Logistics.
- **SKU** — Stock Keeping Unit.
- **ASN** — Advance Shipping Notice.
- **Depot** — facility for storing/handling empty containers.

Keep this handy during your first weeks.
MD,
            ],
            [
                'slug' => 'leave-holidays-payroll-basics',
                'title' => 'Leave, Holidays & Payroll Basics / การลา วันหยุด และเงินเดือน',
                'dept' => 'HR', 'cat' => 'hr-workplace', 'level' => 'onboarding',
                'min_role' => Role::RANK_FRESHER, 'author' => $hr, 'tags' => ['leave', 'payroll'],
                'body' => <<<'MD'
# Leave, Holidays & Payroll / การลาและเงินเดือน

## Leave
- Annual leave is granted per company policy.
- Submit leave requests through the HR system, in advance where possible.

## Payroll
- Salary is paid monthly to your registered bank account.
- Payslips are available from the HR portal.

## ภาษาไทย
- ยื่นใบลาผ่านระบบ HR ล่วงหน้า
- เงินเดือนจ่ายเป็นรายเดือนเข้าบัญชีธนาคารที่ลงทะเบียนไว้
- ดูสลิปเงินเดือนได้จากระบบ HR
MD,
            ],
            [
                'slug' => 'pdpa-data-handling-policy',
                'title' => 'PDPA & Data Handling Policy',
                'dept' => 'ALL', 'cat' => 'policies-compliance', 'level' => 'general',
                'min_role' => Role::RANK_FRESHER, 'author' => $admin, 'tags' => ['pdpa'],
                'body' => <<<'MD'
# PDPA & Data Handling

Thailand's **Personal Data Protection Act (PDPA)** governs how we collect, use, and
store personal data.

## Key rules
- Collect only the data you need, for a clear purpose.
- Never share customer or employee data without authorization.
- Report any suspected data breach to ISD and your manager immediately.

Acknowledge this policy as part of your onboarding checklist.
MD,
            ],
            [
                'slug' => 'warehouse-safety-essentials',
                'title' => 'Warehouse Safety Essentials',
                'dept' => 'WH', 'cat' => 'policies-compliance', 'level' => 'onboarding',
                'min_role' => Role::RANK_FRESHER, 'author' => $wh, 'tags' => ['safety'],
                'body' => <<<'MD'
# Warehouse Safety Essentials

Safety first — always.

## Before you start
1. Wear your PPE (safety shoes, hi-vis vest).
2. Know the locations of fire exits and first-aid kits.
3. Never operate a forklift without certification.

## On the floor
- Keep walkways clear.
- Follow racking load limits.
- Report hazards immediately to your supervisor.
MD,
            ],
            [
                'slug' => 'wms-infor-overview-access-request',
                'title' => 'WMS (Infor) — Overview & Access Request',
                'dept' => 'ISD', 'cat' => 'systems-tools', 'level' => 'general',
                'min_role' => Role::RANK_SENIOR, 'author' => $isd, 'tags' => ['wms', 'infor', 'access-request'],
                'body' => <<<'MD'
# WMS (Infor) — Overview & Access

Our warehouse operations run on **Infor WMS**.

## Requesting access
1. Get manager approval.
2. Submit the access request to ISD with your role and warehouse.
3. ISD provisions your account and roles.

## Environments
- **PROD** — live operations.
- **UAT** — testing and training.
MD,
            ],
            [
                'slug' => 'isd-coding-standards-git-workflow',
                'title' => 'ISD Coding Standards & Git Workflow',
                'dept' => 'ISD', 'cat' => 'engineering-standards', 'level' => 'advanced',
                'min_role' => Role::RANK_SENIOR, 'author' => $isd, 'tags' => ['git'],
                'body' => <<<'MD'
# ISD Coding Standards & Git Workflow

> Advanced / Senior content.

## Branching
- `main` is always deployable.
- Feature branches: `feature/<ticket>-<short-desc>`.
- Open a Pull Request; at least one review before merge.

## Code style
- Follow PSR-12 for PHP; run the linter before pushing.
- Write tests for new behaviour.

## Commits
- Small, focused commits with clear messages.
MD,
            ],
            [
                'slug' => 'internal-deployment-process',
                'title' => 'Internal Deployment Process',
                'dept' => 'ISD', 'cat' => 'engineering-standards', 'level' => 'advanced',
                'min_role' => Role::RANK_SENIOR, 'author' => $admin, 'tags' => ['deployment', 'git'],
                'body' => <<<'MD'
# Internal Deployment Process

> Advanced / Senior content.

## Pipeline
1. Merge to `main` triggers the build.
2. Automated tests must pass.
3. Deploy to **UAT**, verify, then promote to **PROD**.

## Rollback
- Keep the previous release tagged.
- Roll back by redeploying the last known-good tag.

Coordinate PROD deployments with the ISD lead.
MD,
            ],
        ];
    }
}
