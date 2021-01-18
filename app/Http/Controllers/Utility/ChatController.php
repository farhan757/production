<?php

namespace App\Http\Controllers\Utility;

use DB;
use Cache;
use Illuminate\Http\Request;
use Adira\Http\Controllers\Controller;

class ChatController extends Controller
{
    //
    public function getLastChat() {
        $user = $this->getUser();

        $list = DB::table('simple_message')
        ->leftJoin('users', 'simple_message.to_id','=','users.id')
        ->where('from_id','=',$user->id)
        ->orWhere('to_id', '=',$user->id)
        ->orderBy('created_at','desc')
        ->get();
    }

    public function getusers() {
    	$users = DB::table('users')
    	->select(DB::raw('0 as online'),'id', 'name', 'username','email', 'group')
    	->get();

    	$return = array();
    	foreach ($users as $user) {
            if (Cache::has('user-is-online-' . $user->id))
                $user->online=1;

            array_push($return, $user);
        }

        return $return;
    }
}
