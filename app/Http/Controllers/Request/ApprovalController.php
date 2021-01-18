<?php

namespace App\Http\Controllers\Request;

use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApprovalController extends Controller
{
    //
    public function index() {
		$sql = DB::table('incoming_data')
    	->leftJoin('projects','projects.id','=','incoming_data.project_id')
    	->leftJoin('task_status','task_status.id','=','incoming_data.current_status_id')
    	->select('incoming_data.id',DB::raw('projects.name as project_name'),'incoming_data.file_name', 'incoming_data.ticket', 'file_name', DB::raw('task_status.name as status_name'))
    	->where('incoming_data.next_status_id','=',$this->approvalTaskId);

        $results = DB::table('task_result')
        ->join('task_status_to_result','task_result.id','=','task_status_to_result.result_id')
        ->where('task_status_to_result.status_id','=',$this->approvalTaskId)
        ->select('task_result.*')
        ->get();
        
        $info = $this->getUserInfo();
        if($info->customer_id>0)
            $sql->where('projects.customer_id', '=', $info->customer_id);
        if($info->project_id>0)
            $sql->where('incoming_data.project_id','=',$info->project_id);

    	$list = $sql->paginate(10);
	    $view = view('request.approval.index');
        $view->with('list',$list);
        $view->with('results', $results);

        return $view;
    }

    public function update(Request $request) {
        $id = $request->id;
    	$data = DB::table('incoming_data')->where('id','=',$id)->first();
    	$result_id = $request->input('result_id');
    	$note = $request->input('note');

        if($data==null) return response()->json([
            'status'=>0,
            'message'=>'Data not found']);

    	$result = $this->getResult($result_id);
    	switch ($result->isok) {
    		case 'Y':
				$next_status_id = $this->getNextTask($this->approvalTaskId, $data->project_id);
    			break;
    		case 'N':
    			$next_status_id = $this->finishTask;
				break; 
			case 'T':
				$next_status_id = $this->notapp;
				break;	
			case 'R':
				$next_status_id = $this->submitRevisiId;
				break;								   		
    		default:
    			$next_status_id = $this->approvalTaskId;
    			break;
    	}

    	$trans = array();
    	$trans['file_id']=$id;
		$trans['production_id']=0;
		if($result->isok === "T" || $result->isok === "R")
			$trans['status_id']=$next_status_id;
		else
			$trans['status_id']=$this->approvalTaskId;		
    	
    	$trans['result_id']=$result_id;
    	$trans['note']=$note;
    	$this->insertToTransaction($trans);

    	$upd = array();
		$upd['id']=$id;
		//diganti farhan 
		if($result->isok === "T" || $result->isok === "R")
			$upd['current_status_id']=$next_status_id;
		else $upd['current_status_id']=$this->approvalTaskId; //sampai sini
	
		$upd['current_status_result_id']=$result_id;
		if($result->isok === "R")
			$upd['next_status_id']=$this->downloadTaskId;
		else $upd['next_status_id']=$next_status_id;

    	$this->updateIncomingFile($upd);

		//ditambahin farhan
    	if ($request->hasFile('file')) {
    		$data = DB::table('incoming_data')
    		->where('id','=',$id)
    		->first();

    		$file=$request->file('file');
    		$fileName = $file->getClientOriginalName();
    		$pathFile = $this->uploadApproved.DIRECTORY_SEPARATOR.$id;
			$file->move($this->uploadApproved,$id);
			
			if($result->isok === "R"){
				DB::table('incoming_data')
				->where('id',$id)
				->update([
					'file_name'=>$fileName,
					'path_file'=>$pathFile,
					'updated_at'=>Carbon::now()
				]);
			}else{
				DB::table('file_approved')
				->insert([
					'file_id'=>$id,
					'file_name'=>$fileName,
					'path_file'=>$pathFile,
					'note'=>$note,
					'updated_at'=>Carbon::now()
				]);				
			}

    	} //sampai sini

    	return response()->json([
            'status'=>1,
            'message'=>'Berhasil update'
        ]);
	}
	
	public function downloadapp($id){
		$file = DB::table('file_approval')->where('file_id','=',$id)->first();

		return response()->download($file->path_file,$file->file_name);
	}
}
