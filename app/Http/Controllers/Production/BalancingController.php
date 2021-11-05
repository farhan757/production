<?php

namespace App\Http\Controllers\Production;

use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BalancingController extends Controller
{
    //
    public function index(Request $request) {
    	$sql = DB::table('production_data')
        ->leftjoin('production_data_detail','production_data_detail.production_id','=','production_data.id')
    	->leftJoin('projects','projects.id','=','production_data.project_id')
    	->leftJoin('task_status','task_status.id','=','production_data.current_status_id')
    	->leftJoin('task_result','task_result.id','=','production_data.current_status_result_id')
    	->select('production_data.id',DB::raw('projects.name as project_name'),'production_data.file_name', 'production_data.job_ticket',  DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'),'production_data.cycle','production_data.part','production_data.created_at')
    	->addselect(DB::raw('count(production_data.id) as jml_data'))
        ->where('production_data.next_status_id','=',$this->submitBalancingId)
    	->orderBy('production_data.created_at','desc')
        ->groupBy('production_data.id');
    	$ticket = $request->ticket;
        $cycle = $request->cycle;

        $results = DB::table('task_result')
        ->join('task_status_to_result','task_result.id','=','task_status_to_result.result_id')
        ->select('task_result.*')
        ->where('task_status_to_result.status_id','=',$this->submitBalancingId)
        ->get();

        $bf2 = date('Y-m-d',strtotime('-2 days',strtotime(now())));
		$now = date('Y-m-d',strtotime(now()));

		//if(!$this->check($ticket) && !$this->check($cycle))
			//$sql->where('production_data.created_at', '>=', $bf2.' 00:00:00')->where('production_data.created_at','<=',$now.' 23:59:59');

        if($this->check($ticket))
            $sql->where('production_data.job_ticket','like','%'.$ticket.'%');
        if($this->check($cycle))
            $sql->where('production_data.cycle','=',$cycle);

        $list = $sql->paginate(10);

        $view = view('production.balancing.index');
        $view->with('list',$list); 
        $view->with('ticket',$ticket);
        $view->with('cycle',$cycle);
        $view->with('results', $results);
        if(isset($_GET['info'])) $view->with('info',$_GET['info']);
        if(isset($_GET['error'])) $view->with('error', $_GET['error']);
        return $view;
    }

    public function showform($id) {
    	$data = DB::table('production_data')
    	->where('id','=',$id)->first();
    	$results = DB::table('task_result')
    	->join('task_status_to_result','task_result.id','=','task_status_to_result.result_id')
    	->select('task_result.*')
    	->where('task_status_to_result.status_id','=',$this->submitBalancingId)
    	->get();
    	return view('production.balancing.form')->with('results',$results)->with('data',$data);
    }

    public function update(Request $request) {
        $id = $request->id;
    	$note=$request->input('note');
    	$result_id=$request->input('result_id');
    	$this->updateTask($id, $this->submitBalancingId, $result_id, $note);
        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['status']['success']
        ]);
    }

    public function showdetail($id) {
        $data = DB::table('production_data')
        ->leftJoin('customers','production_data.customer_id','=','customers.id')
        ->leftJoin('projects','production_data.project_id','=','projects.id')
        ->leftJoin('task_status','production_data.current_status_id','=','task_status.id')
        ->leftJoin('users','production_data.created_by','=','customers.id')
        ->select('production_data.id','production_data.cycle','production_data.part','production_data.job_ticket',DB::raw('task_status.name as status_name, customers.name as customer_name, projects.name as project_name,task_status.name as last_status'),'production_data.created_at','users.username')
        ->where('production_data.id','=',$id)->first();

        $list = DB::table('production_data_detail')
        ->where('production_id','=',$id)->get();

        return view('production.balancing.detaildata')->with('data',$data)->with('list',$list);
    }
}
