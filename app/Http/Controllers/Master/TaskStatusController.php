<?php

namespace App\Http\Controllers\Master;

use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskStatusController extends Controller
{
    private $forms = array(
        array('add'=>true,'edit'=>true,'field'=>'name','desc'=>'Status Name','type'=>'text','length'=>'50','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'icon','desc'=>'Icon','type'=>'text','length'=>'35','mdf'=>'4','mdi'=>'2','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'desc','desc'=>'Description','type'=>'text','length'=>'300','mdf'=>'4','mdi'=>'8','required'=>false),
    );
    //
    public function index() {
    	$list = DB::table('task_status')
    	->paginate(10);
        $results = DB::table('task_result')
        ->get();

        $view = view('master.taskstatus.index')->with('list',$list);
        $view->with('forms', $this->forms);
        $view->with('results', $results);
        return $view;
    }

    public function get($id) {
    	$data = DB::table('task_status')
        ->select('task_status.*', DB::raw('null as results'))
        ->where('id','=',$id)
        ->first();

        $data->results = DB::table('task_result')
        ->join('task_status_to_result', 'task_status_to_result.result_id','=','task_result.id')
        ->select('task_result.name')
        ->where('task_status_to_result.status_id','=',$id)
        ->get();

    	return response()->json($data);
    }

    public function save($id, Request $request) {

    	DB::table('task_status')
    	->where('id','=',$id)
    	->update([
    		'name'=>$request->name,
    		'icon'=>$request->icon,
    		'desc'=>$request->desc,
			'updated_at'=>Carbon::now(),
    	]);
    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['save']['success']
        ]);
    }

    public function add(Request $request) {
        $this->validator($request->all())->validate();

    	$result = DB::table('task_status')->insert([
    		'name'=>$request->name,
    		'icon'=>$request->icon,
    		'desc'=>$request->desc,
			'created_at'=>Carbon::now(),
    	]);
    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['add']['success']
        ]);
    }

    public function detail($id) {
    	$data = DB::table('task_status')
        ->select('task_status.*', DB::raw('null as results'))
    	->where('id','=',$id)
    	->first();

    	$data->results = DB::table('task_result')
    	->join('task_status_to_result', 'task_status_to_result.result_id','=','task_result.id')
    	->select('task_result.name')
    	->where('task_status_to_result.status_id','=',$id)
    	->get();

    	return response()->json($data);
    }

    public function delete($id) {
    	$data = DB::table('task_status')
    	->where('id','=',$id)
    	->delete();

        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['delete']['success']
        ]);        
    }

    public function getresult($id) {
    	$status = DB::table('task_status_to_result')->where('status_id','=',$id)->get();

        return response()->json($status);
    }

    public function saveresult($id, Request $request)
    {
        DB::table('task_status_to_result')->where('status_id','=',$id)->delete();
        foreach ($request->input('checkbox') as $key => $value) {
            DB::table('task_status_to_result')
            ->insert([
                'status_id'=>$id,
                'result_id'=>$value,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ]);
        }
    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['save']['success']
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:50',
            'icon' => 'required|required|max:35',
        ]);
    }
}
