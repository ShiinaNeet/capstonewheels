<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $this->call(UserSeeder::class);
        $this->call(HelpSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(RequirementSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(VehicleSeeder::class);
        Schema::enableForeignKeyConstraints();
    }
}
