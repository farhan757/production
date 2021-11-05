<?php

namespace App\Http\Controllers\Master;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectsController extends Controller
{
    //
    private $forms = array();

    function __construct() {
        $customers = DB::table('customers')->get();
        $this->forms = array(
            array('add'=>true,'edit'=>true,'field'=>'customer_id','desc'=>'Customer','type'=>'select','length'=>'1','mdf'=>'4','mdi'=>'6','required'=>true, 'data'=>$customers),
            array('add'=>true,'edit'=>true,'field'=>'code','desc'=>'Kode Project','type'=>'text','length'=>'10','mdf'=>'4','mdi'=>'6','required'=>true),
            array('add'=>true,'edit'=>true,'field'=>'name','desc'=>'Nama Project','type'=>'text','length'=>'100','mdf'=>'4','mdi'=>'6','required'=>true),
            array('add'=>true,'edit'=>true,'field'=>'email','desc'=>'Email','type'=>'email','length'=>'190','mdf'=>'4','mdi'=>'6','required'=>'true'),
            array('add'=>true,'edit'=>true,'field'=>'desc','desc'=>'Description','type'=>'text','length'=>'300','mdf'=>'4','mdi'=>'6','required'=>false),
            array('add'=>true,'edit'=>true,'field'=>'active','desc'=>'Active','type'=>'checkbox','length'=>'1','mdf'=>'4','mdi'=>'6','required'=>false),
        );
    }

    public function index() {
    	$list = DB::table('projects')
    	->join('customers', 'projects.customer_id','=','customers.id')
    	->select('projects.id', 'projects.code', 'projects.name' ,DB::raw('customers.name as customer_name'),'projects.active','projects.desc')
    	->get();
        $menus = DB::table('menus')->get();
        $tasks = DB::table('task_status')->orderBy('name','asc')->get();
        $components = DB::table('components')->get();

        $view = view('master.project.index');
        $view->with('list',$list); 
        if(isset($_GET['info'])) $view->with('info',$_GET['info']);
        $view->with('forms', $this->forms);
        $view->with('menus', $menus);
        $view->with('tasks', $tasks);
        $view->with('components', $components);

        return $view;
    }

    public function save(Request $request, $id) {
    	$id = $request->id;
    	$code = $request->code;
    	$customer_id = $request->customer_id;
        $name = $request->name;
        $email = $request->email;
    	$desc = $request->desc;
    	if(isset($request->active)) $active=1; else $active=0;

    	DB::table('projects')
    	->where('id','=', $id)
    	->update([
    		'customer_id'=>$customer_id,
    		'code'=>$code,
    		'name'=>$name,
            'desc'=>$desc,
            'email'=>$email,
    		'active'=>$active,
			'updated_at'=>Carbon::now(),
    	]);
    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['save']['success']
        ]);
    }

    public function add(Request $request) {
    	$code = $_POST['code'];
    	$customer_id = $_POST['customer_id'];
    	$name = $_POST['name'];
        $email = $_POST['email'];
    	$desc = $_POST['desc'];
    	if(isset($_POST['active'])) $active=1; else $active=0;

    	$result = DB::table('projects')->insert([
    		'customer_id'=>$customer_id,
    		'code'=>$code,
    		'name'=>$name,
            'desc'=>$desc,
            'email'=>$email,
    		'active'=>$active,
			'created_at'=>Carbon::now(),
    	]);
    	return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['add']['success']
        ]);
    }

    public function get($id) {
    	$data = DB::table('projects')
        ->select('projects.*', DB::raw('null as tasks'),DB::raw('null as components'))
    	->where('id','=',$id)
    	->first();
        $data->tasks = DB::table('task_status')
        ->join('project_to_task', 'task_status.id', '=', 'project_to_task.status_id')
        ->select('task_status.name')
        ->orderBy('project_to_task.sort', 'asc')
        ->where('project_to_task.project_id','=',$id)
        ->get();
        $data->components = DB::table('components')
        ->join('project_to_component', 'components.id','=', 'project_to_component.component_id')
        ->select('components.name')
        ->orderBy('project_to_component.sort','asc')
        ->where('project_to_component.project_id','=',$id)
        ->get();

    	return response()->json($data);
    }

    public function remove($id) {
    	$data = DB::table('projects')
    	->where('id','=',$id)
        ->delete();
        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['delete']['success']
        ]);        
    }

    public function gettask($id) {
        $list = DB::table('project_to_task')
        ->where('project_id','=',$id)        
        ->get();

        return response()->json($list);
    }

    public function getcomponent($id) {
        $list = DB::table('project_to_component')
        ->where('project_id','=',$id)
        ->get();

        return response()->json($list);
    }

    public function savetask($id, Request $request)
    {
        DB::table('project_to_task')->where('project_id','=',$id)->delete();
        $n=0;
        foreach ($request->input('checkbox') as $key => $value) {
            $sort = $request->input('sort')[$key];
            DB::table('project_to_task')
            ->insert([
                'project_id'=>$id,
                'status_id'=>$value,
                'sort'=>$sort,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ]);
            $n++;
        }
        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['save']['success']
        ]);
    }

    public function savecomponent($id, Request $request)
    {
        DB::table('project_to_component')->where('project_id','=',$id)->delete();
        $n=0;
        if($request->input('checkbox')){
            foreach ($request->input('checkbox') as $key => $value) {
                $sort = $request->input('sort')[$key];
                $price_shel = $request->input('price')[$key];
                DB::table('project_to_component')
                ->insert([
                    'project_id'=>$id,
                    'component_id'=>$value,
                    'sort'=>$sort,
                    'price_jual'=>$price_shel,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now()
                ]);
                $n++;
            }
        }

        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['save']['success']
        ]);    
    }

    public function getByCustomer($customer_id) {
        $sql = DB::table('projects')
        ->where('customer_id','=',$customer_id)
        ->orderBy('name','ASC')
        ->select('id','name');

        $info = $this->getUserInfo();
        if($info->project_id>0)
            $sql->where('id','=',$info->project_id);

        $ret=$sql->get();
        return response()->json($ret);
    }

}
