<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function show() {
    	return view('dashboard.index');
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
