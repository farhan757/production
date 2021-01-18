<?php

namespace App\Http\Controllers\gudang;

use Auth;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListOutgoingController extends Controller
{
    //
    public function index(Request $request) {
    	$sql = DB::table('production_data')
    	->leftJoin('projects','projects.id','=','production_data.project_id')
    	->leftJoin('task_status','task_status.id','=','production_data.current_status_id')
    	->leftJoin('task_result','task_result.id','=','production_data.current_status_result_id')
    	->select('production_data.id',DB::raw('projects.name as project_name'),'production_data.file_name', 'production_data.job_ticket', 'file_name', DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'),'production_data.cycle','production_data.part','production_data.created_at')
    	//->where('production_data.next_status_id','=',$this->gudangTaskId)
    	->orderBy('production_data.updated_at','desc');

        $results = DB::table('task_result')
        ->join('task_status_to_result','task_result.id','=','task_status_to_result.result_id')
        ->select('task_result.*')
        //->where('task_status_to_result.status_id','=',$this->gudangTaskId)
        ->get();

        $ticket = $request->ticket;
        $cycle = $request->filterCycle;

        $nojob = $request->nojob;
        $cycle2 = $request->filterCycle2;

        if($this->check($ticket))
            $sql->where('production_data.job_ticket','=',$ticket);
        if($this->check($cycle))
            $sql->where('production_data.cycle','=',$cycle);

        $list = $sql->paginate(5);

        $infopo = DB::table('outgoing_components')
                    ->leftJoin('users','outgoing_components.input_id','=','users.id')
					->leftJoin('projects','projects.id','=','outgoing_components.project_id')
                    ->select('outgoing_components.tgl_out','outgoing_components.no_job','projects.code','projects.name','users.name as nama')
                    ->orderBy('no_job','Desc');
        if($this->check($nojob))
            $infopo->where('no_job','=',$nojob);
        if($this->check($cycle2))
            $infopo->where('tgl_out','=',$cycle2);        
        $tmp = $infopo->paginate(5);        

        $view = view('gudang.listoutgoing.list');
        $view->with('listats',$list); 
        $view->with('listbwh',$tmp);
        $view->with('ticket',$ticket);
        $view->with('cycle',$cycle);
        $view->with('nojob',$nojob);
        $view->with('cycle2',$cycle2);        
        $view->with('results', $results);
        return $view;
    }   
    
    public function detail($nopo){
        $data = DB::table('outgoing_components')
                ->leftJoin('users','outgoing_components.input_id','=','users.id')
                ->leftJoin('projects','projects.id','=','outgoing_components.project_id')
                ->select('outgoing_components.tgl_out','outgoing_components.no_job','users.name as nama','projects.code','projects.name','outgoing_components.note')
                ->where('outgoing_components.no_job','=',$nopo)
                ->first();

        $sql = DB::table('outgoing_components_detail')				
				->leftJoin('components','components.id','=','outgoing_components_detail.components_id')
				->select('components.code','components.name','outgoing_components_detail.qty_out','components.satuan')
				->where('outgoing_components_detail.outgoing_components_job','=',$nopo)->get();                
        
        return response()->json([
            'data'=>$data,
            'list'=>$sql
        ]);
    }    
}
