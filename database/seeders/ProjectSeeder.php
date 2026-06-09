<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 4 demo projects for the Project Registry (BUILD_GUIDE.md Part A). All are
 * advanced / min role Senior, owned by a Senior — so Freshers can't see them.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $depts = Department::pluck('id', 'code');
        $owners = User::pluck('id', 'email');

        $isd = 'anan.srisuk@company.com';   // Senior, ISD
        $wh = 'kanya.phongphan@company.com'; // Senior, Warehouse

        $projects = [
            [
                'slug' => 'wms-infor-integration-layer',
                'name' => 'WMS (Infor) Integration Layer',
                'repo_url' => 'https://git.mon.local/isd/wms-integration',
                'tech_stack' => 'PHP, REST, SQL',
                'status' => 'Active',
                'dept' => 'ISD', 'owner' => $isd,
                'readme' => <<<'MD'
# WMS (Infor) Integration Layer

Middleware that synchronises orders and inventory between our systems and Infor WMS.

## Overview
- Exposes REST endpoints for order push and stock pull.
- Maps internal SKUs to WMS item codes.

## Setup
1. Copy `.env.example` to `.env` and set the WMS credentials.
2. `composer install`
3. Run the sync worker.
MD,
            ],
            [
                'slug' => 'mon-uob-payment-project',
                'name' => 'MON UOB Payment Project',
                'repo_url' => 'https://git.mon.local/isd/uob-payment',
                'tech_stack' => 'PHP, UOB API',
                'status' => 'Active',
                'dept' => 'ISD', 'owner' => $isd,
                'readme' => <<<'MD'
# MON UOB Payment Project

Integration with the UOB banking API for automated payment processing and reconciliation.

## Overview
- Initiates and tracks payment instructions.
- Reconciles bank statements against invoices.

## Setup
1. Configure the UOB API keys in `.env`.
2. `composer install`
3. Schedule the reconciliation job.
MD,
            ],
            [
                'slug' => 'truck-trailer-validation',
                'name' => 'Truck & Trailer Validation',
                'repo_url' => 'https://git.mon.local/isd/truck-trailer-validation',
                'tech_stack' => 'PHP, MySQL',
                'status' => 'Active',
                'dept' => 'ISD', 'owner' => $isd,
                'readme' => <<<'MD'
# Truck & Trailer Validation

Validates truck and trailer pairings against scheduling and compliance rules before dispatch.

## Overview
- Checks vehicle registration and trailer compatibility.
- Flags expired documents.

## Setup
1. `composer install`
2. Configure the database connection.
3. Import the reference vehicle data.
MD,
            ],
            [
                'slug' => 'nestle-material-serving-project',
                'name' => 'Nestlé Material Serving Project',
                'repo_url' => 'https://git.mon.local/wh/nestle-material-serving',
                'tech_stack' => 'WMS, integration',
                'status' => 'Active',
                'dept' => 'WH', 'owner' => $wh,
                'readme' => <<<'MD'
# Nestlé Material Serving Project

Warehouse material-serving workflow tailored to the Nestlé account.

## Overview
- Sequenced picking to the production line.
- Real-time stock visibility for the customer.

## Setup
1. Configure the WMS connection.
2. Load the customer's material master.
MD,
            ],
        ];

        foreach ($projects as $p) {
            Project::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'name' => $p['name'],
                    'repo_url' => $p['repo_url'],
                    'tech_stack' => $p['tech_stack'],
                    'status' => $p['status'],
                    'readme_markdown' => $p['readme'],
                    'owner_id' => $owners[$p['owner']],
                    'department_id' => $depts[$p['dept']],
                    'audience_level' => 'advanced',
                    'min_role' => Role::RANK_SENIOR,
                ],
            );
        }
    }
}
