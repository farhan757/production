<?php

namespace App\Http\Controllers\PO;

use Auth;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class POMaterialController extends Controller
{
    //
    public function index(Request $request) {
    	$vendor = $this->getVendor();
        $components = $this->getComponents();
		
		$view = view('po.createpo.index');

		if($request->input('nopo')){
			$comp_detail = DB::table('incoming_components_detail')
			->leftJoin('components','components.id','=','incoming_components_detail.components_id')
			->select('incoming_components_detail.id','incoming_components_detail.incoming_components_po','components.name','incoming_components_detail.qty_order','components.code')
			->where('incoming_components_detail.complete','=',0)
			->where('incoming_components_po','=',$request->input('nopo'))->get();	
			$view->with('list',$comp_detail);
			$view->with('nopo',$request->input('nopo'));
			$view->with('tglpo',$request->input('tglpo'));
		}

    	$view->with('vendor',$vendor);
        $view->with('components', $components);
    	return $view;

    } 
    
    public function addform(Request $request)
    {
        # code...
		$components = $this->getComponents();
        return view('po.createpo.add_dua')->with('no', $request->input('id'))->with('components', $components);
    }

    public function upload(Request $request){
    	$user = Auth::user();

    	$error = array();
    	$valid = true;

    	$path_file ='';
    	$nopo = $_POST['nopo'];
    	$tglpo = $request->input('tglpo');
    	$info = $request->input('note');
        $vendor_id = $request->input('vendor_id');
		DB::beginTransaction();
		try{
			DB::table('incoming_components')->insert([
				'no_po' => $nopo,
				'vendor_id' => $vendor_id,
				'input_id' => $user->id,
				'tgl_po' => $tglpo
			]);
	
			for($i=0; $i < count($request->qty); $i++){
				$components_id = $request->components_id[$i];
				$qty = $request->qty[$i];
				$components = $this->getComponents($components_id);
				$id = DB::table('incoming_components_detail')
				->insertGetId([
					'incoming_components_po'=>$nopo,
					'components_id'=>$components_id,
					'components_price'=>$components->price_beli,
					'qty_order'=>$qty,
					'created_at'=>Carbon::now(),
					'updated_at'=>Carbon::now()
				]);
			}
			DB::commit();
			return response()->json([
				'status'=>1,
				'message'=>$this->message['default']['add']['success']
			]);
		}catch(Exception $e){
			DB::rollBack();
			return response()->json([
				'status'=>2,
				'message'=>$this->message['default']['add']['error']
			]);
		}	
    }

	public function delete(Request $request){

		try{
			DB::table('incoming_components_detail')->where('id','=',$request->id)->delete();
			return response()->json([
				'status'=>1,
				'message'=>'ok'
			]);	
		}catch(Exception $e){
			return response()->json([
				'status'=>2,
				'message'=>$e
			]);	
		}	
	
	}

    public function batal(Request $request){

		DB::table('incoming_components')->where('no_po','=',$request->id)->delete();

    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['delete']['success']
        ]);		
	}

    function generateNOPO() {
    	$counter = str_pad($this->getCounter($this->countercomin) ,5,'0',STR_PAD_LEFT);
    	$date = date("Ymd");
    	$project_id = str_pad('PO',3,'0',STR_PAD_LEFT);

    	return $date.$project_id.$counter;
	}   
	
}
