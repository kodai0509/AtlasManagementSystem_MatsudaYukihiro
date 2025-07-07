<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReserveSettingsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('reserve_settings')->truncate();

        $startDate = Carbon::today();
        // いったん1年後まで
        $endDate = Carbon::today()->addYear();


        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            foreach ([1, 2, 3] as $part) {
                DB::table('reserve_settings')->insert([
                    'setting_reserve' => $formattedDate,
                    'setting_part' => $part,
                    'limit_users' => 20,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
