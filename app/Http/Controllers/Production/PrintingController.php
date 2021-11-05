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
        ->leftjoin('production_data_detail','production_data_detail.production_id','=','production_data.id')
    	->leftJoin('projects','projects.id','=','production_data.project_id')
    	->leftJoin('task_status','task_status.id','=','production_data.current_status_id')
    	->leftJoin('task_result','task_result.id','=','production_data.current_status_result_id')
    	->select('production_data.id',DB::raw('projects.name as project_name'),'production_data.file_name', 'production_data.job_ticket',  DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'),'production_data.cycle','production_data.part','production_data.created_at')
    	->addselect(DB::raw('count(production_data.id) as jml_data'))
        ->where('production_data.next_status_id','=',$this->submitPrintingId)
    	->orderBy('production_data.updated_at','desc')
        ->groupBy('production_data.id');

        $results = DB::table('task_result')
        ->join('task_status_to_result','task_result.id','=','task_status_to_result.result_id')
        ->select('task_result.*')
        ->where('task_status_to_result.status_id','=',$this->submitPrintingId)
        ->get();


        $ticket = $request->ticket;
        $cycle = $request->cycle;

		$bf2 = date('Y-m-d',strtotime('-2 days',strtotime(now())));
		$now = date('Y-m-d',strtotime(now()));

		//if(!$this->check($ticket) && !$this->check($cycle))
			//$sql->where('production_data.created_at', '>=', $bf2.' 00:00:00')->where('production_data.created_at','<=',$now.' 23:59:59');

        if($this->check($ticket))
            $sql->where('production_data.job_ticket','like','%'.$ticket.'%');
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
        $user = Auth::user();
        $id = $request->id;
    	$note=$request->input('note');
    	$result_id=$request->input('result_id');
        $this->updateTask($id, $this->submitPrintingId, $result_id, $note);
        DB::table("production_data_detail")
        ->where("production_id","=",$id)
        ->update([
            "cetak" => 1,
            "cetak_user_id" => $user->id,
            "cetak_at" => Carbon::now()
        ]);
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
        ->select('production_data.id','production_data.jenis','production_data.cycle','production_data.part','production_data.job_ticket',DB::raw('task_status.name as status_name, customers.name as customer_name, projects.name as project_name,task_status.name as last_status'),'production_data.created_at','users.username')
        ->where('production_data.id','=',$id)->first();
        $material = DB::table('production_data_detail_list')
        ->join('production_data_detail', 'production_data_detail.id','=','production_data_detail_list.production_data_detail_id')
        ->join('components','production_data_detail_list.component_id','=','components.id')
        ->select('components.id','components.name','components.code','components.satuan',DB::raw('sum(production_data_detail_list.total) as total'))
        ->groupBy('components.id','components.name', 'components.code','components.satuan')
        ->where('production_data_detail.production_id','=',$id)
        ->get();

        $rinci = DB::table('production_data_detail')
                 ->select('production_data_detail.city','production_data_detail.barcode_document as cabang',DB::raw('count(production_data_detail.barcode_document) as total'), 'production_data_detail.ekspedisi','production_data_detail.service')
                 ->where('production_data_detail.barcode_document','!=',"")
                 ->where('production_data_detail.production_id','=',$id)
                 ->groupBy('production_data_detail.barcode_document')
                 ->get();        
        
        $kurir = DB::table('production_data')
                ->join('production_data_detail','production_data_detail.production_id','=','production_data.id')
                ->select('production_data_detail.ekspedisi','production_data_detail.service',DB::raw('SUM(production_data_detail.bst_inserting) AS count_eks'))
                ->where('production_data.id',$id)->groupBy('production_data_detail.ekspedisi','production_data_detail.service')->get();

        return view('production.printing.material')
        ->with('data',$data)
        ->with('material',$material)
        ->with('rinci',$rinci)
        ->with('name',$user->name)->with('kurir',$kurir);
    }
}
