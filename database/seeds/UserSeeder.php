<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'fitri',
            'name' => 'Cinta Fitri',
            'email' => 'fitri@gmail.com',
            'password' => Hash::make('kimcil'),
            'created_at' => now(),
        ]);
    }
}
