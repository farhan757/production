<?php

namespace App\Http\Controllers\Adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;

class GenController extends Controller
{
    ////$this->getCustomers();
    public function index(){
        $customers = DB::table('production_data')
                        ->join('customers','production_data.customer_id','=','customers.id')
                        ->groupBy('customer_id')->orderBy('customers.name')
                        ->select('customers.id','customers.name')->get();
        $view = view('adm.geninv.index');
        $view->with('customers',$customers);
        return $view;
    }

    public function ProjectByid($id){
        $project = DB::table('production_data')
                    ->join('projects','production_data.project_id','=','projects.id')
                    ->where('production_data.customer_id',$id)
                    ->groupBy('project_id')->orderBy('projects.name')                    
                    ->select('projects.id','projects.name')->get();
        return response()->json($project);
    }

    function generateNOINV() {
    	$counter = str_pad($this->getCounter($this->counter_inv) ,4,'0',STR_PAD_LEFT);
    	$thn = date("Y"); $bln = date("m");
    	//$project_id = str_pad('PO',3,'0',STR_PAD_LEFT);

    	return "I$thn$bln$counter";
    } 
    
    public function saveinvoice(Request $request){
        
        $project_id = $request->project_id;
        $tgldari = $request->tgldari;
        $tglsampai = $request->tglsampai;

        $pkp = 0;
        if (isset($request->pkp)){
            $pkp = 1;
        }
        $bkn_pkp = 0;
        if( isset($request->b_pkp)){
            $bkn_pkp = 1;
        }
        $tunai = 0;
        if(isset($request->tunai)){
            $tunai = 1;
        }
        $kredit = 0;
        if(isset($request->kredit)){
            $kredit = 1;
        }

        $t_ppn = $request->t_ppn;
        $t_materai = $request->t_materai;

        $period = $request->period;

        $nama_product = $this->getProjectbyId($project_id);
        $customer = $this->getCustomersById($nama_product->customer_id);
        $company = $this->company();

        $msgBox = "";
        DB::beginTransaction();
        try{
            $tgl_skrg = date('Y-m-d');
            $tgl_jt = date('Y-m-d',strtotime('+14 days', strtotime($tgl_skrg)));
            $noinv = $this->generateNOINV();
            $dataAr = array(
                'no_inv' => $noinv,
                'projects_id' => $project_id,
                'current_status' => 17,
                'generate_date' => Carbon::now(),
                'jatuhtempo_date' => $tgl_jt,
                'materai' => $t_materai,
                'ppn' => $t_ppn,
                'pkp' => $pkp,
                'bkn_pkp' => $bkn_pkp,
                'tunai' => $tunai,
                'kredit' => $kredit,
                'created_id' => Auth::id(),
                'from_date' => $tgldari,
                'until_date' => $tglsampai,
                'period' => $period
            );
            DB::table('invoice')->insert([$dataAr]);

            DB::commit();
            $msgBox = response()->json([
                'status' => 1,
                'message' => 'Berhasil, '.$noinv,
                'data' => $dataAr
            ]);            
        }catch(Exception $e){
            DB::rollBack();            
            $msgBox = response()->json([
                'status' => 2,
                'message' => 'gagal error, '.$e
            ]);
        }

        return $msgBox;
    }

    function preview(Request $request){
        $project_id = $request->project_id;
        $tgldari = $request->tgldari;
        $tglsampai = $request->tglsampai;

        $pkp = $request->pkp;
        $bkn_pkp = $request->b_pkp;
        $tunai = $request->tunai;
        $kredit = $request->kredit;

        $t_ppn = $request->t_ppn;
        $t_materai = $request->t_materai;
        
        $noinv = "";
        if(isset($request->no_inv)){
            $noinv = $request->no_inv;
        }

        $nama_product = $this->getProjectbyId($project_id);
        $customer = $this->getCustomersById($nama_product->customer_id);
        $company = $this->company();

        $jasa = ""; $material="";

        $jasa = DB::table('production_data')
                ->join('components_out','production_data.job_ticket','=','components_out.job_ticket')  
                ->leftJoin('components','components_out.component_id','=','components.id')              
                ->where('production_data.created_at','>=',$tgldari.' 00:00:00')->where('production_data.created_at','<=',$tglsampai.' 23:59:59')
                ->where('production_data.project_id','=',$project_id)->where('components_out.group','=','jasa')
                ->where('production_data.status_warehouse','=',1)
                ->select('components.satuan','components.name','production_data.job_ticket','components_out.component_price',DB::raw('SUM(components_out.qty) AS kwantum'),DB::raw('(components_out.component_price*SUM(components_out.qty)) AS jumlah'))
                ->groupBy('components_out.component_id')->get();

        $material = DB::table('production_data')
                ->join('components_out','production_data.job_ticket','=','components_out.job_ticket')  
                ->leftJoin('components','components_out.component_id','=','components.id')              
                ->where('production_data.created_at','>=',$tgldari.' 00:00:00')->where('production_data.created_at','<=',$tglsampai.' 23:59:59')
                ->where('production_data.project_id','=',$project_id)->where('components_out.group','=','material')
                ->where('production_data.status_warehouse','=',1)
                ->select('components.satuan','components.name','production_data.job_ticket','components_out.component_price',DB::raw('SUM(components_out.qty) AS kwantum'),DB::raw('(components_out.component_price*SUM(components_out.qty)) AS jumlah'))
                ->groupBy('components_out.component_id')->get();                

        $view = view('partial.invoice');
        $view->with('jasa',$jasa);
        $view->with('material',$material);
        $view->with('nama_product',$nama_product->name);
        $view->with('customer',$customer);

        $view->with('pkp',$pkp);
        $view->with('bkn_pkp',$bkn_pkp);
        $view->with('tunai',$tunai);
        $view->with('kredit',$kredit);

        $view->with('t_ppn',$t_ppn);
        $view->with('t_materai',$t_materai);
        $view->with('company',$company);
        $view->with('noinv',$noinv);

        return $view;
    }

    public function perincian(Request $request){
        $project_id = $request->project_id;
        $tgldari = $request->tgldari;
        $tglsampai = $request->tglsampai;

        $pkp = $request->pkp;
        $bkn_pkp = $request->b_pkp;
        $tunai = $request->tunai;
        $kredit = $request->kredit;

        $t_ppn = $request->t_ppn;
        $t_materai = $request->t_materai;
        
        $period = $request->period;
        $noinv = "";
        if(isset($request->no_inv)){
            $noinv = $request->no_inv;
        }

        $nama_product = $this->getProjectbyId($project_id);
        $customer = $this->getCustomersById($nama_product->customer_id);
        $company = $this->company();

        $components_project = ""; $production_data=""; $total_perinci= "";

        $components_project = DB::table('projects')
                              ->join('project_to_component','projects.id','=','project_to_component.project_id')
                              ->leftJoin('components','project_to_component.component_id','=','components.id')
                              ->select('components.name')
                              ->where('projects.id','=',$project_id)
                              ->orderBy('project_to_component.component_id','ASC')->get();

        $production_data = DB::table('production_data')
                           ->join('production_data_detail','production_data.id','=','production_data_detail.production_id')
                           ->leftJoin('projects','production_data.project_id','=','projects.id')
                           ->select('projects.code','production_data.job_ticket','production_data.cycle','production_data.part','production_data.jenis',DB::raw('COUNT(*) AS jml'))
                           ->where('production_data.created_at','>=',$tgldari.' 00:00:00')->where('production_data.created_at','<=',$tglsampai.' 23:59:59')
                           ->where('production_data.project_id','=',$project_id)
                           ->where('production_data.status_warehouse','=',1)
                           ->groupBy('production_data.id')
                           ->groupBy('production_data.file_name')
                           ->groupBy('production_data.job_ticket')
                           ->groupBy('production_data.cycle')
                           ->groupBy('production_data.part')
                           ->orderBy('production_data.created_at','ASC')->get();

        $total_perinci = DB::table('components_out')
                            ->join('components','components_out.component_id','=','components.id')
                            ->join('production_data','production_data.job_ticket','components_out.job_ticket')                            
                            ->select('components_out.component_id','components.name','components.satuan',DB::raw('SUM(components_out.qty) AS jml'))
                            ->where('components_out.tgl_job','>=',$tgldari.' 00:00:00')->where('components_out.tgl_job','<=',$tglsampai.' 23:59:59')
                            ->where('production_data.project_id','=',$project_id)
                            ->where('production_data.status_warehouse','=',1)
                            ->groupBy('components_out.component_id')
                            ->orderBy('components_out.component_id','ASC')->get();
        
        $view = view('partial.perincian');
        $view->with('components_project',$components_project);
        $view->with('production_data',$production_data);
        $view->with('total_perinci',$total_perinci);
        $view->with('company',$company);
        $view->with('period',$period);
        $view->with('info_product',$nama_product);
        $view->with('noinv',$noinv);

        return $view;
    }
    
    public function getComponentsByticket($job_ticket){
        $rinci_components = DB::table('components_out')
                            ->join('components','components_out.component_id','=','components.id')
                            ->where('components_out.job_ticket','=',$job_ticket)
                            ->orderBy('components_out.component_id','ASC')->get();
        return $rinci_components;
    }
}
