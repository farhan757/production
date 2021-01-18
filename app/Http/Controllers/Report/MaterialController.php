<?php

namespace App\Http\Controllers\Report;

use Auth;
use DB;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MaterialController extends Controller
{
    //
    public function index(Request $request) {
        $jenis = $this->getValues('jenis');        
//        $parts = $this->getValues('part');
        
        $projects = DB::table('projects')
	    	->join('customers', 'projects.customer_id','=', 'customers.id')
	    	->select('projects.id', DB::raw('projects.name as project_name, customers.name as customer_name'))
	    	->get();

	    $sql = DB::table('production_data_detail_list')
	    ->select('components.id','components.code','components.name','components.satuan','group', DB::raw('sum(production_data_detail_list.total) as total'))
	    ->leftJoin('components', 'components.id','=','production_data_detail_list.component_id')
	    ->leftJoin('production_data_detail','production_data_detail_list.production_data_detail_id','=','production_data_detail.id')
	    ->leftJoin('production_data', 'production_data.id','=', 'production_data_detail.production_id')
		->groupBy('components.id','components.code','components.name','components.satuan','group')
		->where('production_data.status_warehouse','=',1);

	    $nm_file = '';
	    if($this->check($request->code)) {
	    	$nm_file.='-'.$request->code;
	    	$sql = $sql->where('components.code','=', $request->code);
	    }
		if($this->check($request->job_ticket)) {
    		$sql = $sql->where('production_data.job_ticket','=', $request->job_ticket);
    	}
    	if($this->check($request->start_date)) {
    		$sql = $sql->where('production_data.created_at','>=', $request->start_date);
    	}
    	if($this->check($request->end_date)) {
    		$sql = $sql->where('production_data.created_at','<=', $request->end_date);
    	}
    	if($this->check2($request->customer_id)) {
    		$sql = $sql->where('projects.customer_id', '=', $request->customer_id);
    	}
    	if($this->check2($request->project_id)) {
    		$sql = $sql->where('production_data.project_id','=', $request->project_id);
    	}
    	if($this->check($request->cycle)) {
    		$sql = $sql->where('production_data.cycle','=',$request->cycle);
    	}
    	if($this->check2($request->part)) {
    		$sql = $sql->where('production_data.part','=',$request->part);
    	}
    	if($this->check2($request->jenis)) {
    		$sql = $sql->where('production_data.jenis','=',$request->jenis);
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
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Kode Material");
	        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Deskripsi');
	        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Jenis');
	        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Jumlah');
	        $row=0;
	        foreach ($list as $key => $value) {
	            $row++;
	            //echo $value->policy_no.'<p>';
	            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row+1), $row);            			
	            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row+1), $value->code);
	            $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row+1), $value->name);
	            $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->group);
	            $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), $value->total);
	        }

			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('Sheet1');


			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);


			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Report-Materal.xlsx"');
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
	    	$jeniss = $this->getValues('jenis');
		    $parts = $this->getValues('part');

	    	$view = view('report.material.index');
	    	$view->with('jeniss', $jeniss);
	    	$view->with('parts', $parts);
	    	$view->with('projects', $projects);
	    	if($request->filter) {
	    		$list = $sql->paginate(10);
	    		$view->with('list', $list);
	    		$view->with('show', true);
	    	}
	    	return $view;	    	
	    }
    }
}
