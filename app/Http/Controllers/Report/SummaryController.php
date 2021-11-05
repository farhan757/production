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
	public function index(Request $request)
	{
		$no_amplop = $request->input('no_amplop');
		$no_account = $request->input('no_account');
		$start_date = $request->start_date;
		$end_date = $request->end_date;
		$nama = $request->nama;
		$customer_id = $request->customer_id;
		$project_id = $request->project_id;
		$cycle = $request->cycle;
		$part = $request->part;
		$jenis = $request->jenis;
		$job_ticket = $request->job_ticket;

		$sql = DB::table('production_data')
			->select('production_data.id', DB::raw('projects.name as project_name'), 'production_data.file_name', 'production_data.job_ticket', DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'), 'production_data.cycle', 'production_data.part', 'production_data.created_at', DB::raw('count(*) as jumlah'))
			->leftJoin('projects', 'projects.id', '=', 'production_data.project_id')
			->leftJoin('task_status', 'task_status.id', '=', 'production_data.current_status_id')
			->leftJoin('task_result', 'task_result.id', '=', 'production_data.current_status_result_id')
			->join('production_data_detail', 'production_data.id', '=', 'production_id')
			->groupBy('production_data.id')
			->groupBy('projects.name')
			->groupBy('production_data.file_name')
			->groupBy('production_data.job_ticket')
			->groupBy('task_status.name')
			->groupBy('task_result.name')
			->groupBy('production_data.cycle')
			->groupBy('production_data.part')
			->groupBy('production_data.created_at')
			->orderBy('production_data.created_at', 'desc');

		//$sql = $sql->where('production_data')

		if ($this->check($job_ticket)) {
			$sql = $sql->where('production_data.job_ticket', '=', $job_ticket);
		}
		if ($this->check($start_date)) {
			$sql = $sql->where('production_data.created_at', '>=', $start_date. ' 00:00:00');
		}
		if ($this->check($end_date)) {
			$sql = $sql->where('production_data.created_at', '<=', $end_date. ' 23:59:59');
		}
		if ($this->check2($customer_id)) {
			$sql = $sql->where('projects.customer_id', '=', $customer_id);
		}
		if ($this->check2($project_id)) {
			$sql = $sql->where('production_data.project_id',$project_id);
		}
		if ($this->check($cycle)) {
			$sql = $sql->where('production_data.cycle', '=', $cycle);
		}
		if ($this->check2($part)) {
			$sql = $sql->where('production_data.part', '=', $part);
		}
		if ($this->check2($jenis)) {
			$sql = $sql->where('production_data.jenis', '=', $jenis);
		}

		if (isset($request->download)) {

			$components_project = "";
			$production_data = "";
			$total_perinci = "";

			// create Excel
			$objPHPExcel = new Spreadsheet();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("PT. Tata Layak Prawira")
				->setLastModifiedBy("PT. Tata Layak Prawira")
				->setTitle("Softcopy")
				->setSubject("Office 2007 XLSX Softcopy Document")
				->setDescription("Office 2007 XLSX Softcopy Document");

			// Add some data
			//$objPHPExcel->setActiveSheetIndex(0);			
			$headcol = 1; $sheet = 0;
			$headrow = 0;	

			$customer = DB::table('production_data')					
			->join('customers', 'production_data.customer_id', '=', 'customers.id')
			->join('projects', 'production_data.project_id', '=', 'projects.id');
			if ($this->check2($customer_id)) {
				$customer = $customer->where('customers.id', '=', $customer_id);
			}
			if ($this->check2($project_id)) {
				$customer = $customer->where('project_id', $project_id);
			}
			$customer = $customer->groupBy('customers.id')->orderBy('customers.id')
			->select('customers.id','customers.code', DB::raw('customers.name as customer_name'))
			->get();

			foreach($customer as $vcus){
				$headrow = 0;
				$objPHPExcel->createSheet($sheet);
				$objPHPExcel->setActiveSheetIndex($sheet);

				$project_data = DB::table('production_data')
				->join('projects', 'production_data.project_id', '=', 'projects.id')
				->join('customers', 'production_data.customer_id', '=', 'customers.id');
				$project_data = $project_data->where('production_data.customer_id', $vcus->id);
				$project_data = $project_data->groupBy('project_id')->orderBy('customers.name')
					->select('projects.id', DB::raw('projects.name as project_name, customers.name as customer_name'))
					->get();
									
				foreach ($project_data as $val) {
					
					$headrow++; $headcol = 1;
					$components_project = DB::table('projects')
						->join('project_to_component', 'projects.id', '=', 'project_to_component.project_id')
						->leftJoin('components', 'project_to_component.component_id', '=', 'components.id')
						->where('projects.id', '=', $val->id)
						->select('components.name')
						->orderBy('project_to_component.component_id', 'ASC')->get();
	
					$production_data = DB::table('production_data')
						->join('production_data_detail', 'production_data.id', '=', 'production_data_detail.production_id')
						->leftJoin('projects', 'production_data.project_id', '=', 'projects.id')
						->leftJoin('customers', 'production_data.customer_id', '=', 'customers.id')
						->select('production_data.created_at','projects.code','projects.name', 'customers.name as cust_name', 'production_data.job_ticket', 'production_data.cycle', 'production_data.part', 'production_data.jenis', DB::raw('COUNT(*) AS jml'))
						->where('production_data.created_at', '>=', $start_date. ' 00:00:00')					
						->where('production_data.created_at', '<=', $end_date. ' 23:59:59')					
						->where('production_data.project_id', '=', $val->id)
						->where('production_data.status_warehouse', '=', 1)
						->groupBy('production_data.id')
						->groupBy('production_data.file_name')
						->groupBy('production_data.job_ticket')
						->groupBy('production_data.cycle')
						->groupBy('production_data.part')
						->orderBy('production_data.created_at', 'ASC')->get();
	
					$total_perinci = DB::table('components_out')
						->join('components', 'components_out.component_id', '=', 'components.id')
						->join('production_data', 'production_data.job_ticket', 'components_out.job_ticket')
						->select('components_out.component_id', 'components.name', 'components.satuan', DB::raw('SUM(components_out.qty) AS jml'))
						->where('components_out.tgl_job', '>=', $start_date . ' 00:00:00')
						->where('components_out.tgl_job', '<=', $end_date . ' 23:59:59')				
						->where('production_data.project_id', '=', $val->id)
						->where('production_data.status_warehouse', '=', 1)
						->groupBy('components_out.component_id')
						->orderBy('components_out.component_id', 'ASC')->get();
	
					$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, "No");
					$headcol++;
					$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, "Customer");
					$headcol++;
					$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, "Nama Project");
					$headcol++;
					$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, 'Job Ticket');
					$headcol++;
					$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, 'Cycle');
					$headcol++;
					$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, 'Part');
					$headcol++;
					$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, 'Tgl Upload');
	
					// looping project components use
					foreach ($components_project as $headcolom) {
						$headcol++;
						$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $headcolom->name);
					}
					
					$cntr = 0;
					// looping value production data
					foreach ($production_data as $key => $value) {
						$headcol = 0;
						$headcol++;
						$headrow++;
						$cntr++;
	
						$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $cntr);
						$headcol++;
						$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $value->cust_name);
						$headcol++;
						$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $value->name);
						$headcol++;
						$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $value->job_ticket);
						$headcol++;
						$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $value->cycle);
						$headcol++;
						$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $value->part);
						$headcol++;
						$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $value->created_at);
	
						$components = $this->getComponentsByticket($value->job_ticket);
						foreach ($components as $comp) {
							$headcol++;
							$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $comp->qty);
						}
					}
	
					$headrow++;
					$objPHPExcel->getActiveSheet()->SetCellValue('A' . $headrow, 'JUMLAH');
					$objPHPExcel->getActiveSheet()->mergeCells('A' . $headrow . ':G' . $headrow);
					$headcol=7;
					foreach ($total_perinci as $total) {
						$headcol++;
						$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($headcol, $headrow, $total->jml);
					}
					$headrow++;				
					// Rename worksheet

					
				}
				$objPHPExcel->getActiveSheet()->setTitle($vcus->code);
				// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$objPHPExcel->setActiveSheetIndex($sheet);
				$sheet++;
			}

			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Summary.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
			$objWriter->save('php://output');
		} else {
			$list = $sql->paginate(10);
			$customer = DB::table('production_data')					
					->join('customers', 'production_data.customer_id', '=', 'customers.id')
					->groupBy('customers.id')->orderBy('customers.id')
					->select('customers.id', DB::raw('customers.name as customer_name'))
					->get();


			$jeniss = $this->getValues('jenis');
			$parts = $this->getValues('part');

			$view = view('report.summary.index');
			$view->with('list', $list);
			//$view->with('projects', $projects);
			return $view
				->with('jeniss', $jeniss)
				->with('parts', $parts)
				->with('start_date', $start_date)
				->with('end_date', $end_date)
				->with('customer_id', $customer_id)			
				->with('project_id', $project_id)
				->with('cycle', $cycle)
				->with('part', $part)
				->with('job_ticket', $job_ticket)
				->with('jenis', $jenis)
				->with('customer',$customer);
		}
	}

	function getComponentsByticket($job_ticket)
	{
		$rinci_components = DB::table('components_out')
			->join('components', 'components_out.component_id', '=', 'components.id')
			->where('components_out.job_ticket', '=', $job_ticket)
			->orderBy('components_out.component_id', 'ASC')->get();
		return $rinci_components;
	}

	public function getProj($id)
	{
		# code...
		$projects = DB::table('production_data')
		->join('projects', 'production_data.project_id', '=', 'projects.id')				
		->groupBy('project_id')->where('production_data.customer_id',$id)
		->select('projects.id', DB::raw('projects.name as project_name'))
		->get();

		return $projects;
	}
}
