<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Room::count() > 0) Room::truncate();
        for($i = 1; $i <= 50; $i++) {
            $query = new Room();
            $query->name = "Room ".$i;
            $query->created_at = date('Y-m-d h:i:').$i;
            $query->save();
        }
    }
}
