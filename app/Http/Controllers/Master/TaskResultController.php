<?php

namespace App\Http\Controllers\Master;

use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskResultController extends Controller
{
    private $forms = array(
        array('add'=>true,'edit'=>true,'field'=>'name','desc'=>'Name','type'=>'text','length'=>'50','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'isok','desc'=>'Ok','type'=>'text','length'=>'1','mdf'=>'4','mdi'=>'2','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'desc','desc'=>'Description','type'=>'text','length'=>'300','mdf'=>'4','mdi'=>'8','required'=>false),
    );
    //
    public function index() {
    	$list = DB::table('task_result')
    	->paginate(10);
        $view = view('master.taskresult.index');
        $view->with('list',$list);
        $view->with('forms', $this->forms);

        return $view;
    }

    public function get($id) {
    	$data = DB::table('task_result')
    	->where('id','=',$id)->first();

    	return response()->json($data);
    }

    public function save($id, Request $request) {
    	$name = $request->name;
    	$desc = $request->desc;
    	$isok = $request->isok;

    	DB::table('task_result')
    	->where('id','=',$id)
    	->update([
    		'name'=>$name,
    		'desc'=>$desc,
    		'isok'=>$isok,
			'updated_at'=>Carbon::now(),
    	]);
    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['update']['success']
        ]);
    }

    public function add(Request $request) {
        $name = $request->name;
        $desc = $request->desc;
        $isok = $request->isok;
        $this->validator($request->all())->validate();

    	$result = DB::table('task_result')->insert([
    		'name'=>$name,
    		'isok'=>$isok,
    		'desc'=>$desc,
			'created_at'=>Carbon::now(),
    	]);
    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['add']['success']
        ]);
    }

    public function detail($id) {
    	$data = DB::table('task_result')
    	->where('id','=',$id)
    	->first();

    	return view('master.taskresult.detail')->with('data',$data);
    }

    public function delete($id) {
    	$data = DB::table('task_result')
    	->where('id','=',$id)
    	->delete();
        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['delete']['success']
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:50',
            'isok' => 'required|max:1',
        ]);
    }
}
