<?php

namespace App\Http\Controllers\Report;

use Auth;
use DB;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DistribusiController extends Controller
{
    //
	public function index(Request $request) {
		$no_amplop=$request->input('no_amplop');
    	$no_account=$request->input('no_account');
    	$start_date=$request->start_date;
    	$end_date=$request->end_date;
    	$nama = $request->nama;
    	$customer_id = $request->customer_id;
    	$project_id = $request->project_id;
    	$cycle = $request->cycle;
    	$part = $request->part;
    	$jenis = $request->jenis;
    	$ekspedisi = $request->ekspedisi;
    	$no_manifest = $request->no_manifest;

       	$sql = DB::table('manifest')
    	->leftJoin('production_data','production_data.id','=','manifest.production_id')
    	->leftJoin('projects','projects.id','=','production_data.project_id')
        ->leftJoin('customers','customers.id','=','projects.customer_id')
    	->select(DB::raw('projects.name as project_name, customers.name as customer_name'), 'manifest.no_manifest', 'manifest.cycle', 'manifest.part','manifest.ekspedisi','manifest.service', 'manifest.created_at', 'manifest.tgl_kirim', DB::raw('count(*) as jumlah'))
		->join('production_data_detail','manifest.no_manifest','=','production_data_detail.no_manifest')
		->groupBy('projects.name')
		->groupBy('customers.name')
		->groupBy('manifest.no_manifest')
		->groupBy('manifest.cycle')
		->groupBy('manifest.part')
		->groupBy('manifest.ekspedisi')
		->groupBy('manifest.service')
		->groupBy('manifest.created_at')
		->groupBy('manifest.tgl_kirim')
    	->orderBy('print','desc');

    	if($this->check($no_manifest)) {
    		$sql = $sql->where('manifest.no_manifest','=',$no_manifest);
    	}
    	if($this->check($start_date)) {
    		$sql = $sql->where('manifest.tgl_kirim','>=', $start_date);
    	}
    	if($this->check($end_date)) {
    		$sql = $sql->where('manifest.tgl_kirim','<=', $end_date);
    	}
    	if($this->check2($customer_id)) {
    		$sql = $sql->where('projects.customer_id', '=', $customer_id);
    	}
    	if($this->check2($project_id)) {
    		$sql = $sql->where('production_data.project_id');
    	}
    	if($this->check($cycle)) {
    		$sql = $sql->where('manifest.cycle','=',$cycle);
    	}
    	if($this->check2($part)) {
    		$sql = $sql->where('manifest.part','=',$part);
    	}
    	if($this->check2($jenis)) {
    		$sql = $sql->where('manifest.jenis','=',$jenis);
    	}
    	if($this->check2($ekspedisi)) {
    		$sql = $sql->whereRaw('upper(manifest.ekspedisi) = '."'".$ekspedisi."'");
    	}

    	if(isset($request->download)) {
    		$list = $sql->get();
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
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Tanggal Kirim");
	        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'No Manifeset');
	        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Cycle');
	        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Part');
	        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Project');
	        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Ekspedisi');
	        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Service');
	        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Jumlah');
	        $row=0;
	        foreach ($list as $key => $value) {
	            $row++;
	            //echo $value->policy_no.'<p>';
	            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row+1), $row);            			
	            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row+1), date('Y-m-d', strtotime($value->created_at)));
	            $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row+1), $value->no_manifest);
	            $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->cycle);
	            $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), $value->part);
	            $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row+1), $value->project_name);
	            $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row+1), $value->ekspedisi);
	            $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row+1), $value->service);
	            $objPHPExcel->getActiveSheet()->SetCellValue('I'.($row+1), $value->jumlah);
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

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('Sheet1');


			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);


			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Summary Distribusi.xlsx"');
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
    	} else {
	    	$list = $sql->paginate(10);
			$projects = DB::table('projects')
		    	->join('customers', 'projects.customer_id','=', 'customers.id')
		    	->select('projects.id', DB::raw('projects.name as project_name, customers.name as customer_name'))
		    	->get();

		    $jeniss = $this->getValues('jenis');
		    $parts = $this->getValues('part');

	        $view = view('report.distribusi.index');
	        $view->with('list',$list); 
	        if(isset($_GET['info'])) $view->with('info',$_GET['info']);
	        if(isset($_GET['error'])) $view->with('error', $_GET['error']);
	        return $view
	        ->with('jeniss', $jeniss)
	        ->with('parts', $parts)
	        ->with('projects',$projects)
	        ->with('start_date',$start_date)
	    	->with('end_date', $end_date)
	    	->with('customer_id', $customer_id)
	    	->with('project_id', $project_id)
	    	->with('cycle',$cycle)
	    	->with('part', $part)
	    	->with('jenis', $jenis)
	    	->with('ekspedisi', $ekspedisi);
	    }
    }
}
