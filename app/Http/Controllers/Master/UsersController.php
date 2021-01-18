<?php

namespace App\Http\Controllers\Master;

use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public $forms = array(
        array('add'=>true,'edit'=>true,'field'=>'name','desc'=>'Name','type'=>'text','length'=>'190','mdf'=>'4','mdi'=>'6','required'=>'true'),
        array('add'=>true,'edit'=>true,'field'=>'username','desc'=>'User Name','type'=>'text','length'=>'190','mdf'=>'4','mdi'=>'6','required'=>'true'),
        array('add'=>true,'edit'=>true,'field'=>'email','desc'=>'Email','type'=>'email','length'=>'190','mdf'=>'4','mdi'=>'6','required'=>'true'),
        array('add'=>true,'edit'=>true,'field'=>'password','desc'=>'Password','type'=>'password','length'=>'190','mdf'=>'4','mdi'=>'6','required'=>'true'),
        array('add'=>true,'edit'=>true,'field'=>'password_confirmation','desc'=>'Password Confirmation','type'=>'password','length'=>'190','mdf'=>'4','mdi'=>'6','required'=>''),
    );
    public function __construct()
    {
        $this->middleware('auth');
    }
    //
    public function show()
    {
    	$list = DB::table('users')->paginate(10);
        $customers = DB::table('customers')->get();
        $menus = DB::table('menus')
        ->where('parent','=',0)
        ->select('menus.*', DB::raw('null as contents'))
        ->get();
        $n=0;
        foreach ($menus as $key => $value) {
            $menus[$n]->contents = DB::table('menus')->where('parent','=',$value->id)->get();
            $n++;
        }

    	$view = view('master.Users.index');
        $view->with('list',$list);
        $view->with('customers', $customers);
        $view->with('forms', $this->forms);
        $view->with('menus', $menus);

        return $view;
    }

    public function replacemenu($id, Request $request)
    {
        DB::table('menu_to_user')->where('user_id','=',$id)->delete();
        foreach ($request->input('checkbox') as $key => $value) {
            DB::table('menu_to_user')
            ->insert([
                'user_id'=>$id,
                'menu_id'=>$value,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ]);
        }

        return response()->json([
            'status'=>1,
            'message'=>'Menu berhasil diupdate'
        ]);
    }

    public function replaceInfo($id, Request $request)
    {
       $data = DB::table('user_info')->where('user_id','=',$id);
       if(!$data) {
            DB::table('user_info')->insert([
                'user_id'=>$id,
                'customer_id'=>$request->input('customer_id'),
                'project_id'=>$request->input('project_id'),
                'level'=>$request->input('level')
            ]);
        } else {
            DB::table('user_info')->where('user_id','=',$id)
            ->update([
                'customer_id'=>$request->input('customer_id'),
                'project_id'=>$request->input('project_id'),
                'level'=>$request->input('level')
            ]);
        }

        return Redirect::back()->with('Berhasil add user');
    }

    public function showformInfo($id) {
        $info = DB::table('user_info')
        ->where('user_id','=',$id)
        ->first();

        $customers = DB::table('customers')->get();
        return view('master.Users.infoform')->with('info',$info)->with('customers',$customers);
    }

    public function showmenuform($id)
    {
    	$menu = array();
        /*
        
        $headmenu=DB::table('menus')
                    ->select('menus.*')
                    ->join('menus_to_user','menus.id','=','menus_to_user.menu_id')
                    ->where([['menus_to_user.user_id','=',$id],['menus.parent','=',0],['desc','=','raw']])
                    ->orderBy('menus.order')
                    ->get();
        */
                    /*
        $mainmenu=DB::table('menus')
                    ->select('menus.*','menus_to_user.menu_id')
                    ->leftJoin('menus_to_user','menus.id','=','menus_to_user.menu_id')
                    ->where([['menus_to_user.user_id','=',$id],['menus.parent','=',0]])
                    ->orderBy('menus.order')
                    ->get();
                    */
        $userMenus = DB::table('menus_to_user')->where('user_id',$id);
        $mainmenu=DB::table('menus')
                    ->select('menus.*','user_menu.menu_id')
                    ->leftJoin(DB::raw('(select * from menu_to_user where user_id='.$id.') user_menu'), 'menus.id','=','user_menu.menu_id')
                    ->where('menus.parent','=',0)
                    ->orderBy('menus.order')
                    ->get();

/*
                    ->select('menus.*','menus_to_user.menu_id')
                    ->leftJoin('menus_to_user','menus.id','=','menus_to_user.menu_id')
                    ->where([['menus_to_user.user_id','=',$id],['menus.parent','=',0]])
                    ->orderBy('menus.order')
                    ->get();*/

        foreach ($mainmenu as $key => $value) {
            $menu[$key]['id']=$value->id;
            $menu[$key]['name']=$value->name;
            $menu[$key]['url']=$value->url;
            $menu[$key]['icon']=$value->icon;
            $menu[$key]['desc']=$value->desc;
            if(!is_null($value->menu_id))
            	$menu[$key]['check']='checked';
            else $menu[$key]['check']='';
            $menu[$key]["contents"]=array();
            $submenu=DB::table('menus')
                        ->select('menus.*','user_menu.menu_id')
                        ->leftJoin(DB::raw('(select * from menu_to_user where user_id='.$id.') user_menu'), 'menus.id','=','user_menu.menu_id')
                        ->where('menus.parent','=',$value->id)
                        ->orderBy('menus.order')
                        ->get();
                        /*
                        ->leftJoin('menus_to_user','menus.id','=','menus_to_user.menu_id')
                        ->where([['menus_to_user.user_id','=',$id],['menus.parent','=',$value->id]])
                        ->orderBy('menus.order')
                        ->get();
                        */
            foreach ($submenu as $key2 => $value2) {
                $menu[$key]['contents'][$key2]['id']=$value2->id;
                $menu[$key]['contents'][$key2]['name']=$value2->name;
                $menu[$key]['contents'][$key2]['url']=$value2->url;
                $menu[$key]['contents'][$key2]['icon']=$value2->icon;
                if(!is_null($value2->menu_id))
            		$menu[$key]['contents'][$key2]['check']='checked';
            	else $menu[$key]['contents'][$key2]['check']='';
            }
        }
    	return view('master.Users.menuform')->with('menu',$menu);
    }

    public function showform() {
        $customers = DB::table('customers')
        ->get();
        $view = view('master.Users.form')
        ->with('customers', $customers);

        return $view;
    }

    public function get($id) {
        $data = DB::table('users')
        ->leftJoin('user_info', 'users.id','=','user_info.user_id')
        ->where('users.id','=',$id)
        ->first();

        return response()->json($data);
    }

    public function save(Request $request, $id) {
        $password = "";
        DB::table('users')
        ->where('id', '=', $id)
        ->update([
            'username'=>$request->username,
            'name'=>$request->name,
            'email'=>$request->email,
            'updated_at'=>Carbon::now()
        ]);
        if(isset($request->password) && $request->password!='') {
            DB::table('users')
            ->where('id', '=', $id)
            ->update([
                'password'=>Hash::make($request->password),
            ]);
        }

        DB::table('user_info')
        ->where('user_id','=',$id)
        ->update([
            'user_id' => $id,
            'customer_id' => $request->customer_id,
            'project_id' => $request->project_id,
            'level' => $request->level,
            'updated_at'=> Carbon::now()
        ]);

        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['save']['success']
        ]);
    }

    public function add(Request $request) {
        $this->validator($request->all())->validate();

        $this->create($request->all());

        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['add']['success']
        ]);
    }

    public function delete($id) {
        DB::table('users')
        ->where('id','=',$id)->delete();
        DB::table('user_info')
        ->where('user_id','=',$id)->delete();
        DB::table('menu_to_user')
        ->where('user_id','=',$id)->delete();

        return response()->json([
            'status'=>1,
            'message'=>$this->message['default']['delete']['success']
        ]);
    }

    public function getmenu($id) {
        $data = DB::table('menu_to_user')
        ->where('user_id','=',$id)->get();

        return response()->json($data);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255',
            'username' => 'sometimes|required|max:255|unique:users',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);
    }

    function create(array $data)
    {
        $id = DB::table('users')
        ->insertGetId([
            'name'=>$data['name'],
            'email'    => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'active'   => 1
        ]);
        DB::table('user_info')
        ->insert([
            'user_id' => $id,
            'customer_id' => $data['customer_id'],
            'project_id' => $data['project_id'],
            'level' => $data['level'],
            'created_at'=> Carbon::now()
        ]);
    }
}
