<?php

namespace App\Http\Controllers\Report;

use Auth;
use DB;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DetailController extends Controller
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

    	$sql = DB::table('production_data_detail')
    	->select('production_data_detail.*')
    	->leftJoin('production_data','production_data_detail.production_id','=','production_data.id')
    	->leftJoin('projects','production_data.project_id','=','projects.id');

    	if($this->check($no_amplop)) {
    		$sql = $sql->where('production_data_detail.barcode_env','=',$no_amplop);
    	}
    	if($this->check($no_account)) {
    		$sql = $sql->where('production_data_detail.account_no','=',$no_account);
    	}
    	if($this->check($start_date)) {
    		$sql = $sql->where('production_data_detail.created_at','>=', $start_date);
    	}
    	if($this->check($end_date)) {
    		$sql = $sql->where('production_data_detail.created_at','<=', $end_date);
    	}
    	if($this->check($nama)) {
    		$sql = $sql->where('production_data_detail.penerima','like', '%'.$nama.'%');
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

    	$sql->orderBy('id','desc');
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
	        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'No Amplop');
	        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'No Account');
	        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Penerima');
	        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Alamat1');
	        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Alamat2');
	        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Alamat3');
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
	            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row+1), date('Y-m-d', strtotime($value->created_at)));
	            $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row+1), $value->barcode_env);
	            $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->account_no);
	            $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), $value->penerima);
	            $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row+1), $value->address1);
	            $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row+1), $value->address2);
	            $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row+1), $value->address3);
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
	        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('Sheet1');


			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);


			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Rincian.xlsx"');
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
	    	$view = view('report.detail.index')->with('list',$list);

	    	return $view
	        ->with('jeniss', $jeniss)
	        ->with('parts', $parts)
	        ->with('projects', $projects)
	    	->with('no_amplop', $no_amplop)
	    	->with('no_account', $no_account)
	    	->with('start_date',$start_date)
	    	->with('end_date', $end_date)
	    	->with('nama', $nama)
	    	->with('customer_id', $customer_id)
	    	->with('project_id', $project_id)
	    	->with('cycle',$cycle)
	    	->with('part', $part)
	    	->with('jenis', $jenis);
    	}
    }

    public function showdetail($id) {
    	$data = DB::table('production_data_detail')
    	->where('id','=',$id)
    	->first();

    	$prod = DB::table('production_data')
    	->where('id','=',$data->production_id)
    	->first();
    	$file = null;
    	if($prod->file_id>0) {
    		$file = DB::table('incoming_data')
	    	->where('id','=',$prod->id)->first();
    	}

    	return view('report.detail.detaildata')
    	->with('data',$data)
    	->with('prod',$prod)
    	->with('file',$file);

    }
}
