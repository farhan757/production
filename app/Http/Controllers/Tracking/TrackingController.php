<?php

namespace App\Http\Controllers\Tracking;

use Storage;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrackingController extends Controller
{
    //
    public function index(Request $request) {
		$list=null; $ticket=null;
		if($this->check($request->ticket)){
			$sql = DB::table('production_data')
			->leftJoin('projects','projects.id','=','production_data.project_id')
			->leftJoin('task_status','task_status.id','=','production_data.current_status_id')
			->leftJoin('task_result','task_result.id','=','production_data.current_status_result_id')
			->select('production_data.id',DB::raw('projects.name as project_name'),'production_data.file_name', 'production_data.job_ticket', 'file_name', DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'),'production_data.cycle','production_data.part','production_data.created_at')
			->orderBy('production_data.created_at','desc');
	
			$ticket = $request->ticket;
					
			$info = $this->getUserInfo();
	
			if($this->check($ticket))
				$sql->where('production_data.job_ticket','=',$ticket);
	
			$list = $sql->paginate(5);
		}


    	$view = view('Tracking.index');
        $view->with('ticket',$ticket);        
        $view->with('list',$list); 

        if(isset($_GET['info'])) $view->with('info',$_GET['info']);
        if(isset($_GET['error'])) $view->with('error', $_GET['error']);

        return $view;
    }

    public function selectproject() {
    	$customers = $this->getCustomers();

    	return view('production.joblist.formproject')
    	->with('customers',$customers);
    }

    public function detail($id) {
    	$data = DB::table('production_data')
    	->leftJoin('customers','production_data.customer_id','=','customers.id')
    	->leftJoin('projects','production_data.project_id','=','projects.id')
    	->leftJoin('task_status','production_data.current_status_id','=','task_status.id')
    	->leftJoin('users','production_data.created_by','=','users.id')
    	->select('production_data.id', 'production_data.file_id', 'production_data.cycle','production_data.part', 'production_data.jenis' ,'production_data.job_ticket',DB::raw('task_status.name as status_name, customers.name as customer_name, projects.name as project_name,task_status.name as last_status'),'production_data.created_at','users.username', DB::raw('null as transf'), DB::raw('null as transp'))
    	->where('production_data.id','=',$id)->first();

    	$list = DB::table('production_data_detail')
    	->where('production_id','=',$id)->get();
    	$transactionFile = array();
    	if($data->file_id!=0) {
	    	$transactionFile = DB::table('incoming_data')
	    	->select('incoming_data.*', DB::raw('null as data'))
	    	->where('id', '=', $data->file_id)->first();

	    	$transactionFile->data = DB::table('transaction_history')
	    	->leftJoin('task_status','transaction_history.status_id','=','task_status.id')
	    	->leftJoin('task_result','transaction_history.result_id','=','task_result.id')
	    	->leftJoin('users','transaction_history.user_id','=','users.id')
    		->select('transaction_history.created_at', 'users.username', 'transaction_history.note', DB::raw('task_status.name as status_name, task_status.icon as status_icon, task_result.name as result_name'))
	    	->where('file_id', '=', $data->file_id)
	    	->orderBy('created_at')
	    	->get();
    	}

    	$transactionProduction = DB::table('transaction_history')
    	->leftJoin('task_status','transaction_history.status_id','=','task_status.id')
    	->leftJoin('task_result','transaction_history.result_id','=','task_result.id')
    	->leftJoin('users','transaction_history.user_id','=','users.id')
    		->select('transaction_history.created_at', 'users.username', 'transaction_history.note', DB::raw('task_status.name as status_name, task_status.icon as status_icon, task_result.name as result_name'))
    	->where('transaction_history.production_id', '=', $id)
    	->orderBy('created_at')
    	->get();

    	return response()->json([
    		'status'=>1,
    		'data'=>$data,
    		'list'=>$list,
    		'transP'=>$transactionProduction,
    		'transF'=>$transactionFile
    	]);

    	$view = view('production.joblist.detaildata')->with('data',$data)->with('list',$list)
    	->with('transP', $transactionProduction);

    	if($data->file_id!=0) $view->with('transF', $transactionFile);

    	return $view;
    }
}
