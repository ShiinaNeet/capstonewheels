<?php

namespace Database\Seeders;

use App\Models\Requirement;
use Illuminate\Database\Seeder;

class RequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Requirement::count() > 0) Requirement::truncate();
        for($i = 1; $i <= 50; $i++) {
            $query = new Requirement();
            $query->name = "Requirement ".$i;
            $query->created_at = date('Y-m-d h:i:').$i;
            $query->save();
        }
    }
}
