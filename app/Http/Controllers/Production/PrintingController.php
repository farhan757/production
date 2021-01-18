<?php

namespace App\Http\Controllers\Production;

use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrintingController extends Controller
{
    //
    public function index(Request $request) {
    	$sql = DB::table('production_data')
    	->leftJoin('projects','projects.id','=','production_data.project_id')
    	->leftJoin('task_status','task_status.id','=','production_data.current_status_id')
    	->leftJoin('task_result','task_result.id','=','production_data.current_status_result_id')
    	->select('production_data.id',DB::raw('projects.name as project_name'),'production_data.file_name', 'production_data.job_ticket', 'file_name', DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'),'production_data.cycle','production_data.part','production_data.created_at')
    	->where('production_data.next_status_id','=',$this->submitPrintingId)
    	->orderBy('production_data.updated_at','desc');

        $results = DB::table('task_result')
        ->join('task_status_to_result','task_result.id','=','task_status_to_result.result_id')
        ->select('task_result.*')
        ->where('task_status_to_result.status_id','=',$this->submitPrintingId)
        ->get();


        $ticket = $request->ticket;
        $cycle = $request->cycle;

        if($this->check($ticket))
            $sql->where('production_data.job_ticket','=',$ticket);
        if($this->check($cycle))
            $sql->where('production_data.cycle','=',$cycle);

        $list = $sql->paginate(10);

        $view = view('production.printing.index');
        $view->with('list',$list); 
        $view->with('ticket',$ticket);
        $view->with('cycle',$cycle);
        $view->with('results', $results);
        if(isset($_GET['info'])) $view->with('info',$_GET['info']);
        if(isset($_GET['error'])) $view->with('error', $_GET['error']);
        return $view;
    }

    public function update(Request $request) {
        $id = $request->id;
    	$note=$request->input('note');
    	$result_id=$request->input('result_id');
    	$this->updateTask($id, $this->submitPrintingId, $result_id, $note);
        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['status']['success']
        ]);
    }

    public function printmaterial($id) {
        $user = Auth::user();
        $data = DB::table('production_data')
        ->leftJoin('customers','production_data.customer_id','=','customers.id')
        ->leftJoin('projects','production_data.project_id','=','projects.id')
        ->leftJoin('task_status','production_data.current_status_id','=','task_status.id')
        ->leftJoin('users','production_data.created_by','=','customers.id')
        ->select('production_data.id','production_data.cycle','production_data.part','production_data.job_ticket',DB::raw('task_status.name as status_name, customers.name as customer_name, projects.name as project_name,task_status.name as last_status'),'production_data.created_at','users.username')
        ->where('production_data.id','=',$id)->first();
        $material = DB::table('production_data_detail_list')
        ->join('production_data_detail', 'production_data_detail.id','=','production_data_detail_list.production_data_detail_id')
        ->join('components','production_data_detail_list.component_id','=','components.id')
        ->select('components.id','components.name','components.code','components.satuan',DB::raw('sum(production_data_detail_list.total) as total'))
        ->groupBy('components.id','components.name', 'components.code','components.satuan')
        ->where('production_data_detail.production_id','=',$id)
        ->get();

        return view('production.printing.material')
        ->with('data',$data)
        ->with('material',$material)
        ->with('name',$user->name);
    }
}
