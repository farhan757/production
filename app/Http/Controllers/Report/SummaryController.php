<?php

namespace App\Http\Controllers\Report;

use Auth;
use DB;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SummaryController extends Controller
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
    	$job_ticket = $request->job_ticket;

    	$sql = DB::table('production_data')
->select('production_data.id',DB::raw('projects.name as project_name'),'production_data.file_name', 'production_data.job_ticket', DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'),'production_data.cycle','production_data.part','production_data.created_at', DB::raw('count(*) as jumlah'))
    	->leftJoin('projects','projects.id','=','production_data.project_id')
    	->leftJoin('task_status','task_status.id','=','production_data.current_status_id')
    	->leftJoin('task_result','task_result.id','=','production_data.current_status_result_id')
    	->join('production_data_detail','production_data.id','=','production_id')    	
    	->groupBy('production_data.id')
    	->groupBy('projects.name')
    	->groupBy('production_data.file_name') 
    	->groupBy('production_data.job_ticket')
    	->groupBy('task_status.name')
    	->groupBy('task_result.name')
    	->groupBy('production_data.cycle')
    	->groupBy('production_data.part')
    	->groupBy('production_data.created_at')
    	->orderBy('production_data.created_at','desc');
    	

    	if($this->check($job_ticket)) {
    		$sql = $sql->where('production_data.job_ticket','=', $job_ticket);
    	}
    	if($this->check($start_date)) {
    		$sql = $sql->where('production_data.created_at','>=', $start_date);
    	}
    	if($this->check($end_date)) {
    		$sql = $sql->where('production_data.created_at','<=', $end_date);
    	}
    	if($this->check2($customer_id)) {
    		$sql = $sql->where('projects.customer_id', '=', $customer_id);
    	}
    	if($this->check2($project_id)) {
    		$sql = $sql->where('production_data.project_id');
    	}
    	if($this->check($cycle)) {
    		$sql = $sql->where('production_data.cycle','=',$cycle);
    	}
    	if($this->check2($part)) {
    		$sql = $sql->where('production_data.part','=',$part);
    	}
    	if($this->check2($jenis)) {
    		$sql = $sql->where('production_data.jenis','=',$jenis);
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
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Tanggal");
	        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Job Ticket');
	        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Cycle');
	        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Part');
	        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Project');
	        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Jumlah');
	        $row=0;
	        foreach ($list as $key => $value) {
	            $row++;
	            //echo $value->policy_no.'<p>';
	            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row+1), $row);            			
	            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row+1), date('Y-m-d', strtotime($value->created_at)));
	            $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row+1), $value->job_ticket);
	            $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->cycle);
	            $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), $value->part);
	            $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row+1), $value->project_name);
	            $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row+1), $value->jumlah);
	        }

			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('Sheet1');


			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);


			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Summary.xlsx"');
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
		    
	    	$view = view('report.summary.index');
	        $view->with('list',$list);
	        $view->with('projects',$projects);
	        return $view
	        ->with('jeniss', $jeniss)
	        ->with('parts', $parts)	        
	    	->with('start_date',$start_date)
	    	->with('end_date', $end_date)
	    	->with('customer_id', $customer_id)
	    	->with('project_id', $project_id)
	    	->with('cycle',$cycle)
	    	->with('part', $part)
	    	->with('job_ticket', $job_ticket)
	    	->with('jenis', $jenis);
	    }
    }
}
