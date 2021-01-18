<?php

namespace App\Http\Controllers\PO;

use Auth;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListPOController extends Controller
{
    //
    public function index(Request $request){
		$infopo = DB::table('incoming_components')
					->leftJoin('vendor','vendor.id','=','incoming_components.vendor_id')
                    ->select('incoming_components.print','incoming_components.tgl_po','incoming_components.no_po','incoming_components.complete','vendor.code','vendor.name')
                    ->orderBy('no_po','Desc');
        if($this->check($request->nopo))
            $infopo->where('no_po','=',$request->nopo);
        if($this->check($request->tglpo))
            $infopo->where('tgl_po','=',$request->tglpo);
        
        $tmp = $infopo->paginate(5);
        return view('po.listpo.index')->with('listpo',$tmp)->with('nopo',$request->nopo)->with('tglpo',$request->tglpo);
    }

	public function printPO($nopo){
		$user = Auth::user();
		$infopo = DB::table('incoming_components')
					->leftJoin('users','incoming_components.input_id','=','users.id')
					->leftJoin('vendor','vendor.id','=','incoming_components.vendor_id')
                    ->select('incoming_components.tgl_po','incoming_components.no_po','incoming_components.complete','users.name as nama','vendor.code','vendor.name','vendor.pic')
                    ->where('incoming_components.no_po','=',$nopo)
					->first();

		$sql = DB::table('incoming_components')
				->join('incoming_components_detail','incoming_components.no_po','=','incoming_components_detail.incoming_components_po')
				->leftJoin('components','components.id','=','incoming_components_detail.components_id')
				->select('components.code','components.name','incoming_components_detail.qty_order','components.satuan')
				->where('incoming_components.no_po','=',$nopo)->get();

        DB::table('incoming_components')
        ->where('incoming_components.no_po','=',$nopo)
        ->update([
            'print'=> 1
        ]);

		$view = view('po.listpo.printtt');
		$view->with('data',$sql);
		$view->with('infopo',$infopo);		
		$view->with('printby',$user->name);				
		return $view;
    } 
    
    public function detail($nopo){
        $data = DB::table('incoming_components')
                ->leftJoin('users','incoming_components.input_id','=','users.id')
                ->leftJoin('vendor','vendor.id','=','incoming_components.vendor_id')
                ->select('incoming_components.tgl_po','incoming_components.no_po','incoming_components.complete','users.name as nama','vendor.code','vendor.name','vendor.pic')
                ->where('incoming_components.no_po','=',$nopo)
                ->first();

        $sql = DB::table('incoming_components_detail')				
				->leftJoin('components','components.id','=','incoming_components_detail.components_id')
				->select('components.code','components.name','incoming_components_detail.qty_order','incoming_components_detail.qty_arrive','components.satuan')
				->where('incoming_components_detail.incoming_components_po','=',$nopo)->get();                
        
        return response()->json([
            'data'=>$data,
            'list'=>$sql
        ]);
    }

	public function delete(Request $request){

		DB::table('incoming_components')->where('no_po','=',$request->id)->delete();

    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['delete']['success']
        ]);		
	}    
}
