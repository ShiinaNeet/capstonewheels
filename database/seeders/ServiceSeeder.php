<?php

namespace Database\Seeders;

use App\Models\Schedule\ServiceSchedule;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (ServiceSchedule::count() > 0)
            ServiceSchedule::truncate();
        if (Service::count() > 0)
            Service::truncate();
    }
}
