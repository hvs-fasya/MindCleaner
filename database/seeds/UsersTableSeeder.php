<?php

use Illuminate\Database\Seeder;

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
            'name' => 'hvs-fasya',
            'email' => 'hvs-fasya@mail.ru',
            'password' => bcrypt('5953947'),
        ]);
    }
}
