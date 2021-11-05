<?php

namespace App\Http\Controllers\Adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ListInvController extends Controller
{
    //
    public function index(Request $request){
        $listinv = DB::table('invoice');
        $listok = $listinv->join('projects','projects.id','=','invoice.projects_id');
        $listok = $listinv->leftJoin('task_status','task_status.id','=','invoice.current_status');
        $listok = $listinv->leftJoin('customers','customers.id','=','projects.customer_id');
        $listok = $listinv->select('customers.name as nm_cust','projects.name as nm_pro','task_status.name as nm_rs','invoice.id','invoice.no_inv','invoice.generate_date','invoice.jatuhtempo_date','invoice.period');
        
        if(isset($request->no_inv)){
            $listok = $listinv->where('invoice.no_inv','=',$request->no_inv);            
        }

        if(isset($request->tglgen)){
            $listok = $listinv->where('invoice.generate_date','=',$request->tglgen);            
        }        

        $listok = $listinv->paginate(10);

        $results = DB::table('task_status')
                    ->where('task_status.id','>=',15)->where('task_status.id','!=',16)->where('task_status.id','!=',17)->get();

        $view = view('adm.listinv.index');
        $view->with('listinv',$listok);
        $view->with('results',$results);     
        $view->with('dataid',$request->id);             
        $view->with('no_inv',$request->no_inv);
        $view->with('tglgen',$request->tglgen);
        $info = $this->getUserInfo();
        $view->with('level',$info->level);
        return  $view;
    }

    public function delete(Request $request)
    {
        # code...
        DB::table("invoice")->where("invoice.id","=",$request->id)->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Delete success'
        ]);
    }

    public function update(Request $request){
        $id = $request->id;
        $result_id = $request->result_id;
        $note = $request->note;
        $tglbayar = $request->tglbayar;
        $msg = "";

        DB::beginTransaction();
        try{
            if($result_id == 18){
                DB::table('invoice')
                ->where('id','=',$id)
                ->update([
                    "current_status" => $result_id,
                    "updated_id" => Auth::id(),
                    "updated_at" => Carbon::now(),
                    "pay_date" => $tglbayar
                    ]);
    
            }else{
                DB::table('invoice')
                ->where('id','=',$id)
                ->update([
                    "current_status" => $result_id,
                    "updated_id" => Auth::id(),
                    "updated_at" => Carbon::now()
                ]);
            }      
         
            //ditambahin farhan
            if ($request->hasFile('file')) {
                $data = DB::table('invoice')
                ->where('id','=',$id)
                ->first();

                $file=$request->file('file');
                $fileName = $file->getClientOriginalName();
                $pathFile = $this->uploadInv.DIRECTORY_SEPARATOR.$id;
                $file->move($this->uploadInv,$id);            

                DB::table('file_invoice')
                ->insert([
                    'invoice_id'=>$id,
                    'file_name'=>$fileName,
                    'path_file'=>$pathFile,
                    'note'=>$note,
                    'created_at'=>Carbon::now()
                ]);                                
            } //sampai sini
            
         
            DB::commit();

            $msg = response()->json([
                "status" => 1,
                "message" => "Berhasil"
            ]);
        }catch(Exception $e){
            DB::rollBack();
            $msg = response()->json([
                "status" => 2,
                "message" => $e
            ]);
        }
        
        return $msg;
    }

    public function detail($id){
        $data = DB::table('invoice')
        ->join('projects','projects.id','=','invoice.projects_id')        
        ->leftJoin('customers','customers.id','=','projects.customer_id')
        ->select('projects.code as kd_pro','customers.code as kd_cust','customers.name as nm_cust','projects.name as nm_pro','invoice.*')
        ->first();
        
        $file = DB::table('file_invoice')
        ->where('invoice_id','=',$id)->orderBy('file_id','DESC')->get();
        
        $dt_json = "";
        $dt_json = response()->json([
            "data" => $data,
            "bukti" => $file
        ]);

        return $dt_json;
    }

    public function cetak($id){
        $data = DB::table('invoice')
        ->where('id','=',$id)
        ->first();
        
        $msgBox = response()->json([
            'status' => 1,            
            'data' => $data
        ]);   

        return $msgBox;
    }

    public function download($id){
        $bukti = DB::table('file_invoice')->where('file_id','=',$id)->first();

    	return response()->download($bukti->path_file, $bukti->file_name);        
    }
}
