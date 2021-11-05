<?php

namespace App\Http\Controllers\Production;

use Auth;
use DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPExcel_Shared_Date;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DistribusiController extends Controller
{
    //
    public function index(Request $request) {
    	$sql = DB::table('manifest')
    	->leftJoin('production_data','production_data.id','=','manifest.production_id')
        ->leftjoin('production_data_detail','production_data_detail.no_manifest','=','manifest.no_manifest')
        ->leftJoin('projects','projects.id','=','production_data.project_id')
        ->leftJoin('customers','customers.id','=','projects.customer_id')
    	->select('production_data.job_ticket',DB::raw('projects.name as project_name, customers.name as customer_name'), 'manifest.no_manifest', 'manifest.cycle', 'manifest.part','manifest.ekspedisi','manifest.service','manifest.print', 'manifest.created_at', 'manifest.tgl_kirim')
    	->addselect(DB::raw('count(manifest.no_manifest) as jml_data'))       
        ->orderBy('manifest.production_id','desc')
        //->orderBy('tgl_kirim', 'desc')
        ->groupBy('manifest.no_manifest');
        $ticket = $request->ticket;
        $no_manifest = $request->no_manifest;
        $cycle = $request->cycle;
        $project_id = $request->project_id;

        $bf2 = date('Y-m-d',strtotime('-3 days',strtotime(now())));
		$now = date('Y-m-d',strtotime(now()));

		//if(!$this->check($ticket) && !$this->check($cycle))
			//$sql->where('manifest.created_at', '>=', $bf2.' 00:00:00')->where('manifest.created_at','<=',$now.' 23:59:59');

        if($this->check($ticket))
            $sql->where('production_data.job_ticket','like','%'.$ticket.'%');
        if($this->check($cycle))
            $sql->where('production_data.cycle','=',$cycle);
        if($this->check($no_manifest))
            $sql->where('manifest.no_manifest','=',$no_manifest);
        if($this->check2($project_id))
            $sql->where('production_data.project_id','=',$project_id);

        $list = $sql->paginate(10);

        $project = DB::table('production_data')
                    ->join('projects','projects.id','=','production_data.project_id')
                    ->orderBy('projects.name','ASC')
                    ->groupBy('production_data.project_id')->get();

        $view = view('production.distribusi.index');
        $view->with('list',$list); 
        $view->with('ticket',$ticket);
        $view->with('cycle',$cycle);
        $view->with('no_manifest',$no_manifest);
        $info = $this->getUserInfo();
        $view->with('level',$info->level);
        $view->with('project',$project);
        $view->with('project_id',$project_id);

        if(isset($_GET['info'])) $view->with('info',$_GET['info']);
        if(isset($_GET['error'])) $view->with('error', $_GET['error']);
        return $view;
    }

    public function showform($no_manifest) {
    	$data = DB::table('manifest')
    	->where('no_manifest','=',$no_manifest)->first();
    	return view('production.distribusi.form')->with('data',$data);
    }

    public function update(Request $request) {
        $no_manifest = $request->no_manifest;
    	$tgl_kirim=$request->input('tgl_kirim');
    	DB::table('manifest')
    	->where('no_manifest','=',$no_manifest)
    	->update([
            'tgl_kirim'=>$request->tgl_kirim,
            'nama_kurir'=>$request->nama_kurir,
            'created_by' => Auth::id()
        ]);

        return response()->json([
            'status'=>1,
            'message'=>'Success update tanggal kirim'
        ]);
    }

    public function detail($no_manifest) {
        $data = DB::table('manifest')
        ->where('manifest.no_manifest','=',$no_manifest)->first();

        $list = DB::table('production_data_detail')
        ->where('no_manifest','=',$no_manifest)->get();

        return response()->json([
            'data'=>$data,
            'list'=>$list
        ]);
    }

    public function download($no_manifest) {
    	$manifest = DB::table('manifest')
    	->where('no_manifest','=',$no_manifest)->first();

    	$list = DB::table('production_data_detail')
    	->where('no_manifest','=',$no_manifest)
    	->get();

    	$objPHPExcel = new Spreadsheet();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("PT. Tata Layak Prawira")
									 ->setLastModifiedBy("PT. Tata Layak Prawira")
									 ->setTitle("Softcopy")
									 ->setSubject("Office 2007 XLSX Softcopy Document")
									 ->setDescription("Office 2007 XLSX Softcopy Document");

		// Add some data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', "No");
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'No Amplop');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Penerima');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Alamat1');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Alamat2');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Alamat3');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Alamat4');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Alamat5');        
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Kota');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Kode Pos');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Telp');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Ekspedisi');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Serivice');
        $row=0;
        foreach ($list as $key => $value) {
            $row++;
            //echo $value->policy_no.'<p>';
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row+1), $row);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row+1), $value->barcode_env);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row+1), $value->penerima);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->address1);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), $value->address2);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row+1), $value->address3);
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row+1), $value->address4);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row+1), $value->address5);            
            $objPHPExcel->getActiveSheet()->SetCellValue('I'.($row+1), $value->city);
            $objPHPExcel->getActiveSheet()->SetCellValue('J'.($row+1), $value->pos);
            $objPHPExcel->getActiveSheet()->SetCellValue('K'.($row+1), $value->telp);
            $objPHPExcel->getActiveSheet()->SetCellValue('L'.($row+1), $value->ekspedisi);
            $objPHPExcel->getActiveSheet()->SetCellValue('M'.($row+1), $value->service);    
        }

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Sheet1');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Softcopy-'.$manifest->ekspedisi.'-'.$manifest->service.'-'.$no_manifest.'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
		$objWriter->save('php://output');

    }
    
    public function print($no_manifest) {
        $user = Auth::user();

        $template = DB::table('manifest')
        ->leftJoin('production_data','manifest.production_id','=','production_data.id')
        ->leftJoin('project_to_component','production_data.project_id','=','project_to_component.project_id')
        ->leftJoin('components','project_to_component.component_id','=','components.id')
        ->select('components.id','components.name','components.group')
        ->where('manifest.no_manifest','=',$no_manifest)
        ->where('components.group','=','jasa')
        ->where('components.name', 'like', '%printing%')
        ->orderBy('project_to_component.sort','ASC')->get();

    	$data = DB::table('manifest')
        ->leftJoin('users','users.id','=','manifest.created_by')
    	->leftJoin('production_data','manifest.production_id','=','production_data.id')
    	->leftJoin('projects','production_data.project_id','=','projects.id')
    	->leftJoin('customers','projects.customer_id','=','customers.id')
    	->select('users.name as user_name','manifest.print',DB::raw('production_data.id as prod_id'),DB::raw('production_data.jenis as prod_jenis'),DB::raw('production_data.job_ticket as job_tiket'),DB::raw('projects.name as project_name'),DB::raw('projects.email as project_email'),DB::raw('customers.name as customer_name'),'manifest.jenis','manifest.cycle','manifest.part','manifest.no_manifest', 'manifest.tgl_kirim','manifest.ekspedisi','manifest.service','manifest.nama_kurir')
    	->where('no_manifest','=',$no_manifest)->first();

        $total = DB::select("select sum(ok.total) as totall
                            from (select count(*) as total from production_data_detail
                                  where production_data_detail.no_manifest = $no_manifest
                                  group by production_data_detail.barcode_document) ok
        ");
        $obj = json_decode(json_encode($total));
        
        $rinci = DB::table('production_data_detail')
                 ->select('production_data_detail.city','production_data_detail.barcode_document as cabang',DB::raw('count(production_data_detail.barcode_document) as total'))
                 ->where('production_data_detail.barcode_document','!=',"")
                 ->where('production_data_detail.no_manifest','=',$no_manifest)
                 ->groupBy('production_data_detail.barcode_document')
                 ->get();

        //var_dump($rinci);         
        $printing = DB::table('production_data_detail');
        $printing=$printing->join('production_data_detail_list','production_data_detail.id','=','production_data_detail_list.production_data_detail_id');
        $printing=$printing->leftJoin('components','components.id','=','production_data_detail_list.component_id');
        $printing=$printing->select(DB::raw('SUM(production_data_detail_list.total) AS jml'));
                   
                foreach($template as $value){
                    if(count($template) == 1){
                        $printing=$printing->where('components.id','=',$value->id);
                    }else{
                        $printing=$printing->orWhere('components.id','=',$value->id);
                    }                    
                }
                                        
                    $printing->where('production_data_detail.no_manifest','=',$no_manifest);
                    $printing = $printing->groupBy('production_data_detail_list.component_id')->get();
        $t_print = 0;
        foreach($printing as $value){
            $t_print = $t_print+$value->jml;
        }
        
        $inserting = DB::table('production_data_detail')                                        
                    ->select(DB::raw('SUM(production_data_detail.bst_inserting) AS jml'))
                    ->where('production_data_detail.no_manifest','=',$no_manifest)->first();                    

        $tmp = 'Kurir : '.$data->nama_kurir.', Ekspedisi : '.$data->ekspedisi.', Service : '.$data->service;
        
        if($data->print == 0){
            DB::table('manifest')
            ->where('no_manifest','=',$no_manifest)
            ->increment('print');           
            $this->updateTask($data->prod_id, $this->distribusiId, $this->resultSuccesDefault, $tmp);
        
            /*$body_mail = $this->build_email($data,$total);
            $subject_mail = $this->getBodyMail(1)->subject;
            $this->insertMailNotif($data,$subject_mail,$body_mail);*/
        }

        return view('production.distribusi.printtt')
        ->with('data',$data)
        ->with('total',$obj[0]->totall)
        ->with('rinci',$rinci)
        ->with('name', $data->user_name)
        ->with('printing',$t_print)

        ->with('inserting',$inserting->jml)
        ->with('name_kurir',$data->nama_kurir);      	      
    }    

    public function cekprint($no_manifest){
        $hasil = DB::table('manifest')->where('manifest.no_manifest','=',$no_manifest)
        ->where('manifest.print','=',0)->first();

        
        $nilai = "NO";
        if(!is_null($hasil)){
            $nilai = "OK";
        }
        
        return response()->json([
            "nilai" => $nilai
        ]);
    }
}
