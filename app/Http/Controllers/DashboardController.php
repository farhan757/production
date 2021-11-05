<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function show() {
        
        $view = view('dashboard.index');
        
        return $view;		
	}
    
    public function showGrafik() {
        $customers = $this->getCustomers();
        $view = view('dashboard.grafik');
        $view->with('customers',$customers);
        return $view;		
	}	
	
	public function getrange($per='year',$segment=0,$info=1, $start=null,$end=null){
		$MONTHS = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December','Null');		
        $query = DB::table('production_data');
        $lb = "Printing";
        if($info == 1){
            $query->select(DB::raw('SUM(components_out.qty) AS jumlah'));
        }else{
            $lb = "Pendapatan Rp";
            $query->select(DB::raw('SUM(components_out.qty * components_out.`component_price`) AS jumlah'));
        }
		
		$query->addSelect(DB::raw('YEAR(production_data.created_at) as tahun'));
		$query->leftJoin('components_out','components_out.job_ticket','=','production_data.job_ticket');
		$query->leftJoin('components','components_out.component_id','=','components.id');
        if($info == 1){
            $query->where('components_out.group','=','jasa');
        }
        if($info == 1){
            $query->where('components.name','LIKE','%printing%');
        }

		$query->where('production_data.status_warehouse','=',1);
        if($start != null && $end != null){
            $query->where('production_data.created_at','>=',$start.' 00:00:00')->where('production_data.created_at','<=',$end.' 23:59:59');
        }		
		if($segment != 0){
			$query->where('production_data.customer_id','=',$segment);
		}
        if($per=='month' || $per=='day')
        {
            $query->addSelect(DB::raw('MONTH(production_data.created_at) as bulan'));                
        }
        if($per=='day')
        {
            $query->addSelect(DB::raw('DAY(production_data.created_at) as tanggal'));
        }

        switch ($per) {                
            case 'day':
                $query->groupBy('tahun','bulan','tanggal');
                break;
            case 'month':
                $query->groupBy('tahun','bulan');
                break;
            default:
                $query->groupBy('tahun');
                break;
		}	
		$data = $query->get();
		
        $nwArray=array();
        $nwArray['total']= array();
		$nwArray['labels']= array();
		
		$total = 0;

        foreach ($data as $key => $value) {
            array_push($nwArray['total'], $value->jumlah);

            $total = $total+$value->jumlah;
            if($per=='month' || $per=='day') {
                if($value->bulan==null) $bulan = "Null";
                else {
                    $bulan = $MONTHS[$value->bulan-1];
                }
            }
            switch ($per) {
                case 'day':
                    array_push($nwArray['labels'], $value->tanggal."-".$bulan."-".$value->tahun);
                    break;
                case 'month':
                    array_push($nwArray['labels'], $bulan."-".$value->tahun);
                    break;                
                default:
                    array_push($nwArray['labels'], $value->tahun);
                    break;
            }

        }

        $nwArray['lbl_total'] = number_format($total);
        $nwArray['lbl_info'] = $lb;
        return response()->json($nwArray);
	}


    public function get($type) {
    	switch ($type) {
    		case 'submit':
				return $this->getSubmitToday();
    			break;
    		case 'prod':
    			return $this->getProductionSubmitToday();
    			break;
    		case 'material':
    			return $this->getMaterialToday();
    			break;
    		case 'deliv':
    			return $this->getDeliveryToday();
	    		break;
    	}
    }

    function getSubmitToday() {
		$info = $this->getUserInfo();       

    	$sql = DB::table('incoming_data')
    	->leftJoin('projects','incoming_data.project_id','=','projects.id')
    	->whereDate('incoming_data.created_at', Carbon::today());
    	if($info->customer_id>0)
            $sql->where('projects.customers_id', '=', $info->customer_id);
        if($info->project_id>0)
            $sql->where('incoming_data.project_id','=',$info->project_id);

    	return $sql->count();
    }

    function getProductionSubmitToday() {
		$info = $this->getUserInfo();       

    	$sql = DB::table('production_data')
    	->leftJoin('projects','production_data.project_id','=','projects.id')
    	->whereDate('production_data.created_at', Carbon::today());
		
		if($info->customer_id>0)
            $sql->where('projects.customers_id', '=', $info->customer_id);
        if($info->project_id>0)
            $sql->where('production_data.project_id','=',$info->project_id);

    	return $sql->count();
    }

    function getMaterialToday() {
		$info = $this->getUserInfo();       

    	$sql = DB::table('production_data_detail_list')
    	->leftJoin('components','components.id','=','production_data_detail_list.component_id')
    	->leftJoin('production_data_detail','production_data_detail.id','=','production_data_detail_list.production_data_detail_id')
    	->leftJoin('production_data','production_data.id','=','production_data_detail.production_id')	
    	->whereDate('production_data.created_at', Carbon::today())
		->where('components.group','=','material')
		->where('production_data.status_warehouse','=',1);

		if($info->customer_id>0)
            $sql->where('production_data.customers_id', '=', $info->customer_id);
        if($info->project_id>0)
            $sql->where('production_data.project_id','=',$info->project_id);


    	return $sql->sum('production_data_detail_list.total');
    }

    function getDeliveryToday() {
		$info = $this->getUserInfo();

		$sql = DB::table('manifest')
		->join('production_data_detail','production_data_detail.no_manifest','=','manifest.no_manifest')
		->leftJoin('production_data','production_data.id','=','production_data_detail.production_id')
    	->whereDate('manifest.created_at', Carbon::today());

		if($info->customer_id>0)
            $sql->where('production_data.customers_id', '=', $info->customer_id);
        if($info->project_id>0)
            $sql->where('production_data.project_id','=',$info->project_id);

        return $sql->count();
    }
}
