<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (News::count() > 0) News::truncate();
        for($i = 1; $i <= 50; $i++) {
            $query = new News();
            $query->title = "News ".$i;
            $query->description = "Sample description";
            $query->created_at = date('Y-m-d h:i:').$i;
            $query->save();
        }
    }
}
