<?php

namespace App\Http\Controllers\Profile;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    //
    public function show()
    {
    	$list = DB::table('user_group')->paginate(10);
    	return view('Profile.Group.index')->with('list',$list);
    }
}
