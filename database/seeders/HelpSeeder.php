<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class HelpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Faq::count() > 0) Faq::truncate();
        for($i = 1; $i <= 50; $i++) {
            $query = new Faq();
            $query->question = "Question ".$i;
            $query->answer = "Sample answer";
            $query->created_at = date('Y-m-d h:i:').$i;
            $query->save();
        }
    }
}
