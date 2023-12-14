<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Vehicle::count() > 0) Vehicle::truncate();
        for($i = 1; $i <= 50; $i++) {
            $query = new Vehicle();
            $query->name = "Vehicle ".$i;
            $query->capacity = 4;
            $query->transmission = mt_rand(0, 1);
            $query->created_at = date('Y-m-d h:i:').$i;
            $query->save();
        }
    }
}
