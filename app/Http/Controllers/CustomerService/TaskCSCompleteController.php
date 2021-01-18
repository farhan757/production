<?php

namespace Adira\Http\Controllers\CustomerService;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Adira\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaskCSCompleteController extends Controller
{
    //
    function show()
    {
    	$list = DB::table('customer_care')
    	->leftJoin('desc_status','desc_status.id','=','customer_care.status')
    	->leftJoin('users','users.id','=','customer_care.uid_req')
    	->select('customer_care.id','customer_care.title','desc_status.name','customer_care.create_dt','customer_care.priority','users.username')
    	->where('complete','=',1)
    	->orderBy('create_dt','DESC')
    	->paginate(10);
    	return view('CustomerService.TaskComplete.list')->with('list',$list);
    }

    function download($id)
    {
    	$data = DB::table('customer_care')->where('id','=',$id)->first();
    	$files2 = array();
		$dir = storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'uploaded'.DIRECTORY_SEPARATOR. date('Ymd',strtotime($data->create_dt)). DIRECTORY_SEPARATOR .$data->id;
		if(is_dir($dir))
		{
			$n=0;
			$files = scandir($dir);
			foreach ($files as $key => $value) 
			{				
				if (!in_array($value,array(".","..")))
				{
					// "is_dir" only works from top directory, so append the $dir before the file
					if (is_dir($dir.DIRECTORY_SEPARATOR .$value))
					{  
						//$MyFileType[$i] = "D" ; // D for Directory
					} else
					{
						//$MyFileType[$i] = "F" ; // F for File
						$files2[] = $dir.DIRECTORY_SEPARATOR.$value;
						$n++;
					}
				}
			}
			
		}
		if(count($files2)>0)
		{
			$f_name=storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$this->generateRandomString(5).".zip";
			if(is_file($f_name))
			{
				delete($f_name);
			}

        	\Zipper::make($f_name)->add($files2)->close();
		}

		return response()->download($f_name)->deleteFileAfterSend(true);
    }

	function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

    function showdetail($id)
    {
    	$data = DB::table('customer_care')->where('id','=',$id)->first();
    	$files2 = array();
			$dir = storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'uploaded'.DIRECTORY_SEPARATOR. date('Ymd',strtotime($data->create_dt)). DIRECTORY_SEPARATOR .$data->id;
			if(is_dir($dir))
			{
				$n=0;
				$files = scandir($dir);
				foreach ($files as $key => $value) 
				{				
					if (!in_array($value,array(".","..")))
					{
						// "is_dir" only works from top directory, so append the $dir before the file
						if (is_dir($dir.DIRECTORY_SEPARATOR .$value))
						{  
							//$MyFileType[$i] = "D" ; // D for Directory
						} else
						{
							//$MyFileType[$i] = "F" ; // F for File
							$files2[] = $value;
							$n++;
						}
					}
				}
				
			}

    	$issama = 0;
    	if($data->uid_req==Auth::id()) $issama=1;
    	$timeline = DB::table('status_cs')
    	->leftJoin('users','users.id','=','status_cs.uid_updated')
    	->leftJoin('desc_status','desc_status.id','=','status_cs.id_status')
    	->select('users.username','status_cs.ket','status_cs.update_dt','desc_status.name','desc_status.icon')
    	->where('id_cs','=',$id)->get();
    	return view('CustomerService.TaskComplete.detail')->with('data',$data)->with('list',$timeline)->with('issama',$issama)->with('files',$files2);
    }
}
