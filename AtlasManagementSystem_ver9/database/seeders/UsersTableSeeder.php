<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'over_name' => '山田',
                'under_name' => '太郎',
                'over_name_kana' => 'ヤマダ',
                'under_name_kana' => 'タロウ',
                'mail_address' => 'yamada@example.com',
                'sex' => 1,
                'birth_day' => '1990-01-01',
                'role' => 1, // 管理者
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
            ],
            [
                'over_name' => '佐藤',
                'under_name' => '花子',
                'over_name_kana' => 'サトウ',
                'under_name_kana' => 'ハナコ',
                'mail_address' => 'sato@example.com',
                'sex' => 2,
                'birth_day' => '1992-02-02',
                'role' => 2, // 一般ユーザー
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
            ],
            [
                'over_name' => '鈴木',
                'under_name' => '一郎',
                'over_name_kana' => 'スズキ',
                'under_name_kana' => 'イチロウ',
                'mail_address' => 'suzuki@example.com',
                'sex' => 1,
                'birth_day' => '1995-03-03',
                'role' => 2, // 一般ユーザー
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}
