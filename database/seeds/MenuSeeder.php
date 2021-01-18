<?php

use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('master_menu')
        ->insert([
        	[
        	'order'=>1,
        	'name'=>'MAIN MENU',
        	'parent'=>0,
        	'active'=>1,
        	'url'=>'',
        	'icon'=>'',
        	'desc'=>'DIVIDER',
        ],
        [
        	'order'=>2,
        	'name'=>'Dashboard',
        	'parent'=>0,
        	'active'=>1,
        	'desc'=>'Dashboard',
        	'icon'=>'fas-tachometer-alt',
        	'url'=>'dashboard',
        ],
        [
        	'order'=>3,
        	'name'=>'Profile',
        	'parent'=>0,
        	'active'=>1,
        	'desc'=>'Profile',
        	'icon'=>'fas-fa-user',
        	'url'=>'profile',
        ],
        [
        	'order'=>4,
        	'name'=>'Master',
        	'parent'=>0,
        	'active'=>1,
        	'desc'=>'Master',
        	'icon'=>'fas-server',
        	'url'=>'',
        ],
        [
        	'order'=>1,
        	'name'=>'Users',
        	'parent'=>4,
        	'active'=>1,
        	'desc'=>'Users',
        	'icon'=>'',
        	'url'=>'users',
        ]]
    );
    }
}
