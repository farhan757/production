<?php

namespace App\Http\Controllers\Master;

use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ComponentController extends Controller
{
    //
    private $forms = array();

    function __construct() {
        $groups = $this->getValues('group-component');

        $this->forms = array(
        array('add'=>true,'edit'=>true,'field'=>'code','desc'=>'Code','type'=>'text','length'=>'50','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'name','desc'=>'Name','type'=>'text','length'=>'190','mdf'=>'4','mdi'=>'6','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'satuan','desc'=>'Satuan','type'=>'text','length'=>'150','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'price','desc'=>'Price','type'=>'number','length'=>'11','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'stock','desc'=>'Stock','type'=>'number','length'=>'11','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'group','desc'=>'Group','type'=>'selectg','length'=>'1','mdf'=>'4','mdi'=>'4','required'=>true, 'data'=>$groups),
        );
    }

    public function index() {
    	$list = DB::table('components')
    	->paginate(10);
    	$view = view('master.component.index')->with('list',$list);
        $view->with('forms', $this->forms);

        return $view;
    }

    public function get($id) {
    	$data = DB::table('components')
    	->where('id','=',$id)->first();

    	return response()->json($data);
    }

    public function save($id, Request $request) {
    	$code = $request->code;
    	$name = $request->name;
    	$satuan = $request->satuan;
        $price = $request->price;
        $stock = $request->stock;
    	$group = $request->group;

    	DB::table('components')
    	->where('id','=',$id)
    	->update([
    		'code'=>$code,
    		'name'=>$name,
    		'satuan'=>$satuan,
            'price'=>$price,
            'stock'=>$stock,
    		'group'=>$group,
			'updated_at'=>Carbon::now(),
    	]);
    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['save']['success'],
        ]);
    }

    public function add(Request $request) {
        $code = $request->code;
        $name = $request->name;
        $satuan = $request->satuan;
        $price = $request->price;
        $stock = $request->stock;
        $group = $request->group;

    	$result = DB::table('components')->insert([
    		'code'=>$code,
    		'name'=>$name,
    		'satuan'=>$satuan,
            'price'=>$price,
            'stock'=>$stock,
    		'group'=>$group,
			'created_at'=>Carbon::now(),
    	]);
        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['add']['success']
        ]);
    }

    public function detail($id) {
    	$data = DB::table('components')
    	->where('id','=',$id)
    	->first();

    	return view('master.component.detail')->with('data',$data);
    }

    public function delete($id) {
    	$data = DB::table('components')
    	->where('id','=',$id)
    	->delete();

        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['add']['success']
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'code'     => 'required|max:50',
            'name' => 'required|max:190',
            'satuan' => 'required|max:150',
            'price' => 'required',
            'stock' => 'required',
            'group' => 'required|max:150',
        ]);
    }    
}
