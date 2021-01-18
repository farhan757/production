<?php

namespace App\Http\Controllers\Adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;

class GenController extends Controller
{
    //
    public function index(){
        $customers = $this->getCustomers();
        $view = view('adm.geninv.index');
        $view->with('customers',$customers);
        return $view;
    }

    function generateNOINV() {
    	$counter = str_pad($this->getCounter($this->counter_inv) ,5,'0',STR_PAD_LEFT);
    	$thn = date("Y"); $bln = date("m");
    	$project_id = str_pad('PO',3,'0',STR_PAD_LEFT);

    	return $counter.'/INV-TLP/'.$this->angkaRomawi($bln).'/'.$thn;
    } 
    
    public function saveinvoice(Request $request){
        $project_id = $request->project_id;
        $tgldari = $request->tgldari;
        $tglsampai = $request->tglsampai;

        $pkp = $request->pkp;
        $bkn_pkp = $request->b_pkp;
        $tunai = $request->tunai;
        $kredit = $request->kredit;

        $t_ppn = $request->t_ppn;
        $t_materai = $request->t_materai;
        
        $arr = "";
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
        
        $nama_product = $this->getProjectbyId($project_id);
        $customer = $this->getCustomersById($nama_product->customer_id);
        $company = $this->company();

        $jasa = DB::table('production_data')
                ->join('components_out','production_data.job_ticket','=','components_out.job_ticket')  
                ->leftJoin('components','components_out.component_id','components.id')              
                ->where('production_data.project_id','=',$project_id)->where('components_out.group','=','jasa')
                ->select('components.satuan','components.name','production_data.job_ticket','components_out.component_price',DB::raw('SUM(components_out.qty) AS kwantum'),DB::raw('(components_out.component_price*SUM(components_out.qty)) AS jumlah'))
                ->groupBy('components_out.component_id')->get();
        $material = DB::table('production_data')
                ->join('components_out','production_data.job_ticket','=','components_out.job_ticket')  
                ->leftJoin('components','components_out.component_id','components.id')              
                ->where('production_data.project_id','=',$project_id)->where('components_out.group','=','material')
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
        
        $nama_product = $this->getProjectbyId($project_id);
        $customer = $this->getCustomersById($nama_product->customer_id);
        $company = $this->company();

        $components_project = DB::table('projects')
                              ->join('project_to_component','projects.id','=','project_to_component.project_id')
                              ->leftJoin('components','project_to_component.component_id','=','components.id')
                              ->select('components.name')
                              ->where('projects.id','=',$project_id)
                              ->orderBy('project_to_component.component_id','ASC')->get();

        $production_data = DB::table('production_data')
                           ->join('production_data_detail','production_data.id','=','production_data_detail.production_id')
                           ->leftJoin('projects','production_data.project_id','=','projects.id')
                           ->select('projects.name','production_data.job_ticket','production_data.cycle','production_data.part','production_data.jenis',DB::raw('COUNT(*) AS jml'))
                           ->where('production_data.project_id','=',$project_id)
                           ->groupBy('production_data.id')
                           ->groupBy('production_data.file_name')
                           ->groupBy('production_data.job_ticket')
                           ->groupBy('production_data.cycle')
                           ->groupBy('production_data.part')
                           ->orderBy('production_data.created_at','ASC')->paginate(200000);

        $total_perinci = DB::table('components_out')
                            ->join('components','components_out.component_id','=','components.id')
                            ->select('components_out.component_id','components.name','components.satuan',DB::raw('SUM(components_out.qty) AS jml'))
                            ->groupBy('components_out.component_id')
                            ->orderBy('components_out.component_id','ASC')->get();
        
        $view = view('partial.perincian');
        $view->with('components_project',$components_project);
        $view->with('production_data',$production_data);
        $view->with('total_perinci',$total_perinci);
        $view->with('company',$company);
        
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
