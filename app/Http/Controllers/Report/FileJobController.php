<?php

namespace App\Http\Controllers\Report;

use Auth;
use DB;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use Zip;
use Storage;
class FileJobController extends Controller
{
    //
    public function index(Request $request) {
    	$start_date=$request->start_date;
    	$end_date=$request->end_date;
    	$project_id = $request->project_id;
    	$cycle = $request->cycle;
    	$part = $request->part;
    	$jenis = $request->jenis;
    	$job_ticket = $request->job_ticket;

    	$sql = DB::table('production_data');				
    	    	
    	if($this->check($start_date)) {
    		$sql = $sql->where('production_data.created_at','>=', $start_date.' 00:00:00');
    	}
    	if($this->check($end_date)) {
    		$sql = $sql->where('production_data.created_at','<=', $end_date.' 23:59:59');
    	}

    	if($this->check2($project_id)) {
    		$sql = $sql->where('production_data.project_id',$project_id);
    	}

		$sql = $sql->where('production_data.status_warehouse',1);
    	if(isset($request->download)) {
    		$list = $sql->get();
			//$zip = new ZipArchive;			
			$zipname = "File Job $start_date sd $end_date.zip";
			$zip = Zip::create(public_path($zipname));

			foreach($list as $value){
				$zip->add($value->path_file);
			}			
			//
			$zip->close();
			//return Response::download($this->uploadTemp.'/download.zip', 'download.zip', array('Content-Type: application/octet-stream','Content-Length: '. filesize($this->uploadTemp)));
			return Response::download(public_path($zipname))->deleteFileAfterSend(true);
			
		}	
				

		$projects = DB::table('projects')
			->join('customers', 'projects.customer_id','=', 'customers.id')
			->select('projects.id', DB::raw('projects.name as project_name, customers.name as customer_name'))
			->get();
		$jeniss = $this->getValues('jenis');
		$parts = $this->getValues('part');
		
		$view = view('report.filejoblist.index');		
		$view->with('projects',$projects);		
		$view->with('jeniss',$jeniss);
		$view->with('parts',$parts);
		return $view;
    }
}
