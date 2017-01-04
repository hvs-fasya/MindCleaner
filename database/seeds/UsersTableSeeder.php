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
        Schema::table('events', function ($table) {
            $table->dropForeign(['user_id']);
        });
        DB::table('users')->truncate();
        DB::table('users')->insert([
            'name' => 'hvs-fasya',
            'email' => 'hvs-fasya@mail.ru',
            'password' => bcrypt('5953947'),
            'sex' => 'f',
            'is_admin' => true
        ]);
        Schema::table('events', function ($table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }
}
