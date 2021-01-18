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
        ->leftJoin('projects','projects.id','=','production_data.project_id')
        ->leftJoin('customers','customers.id','=','projects.customer_id')
    	->select(DB::raw('projects.name as project_name, customers.name as customer_name'), 'manifest.no_manifest', 'manifest.cycle', 'manifest.part','manifest.ekspedisi','manifest.service','manifest.print', 'manifest.created_at', 'manifest.tgl_kirim')
    	->orderBy('print','asc')
        ->orderBy('tgl_kirim', 'desc');
        $ticket = $request->ticket;
        $no_manifest = $request->no_manifest;
        $cycle = $request->cycle;

        if($this->check($ticket))
            $sql->where('production_data.job_ticket','=',$ticket);
        if($this->check($cycle))
            $sql->where('production_data.cycle','=',$cycle);
        if($this->check($no_manifest))
            $sql->where('manifest.no_manifest','=',$no_manifest);

        $list = $sql->paginate(10);

        $view = view('production.distribusi.index');
        $view->with('list',$list); 
        $view->with('ticket',$ticket);
        $view->with('cycle',$cycle);
        $view->with('no_manifest',$no_manifest);
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
            'nama_kurir'=>$request->nama_kurir
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
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Kota');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Kode Pos');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Telp');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Ekspedisi');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Serivice');
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
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.($row+1), $value->city);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.($row+1), $value->pos);
            $objPHPExcel->getActiveSheet()->SetCellValue('I'.($row+1), $value->telp);
            $objPHPExcel->getActiveSheet()->SetCellValue('J'.($row+1), $value->ekspedisi);
            $objPHPExcel->getActiveSheet()->SetCellValue('K'.($row+1), $value->service);    
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

    	$data = DB::table('manifest')
    	->leftJoin('production_data','manifest.production_id','=','production_data.id')
    	->leftJoin('projects','production_data.project_id','=','projects.id')
    	->leftJoin('customers','projects.customer_id','=','customers.id')
    	->select(DB::raw('production_data.id as prod_id'),DB::raw('projects.name as project_name'),DB::raw('customers.name as customer_name'),'manifest.cycle','manifest.part','manifest.no_manifest', 'manifest.tgl_kirim','manifest.ekspedisi','manifest.service','manifest.nama_kurir')
    	->where('no_manifest','=',$no_manifest)->first();
    	$total = DB::table('production_data_detail')
    	->where('production_data_detail.no_manifest','=',$no_manifest)->count();
        DB::table('manifest')
        ->where('no_manifest','=',$no_manifest)
        ->increment('print');

        $tmp = 'Kurir : '.$data->nama_kurir.', Ekspedisi : '.$data->ekspedisi.', Service : '.$data->service;
        
        $this->updateTask($data->prod_id, $this->distribusiId, $this->resultSuccesDefault, $tmp);
    	return view('production.distribusi.printtt')
        ->with('data',$data)
        ->with('total',$total)
        ->with('name', $user->name)
        ->with('name_kurir',$data->nama_kurir);        
    }
}
