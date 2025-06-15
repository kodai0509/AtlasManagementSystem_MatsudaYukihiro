<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReserveSettingsTableSeeder extends Seeder
{
    public function run()
    {

        for ($i = 0; $i < 3; $i++) {
            $date = Carbon::today()->addDays($i)->format('Y-m-d');
            foreach ([1, 2, 3] as $part) {
                DB::table('reserve_settings')->insert([
                    'setting_reserve' => $date,
                    'setting_part' => $part,
                    'limit_users' => 20,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
