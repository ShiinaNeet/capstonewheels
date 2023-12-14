<?php

namespace Database\Seeders;

use App\Models\AuditTrail;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() > 0) {
            Schema::disableForeignKeyConstraints();
            User::truncate();
            UserDetail::truncate();
            Schema::enableForeignKeyConstraints();
        }

        # ADMIN ---------------------------------
        $user            = new User();
        $user->user_type = User::TYPE_ADMIN;
        $user->email     = 'admin@email.com';
        $user->password  = bcrypt('password');
        $user->save();

        $user_details               = new UserDetail();
        $user_details->user_id      = $user->id;
        $user_details->firstname    = "Admin";
        $user_details->lastname     = "Admin";
        // $user_details->gender       = 1;
        // $user_details->birthdate    = "2000-01-01";
        // $user_details->address      = "123 Juan Street, De La Cruz";
        // $user_details->barangay     = "Barangay";
        // $user_details->city         = "City";
        // $user_details->province     = "Province";
        // $user_details->region       = "Region";
        $user_details->mobile       = "+639123456789";
        $user_details->save();

        # SECRETARY -----------------------------
        $user            = new User();
        $user->user_type = User::TYPE_SECRETARY;
        $user->email     = 'secretary@email.com';
        $user->password  = bcrypt('password');
        $user->save();

        $user_details               = new UserDetail();
        $user_details->user_id      = $user->id;
        $user_details->firstname    = "Secretary";
        $user_details->lastname     = "Secretary";
        // $user_details->gender       = 1;
        // $user_details->birthdate    = "2000-01-01";
        // $user_details->address      = "123 Juan Street, De La Cruz";
        // $user_details->barangay     = "Barangay";
        // $user_details->city         = "City";
        // $user_details->province     = "Province";
        // $user_details->region       = "Region";
        $user_details->mobile       = "+639123456789";
        $user_details->save();

        # INSTRUCTOR ----------------------------
        $user            = new User();
        $user->user_type = User::TYPE_INSTRUCTOR;
        $user->email     = 'instructor@email.com';
        $user->password  = bcrypt('password');
        $user->save();

        $user_details               = new UserDetail();
        $user_details->user_id      = $user->id;
        $user_details->firstname    = "Instructor";
        $user_details->lastname     = "Instructor";
        // $user_details->gender       = 1;
        // $user_details->birthdate    = "2000-01-01";
        // $user_details->address      = "123 Juan Street, De La Cruz";
        // $user_details->barangay     = "Barangay";
        // $user_details->city         = "City";
        // $user_details->province     = "Province";
        // $user_details->region       = "Region";
        $user_details->mobile       = "+639123456789";
        $user_details->save();

        # STUDENT ------------------------------
        User::create([
            'user_type' => User::TYPE_STUDENT,
            'email'     => 'student@email.com',
            'password'  => bcrypt('password')
        ]);
    }
}
