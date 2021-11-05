<?php

namespace App\Http\Controllers\gudang;

use Auth;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OutgoingMaterialController extends Controller
{
    //
    public function index(Request $request) {
    	$project = $this->getProject();
        $components = $this->getComponents();
		
		$view = view('gudang.listoutgoing.index');

		if($request->input('nojob')){
			$comp_detail = DB::table('outgoing_components_detail')
			->leftJoin('components','components.id','=','outgoing_components_detail.components_id')
			->select('outgoing_components_detail.id','outgoing_components_detail.outgoing_components_job','components.name','outgoing_components_detail.qty_out','components.code')
			->where('outgoing_components_job','=',$request->input('nojob'))->paginate(5);	
			$view->with('list',$comp_detail);
			$view->with('nojob',$request->input('nojob'));
			$view->with('tgljob',$request->input('tgljob'));
		}

    	$view->with('project',$project);
        $view->with('components', $components);
    	return $view;

    } 
    
    public function upload(Request $request){
    	$user = Auth::user();

    	$error = array();
    	$valid = true;

    	$path_file ='';
    	$nojob = $_POST['nojob'];
    	$tgljob = $request->input('tgljob');
    	$info = $request->input('note');
        $project_id = $request->input('project_id');

		DB::beginTransaction();
		try{
			DB::table('outgoing_components')->insert([
				'no_job' => $nojob,
				'project_id' => $project_id,
				'input_id' => $user->id,
				'tgl_out' => $tgljob,
				'note' => $info
			]);
	
			for($i=0; $i < count($request->qty); $i++){				
				$components_id = $request->components_id[$i];
				$qty = $request->qty[$i];
				$components = $this->getComponents($components_id);
				$id = DB::table('outgoing_components_detail')
				->insertGetId([
					'outgoing_components_job'=>$nojob,
					'components_id'=>$components_id,
					'components_price'=>$components->price_beli,
					'qty_out'=>$qty,
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

		DB::table('outgoing_components_detail')->where('id','=',$request->id)->delete();

    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['delete']['success']
        ]);		
    }
    
    public function batal(Request $request){

		DB::table('outgoing_components')->where('no_job','=',$request->id)->delete();

    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['delete']['success']
        ]);		
	}

    function generateNOPO($id_project) {
    	$counter = str_pad($this->getCounter($this->countercomout) ,5,'0',STR_PAD_LEFT);
        $date = date("Ymd");
        $project = $this->getProjectbyId($id_project);
    	$project_id = str_pad($project->code,3,'0',STR_PAD_LEFT);

    	return $date.$project_id.$counter;
	}       
}
