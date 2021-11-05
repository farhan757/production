<?php

namespace App\Http\Controllers\Request;

use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DownloadController extends Controller
{
    //
    public function index() {
    	$list = DB::table('incoming_data')
    	->leftJoin('projects','projects.id','=','incoming_data.project_id')
    	->leftJoin('task_status','task_status.id','=','incoming_data.current_status_id')
    	->select('incoming_data.method_id','incoming_data.id',DB::raw('projects.name as project_name'),'incoming_data.file_name', 'incoming_data.ticket', 'file_name', DB::raw('task_status.name as status_name'))
		->where('incoming_data.next_status_id','=',$this->downloadTaskId)	
		->orderBy('id','DESC')	
    	->paginate(10);

    	return view('request.download.index')->with('list',$list);
    }

    public function download($id) {
    	$user = Auth::user();

    	$data = DB::table('incoming_data')
    	->where('id','=',$id)->first();

    	$next_status_id = $this->getNextTask($this->downloadTaskId, $data->project_id);

    	$trans = array();
    	$trans['file_id']=$id;
    	$trans['production_id']=0;
    	$trans['status_id']=$this->downloadTaskId;
    	$trans['result_id']=$this->statusSuccess;
    	$trans['note']='';
    	$this->insertToTransaction($trans);
    	
    	DB::table('incoming_data')
    	->where('id','=',$id)
    	->update([
    		'current_status_id'=>$this->downloadTaskId,
    		'current_status_result_id'=>$this->statusSuccess,
    		'next_status_id'=>$next_status_id,
    		'updated_at'=>Carbon::now(),
    		'updated_by'=>$user->id
    	]);

    	return response()->download($data->path_file, $data->file_name);
    }
}
