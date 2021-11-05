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
        $customer = DB::table('customers')->get();

        $this->forms = array(
        array('add'=>true,'edit'=>true,'field'=>'code','desc'=>'Code','type'=>'text','length'=>'50','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'name','desc'=>'Name','type'=>'text','length'=>'190','mdf'=>'4','mdi'=>'6','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'satuan','desc'=>'Satuan','type'=>'text','length'=>'150','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'oninput'=>'cekPrice()','field'=>'price_beli','desc'=>'Price Buy','type'=>'number','length'=>'11','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'saldo_awal','desc'=>'Saldo Awal','type'=>'number','length'=>'11','mdf'=>'4','mdi'=>'4','required'=>false, 'readonly'=>true),
        array('add'=>true,'edit'=>true,'field'=>'saldo_akhir','desc'=>'Saldo Akhir','type'=>'number','length'=>'11','mdf'=>'4','mdi'=>'4','required'=>false,'readonly'=>true),
        
        array('add'=>true,'edit'=>true,'oninput'=>'stokawal()','field'=>'stock_awal','desc'=>'Stock Awal','type'=>'number','length'=>'11','mdf'=>'4','mdi'=>'4','required'=>false),
        array('add'=>true,'edit'=>true,'oninput'=>'stock_akhir()','field'=>'stock','desc'=>'Stock Riil','type'=>'number','length'=>'11','mdf'=>'4','mdi'=>'4','required'=>true),
        array('add'=>true,'edit'=>true,'field'=>'group','desc'=>'Group','type'=>'selectg','length'=>'1','mdf'=>'4','mdi'=>'4','required'=>true, 'data'=>$groups),
        array('add'=>true,'edit'=>true,'field'=>'customer_id','desc'=>'Customer','type'=>'select','length'=>'1','mdf'=>'4','mdi'=>'4','required'=>true, 'data'=>$customer),
        
        );
    }

    public function index() {
    	$list = DB::table('components')
    	->get();
        $cekupdate_saldo = DB::table('table_counter')->where('code','counter_saldo')->first();
    	$view = view('master.component.index')->with('list',$list);
        $view->with('forms', $this->forms);
        $view->with('cek_updatesaldo',$cekupdate_saldo->counter);

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
        $price = $request->price_beli;
        $stock = $request->stock;
    	$group = $request->group;
        $customer_id = $request->customer_id;
        $saldo_awal = $request->saldo_awal;
        $saldo_akhir = $request->saldo_akhir;
        $stock_awal = $request->stock_awal;
    	DB::table('components')
    	->where('id','=',$id)
    	->update([
            'stock_awal' => $stock_awal,
            'saldo_akhir' => $saldo_akhir,
            'saldo_awal' => $saldo_awal,
            'customer_id'=>$customer_id,
    		'code'=>$code,
    		'name'=>$name,
    		'satuan'=>$satuan,
            'price_beli'=>$price,
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
        $price = $request->price_beli;
        $stock = $request->stock;
        $group = $request->group;
        $customer_id = $request->customer_id;
        $saldo_awal = $request->saldo_awal;
        $saldo_akhir = $request->saldo_akhir;
        $stock_awal = $request->stock_awal;
    	$result = DB::table('components')->insert([
            'stock_awal' => $stock_awal,
            'saldo_akhir' => $saldo_akhir,
            'saldo_awal' => $saldo_awal,
            'customer_id'=>$customer_id,
    		'code'=>$code,
    		'name'=>$name,
    		'satuan'=>$satuan,
            'price_beli'=>$price,
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
            'price_beli' => 'required',
            'stock' => 'required',           
            'group' => 'required|max:150',
            'customer_id' => 'required|max:150',
        ]);
    } 

    public function update_saldo_awal()
    {
        # code...
        $data = $this->getComponents(); $msg ="";
        DB::beginTransaction();
        foreach($data as $val){
            try{
                $saldo_akhir = $val->stock*$val->price_beli;
                DB::table('components')
                ->where('id',$val->id)->update([
                    'stock_awal' => $val->stock,
                    'saldo_akhir' => $saldo_akhir
                ]);

                $child = $this->getComponents($val->id);
                $saldo_awal = $child->stock_awal*$child->price_beli;
                DB::table('components')
                ->where('id',$val->id)->update([
                    'saldo_awal' => $saldo_awal
                ]);
                DB::commit();  
                $msg="sukses";              
            }catch(Exception $e){
                DB::rollBack();
                $msg="gagal"; 
            }
        }
        if($msg == "sukses"){
            DB::table('table_counter')->where('code','counter_saldo')->update([
                'counter' => 1
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => $msg
        ]);
    }
}
