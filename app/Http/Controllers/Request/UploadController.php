<?php

namespace App\Http\Controllers\Request;

use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    //
    public function index() {
    	//$list = DB::table('incoming_data')
    	//->paginate(10);
        $jenis = $this->getValues('jenis');
        $parts = $this->getValues('part');
    	$customers = $this->getCustomers();

    	return view('request.upload.index')
    	->with('customers',$customers)
        ->with('jenis', $jenis)
        ->with('parts', $parts);
    }

    public function upload(Request $request) {
    	$user = Auth::user();

    	$error = array();
    	$valid = true;

    	$path_file ='';
    	$customer_id = $request->input('customer_id');
    	$method_id = $request->input('method_id');
    	$info = $request->input('note');
    	$project_id = $request->input('project_id');
    	$current_status = 1;
        $result_id = $this->resultSuccesDefault;

        $cycle=$request->input('cycle');
        $part=$request->input('part');
        $jenis=$request->input('jenis');
        if($jenis="REG") {
            if(DB::table('incoming_data')->where([
                ['project_id','=',$project_id],
                ['cycle','=',$cycle],
                ['part','=',$part],
                ['jenis','=',$jenis]
            ])->exists()) {
                return response()->json([
                    'status'=>0,
                    'message'=>'Data already exist'
                ]);
            }
        }

        $next_status = $this->getNextTask($current_status, $project_id);
        $ticket = $this->generateTicket($project_id);

    	$id = DB::table('incoming_data')
    	->insertGetId([
    		'ticket'=>$ticket,
    		'project_id'=>$project_id,
    		'method_id'=>$method_id,
    		'info'=>$info,
    		'current_status_id'=>$current_status,
    		'next_status_id'=>$next_status,
    		'file_name'=>'',
    		'path_file'=>'',
    		'current_status_result_id'=>$result_id,
    		'created_by'=>$user->id,
    		'updated_by'=>$user->id,
            'cycle'=>$cycle,
            'part'=>$part,
            'jenis'=>$jenis,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
    	]);

    	if($method_id==1) {
	    	if ($request->file('file')->isValid()) {
	    		$file=$request->file('file');
	    		$fileName = $file->getClientOriginalName();
	    		$pathFile = $this->uploadedFile.DIRECTORY_SEPARATOR.$id;
	    		$file->move($this->uploadedFile,$id);

	    		DB::table('incoming_data')
	    		->where('id','=',$id)
	    		->update([
	    			'file_name'=>$fileName,
	    			'path_file'=>$pathFile,
		            'updated_at'=>Carbon::now()
	    		]);
	    	} else {
	    		return response()->json([
                    'status'=>0,
                    'message'=>'File not found'
                ]);
	    	}
    	}

    	$trans = array();
    	$trans['file_id']=$id;
    	$trans['production_id']=0;
    	$trans['status_id']=$current_status;
    	$trans['result_id']=$result_id;
    	$trans['note']='';
    	$this->insertToTransaction($trans);
    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['add']['success']
        ]);
    }

    function generateTicket($project_id) {
    	$counter = str_pad($this->getCounter($this->counterFileKey) ,5,'0',STR_PAD_LEFT);
    	$date = date("Ymd");
    	$project_id = str_pad($project_id,3,'0',STR_PAD_LEFT);

    	return $date.$project_id.$counter;
    }

 }
