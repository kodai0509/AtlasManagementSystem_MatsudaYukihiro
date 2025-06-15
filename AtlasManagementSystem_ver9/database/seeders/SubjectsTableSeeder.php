<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 外部キー制約を一時的に無効化
        Schema::disableForeignKeyConstraints();

        DB::table('subject_users')->truncate();
        DB::table('subjects')->truncate();

        // 外部キー制約を有効化
        Schema::enableForeignKeyConstraints();

        // 教科
        DB::table('subjects')->insert([
            [
                'subject' => '国語',
                'created_at' => Carbon::now(),
            ],
            [
                'subject' => '数学',
                'created_at' => Carbon::now(),
            ],
            [
                'subject' => '英語',
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}
