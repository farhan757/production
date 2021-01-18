<?php

namespace App\Http\Controllers\Master;

use DB;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomersController extends Controller
{
    //
    private $forms = array(
        array('add'=>true,'edit'=>true,'field'=>'code','desc'=>'Customer Code','type'=>'text','length'=>'10','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'name','desc'=>'Nama','type'=>'text','length'=>'100','mdf'=>'4','mdi'=>'6','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'pic','desc'=>'PIC','type'=>'text','length'=>'100','mdf'=>'4','mdi'=>'6','required'=>false),
        array('add'=>true,'edit'=>true,'field'=>'telp','desc'=>'Telp','type'=>'text','length'=>'50','mdf'=>'4','mdi'=>'4','required'=>false),
        array('add'=>true,'edit'=>true,'field'=>'address1','desc'=>'Address1','type'=>'text','length'=>'150','mdf'=>'4','mdi'=>'8','required'=>false),
        array('add'=>true,'edit'=>true,'field'=>'address2','desc'=>'Address2','type'=>'text','length'=>'150','mdf'=>'4','mdi'=>'8','required'=>false),
        array('add'=>true,'edit'=>true,'field'=>'address3','desc'=>'Address3','type'=>'text','length'=>'150','mdf'=>'4','mdi'=>'8','required'=>false),
        array('add'=>true,'edit'=>true,'field'=>'city','desc'=>'City','type'=>'text','length'=>'50','mdf'=>'4','mdi'=>'6','required'=>false),
        array('add'=>true,'edit'=>true,'field'=>'zipcode','desc'=>'Zipcode','type'=>'text','length'=>'15','mdf'=>'4','mdi'=>'2','required'=>false),

    );
    public function index() {
    	$list = DB::table('customers')->paginate(10);
        $view = view('master.customer.index')->with('list',$list);
        $view->with('forms',$this->forms);

        return $view;
    }

    public function get($id) {
    	$data = DB::table('customers')
    	->where('id','=',$id)->first();

    	return response()->json($data);
    }

    public function save(Request $request, $id) {
        $code = $request->code;
        $name = $request->name;
        $pic = $request->pic;
        $telp = $request->telp;
        $address1 = $request->address1;
        $address2 = $request->address2;
        $address3 = $request->address3;
        $city = $request->city;
        $zipcode = $request->post_code;
        $desc = $request->desc;
    	if(isset($request->active)) $active=1; else $active=0;

    	DB::table('customers')
    	->where('id','=',$id)
    	->update([
    		'code'=>$code,
    		'name'=>$name,
    		'pic'=>$pic,
    		'telp'=>$telp,
    		'address1'=>$address1,
    		'address2'=>$address2,
    		'address3'=>$address3,
    		'city'=>$city,
    		'zipcode'=>$zipcode,
    		'desc'=>$desc,
    		'active'=>$active,
			'updated_at'=>Carbon::now(),
    	]);
    	return response()->json([
            'status'=>1,
            'message'=>'Data berhasil dirubah'
        ]);
    }

    public function add(Request $request) {
        $this->validator($request->all())->validate();

    	$code = $request->code;
    	$name = $request->name;
    	$pic = $request->pic;
    	$telp = $request->telp;
    	$address1 = $request->address1;
    	$address2 = $request->address2;
    	$address3 = $request->address3;
    	$city = $request->city;
    	$zipcode = $request->post_code;
    	$desc = $request->desc;
    	if(isset($request->active)) $active=1; else $active=0;

    	$result = DB::table('customers')->insert([
    		'code'=>$code,
    		'name'=>$name,
    		'pic'=>$pic,
    		'telp'=>$telp,
    		'address1'=>$address1,
    		'address2'=>$address2,
    		'address3'=>$address3,
    		'city'=>$city,
    		'zipcode'=>$zipcode,
    		'desc'=>$desc,
    		'active'=>$active,
			'created_at'=>Carbon::now(),
    	]);
    	return response()->json([
            'status'=>1,
            'message'=>'Data berhasil ditambahkan'
        ]);
    }

    public function detail($id) {
    	$data = DB::table('customers')
    	->where('id','=',$id)
    	->first();

    	return view('master.customer.detail')->with('data',$data);
    }

    public function delete($id) {
    	$data = DB::table('customers')
    	->where('id','=',$id)
    	->delete();

        return response()->json([
            'status'=>1,
            'message'=>'Data berhasil dihapus'
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'code'     => 'required|max:10|unique:customers',
            'name' => 'required|max:100',
        ]);
    }
}
