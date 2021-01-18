<?php

namespace App\Http\Controllers\Gudang;

use Auth;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListIncPOController extends Controller
{
    //
    public function index(Request $request){
        $infopo = DB::table('incoming_components')
                    ->leftJoin('users','incoming_components.input_id','=','users.id')
					->leftJoin('vendor','vendor.id','=','incoming_components.vendor_id')
                    ->select('incoming_components.print','incoming_components.tgl_po','incoming_components.no_po','incoming_components.complete','vendor.code','vendor.name','users.name as nama')
                    ->orderBy('no_po','Desc');
        if($this->check($request->nopo))
            $infopo->where('no_po','=',$request->nopo);
        if($this->check($request->tglpo))
            $infopo->where('tgl_po','=',$request->tglpo);        
        $tmp = $infopo->paginate(5);
        

        return view('gudang.listincoming.index')->with('listpo',$tmp)->with('nopo',$request->nopo)->with('tglpo',$request->tglpo);
        return response()->json([
            'code'=>1,
            'listpo'=>$tmp,
            'nopo'=>$request->nopo,
            'tglpo'=>$request->tglpo
        ]);         
    }   
    
    public function detail($nopo){
        $sql = DB::table('incoming_components_detail')				
				->leftJoin('components','components.id','=','incoming_components_detail.components_id')
				->select(DB::raw('incoming_components_detail.qty_order-incoming_components_detail.qty_arrive as qty_minus' ),'incoming_components_detail.id','components.code','components.name','incoming_components_detail.qty_order','incoming_components_detail.qty_arrive','components.satuan')
				->where('incoming_components_detail.incoming_components_po','=',$nopo)->get();                
        
                

        return $sql;
        return response()->json([
            'code'=>1,
            'list'=>$sql
        ]);        
    }  
    
    public function listdetail($nopo){
        $data = DB::table('incoming_components')
                ->leftJoin('users','incoming_components.input_id','=','users.id')
                ->leftJoin('vendor','vendor.id','=','incoming_components.vendor_id')
                ->select('incoming_components.tgl_po','incoming_components.no_po','incoming_components.complete','users.name as nama','vendor.code','vendor.name','vendor.pic')
                ->where('incoming_components.no_po','=',$nopo)
                ->first();

        $sql = DB::table('incoming_components_detail')				
				->leftJoin('components','components.id','=','incoming_components_detail.components_id')
				->select('components.code','components.name','incoming_components_detail.qty_order','incoming_components_detail.qty_arrive','components.satuan')
				->where('incoming_components_detail.incoming_components_po','=',$nopo)->get();                
        
        $bukti = DB::table('file_inc_components')->where('file_po','=',$nopo)->get();

        return response()->json([
            'data'=>$data,
            'list'=>$sql,
            'bukti'=>$bukti
        ]);
    }



    public function UpdateIncPO(Request $request){
        $nopo = $request->input('nopo');
        $code = $request->input('code');
        
        DB::beginTransaction();
        try{
            
            foreach($code as $key => $value){
                $qty_in = $request->input('qty_arrive')[$key];
                $qty_order = $request->input('qty_order')[$key];                

                $status = DB::table("incoming_components_detail")->where('id','=',$value)->first();
                if($status->complete == 0){
                    if($qty_in <= $qty_order){
                        DB::table('incoming_components_detail')
                            ->where('id','=',$value)->where('incoming_components_po','=',$nopo)
                            ->increment('qty_arrive',$qty_in);
                        echo "Berhasil masuk comp detail </br>";

                        $components = $this->getComponents($status->components_id);
                        DB::table("components_in")->insert([
                            'incoming_comp_po' => $nopo,
                            'component_id' => $status->components_id,
                            'component_price' => $status->components_price,
                            'qty' => $qty_in,
                            'group' => $components->group
                        ]);
                    }
                }
    
                DB::statement("
                    update incoming_components_detail
                    set complete = if(qty_order=qty_arrive,1,0)
                    where id = $value
                ");            
 
            }
            $statusnya = DB::table("incoming_components_detail")->where('incoming_components_po','=',$nopo)->where('complete',0)->count();
            if($statusnya == 0){
                DB::table('incoming_components')->where('no_po','=',$nopo)->update(['complete'=>1]);
                echo "Berhasil masuk comp </br> $statusnya";
            } 
        
            echo "Berhasil masuk comp </br> $statusnya";

            if($request->file('file')->isValid()){
                $file=$request->file('file');
                $fileName = $file->getClientOriginalName();
                $pathFile = $this->uploadPO.DIRECTORY_SEPARATOR.$nopo;
                $file->move($this->uploadPO,$nopo);

				DB::table('file_inc_components')
				->insert([
					'file_po'=>$nopo,
					'file_name'=>$fileName,
					'path_file'=>$pathFile,
					'created_at'=>Carbon::now()
				]);                
            }

            DB::commit();         
        }catch(Exception $e){
            DB::rollBack();
        }
        
        return  redirect()->back();
        return response()->json([
            'code'=>1,
            'message'=>'berhasil'
        ]);        
    }

    public function downloadbukti($id) {
    
        $bukti = DB::table('file_inc_components')->where('file_id','=',$id)->first();

    	return response()->download($bukti->path_file, $bukti->file_name);
    }
}
