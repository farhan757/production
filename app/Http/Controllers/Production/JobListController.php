<?php

namespace App\Http\Controllers\Production;

use Storage;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobListController extends Controller
{
    //
    public function index(Request $request) {

    	$sql = DB::table('production_data')
    	->leftJoin('projects','projects.id','=','production_data.project_id')
    	->leftJoin('task_status','task_status.id','=','production_data.current_status_id')
    	->leftJoin('task_result','task_result.id','=','production_data.current_status_result_id')
    	->select('production_data.id',DB::raw('projects.name as project_name'),'production_data.file_name', 'production_data.job_ticket', 'file_name', DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'),'production_data.cycle','production_data.part','production_data.created_at')
    	->orderBy('production_data.created_at','desc');

        $jenis = $this->getValues('jenis');
        $parts = $this->getValues('part');
        $customers = $this->getCustomers();

        $ticket = $request->ticket;
		$cycle = $request->filterCycle;
		
		$info = $this->getUserInfo();
		if($info->customer_id>0)
			$sql->where('projects.customer_id', '=', $info->customer_id);
        if($this->check($ticket))
            $sql->where('production_data.job_ticket','=',$ticket);
        if($this->check($cycle))
            $sql->where('production_data.cycle','=',$cycle);

    	$list = $sql->paginate(10);

    	$view = view('production.joblist.index');
        $view->with('ticket',$ticket);
        $view->with('filterCycle',$cycle);
        $view->with('list',$list); 
        $view->with('customers', $customers);
        $view->with('jenis', $jenis);
		$view->with('parts', $parts);
		$view->with('customer_id',$info->customer_id);

        if(isset($_GET['info'])) $view->with('info',$_GET['info']);
        if(isset($_GET['error'])) $view->with('error', $_GET['error']);

        return $view;
    }

    public function selectproject() {
    	$customers = $this->getCustomers();

    	return view('production.joblist.formproject')
    	->with('customers',$customers);
    }

    public function detail($id) {
    	$data = DB::table('production_data')
    	->leftJoin('customers','production_data.customer_id','=','customers.id')
    	->leftJoin('projects','production_data.project_id','=','projects.id')
    	->leftJoin('task_status','production_data.current_status_id','=','task_status.id')
    	->leftJoin('users','production_data.created_by','=','users.id')
    	->select('production_data.id', 'production_data.file_id', 'production_data.cycle','production_data.part', 'production_data.jenis' ,'production_data.job_ticket',DB::raw('task_status.name as status_name, customers.name as customer_name, projects.name as project_name,task_status.name as last_status'),'production_data.created_at','users.username', DB::raw('null as transf'), DB::raw('null as transp'))
    	->where('production_data.id','=',$id)->first();

    	$list = DB::table('production_data_detail')
    	->where('production_id','=',$id)->get();
    	$transactionFile = array();
    	if($data->file_id!=0) {
	    	$transactionFile = DB::table('incoming_data')
	    	->select('incoming_data.*', DB::raw('null as data'))
	    	->where('id', '=', $data->file_id)->first();

	    	$transactionFile->data = DB::table('transaction_history')
	    	->leftJoin('task_status','transaction_history.status_id','=','task_status.id')
	    	->leftJoin('task_result','transaction_history.result_id','=','task_result.id')
	    	->leftJoin('users','transaction_history.user_id','=','users.id')
    		->select('transaction_history.created_at', 'users.username', 'transaction_history.note', DB::raw('task_status.name as status_name, task_status.icon as status_icon, task_result.name as result_name'))
	    	->where('file_id', '=', $data->file_id)
	    	->orderBy('created_at')
	    	->get();
    	}

    	$transactionProduction = DB::table('transaction_history')
    	->leftJoin('task_status','transaction_history.status_id','=','task_status.id')
    	->leftJoin('task_result','transaction_history.result_id','=','task_result.id')
    	->leftJoin('users','transaction_history.user_id','=','users.id')
    		->select('transaction_history.created_at', 'users.username', 'transaction_history.note', DB::raw('task_status.name as status_name, task_status.icon as status_icon, task_result.name as result_name'))
    	->where('transaction_history.production_id', '=', $id)
    	->orderBy('created_at')
    	->get();

    	return response()->json([
    		'status'=>1,
    		'data'=>$data,
    		'list'=>$list,
    		'transP'=>$transactionProduction,
    		'transF'=>$transactionFile
    	]);

    	$view = view('production.joblist.detaildata')->with('data',$data)->with('list',$list)
    	->with('transP', $transactionProduction);

    	if($data->file_id!=0) $view->with('transF', $transactionFile);

    	return $view;
    }

    public function upload(Request $request) {
    	$file_id = 0;
		$cycle = $request->cycle;
		$part = $request->part;
		$customer_id = $request->customer_id;
		$project_id = $request->project_id;
		$jenis = $request->jenis;
		$note = $request->note;
		if($jenis=='REG') {
			if($this->checkProductionExist($request)) {
				return response()->json([
					'status'=>0,
					'message'=>'Project, Cycle, Part Already exist'
				]);				
				//return back()->withErrors(['error', 'Project, Cycle, Part Already exist']);
			}			
		}

		if($request->file('file')->isValid()) {
			$current_status = $this->submitJobListId;
			$result_id = $this->statusSuccess;

			$project = DB::table('projects')->where('id','=',$project_id)->first();
			$user = Auth::user();

			$file=$request->file('file');
    		$fileName = $file->getClientOriginalName();
    		$pathFile = $this->uploadTemp.DIRECTORY_SEPARATOR.$fileName;
    		$file->move($this->uploadTemp,$fileName);

    		$next_status_id = $this->getNextTask($current_status, $project_id);
			$job_ticket = $this->generateProdTicket($project_id);
			$components = $this->getProjectComponent($project_id);

			$extension = pathinfo($pathFile, PATHINFO_EXTENSION);

			$dataList = array();
			switch ($extension) {
				case 'xlsx':
					$dataList = $this->readExel($pathFile, $components);
					break;
				case 'xls':
					$dataList = $this->readExel($pathFile, $components);
					break;
				case 'txt':
					$dataList = $this->readText($pathFile, $components);
					break;
				case 'sof':
					$dataList = $this->readText($pathFile, $components);
					break;
				default:
					return back()->withErrors(['error', 'Ekstension File not define']);
					break;
			}

			if(array_key_exists('error', $dataList)) {
				return response()->json([
					'status'=>0,
					'message'=>$dataList
				]);					
				//return back()->withErrors($dataList);
			}

			$scan_qc=1;
			$scan_distribusi=1;
			if($this->checkScanQC($project_id))
				$scan_qc=0;
			if($this->checkScanDistribusi($project_id))
				$scan_distribusi=0;

			$prod['file_id']=$file_id;
            $prod['job_ticket']=$job_ticket;
            $prod['file_name']=$fileName;
            $prod['path_file']=$pathFile;
            $prod['cycle']=$cycle;
            $prod['part']=$part;
            $prod['jenis']=$jenis;
            $prod['customer_id']=$project->customer_id;
            $prod['project_id']=$project_id;
            $prod['current_status_id']=$this->submitJobListId;
            $prod['current_status_result_id']=$this->statusSuccess;
            $prod['next_status_id']=$next_status_id;
            $prod['created_by']=$user->id;

			$id_prod = $this->insertToProduction($prod);

			foreach ($dataList as $key => $value) {
				$value['scan_qc'] = $scan_qc;
				$value['scan_distribusi'] = $scan_distribusi;
				$id_detail = $this->insertToDetail($value, $id_prod);
				$this->insertComponents($value['components'], $id_detail);
			}
			
			 $this->updateTask($id_prod, $current_status, $result_id, $note);
			 
			 return response()->json([
				'status'=>1,
				'message'=>$job_ticket
			]);	
		     //return view('production.joblist.landing')->with('job_ticket',$job_ticket); 
		} else {
			return response()->json([
				'status'=>0,
				'message'=>'Error File not valid'
			]);				
			//return back()->withError(['msg', 'Error File not valid']);
		}
    }

    public function upload3(Request $request) {
		$file_id = 0;
		$cycle = $request->cycle;
		$part = $request->part;
		$customer_id = $request->customer_id;
		$project_id = $request->project_id;
		$jenis = $request->jenis;
		$note = $request->note;

		if($request->file('file')->isValid()) {
			$current_status = $this->submitJobListId;
			$result_id = $this->statusSuccess;

//			$incoming = DB::table('incoming_data')->where('id','=',$id)->first();
			$project = DB::table('projects')->where('id','=',$project_id)->first();
			$user = Auth::user();

			$file=$request->file('file');
    		$fileName = $file->getClientOriginalName();
    		$pathFile = $this->uploadTemp.DIRECTORY_SEPARATOR.$fileName;
    		$file->move($this->uploadTemp,$fileName);

    		$next_status_id = $this->getNextTask($current_status, $project_id);
			$job_ticket = $this->generateProdTicket($project_id);
			$components = $this->getProjectComponent($project_id);

			$scan_qc=1;
			$scan_distribusi=1;
			if($this->checkScanQC($project_id))
				$scan_qc-0;
			if($this->checkScanDistribusi($project_id))
				$scan_distribusi=0;

			$id_prod = DB::table('production_data')			
			->insertGetId([
				'file_id'=>$file_id,
				'job_ticket'=>$job_ticket,
				'file_name'=>$fileName,
				'path_file'=>$pathFile,
				'cycle'=>$cycle,
				'part'=>$part,
				'jenis'=>$jenis,
				'customer_id'=>$project->customer_id,
				'project_id'=>$project_id,
				'current_status_id'=>$this->submitJobListId,
				'current_status_result_id'=>$this->statusSuccess,
				'next_status_id'=>$next_status_id,
				'created_by'=>$user->id,
				'created_at'=>Carbon::now()

			]);

			$objPHPExcel = new \PHPExcel();
		     //$fileExcel = Yii::getAlias('@webroot/templates/operator.xls');
		     $inputFileType = \PHPExcel_IOFactory::identify($pathFile);
		     $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
		     $objReader->setReadDataOnly(true);
		     /**  Load $inputFileName to a PHPExcel Object  * */
		     $objPHPExcel = $objReader->load($pathFile);
		     $total_sheets = $objPHPExcel->getSheetCount();
		     $allSheetName = $objPHPExcel->getSheetNames();
		     $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
		     $highestRow = $objWorksheet->getHighestRow();
		     $highestColumn = $objWorksheet->getHighestColumn();
		     $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
		     $temp = array();
		     if($highestRow>1) {

		     for ($row = 2; $row <= $highestRow; ++$row) {
		     	$no = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
				$seq = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
				$barcode_env = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
				$barcode_document = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
				$account_no = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
				$account_no2 = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
				$penerima = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
				$tertanggung = $objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
				$address1 = $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
				$address2 = $objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
				$address3 = $objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
				$city = $objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
				$pos = $objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
				$telp = $objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
				$ekspedisi = $objWorksheet->getCellByColumnAndRow(14, $row)->getValue();
				$service = $objWorksheet->getCellByColumnAndRow(15, $row)->getValue();
				$tmp_id = DB::table('production_data_detail')
				->insertGetId([
					'production_id'=>$id_prod,
					'seq'=>$seq,
					'barcode_env'=>$barcode_env,
					'barcode_document'=>$barcode_document,
					'account_no'=>$account_no,
					'account_no2'=>$account_no2,
					'penerima'=>$penerima,
					'tertanggung'=>$tertanggung,
					'address1'=>$address1,
					'address2'=>$address2,
					'address3'=>$address3,
					'city'=>$city,
					'pos'=>$pos,
					'telp'=>$telp,
					'ekspedisi'=>$ekspedisi,
					'service'=>$service,
					'scan_qc'=>$scan_qc,
					'scan_distribusi'=>$scan_distribusi,
					'created_at'=>Carbon::now()
				]);
				$cntr=0;
				foreach ($components as $key => $value) {
					$cntr++;
					$col=15+$cntr;
		            $tmpval = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
		            DB::table('production_data_detail_list')
		            ->insert([
		            	'production_data_detail_id'=>$tmp_id,
		            	'component_id'=>$value->component_id,
		            	'total'=>$tmpval
		            ]);
				}
//		         for ($col = 16; $col < $components; ++$col) {
//		             $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
//		         }

		     }
		     $this->updateTask($id, $this->current_status, $result_id, $note);

		     return view('production.joblist.landing')->with('job_ticket',$job_ticket); 
		 } else return back()->withError('error', 'File kosong');
			$trans = array();
	    	$trans['file_id']=$file_id;
	    	$trans['production_id']=$id_prod;
	    	$trans['status_id']=$current_status;
	    	$trans['result_id']=$result_id;
	    	$trans['note']=$note;
	    	$this->insertToTransaction($trans);
	    	$next_status = $this->getNextTask($current_status, $data->project_id);

	    	$upd = array();
	    	$upd['id']=$file_id;
	    	$upd['current_status_id']=$current_status;
	    	$upd['current_status_result_id']=$result_id;
	    	$upd['next_status_id']=$next_status_id;
	    	$this->updateIncomingFile($upd);
		} else {
			return back()->withError(['msg', 'Error']);
		}
	}

	public function download($id) {
		$data = DB::table('production_data')
		->where('id','=',$id)->first();

		return Storage::download($data->path_file);
	}
}
