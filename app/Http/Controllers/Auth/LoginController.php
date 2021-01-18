<?php

namespace App\Http\Controllers\Auth;

use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
    * Customize by arfi add session for menu
    **/
    public function saveSession(Request $request)
    {
        $id=Auth::id();

        //add this if contain user_info and level
        $user_info = DB::table('user_info')
        ->where('user_id','=',$id)
        ->first();
        if(!$user_info) return;
        $request->session()->put(base64_encode($this->keyUserInfo), base64_encode(json_encode($user_info)));


        $name=Auth::user()->name;

        $request->session()->put('user_name',$name);

        /*$data_user=DB::table('profile')
                ->where('user_id','=',$id)
                ->first();
        $request->session()->put('data_user',$data_user);
        */
        $menus = array();
        $mainmenu=DB::table('menus')
                    ->select('menus.*')
                    ->join('menu_to_user','menus.id','=','menu_to_user.menu_id')
                    ->where([['menu_to_user.user_id','=',$id],['menus.parent','=',0]])
                    ->where('menus.active','=',1)
                    ->orderBy('menus.order')
                    ->get();
        foreach ($mainmenu as $key => $value) {
            $menu = array();
            $menu['name']=$value->name;
            $menu['url']=$value->url;
            $menu['icon']=$value->icon;
            $menu['desc']=$value->desc;
            $menu["contents"]=array();
            $submenu=DB::table('menus')
                        ->select('menus.*')
                        ->join('menu_to_user','menus.id','=','menu_to_user.menu_id')
                        ->where([['menu_to_user.user_id','=',$id],['menus.parent','=',$value->id]])
                        ->where('menus.active','=',1)
                        ->orderBy('menus.order')
                        ->get();
            foreach ($submenu as $key2 => $value2) {
                $menu['contents'][$key2]['id']=$value2->id;
                $menu['contents'][$key2]['name']=$value2->name;
                $menu['contents'][$key2]['url']=$value2->url;
                $menu['contents'][$key2]['icon']=$value2->icon;
                $menu['contents'][$key2]['desc']=$value->desc;
            }
            array_push($menus, $menu);
        }

        $request->session()->put('menu',$menus);

    }
}
