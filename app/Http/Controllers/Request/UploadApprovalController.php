<?php

namespace App\Http\Controllers\Request;

use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadApprovalController extends Controller
{
    //
    public function index() {
		$list = DB::table('incoming_data')
    	->leftJoin('projects','projects.id','=','incoming_data.project_id')
    	->leftJoin('task_status','task_status.id','=','incoming_data.current_status_id')
    	->select('incoming_data.id',DB::raw('projects.name as project_name'),'incoming_data.file_name', 'incoming_data.ticket', 'file_name', DB::raw('task_status.name as status_name'))
    	->where('incoming_data.next_status_id','=',$this->uploadApprovalTaskId)
    	->paginate(10);

    	return view('request.uploadapproval.index')->with('list',$list);
    }

    public function upload(Request $request) {
        $id = $request->id;
    	$user = Auth::user();

    	$error = array();
    	$valid = true;

    	$note = $request->input('note');

    	if ($request->file('file')->isValid()) {
    		$data = DB::table('incoming_data')
    		->where('id','=',$id)
    		->first();

    		$file=$request->file('file');
    		$fileName = $file->getClientOriginalName();
    		$pathFile = $this->uploadApproval.DIRECTORY_SEPARATOR.$id;
    		$file->move($this->uploadApproval,$id);

			$cek = DB::table('file_approval')->where('file_id',$id)->first();
			if($cek){
				DB::table('file_approval')
				->where('file_id',$id)
				->update([
					'file_name'=>$fileName,
					'path_file'=>$pathFile,
					'updated_at'=>Carbon::now()
				]);				
			}else{
				DB::table('file_approval')
				->insert([
					'file_id'=>$id,
					'file_name'=>$fileName,
					'path_file'=>$pathFile,
					'note'=>$note,
					'updated_at'=>Carbon::now()
				]);
			}
    		$current_status = $this->uploadApprovalTaskId;
    		$result_id = $this->statusSuccess;

    		$trans = array();
	    	$trans['file_id']=$id;
	    	$trans['production_id']=0;
	    	$trans['status_id']=$current_status;
	    	$trans['result_id']=$result_id;
	    	$trans['note']=$note;
	    	$this->insertToTransaction($trans);
	    	$next_status = $this->getNextTask($current_status, $data->project_id);

	    	$upd = array();
	    	$upd['id']=$id;
	    	$upd['current_status_id']=$current_status;
	    	$upd['current_status_result_id']=$result_id;
	    	$upd['next_status_id']=$next_status;
	    	$this->updateIncomingFile($upd);
	    	return response()->json([
                'status'=>1,
                'message'=>'Berhasil upload approval'
            ]);
    	} else {
    		array_push($error, "File not found");
    		return response()->json([
                'status'=>0,
                'message'=>'Error : ',
                'error'=>$error
            ]);
    	}    	
    }
}
