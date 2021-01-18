<?php

namespace Adira\Http\Controllers\CustomerService;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Adira\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RequestCSController extends Controller
{
    //
    function show()
    {
    	return view('CustomerService.Request.index');
    }

    function receive(Request $request)
    {
    	if($request->input('inputTitle')!=null && $request->input('inputTitle')!="")
        {
	    	$id = DB::table('customer_care')
	    	->insertGetId([
	    		'title'=>$request->input('inputTitle'),
	    		'desc'=>$request->input('inputNote'),
	    		'status'=>1,
	    		'priority'=>$request->input('selectPriority'),
	    		'uid_req'=>Auth::id(),
	    		'create_dt'=>Carbon::now(),
	    		'update_dt'=>Carbon::now(),
	    	]);
            DB::table('status_cs')
            ->insert([
                'id_cs'=>$id,
                'id_status'=>1,
                'uid_updated'=>Auth::id(),
                'ket'=>'',
                'update_dt'=>Carbon::now()
            ]);

            // upload file

            if($request->hasFile('input_file'))
            {
                $dir = storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'uploaded'.DIRECTORY_SEPARATOR. date('Ymd'). DIRECTORY_SEPARATOR .$id;
                if(!is_dir($dir))
                {
                    mkdir($dir, 0777, true);
                }
                $path = $request->input_file->storeAs('uploaded'.DIRECTORY_SEPARATOR. date('Ymd'). DIRECTORY_SEPARATOR .$id,$request->input_file->getClientOriginalName());

            }
    		return view('CustomerService.Request.index')->with('data','Complete');

    	} else return view('CustomerService.Request.index')->with('error','Title wajib diisi');
    }
}
