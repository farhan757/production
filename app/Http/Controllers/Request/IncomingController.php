<?php

namespace App\Http\Controllers\Request;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IncomingController extends Controller
{
    //
    public function index() {


    	$sql = DB::table('incoming_data')
    	->leftJoin('projects','projects.id','=','incoming_data.project_id')
    	->leftJoin('task_status','task_status.id','=','incoming_data.current_status_id')
    	->select('incoming_data.id',DB::raw('projects.name as project_name'),'incoming_data.file_name', 'incoming_data.ticket', 'file_name', DB::raw('task_status.name as status_name'));

        $info = $this->getUserInfo();
        if($info->customer_id>0)
            $sql->where('projects.customer_id', '=', $info->customer_id);
        if($info->project_id>0)
            $sql->where('incoming_data.project_id','=',$info->project_id);

    	$list=$sql->paginate(10);

    	return view('request.incoming.index')->with('list',$list);
    }

    public function showdetail($id) {
    	$data = DB::table('incoming_data')
    	->leftJoin('projects','projects.id','=','incoming_data.project_id')
    	->leftJoin('task_status','task_status.id','=','incoming_data.current_status_id')
    	->select('incoming_data.*',DB::raw('projects.name as project_name'), 'file_name', DB::raw('task_status.name as status_name'), DB::raw('null as transf'), DB::raw('null as transp'))
    	->where('incoming_data.id','=',$id)
    	->first();

        $transactionFile = DB::table('transaction_history')
        ->leftJoin('task_status','transaction_history.status_id','=','task_status.id')
        ->leftJoin('task_result','transaction_history.result_id','=','task_result.id')
        ->leftJoin('users','transaction_history.user_id','=','users.id')
        ->select('transaction_history.created_at', 'users.username', 'transaction_history.note', DB::raw('task_status.name as status_name, task_status.icon as status_icon, task_result.name as result_name'))
        ->where('file_id', '=', $id)
        ->orderBy('created_at')
        ->get();

        $productions = DB::table('production_data')
        ->select('production_data.*', DB::raw('null as data'))
        ->where('file_id','=',$id)
        ->get();

        $pros = array();
        foreach ($productions as $key => $value) {
            $productions[$key]->data = DB::table('transaction_history')
                ->leftJoin('task_status','transaction_history.status_id','=','task_status.id')
                ->leftJoin('task_result','transaction_history.result_id','=','task_result.id')
                ->leftJoin('users','transaction_history.user_id','=','users.id')
                    ->select('transaction_history.created_at', 'users.username', 'transaction_history.note', DB::raw('task_status.name as status_name, task_status.icon as status_icon, task_result.name as result_name'))
                ->where('transaction_history.production_id', '=', $value->id)
                ->orderBy('created_at')
                ->get();
        }
        $data->transf = $transactionFile;
        $data->transp = $productions;
//        var_dump($pros);
 //       die();
        return response()->json($data);
    }
}
