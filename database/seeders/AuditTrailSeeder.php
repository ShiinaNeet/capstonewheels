<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AuditTrailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (AuditTrail::count() > 0) {
            Schema::disableForeignKeyConstraints();
            AuditTrail::truncate();
            Schema::enableForeignKeyConstraints();
        }

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_SYSTEM;
        $audit->category            = AuditTrail::CATEGORY_ACCOUNT;
        $audit->action_type         = AuditTrail::ACTION_CREATE;
        $audit->action_description  = "Created account <email>";
        $audit->save();

        // $audit                      = new AuditTrail();
        // $audit->action_user_id      = AuditTrail::USER_ADMIN;
        // $audit->category            = AuditTrail::CATEGORY_ACCOUNT;
        // $audit->action_type         = AuditTrail::ACTION_DELETE;
        // $audit->action_description  = "Deleted account <email>";
        // $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_SECRETARY;
        $audit->category            = AuditTrail::CATEGORY_NEWS;
        $audit->action_type         = AuditTrail::ACTION_CREATE;
        $audit->action_description  = "Created news <name>";
        $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_SECRETARY;
        $audit->category            = AuditTrail::CATEGORY_HELP;
        $audit->action_type         = AuditTrail::ACTION_UPDATE;
        $audit->action_description  = "Updated help <name>'s name";
        $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = User::TYPE_INSTRUCTOR + 2;
        $audit->category            = AuditTrail::CATEGORY_SERVICE;
        $audit->action_type         = AuditTrail::ACTION_DELETE;
        $audit->action_description  = "Deleted service <name>";
        $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_ADMIN;
        $audit->category            = AuditTrail::CATEGORY_SCHEDULE;
        $audit->action_type         = AuditTrail::ACTION_CREATE;
        $audit->action_description  = "Created schedule for service <name>";
        $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_ADMIN;
        $audit->category            = AuditTrail::CATEGORY_ROOM;
        $audit->action_type         = AuditTrail::ACTION_UPDATE;
        $audit->action_description  = "Updated room <name>'s description";
        $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_SECRETARY;
        $audit->category            = AuditTrail::CATEGORY_VEHICLE;
        $audit->action_type         = AuditTrail::ACTION_DELETE;
        $audit->action_description  = "Deleted vehicle <name>";
        $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_ADMIN;
        $audit->category            = AuditTrail::CATEGORY_DATABASE;
        $audit->action_type         = AuditTrail::ACTION_CREATE;
        $audit->action_description  = "Created backup of database";
        $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_STUDENT;
        $audit->category            = AuditTrail::CATEGORY_ENROLLMENT;
        $audit->action_type         = AuditTrail::ACTION_CREATE;
        $audit->action_description  = "Created enroll schedservice <name>";
        $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_STUDENT;
        $audit->category            = AuditTrail::CATEGORY_PAYMENT;
        $audit->action_type         = AuditTrail::ACTION_UPDATE;
        $audit->action_description  = "Updated payment paid <amount> to <reference_no> marked <status> to <new_status>";
        $audit->save();

        $audit                      = new AuditTrail();
        $audit->action_user_id      = AuditTrail::USER_ADMIN;
        $audit->category            = AuditTrail::CATEGORY_REPORT;
        $audit->action_type         = AuditTrail::ACTION_CREATE;
        $audit->action_description  = "Created report of <month> <year> for service <name>";
        $audit->save();
    }
}
