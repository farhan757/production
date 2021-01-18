<?php

namespace App\Http\Controllers\Request;

use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadJobListController extends Controller
{
    //
	public function index() {
		$list = DB::table('incoming_data')
    	->leftJoin('projects','projects.id','=','incoming_data.project_id')
    	->leftJoin('task_status','task_status.id','=','incoming_data.current_status_id')
		->leftJoin('task_result','task_result.id','=','incoming_data.current_status_result_id')
		->leftJoin('file_approved','file_approved.file_id','=','incoming_data.id')
		->select('incoming_data.id',DB::raw('projects.name as project_name'),'incoming_data.file_name', 'incoming_data.ticket', 
		DB::raw('file_approved.file_id as id_file_app, file_approved.file_name as f_name, file_approved.path_file as p_file'),
		DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'))
    	->where('incoming_data.next_status_id','=',$this->submitJobListId)
    	->paginate(10);

		return view('request.uploadjoblist.index')->with('list',$list);
	}

	public function upload(Request $request) {
		if($request->file('file')->isValid()) {
			$id = $request->id;
			$current_status = $this->submitJobListId;
			$result_id = $this->statusSuccess;

			$note = $request->input('note');

			$incoming = DB::table('incoming_data')->where('id','=',$id)->first();
			$project = DB::table('projects')->where('id','=',$incoming->project_id)->first();
			$user = Auth::user();

			$file=$request->file('file');
    		$fileName = $file->getClientOriginalName();
    		$pathFile = $this->uploadTemp.DIRECTORY_SEPARATOR.$fileName;
    		$file->move($this->uploadTemp,$fileName);

    		$next_status_id = $this->getNextTask($current_status, $incoming->project_id);
			$job_ticket = $this->generateProdTicket($incoming->project_id);
			$components = $this->getProjectComponent($incoming->project_id);
			
			$extension = pathinfo($pathFile, PATHINFO_EXTENSION);

			$dataList = array();
			switch ($extension) {
				case 'xlsx':
					$dataList = $this->readExel($pathFile, $components);
					break;
				case 'xls':
					$dataList = $this->readExel($pathFile, $components);
					break;
				case 'txt':
					$dataList = $this->readText($pathFile, $components);
					break;
				case 'sof':
					$dataList = $this->readText($pathFile, $components);
					break;
				default:
					return response()->json([
						'status'=>0,
						'message'=>'Ekstension File not define'
					]);
					break;
			}

			if(array_key_exists('error', $dataList)) {
				return response()->json([
					'status'=>2,
					'message'=>'Ada Error',
					'error'=>$dataList
				]);
			}

			$scan_qc=1;
			$scan_distribusi=1;
			if($this->checkScanQC($incoming->project_id))
				$scan_qc=0;
			if($this->checkScanDistribusi($incoming->project_id))
				$scan_distribusi=0;

			$prod['file_id']=$id;
            $prod['job_ticket']=$job_ticket;
            $prod['file_name']=$fileName;
            $prod['path_file']=$pathFile;
            $prod['cycle']=$incoming->cycle;
            $prod['part']=$incoming->part;
            $prod['jenis']=$incoming->jenis;
            $prod['customer_id']=$project->customer_id;
            $prod['project_id']=$incoming->project_id;
            $prod['current_status_id']=$this->submitJobListId;
            $prod['current_status_result_id']=$this->statusSuccess;
            $prod['next_status_id']=$next_status_id;
            $prod['created_by']=$user->id;

			$id_prod = $this->insertToProduction($prod);

			foreach ($dataList as $key => $value) {
				$value['scan_qc'] = $scan_qc;
				$value['scan_distribusi'] = $scan_distribusi;
				$id_detail = $this->insertToDetail($value, $id_prod);
				$this->insertComponents($value['components'], $id_detail);
			}

			$trans = array();
	    	$trans['file_id']=$id;
	    	$trans['production_id']=$id_prod;
	    	$trans['status_id']=$current_status;
	    	$trans['result_id']=$result_id;
	    	$trans['note']=$note;
	    	$this->insertToTransaction($trans);
	    	$next_status = $this->getNextTask($current_status, $incoming->project_id);

	    	$upd = array();
	    	$upd['id']=$id;
	    	$upd['current_status_id']=$current_status;
	    	$upd['current_status_result_id']=$result_id;
	    	$upd['next_status_id']=$next_status_id;
	    	$this->updateIncomingFile($upd);
		     return response()->json([
		     	'status'=>1,
		     	'message'=>'Success'
		     ]); 
		 } else {
		 	return response([
			 	'status'=>0,
			 	'message'=>'File kosong'
		 	]);
		 }
			/*$trans = array();
	    	$trans['file_id']=$id;
	    	$trans['production_id']=$id_prod;
	    	$trans['status_id']=$current_status;
	    	$trans['result_id']=$result_id;
	    	$trans['note']=$note;
	    	$this->insertToTransaction($trans);
	    	$next_status = $this->getNextTask($current_status, $data->project_id);

	    	$upd = array();
	    	$upd['id']=$id;
	    	$upd['current_status_id']=$current_status;
	    	$upd['current_status_result_id']=$result_id;
	    	$upd['next_status_id']=$next_status_id;
	    	$this->updateIncomingFile($upd);
		} else {
			return back()->withError(['msg', 'Error']);
		}*/
	}

	public function downloadatt($id){
		$file = DB::table('file_approved')->where('file_id',$id)->first();

		return response()->download($file->path_file,$file->file_name);
	}
}
