<?php

use Illuminate\Database\Seeder;

class UserMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_to_menu')
        ->insert([
        	[
        		'user_id'=>1,
        		'menu_id'=>1,
        	],
        	[
        		'user_id'=>1,
        		'menu_id'=>2,
        	],
        	[
        		'user_id'=>1,
        		'menu_id'=>3,
        	],
        	[
        		'user_id'=>1,
        		'menu_id'=>4,
        	],
        	[
        		'user_id'=>1,
        		'menu_id'=>5,
        	],        	
        ]
    	);
    }
}
