<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;

class ScanDistribusiController extends Controller
{
    //
    public function index(Request $request)
    {
        # code...
		$sql = DB::table('production_data')
			->leftjoin('production_data_detail','production_data_detail.production_id','=','production_data.id')
			->leftJoin('projects', 'projects.id', '=', 'production_data.project_id')
			->leftJoin('task_status', 'task_status.id', '=', 'production_data.current_status_id')
			->leftJoin('task_result', 'task_result.id', '=', 'production_data.current_status_result_id')
			->select('production_data.status_warehouse', 'production_data.id', DB::raw('projects.name as project_name'), 'production_data.file_name', 'production_data.job_ticket', DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'), 'production_data.cycle', 'production_data.part', 'production_data.created_at')
			->addselect(DB::raw('count(production_data.id) as jml_data'))
			->orderBy('production_data.created_at', 'desc')
			->groupBy('production_data.id');

		$jenis = $this->getValues('jenis');
		$parts = $this->getValues('part');
		$customers = $this->getCustomers();

        $ticket = $request->ticket;
		$cycle = $request->filterCycle;

		$info = $this->getUserInfo();

		$bf2 = date('Y-m-d',strtotime('-2 days',strtotime(now())));
		$now = date('Y-m-d',strtotime(now()));

		//if(!$this->check($ticket) && !$this->check($cycle))
			//$sql->where('production_data.created_at', '>=', $bf2.' 00:00:00')->where('production_data.created_at','<=',$now.' 23:59:59');

		if ($info->customer_id > 0)
			$sql->where('projects.customer_id', '=', $info->customer_id);
		if ($this->check($ticket))
			$sql->where('production_data.job_ticket', 'like', '%' . $ticket . '%');
		if ($this->check($cycle))
			$sql->where('production_data.cycle', '=', $cycle);
		if ($info->level != 2)
			$sql->where('production_data.created_by', '=', $info->user_id);

		$list = $sql->paginate(10);

        $view = view('production.scandistribusi.index');
		$view->with('ticket', $ticket);
		$view->with('filterCycle', $cycle);
		$view->with('list', $list);
		$view->with('customers', $customers);
		$view->with('jenis', $jenis);
		$view->with('parts', $parts);
		$view->with('customer_id', $info->customer_id);
		$view->with('level', $info->level); 

        return $view;
    }

    public function formscan(Request $r)
    {
        # code...
        $id = $r->id;
        $data = DB::table('production_data')
                ->leftJoin('customers','customers.id','=','production_data.customer_id')
                ->leftJoin('projects','projects.id','=','production_data.project_id')
                ->leftJoin('master_value','master_value.code','=','production_data.jenis')
                ->select('production_data.*','customers.name as cust_name','master_value.name as jen_name')
                ->addSelect('projects.name as pro_name')
                ->where('production_data.id','=',$id)->first();
        $view = view('production.scandistribusi.form');
        $view->with('data',$data);
        return $view;                
    }

    public function checkTicket($id)
    {
        # code...
        $totalscan = DB::table('production_data_detail')->where('production_id',$id)->where('scan_distribusi',1)->count();
        $data = DB::table('production_data_detail')->where('production_id',$id)->get();

        return response()->json([
            'data' => $data,
            'totalscan' => $totalscan,
            'jumlah' => count($data)
        ]);
    }

	function scanok($id,$nopol)
    {
		$info = $this->getUserInfo();
		$ret = DB::table('production_data_detail')->where([
            ['production_id','=',$id],
            ['barcode_env','=',$nopol],            
            ['scan_distribusi','=',1]
        ])->get();
		$status = 1;
		if(count($ret) == 1){
			$status = 0;			
		}else{
			$ret = DB::table('production_data_detail')
			->where([
				['production_id','=',$id],
				['barcode_env','=',$nopol],            
				['scan_distribusi','=',0]
			])
			->update([
				'scan_distribusi_at'=>Carbon::now(),
				'scan_distribusi_user_id'=> $info->user_id,
				'scan_distribusi'=>1
			]);			
		}


        $data['status']=$status;
        $data['jumlah']=count((array)$ret);
        $data['data']=$ret;
        return response()->json($data);
        //echo 'test';
    }

	public function update(Request $request) {
        $id = $request->id; 

        $totalscan = DB::table('production_data_detail')->where('production_id',$id)->where('scan_distribusi',1)->count();
        $data = DB::table('production_data_detail')->where('production_id',$id)->count();
		$result_id = $this->onProgressResult;
		if($totalscan == $data){
			$result_id = $this->finisResult;
		}
    	$this->updateTask($id, $this->scanDistribusiId, $result_id, "");
        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['status']['success']
        ]);
    }
}
