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
        $listjob = DB::table('components');
        $listjob->leftJoin('components_out','components_out.component_id','=','components.id');
        $listjob->join('production_data','components_out.job_ticket','=','production_data.job_ticket');
        $listjob->select(
                    'components.code', 'components.name as nama_material', 'components_out.job_ticket as no_job', 'components_out.qty', 'components_out.tgl_out',
                    'production_data.cycle', 'production_data.part','components.price_beli'
        );
        if($this->check($kode_mat)){
            $listjob = $listjob->where('components.id','=',$kode_mat);
            $listjob = $listjob->where('components_out.tgl_out','>=',$tgldari.' 00:00:00');
            $listjob = $listjob->where('components_out.tgl_out','<=',$tglsampai.' 23:59:59');
        }
        $listjob =  $listjob->get();

        $listtest = DB::table('components');
        $listtest->leftJoin('components_out','components_out.component_id','=','components.id');
        $listtest->join('outgoing_components','components_out.job_ticket','=','outgoing_components.no_job');
        $listtest->select(
                    'components.code', 'components.name as nama_material', 'components_out.job_ticket as no_job', 'components_out.qty', 'components_out.tgl_out',
                    'components.price_beli'
        );
        if($this->check($kode_mat)){
            $listtest = $listtest->where('components_out.tgl_out','>=',$tgldari.' 00:00:00');
            $listtest = $listtest->where('components_out.tgl_out','<=',$tglsampai.' 23:59:59');                        
            $listtest = $listtest->where('components.id','=',$kode_mat);
        }
        $listtest = $listtest->get(); 
        
        $listtmsk = DB::table('components');
        $listtmsk->leftJoin('components_in','components_in.component_id','=','components.id'); 
        $listtmsk->join('incoming_components','components_in.incoming_comp_po','=','incoming_components.no_po');
           
        $listtmsk->select(
                    'components.code', 'components.name as nama_material', 'components_in.incoming_comp_po as no_job', 'components_in.qty', 'components_in.tgl_in',
                    'components.price_beli'
        );
        if($this->check($kode_mat)){
            $listtmsk = $listtmsk->where('components_in.tgl_in','>=',$tgldari.' 00:00:00');
            $listtmsk = $listtmsk->where('components_in.tgl_in','<=',$tglsampai.' 23:59:59');
            $listtmsk = $listtmsk->where('components.id','=',$kode_mat);
        }
        $listtmsk = $listtmsk->get();  
         
        $stock = DB::table('components')
                    ->leftJoin('customers','customers.id','=','components.customer_id')
                    ->select('components.*',DB::raw('if(components.customer_id = 0,"Internal",customers.name) as cust_name'))
                    ->where('components.id',$kode_mat)->first();
                    

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
            $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Customer");
            $objPHPExcel->getActiveSheet()->SetCellValue('A3', "Nama Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('A4', "Kode Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('A5', "Stok Awal");
            $objPHPExcel->getActiveSheet()->SetCellValue('A6', "Stok Akhir");
            $objPHPExcel->getActiveSheet()->SetCellValue('A7', "Periode Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('B2', ":$stock->cust_name");
            $objPHPExcel->getActiveSheet()->SetCellValue('B3', ":$stock->name");
            $objPHPExcel->getActiveSheet()->SetCellValue('B4', ":$stock->code");
            $objPHPExcel->getActiveSheet()->SetCellValue('B5', ":$stock->stock_awal");
            $objPHPExcel->getActiveSheet()->SetCellValue('B6', ":$stock->stock");
            $objPHPExcel->getActiveSheet()->SetCellValue('B7', ":$tgldari s/d $tglsampai");            
            $objPHPExcel->getActiveSheet()->SetCellValue('A9', "List Pemakaian Material");            			
			$objPHPExcel->getActiveSheet()->SetCellValue('A10', "Tgl Trans/Cycle/Part");
	        $objPHPExcel->getActiveSheet()->SetCellValue('B10', 'No Job');
	        $objPHPExcel->getActiveSheet()->SetCellValue('C10', 'Nama Material');
	        $objPHPExcel->getActiveSheet()->SetCellValue('D10', 'Code');
	        $objPHPExcel->getActiveSheet()->SetCellValue('E10', 'Price(Rp)');
            $objPHPExcel->getActiveSheet()->SetCellValue('F10', 'Qty Out');
            $objPHPExcel->getActiveSheet()->SetCellValue('G10', 'Rupiah Out');
            $objPHPExcel->getActiveSheet()->SetCellValue('H10', 'Qty In');
            $objPHPExcel->getActiveSheet()->SetCellValue('I10', 'Rupiah In');
            $row=10; $keluar=0; $masuk=0;
            foreach($listjob as $index=>$value)
            {
                $row++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row), $value->tgl_out.'/'.$value->cycle.'/'.$value->part); 
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row), $value->no_job); 
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row), $value->nama_material); 
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row), $value->code); 
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row), $value->price_beli); 
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row), $value->qty); 
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row), $value->price_beli*$value->qty); 
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row), 0); 
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.($row), 0);
                $keluar =  $keluar+($value->price_beli*$value->qty);                
            }    
            foreach($listtest as $index=>$value)
            {
                $row++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row), $value->tgl_out); 
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row), $value->no_job); 
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row), $value->nama_material); 
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row), $value->code); 
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row), $value->price_beli); 
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row), $value->qty); 
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row), $value->price_beli*$value->qty);  
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row), 0); 
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.($row), 0);  
                $keluar =  $keluar+($value->price_beli*$value->qty);              
            }   
            foreach($listtmsk as $index=>$value)
            {
                $row++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row), $value->tgl_in); 
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row), $value->no_job); 
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row), $value->nama_material); 
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row), $value->code); 
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row), $value->price_beli); 
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row), 0);
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row), 0); 
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row), $value->qty); 
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.($row), $value->price_beli*$value->qty); 
                $masuk =  $masuk+($value->price_beli*$value->qty);                
            } 
            $saldo_akhir = $stock->saldo_awal+$masuk-$keluar;
            $row++;
            $row++;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row),'Summary');                
            $row++;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row),'Beginning Balance');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row),"Rp. ".number_format($stock->saldo_awal));
            $row++;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row),'Balance used');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row),"Rp. ".number_format($keluar));
            $row++;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row),'Incoming Balance');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row),"Rp. ".number_format($masuk));
            $row++;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row),'Current Balance');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row),"Rp. ".number_format($saldo_akhir));                        
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
        ini_set('memory_limit', '-1');     
    	//return response()->json([
        //    'ok' =>$listtest,
         //   'sampai' => $tglsampai
        //]);
        return $view;
    	
        //echo $kode_mat;
    }
    
    public function liststok(Request $request){
        $saldoawal = $this->SaldoAwal(); $saldoakhir = $this->SaldoAkhir(); $saldopakai = $this->SaldoPakai();
        $saldomasuk = $this->SaldoMasuk();
        $list = DB::table('components') 
                ->leftJoin('customers','customers.id','=','components.customer_id')
                ->select('components.*',DB::raw('if(components.customer_id = 0,"Internal",customers.name) as cust_name'))                
                ->where('group','material');
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
            $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Customer");
            $objPHPExcel->getActiveSheet()->SetCellValue('B2', "Nama Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('C2', "Kode Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('D2', "Satuan Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('E2', "Harga Material");
            $objPHPExcel->getActiveSheet()->SetCellValue('F2', "Stok Material"); 
            $objPHPExcel->getActiveSheet()->SetCellValue('G2', "Saldo Akhir");                                                    
            $row=1;
            foreach($list as $value){
                $row++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.($row+1), $value->cust_name);                
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.($row+1), $value->name); 
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.($row+1), $value->code); 
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.($row+1), $value->satuan); 
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.($row+1), number_format($value->price_beli)); 
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.($row+1), $value->stock);
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row+1), number_format($value->saldo_akhir));                 
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
