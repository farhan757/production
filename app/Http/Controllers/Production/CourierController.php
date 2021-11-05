<?php

namespace App\Http\Controllers\Production;

use Auth;
use DB;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourierController extends Controller
{
    // 

    public function index(Request $request) {
    	$no_amplop=$request->no_amplop;
    	$no_account=$request->input('no_account');
    	$start_date=$request->start_date;
    	$end_date=$request->end_date;
    	$nama = $request->nama;
    	$customer_id = $request->customer_id;
    	$project_id = $request->project_id;
    	$cycle = $request->cycle;
    	$part = $request->part;
    	$jenis = $request->jenis;

		$list = null; $sql=null;

		if($this->check($no_amplop) || $this->check($no_account) || $this->check($start_date) 
		|| $this->check($end_date) || $this->check($nama) || $this->check2($customer_id) 
		|| $this->check2($project_id) || $this->check($cycle) || $this->check2($part) || $this->check2($jenis))
		{
			$sql = DB::table('production_data_detail')
			->select('production_data_detail.*')
			->leftJoin('production_data','production_data_detail.production_id','=','production_data.id')
			->leftJoin('projects','production_data.project_id','=','projects.id');
	
			if($this->check($no_amplop)) {
				$sql = $sql->where('production_data_detail.barcode_env','=',$no_amplop);
			}
			if($this->check($no_account)) {
				$sql = $sql->where('production_data_detail.account_no','=',$no_account);
			}
			if($this->check($start_date)) {
				$sql = $sql->where('production_data_detail.created_at','>=', $start_date);
			}
			if($this->check($end_date)) {
				$sql = $sql->where('production_data_detail.created_at','<=', $end_date);
			}
			if($this->check($nama)) {
				$sql = $sql->where('production_data_detail.penerima','like', '%'.$nama.'%');
			}
			if($this->check2($customer_id)) {
				$sql = $sql->where('projects.customer_id', '=', $customer_id);
			}
			if($this->check2($project_id)) {
				$sql = $sql->where('production_data.project_id','=',$project_id);
			}
			if($this->check($cycle)) {
				$sql = $sql->where('production_data.cycle','=',$cycle);
			}
			if($this->check2($part)) {
				$sql = $sql->where('production_data.part','=',$part);
			}
			if($this->check2($jenis)) {
				$sql = $sql->where('production_data.jenis','=',$jenis);
			}
			
			$sql->orderBy('id','desc');
			$list = $sql->paginate(10);
		}
    	
	    	$projects = DB::table('projects')
	    	->join('customers', 'projects.customer_id','=', 'customers.id')
	    	->select('projects.id', DB::raw('projects.name as project_name, customers.name as customer_name'))
	    	->get();

		    $jeniss = $this->getValues('jenis');
		    $parts = $this->getValues('part');
	    	$view = view('production.courier.index')->with('list',$list);

	    	return $view
	        ->with('jeniss', $jeniss)
	        ->with('parts', $parts)
	        ->with('projects', $projects)
	    	->with('no_amplop', $no_amplop)
	    	->with('no_account', $no_account)
	    	->with('start_date',$start_date)
	    	->with('end_date', $end_date)
	    	->with('nama', $nama)
	    	->with('customer_id', $customer_id)
	    	->with('project_id', $project_id)
	    	->with('cycle',$cycle)
	    	->with('part', $part)
	    	->with('jenis', $jenis);
    	
    }

    public function showdetail($id) {
    	$data = DB::table('production_data_detail')
    	->where('id','=',$id)
    	->first();

    	$prod = DB::table('production_data')
    	->where('id','=',$data->production_id)
    	->first();
    	$file = null;
    	if($prod->file_id>0) {
    		$file = DB::table('incoming_data')
	    	->where('id','=',$prod->id)->first();
    	}

    	return view('production.courier.detaildata')
    	->with('data',$data)
    	->with('prod',$prod)
    	->with('file',$file);

	}
	
	public function saveUpdate(Request $request){
		$courier = $request->courier;
		$service = $request->service;
		$id = $request->id;

		$msg = array();
		try{
			DB::table("production_data_detail")
			->where('id','=',$id)
			->update([
				"ekspedisi" => $courier,
				"service" => $service
			]);

			
			$msg = array(
				"status" => 1,
				"message" => $this->message["default"]["save"]["success"]
			);
			return response()->json($msg);
		}catch(Exception $e)
		{
			$msg = array(
				"status" => 2,
				"message" => $e
			);
			return response()->json($msg);
		}
		$msg = array(
			"status" => 3,
			"message" => $e,$this->message["default"]["save"]["error"]
		);
		return response()->json($msg);
	}
}
