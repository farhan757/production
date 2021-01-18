<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $keyUserInfo = "userInfo";
//    public $uploadedFile = 'D:\www\production\storage\app\uploaded\incomingFiles';
//    public $uploadApproval = 'D:\www\production\storage\app\uploaded\approvalFiles';
//    public $uploadTemp = 'D:\www\production\storage\app\uploaded\temp';
    public $uploadedFile = '/var/www/production/storage/app/uploaded/incomingFiles';
    public $uploadApproval = '/var/www/production/storage/app/uploaded/approvalFiles';
    public $uploadApproved = '/var/www/production/storage/app/uploaded/approvedFiles';
    public $uploadPO = '/var/www/production/storage/app/uploaded/po';
    public $uploadTemp = '/var/www/production/storage/app/uploaded/temp';
    public $counterFileKey = 'counter_file'; 
    public $counterProdKey = 'counter_prod';
    public $counter_inv = 'counter_inv'; 
    public $resultSuccesDefault = 1;
    public $statusSuccess = 1;
    public $finishTask = 100;
    public $notapp = 14;
    public $submitRevisiId=15;
    public $gudangTaskId=16;
    public $countercomout = 'coun_com_out';
    public $countercomin = 'coun_com_in';

    public $downloadTaskId=2;
    public $uploadApprovalTaskId=13;
    public $approvalTaskId=3;
    public $submitJobListId=5;
    public $submitPrintingId=6;
    public $submitBalancingId=9;
    public $distribusiId=11;

    public $scanQCId=8;
    public $scanDistribusiId=10;

    public $message=array(
        'default'=>array(
            'add'=>array(
                'success'=>'Data berhasil ditambahkan',
                'error'=>'Gagal menambahkan data'
            ),
            'save'=>array(
                'success'=>'Data berhasil disimpan',
                'error'=>'Gagal menyimpan data'
            ),
            'delete'=>array(
                'success'=>'Data berhasil dihapus',
                'error'=>'Gagal menhapus data'
            ),
            'status'=>array(
                'success'=>'Berhasil update status',
                'error'=>'Update status gagal'
            ),
        ),
    );
    public $defaultMessageAddSuccess = 'Data berhasil ditambahkan';
    public $defaultMessageSaveSuccess = 'Data berhasil diubah';

    public function getUser() {
        return Auth::user();
    }

    public function getUserInfo() {
    	return json_decode(base64_decode(session(base64_encode($this->keyUserInfo))));
    }

    public function getCounter($code) {
    	$tmp = DB::table('table_counter')
    	->where('code','=',$code)
    	->select('counter')
    	->first();
    	if($tmp) {
	    	$counter = $tmp->counter+1;
	    	DB::table('table_counter')
	    	->where('code','=',$code)
	    	->update([
	    		'counter'=>$counter
	    	]);
    	} else {
    		$counter=1;
    		DB::table('table_counter')
    		->insert([
    			'code'=>$code,
    			'counter'=>$counter
    		]);
    	}

    	return $counter;
    }

    public function getNextTask($current_task, $project_id) {
    	$task_now = DB::table('project_to_task')
    	->where([
    		['status_id','=',$current_task],
    		['project_id','=',$project_id]
    	])
    	->first();
    	$task_next = DB::table('project_to_task')
    	->where([
    		['sort','>', $task_now->sort],
    		['project_id','=',$project_id]
    	])->orderBy('sort')->first();

    	if($task_next) {
    		return $task_next->status_id;
    	} else return $finishTask;
    }

    public function insertToTransaction($transaction) {
        $user = Auth::user();

    	DB::table('transaction_history')
    	->insert([
    		'file_id'=>$transaction['file_id'],
    		'production_id'=>$transaction['production_id'],
    		'status_id'=>$transaction['status_id'],
    		'result_id'=>$transaction['result_id'],
    		'note'=>$transaction['note'],
    		'user_id'=>$user->id,
    		'created_at'=>Carbon::now()
    	]);
    }

    public function getCustomers() {
        $sql = DB::table('customers');

        $info = $this->getUserInfo();
        if($info->customer_id>0)
            $sql->where('id', '=', $info->customer_id);
        
        return $sql->get();
    }

    public function getCustomersById($customer_id) {
        $sql = DB::table('customers');        
        if($customer_id>0)
            $sql->where('id', '=', $customer_id);
        
        return $sql->first();
    }    

    public function getVendor(){
        $sql = DB::table('vendor');
        
        return $sql->get();
    }

    public function getComponents($components_id=0){
        $sql = DB::table('components')
                ->where('group','=','material');
        if($components_id != 0){
            $sql->where('id','=',$components_id);
            return $sql->first();
        }else{
            return $sql->get();
        }
        
    }

    public function getProject() {
        $sql = DB::table('projects');

        $info = $this->getUserInfo();
        if($info->project_id>0)
            $sql->where('id', '=', $info->project_id);

        return $sql->get();
    }

    public function getProjectbyId($project_id) {
        $sql = DB::table('projects')
        ->where('id', '=', $project_id)->first();
        return $sql;
    }

    public function updateIncomingFile($data) {
        $user = Auth::user();

        DB::table('incoming_data')
        ->where('id','=',$data['id'])
        ->update([
            'current_status_id'=>$data['current_status_id'],
            'current_status_result_id'=>$data['current_status_result_id'],
            'next_status_id'=>$data['next_status_id'],
            'updated_by'=>$user->id,
            'updated_at'=>Carbon::now()
        ]);
    }

    public function updateProductionData($data) {
        $user = Auth::user();

        DB::table('production_data')
        ->where('id','=',$data['id'])
        ->update([
            'current_status_id'=>$data['current_status_id'],
            'current_status_result_id'=>$data['current_status_result_id'],
            'next_status_id'=>$data['next_status_id'],
            'updated_by'=>$user->id,
            'updated_at'=>Carbon::now()
        ]);
    }

    public function getResult($result_id) {
        return DB::table('task_result')->where('id','=',$result_id)->first();
    }

    public function getProjectComponent($project_id) {
        return DB::table('project_to_component')
        ->select('component_id')
        ->orderBy('sort')->where('project_id','=',$project_id)->get();
    }

    public function generateProdTicket($project_id) {
        $counter = str_pad($this->getCounter($this->counterProdKey) ,5,'0',STR_PAD_LEFT);
        $project = DB::table('projects')
        ->where('id','=',$project_id)->first();
        $customer = DB::table('customers')
        ->where('id','=',$project->customer_id)->first();

        $date = date("Ymd");
        $project_id = str_pad($project_id,3,'0',STR_PAD_LEFT);

        return $customer->code.$date.$project_id.$counter;
    }

    public function checkProductionExist(Request $request) {
        return DB::table('production_data')
        ->where([
            ['cycle','=',$request->cycle],
            ['part','=',$request->part],
            ['project_id',$request->project_id],
        ])->exists();
    }

    public function checkScanQC($project_id) {
        return DB::table('project_to_task')
        ->where([
            ['project_id','=',$project_id],
            ['status_id','=',$this->scanQCId]
        ])->exists();
    }

    public function checkScanDistribusi($project_id) {
        return DB::table('project_to_task')
        ->where([
            ['project_id','=',$project_id],
            ['status_id','=',$this->scanDistribusiId]
        ])->exists();
    }

    public function test($project_id) {
        if($this->checkScanDistribusi($project_id))
            echo 'ada';
        else echo 'tidak ada';
    }

    public function updateTaskFile($file_id, $status_id, $result_id, $note) {
        $prod = DB::table('incoming_data')
        ->where('id','=',$file_id)->first();

        $results = $this->getResult($result_id);
        switch ($results->isok) {
            case 'P':
                $next_status_id = $status_id;
                break;
            case 'Y':
                $next_status_id = $this->getNextTask($status_id, $prod->project_id);
                break;
            case 'N':
                $next_status_id = $this->finishTask;
                break;            
        }

        $upd = array();
        $upd['id']=$file_id;
        $upd['current_status_id']=$status_id;
        $upd['current_status_result_id']=$result_id;
        $upd['next_status_id']=$next_status_id;

        $this->updateIncomingFile($upd);

        $transaction['file_id']=$file_id;
        $transaction['production_id']=0;
        $transaction['status_id']=$status_id;
        $transaction['result_id']=$result_id;
        $transaction['note']=$note;
        $this->insertToTransaction($transaction);
    }

    public function updateTask($production_id, $status_id, $result_id, $note) {
        $prod = DB::table('production_data')
        ->where('id','=',$production_id)->first();

        $results = $this->getResult($result_id);
        switch ($results->isok) {
            case 'P':
                $next_status_id = $status_id;
                break;
            case 'Y':
                $next_status_id = $this->getNextTask($status_id, $prod->project_id);
                break;
            case 'N':
                $next_status_id = $this->finishTask;
                break;            
        }

        $upd = array();
        $upd['id']=$production_id;
        $upd['current_status_id']=$status_id;
        $upd['current_status_result_id']=$result_id;
        $upd['next_status_id']=$next_status_id;

        $this->updateProductionData($upd);

        $transaction['file_id']=0;
        $transaction['production_id']=$production_id;
        $transaction['status_id']=$status_id;
        $transaction['result_id']=$result_id;
        $transaction['note']=$note;
        $this->insertToTransaction($transaction);

        if($next_status_id==$this->submitPrintingId){
            $msg = $this->insertToComponentOut($production_id);
            if($msg=="sukses"){
                DB::table('production_data')
                ->where('id',$production_id)
                ->update([
                    'status_warehouse' => 1
                ]);
            }
        }

        if($next_status_id==$this->distribusiId) { // jika next adalah distribusi id, maka buat manifest            
                $this->generateManifest($production_id);
        }
    }

    public function insertToComponentOut($production_id){
        $sql = DB::table('production_data_detail_list')
        ->select('production_data.job_ticket','components.id','components.code','components.name','components.satuan','group','components.price', DB::raw('sum(production_data_detail_list.total) as total'), )
        ->leftJoin('components', 'components.id','=','production_data_detail_list.component_id')
        ->leftJoin('production_data_detail','production_data_detail_list.production_data_detail_id','=','production_data_detail.id')
        ->leftJoin('production_data', 'production_data.id','=', 'production_data_detail.production_id')
        ->groupBy('components.id','components.code','components.name','components.satuan','group')
        ->where('production_data.id','=', $production_id)->get(); 

        $msg="sukses";
        DB::beginTransaction();
        try
        {            
            foreach($sql as $value){
                DB::table('components_out')->insert([
                    'job_ticket' => $value->job_ticket,
                    'component_id' => $value->id,
                    'component_price' => $value->price,
                    'group' => $value->group,
                    'qty' => $value->total
                ]);                
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            $msg="gagal";
        }
        return $msg;
    }


    public function generateManifest($production_id) {

        $prod = DB::table('production_data')
        ->where('id','=',$production_id)
        ->first();

        $data = DB::table('production_data_detail')
        ->where([
            ['production_id','=',$production_id],
            ['scan_distribusi','=',1]
        ])
        ->whereNull('no_manifest')
        ->select('ekspedisi','service')
        ->groupBy('ekspedisi','service')
        //->orderBy('service', 'asc')
        ->get();
        
        foreach ($data as $key => $value) {

            $no_manifest = $this->getNoManifest();
            $manifest = array();
            $manifest['no_manifest']=$no_manifest;
            $manifest['cycle']=$prod->cycle;
            $manifest['part']=$prod->part;
            $manifest['jenis']=$prod->jenis;
            $manifest['ekspedisi']=$value->ekspedisi;
            $manifest['service']=$value->service;
            $manifest['production_id']=$production_id;
            $manifest['print']=0;
            
            DB::table('production_data_detail')
            ->where([
                ['production_id', '=', $production_id],
                ['scan_distribusi','=',1],
                ['ekspedisi','=',$value->ekspedisi],
                ['service','=',$value->service]
            ])
            ->whereNull('no_manifest')
            ->update([
                'no_manifest'=>$no_manifest
            ]);
            $this->insertManifest($manifest);
        }
        

    }

    public function insertManifest($data) {
        $user = Auth::user();
        DB::table('manifest')
        ->insert([
            'no_manifest'=>$data['no_manifest'],
            'production_id'=>$data['production_id'],
            'cycle'=>$data['cycle'],
            'part'=>$data['part'],
            'jenis'=>$data['jenis'],
            'ekspedisi'=>$data['ekspedisi'],
            'service'=>$data['service'],
            'print'=>$data['print'],
            'created_by'=>$user->id,
            'created_at'=>Carbon::now()
        ]);
    }

    public function getNoManifest() {
        $rand = rand(10,999999);
        $no_manifest =  str_pad($rand, 6, '0', STR_PAD_LEFT);
        $chek = DB::table('manifest')
        ->where('no_manifest')->exists();
        if(!$chek) {
            return $no_manifest;
        } else return $this->getNoManifest();
    }

    function check($data) {
        if(isset($data)) {
            if($data!=false) {
                return true;
            }  else return false;
        } else return false;
    }

    function check2($data) {
        if(isset($data)) {
            if($data!='0') {
                return true;
            }  else return false;
        } else return false;
    }


    function readExel($file_name, $components) {
        $error = array();
        $error['error']="Error dibaris berikut :";
        $error['row']=array();
        $return = array();

        $spreadsheet = IOFactory::load($file_name);
        $objWorksheet = $spreadsheet->getActiveSheet();
        //$sheetData = $spreadsheet->getSheet(0)->toArray(null, true, true, true);
//      var_dump($sheetData);
//        $sheetData = $spreadsheet->getSheet(0)->toArray(null, true, true, true);
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();

        $temp = array();
        if($highestRow>1) {

            for ($row = 2; $row <= $highestRow; ++$row) {
                $err=0;
                $tmpret = array();
                $tmpret['no'] =  $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
                $tmpret['seq'] =  $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
                $tmpret['barcode_env'] =  $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
                $tmpret['barcode_document'] =  $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                $tmpret['account_no'] =  $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
                $tmpret['account_no2'] =  $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
                $tmpret['penerima'] =  $objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
                $tmpret['tertanggung'] =  $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
                $tmpret['address1'] =  $objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
                $tmpret['address2'] =  $objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
                $tmpret['address3'] =  $objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
                $tmpret['city'] =  $objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
                $tmpret['pos'] =  $objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
                $tmpret['telp'] =  $objWorksheet->getCellByColumnAndRow(14, $row)->getValue();
                $tmpret['ekspedisi'] =  $objWorksheet->getCellByColumnAndRow(15, $row)->getValue();
                $tmpret['service'] =  $objWorksheet->getCellByColumnAndRow(16, $row)->getValue();

                $cntr=0;
                $tmpcomp = array();
                foreach ($components as $key => $value) {
                    $cntr++;
                    $col=16+$cntr;
                    $cmp = array();
                    $cmp['component_id']=$value->component_id;
                    $tmpval = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    if(!is_null($tmpval)) {
                        $cmp['value']=$tmpval;
                        array_push($tmpcomp, $cmp);
                    } else $err=1;
                }
                $tmpret['components']=$tmpcomp;
                if($err==1) {
                    array_push($error['row'], $row);
                } else {
                    array_push($return, $tmpret);
                }
            }
        }
        if(count($error['row'])>2) {
            return $error;
        } else return $return;
    }

    function readText($file_name, $components, $delimited='|') {
        $error = array();
        $error['error']="Error :";
        $return = array();
        $file = fopen($file_name, 'r');
        $cntf=0;
        while (!feof($file)) {
            $err = 0;
            $cntf++;
            $text = fgets($file);
            if($cntf>1 && $text!="") {
                $index = $cntf-1;
                $texts = explode($delimited, $text);
                $tmpret = array();
                $tmpret['no'] = $texts[0];
                $tmpret['seq'] = $texts[1];
                $tmpret['barcode_env'] = $texts[2];
                $tmpret['barcode_document'] = $texts[3];
                $tmpret['account_no'] = $texts[4];
                $tmpret['account_no2'] = $texts[5];
                $tmpret['penerima'] = $texts[6];
                $tmpret['tertanggung'] = $texts[7];
                $tmpret['address1'] = $texts[8];
                $tmpret['address2'] = $texts[9];
                $tmpret['address3'] = $texts[10];
                $tmpret['city'] = $texts[11];
                $tmpret['pos'] = $texts[12];
                $tmpret['telp'] = $texts[13];
                $tmpret['ekspedisi'] = $texts[14];
                $tmpret['service'] = $texts[15];

                $cntr=0;
                $tmpcomp = array();
                foreach ($components as $key => $value) {
                    $cntr++;
                    $col=15+$cntr;
                    $cmp = array();
                    $cmp['component_id']=$value->component_id;
                    if(array_key_exists($col, $texts)) {
                        $tmpval = $texts[$col];
                        $cmp['value']=$tmpval;
                        array_push($tmpcomp, $cmp);
                    } else $err=1;
                }
                $tmpret['components']=$tmpcomp;

                if($err==1) {
                    array_push($error, $text);
                } else {
                    array_push($return, $tmpret);
                }
            }
        }
        fclose($file);

        if(count($error)>2) {
            return $error;
        } else
        return $return;
    }

    function insertToProduction($data) {
        return DB::table('production_data')            
            ->insertGetId([
                'file_id'=>$data['file_id'],
                'job_ticket'=>$data['job_ticket'],
                'file_name'=>$data['file_name'],
                'path_file'=>$data['path_file'],
                'cycle'=>$data['cycle'],
                'part'=>$data['part'],
                'jenis'=>$data['jenis'],
                'customer_id'=>$data['customer_id'],
                'project_id'=>$data['project_id'],
                'current_status_id'=>$data['current_status_id'],
                'current_status_result_id'=>$data['current_status_result_id'],
                'next_status_id'=>$data['next_status_id'],
                'created_by'=>$data['created_by'],
                'created_at'=>Carbon::now(),
                'updated_by'=>$data['created_by'],
                'updated_at'=>Carbon::now()
            ]);
    }

    function insertComponents($components, $id_detail) {
        $cntr=0;
        foreach ($components as $key => $value) {
            $cntr++;
            DB::table('production_data_detail_list')
            ->insert([
                'production_data_detail_id'=>$id_detail,
                'component_id'=>$value['component_id'],
                'total'=>$value['value']
            ]);
        }
    }

    function insertToDetail($data, $id_prod) {
        $tmp_id = DB::table('production_data_detail')
            ->insertGetId([
                'production_id'=>$id_prod,
                'seq'=>$data['seq'],
                'barcode_env'=>$data['barcode_env'],
                'barcode_document'=>$data['barcode_document'],
                'account_no'=>$data['account_no'],
                'account_no2'=>$data['account_no2'],
                'penerima'=>$data['penerima'],
                'tertanggung'=>$data['tertanggung'],
                'address1'=>$data['address1'],
                'address2'=>$data['address2'],
                'address3'=>$data['address3'],
                'city'=>$data['city'],
                'pos'=>$data['pos'],
                'telp'=>$data['telp'],
                'ekspedisi'=>$data['ekspedisi'],
                'service'=>$data['service'],
                'scan_qc'=>$data['scan_qc'],
                'scan_distribusi'=>$data['scan_distribusi'],
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ]);

            return $tmp_id;
    }

    public function getValues($param) {
        return DB::table('master_value')
        ->where('flag', $param)->get();
    }

    public function angkaRomawi($val){
        $arr = array(
            '1' => 'I',
            '2' => 'II',
            '3' => 'III',
            '4' => 'IV',
            '5'=>'V',
            '6'=>'VI',
            '7'=>'VII',
            '8'=>'VIII',
            '9'=>'IX',
            '10'=>'X',
            '11'=>'XI',
            '12'=>'XII'
        );
        $intVal = (int)$val;
        $strVal = (string)$intVal;
        $getVal = $arr[$strVal];
        return $getVal;
    }

    function company(){
        $sql = DB::table('company')->first();
        return $sql;
    }

    public function terbilang($nilai) {
        if($nilai<0) {
            $hasil = "minus ". trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }     		
        return $hasil.' rupiah';
    }    

	public function penyebut($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->penyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . $this->penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}    
}
