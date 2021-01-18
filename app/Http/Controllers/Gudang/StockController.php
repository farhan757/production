<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class StockController extends Controller
{
    //
    public function index(Request $request) {
        $kode_mat = $request->input('components_id');
        $tgldari = $request->input('dari');
        $tglsampai = $request->input('sampai');
        $components = $this->getComponents();
		
        $view = view('gudang.stock.index');
        $view->with('components', $components);
        ///return $view;
        $listjob = DB::table('components')
                ->leftJoin('components_out','components_out.component_id','=','components.id')
                ->Join('production_data','components_out.job_ticket','=','production_data.job_ticket')
                ->select(
                    'components.code', 'components.name as nama_material', 'components_out.job_ticket as no_job', 'components_out.qty', 'components_out.tgl_out as tgl_trans',
                    'production_data.cycle', 'production_data.part'
                )
                ->where('components.id','=',$kode_mat)
                ->where('components_out.tgl_out','>=',$tgldari.' 00:00:00')
                ->where('components_out.tgl_out','<=',$tglsampai.' 23:59:59')
                ->get();

        $listtest = DB::table('components')
                ->leftJoin('components_out','components_out.component_id','=','components.id')
                ->Join('outgoing_components','components_out.job_ticket','=','outgoing_components.no_job')
                ->select(
                    'components.code', 'components.name as nama_material', 'components_out.job_ticket as no_job', 'components_out.qty', 'components_out.tgl_out as tgl_trans'
                )
                ->where('components_out.tgl_out','>=',$tgldari.' 00:00:00')
                ->where('components_out.tgl_out','<=',$tglsampai.' 23:59:59')                        
                ->where('components.id','=',$kode_mat)
                ->get(); 

        $listtmsk = DB::table('components')
                ->leftJoin('components_in','components_in.component_id','=','components.id')
                ->Join('incoming_components','components_in.incoming_comp_po','=','incoming_components.no_po')
                ->select(
                    'components.code', 'components.name as nama_material', 'components_in.incoming_comp_po as no_job', 'components_in.qty', 'components_in.tgl_in as tgl_trans'
                )
                ->where('components_in.tgl_in','>=',$tgldari.' 00:00:00')
                ->where('components_in.tgl_in','<=',$tglsampai.' 23:59:59')                        
                ->where('components.id','=',$kode_mat)
                ->get();    
        $stock = $this->getComponents($kode_mat);

		if($request->submit == 'submit'){                                             
            $view->with('listjob',$listjob);
            $view->with('listtest',$listtest);
            $view->with('listmsk',$listtmsk);
            $view->with('nojob',$kode_mat);     
            $view->with('tgldari',$tgldari);
            $view->with('tglsampai',$tglsampai); 
            $view->with('comp',$stock);                  
        }
        
		if($request->export == 'export'){                                             
  
    		$objPHPExcel = new Spreadsheet();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("PT. Tata Layak Prawira")
										 ->setLastModifiedBy("PT. Tata Layak Prawira")
										 ->setTitle("Stock Material")
										 ->setSubject("Office 2007 XLSX Stock Material")
                                         ->setDescription("Office 2007 XLSX Stock Material");    
			// Add some data
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', "PT TATA LAYAK PRAWIRA");
            $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Nama Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('A3', "Kode Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('A4', "Stok Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('A5', "Periode Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('B2', ":");
            $objPHPExcel->getActiveSheet()->SetCellValue('B3', ":");
            $objPHPExcel->getActiveSheet()->SetCellValue('B4', ":");
            $objPHPExcel->getActiveSheet()->SetCellValue('B5', ":");
            $objPHPExcel->getActiveSheet()->SetCellValue('C2', "$stock->name");
            $objPHPExcel->getActiveSheet()->SetCellValue('C3', "$stock->code");
            $objPHPExcel->getActiveSheet()->SetCellValue('C4', "$stock->stock");
            $objPHPExcel->getActiveSheet()->SetCellValue('C5', "$tgldari s/d $tglsampai");            
            $objPHPExcel->getActiveSheet()->SetCellValue('D7', "List Pemakaian Material");            			
			$objPHPExcel->getActiveSheet()->SetCellValue('D9', "Tgl Trans/Cycle/Part");
	        $objPHPExcel->getActiveSheet()->SetCellValue('E9', 'No Job');
	        $objPHPExcel->getActiveSheet()->SetCellValue('F9', 'Nama Material');
	        $objPHPExcel->getActiveSheet()->SetCellValue('G9', 'Code');
	        $objPHPExcel->getActiveSheet()->SetCellValue('H9', 'Qty Out');
            $objPHPExcel->getActiveSheet()->SetCellValue('I9', 'Qty In');
            $row=9;
            foreach($listjob as $index=>$value)
            {
                $row++;
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->tgl_trans.'/'.$value->cycle.'/'.$value->part); 
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), $value->no_job); 
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row+1), $value->nama_material); 
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row+1), $value->code); 
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row+1), $value->qty); 
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.($row+1), 0);                 
            }    
            foreach($listtest as $index=>$value)
            {
                $row++;
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->tgl_trans); 
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), $value->no_job); 
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row+1), $value->nama_material); 
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row+1), $value->code); 
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row+1), $value->qty); 
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.($row+1), 0);                 
            }   
            foreach($listtmsk as $index=>$value)
            {
                $row++;
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->tgl_trans); 
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), $value->no_job); 
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row+1), $value->nama_material); 
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row+1), $value->code); 
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row+1), 0); 
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.($row+1), $value->qty);                 
            }                                 
			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('Sheet1');


			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
            $nmfile = "Stock Material $stock->code .xlsx";

			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$nmfile.'"');
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
    	//return response()->json([
        //    'ok' =>$listtest,
         //   'sampai' => $tglsampai
        //]);
        return $view;
    	
        //echo $kode_mat;
    }
    
    public function liststok(Request $request){
        $list = DB::table('components')->where('group','material');
        if($this->check($request->code)){
            $list = $list->where('code',$request->code);
        }

        if($request->export == "export"){
            $list = $list->get();
            $objPHPExcel = new Spreadsheet();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("PT. Tata Layak Prawira")
										 ->setLastModifiedBy("PT. Tata Layak Prawira")
										 ->setTitle("Stock Material")
										 ->setSubject("Office 2007 XLSX Stock Material")
                                         ->setDescription("Office 2007 XLSX Stock Material"); 
			// Add some data
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', "PT TATA LAYAK PRAWIRA");
            $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Nama Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('B2', "Kode Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('C2', "Satuan Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('D2', "Harga Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('E2', "Stok Material");                                                    
            $row=2;
            foreach($list as $value){
                $row++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row+1), $value->name); 
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row+1), $value->code); 
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row+1), $value->satuan); 
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->price); 
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), $value->stock);                 
            }

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('Sheet1');


			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);

			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="List Material.xlsx"');
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

        $list = $list->orderBy('stock','ASC')->paginate(10);
        return view('gudang.liststock.index')->with('list',$list)->with('code',$request->code);
    }

}
