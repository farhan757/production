<?php

namespace App\Http\Controllers\Gudang;

use Auth;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    //
    public function index(Request $request)
    {
        $sql = DB::table('production_data')
            ->leftjoin('production_data_detail','production_data_detail.production_id','=','production_data.id')
            ->leftJoin('projects', 'projects.id', '=', 'production_data.project_id')
            ->leftJoin('task_status', 'task_status.id', '=', 'production_data.current_status_id')
            ->leftJoin('task_result', 'task_result.id', '=', 'production_data.current_status_result_id')
            ->select('production_data.id', DB::raw('projects.name as project_name'), 'production_data.file_name', 'production_data.job_ticket', DB::raw('task_status.name as status_name'), DB::raw('task_result.name as result_name'), 'production_data.cycle', 'production_data.part', 'production_data.created_at')
            ->addselect(DB::raw('count(production_data.id) as jml_data'))
            ->where('production_data.next_status_id', '=', $this->gudangTaskId)
            ->orderBy('production_data.updated_at', 'desc')
            ->groupBy('production_data.id');

        $results = DB::table('task_result')
            ->join('task_status_to_result', 'task_result.id', '=', 'task_status_to_result.result_id')
            ->select('task_result.*')
            ->where('task_status_to_result.status_id', '=', $this->gudangTaskId)
            ->get();


        $ticket = $request->ticket;
        $cycle = $request->filterCycle;

        if ($this->check($ticket))
            $sql->where('production_data.job_ticket', 'like','%'. $ticket.'%');
        if ($this->check($cycle))
            $sql->where('production_data.cycle', '=', $cycle);

        $list = $sql->paginate(10);

        $view = view('gudang.approval.index');
        $view->with('list', $list);
        $view->with('ticket', $ticket);
        $view->with('cycle', $cycle);
        $view->with('results', $results);
        if (isset($_GET['info'])) $view->with('info', $_GET['info']);
        if (isset($_GET['error'])) $view->with('error', $_GET['error']);
        return $view;
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $note = $request->input('note');
        $result_id = $request->input('result_id');
        $status_cancel = DB::table('production_data')->where('id', $request->id)->first();
        if ($status_cancel->current_status_result_id == 11) {
            return response()->json([
                'status' => 2,
                'message' => "Canot Approve, because data has been Cancel Job"
            ]);            
        }else{
            $msg = $this->updateTask($id, $this->gudangTaskId, $result_id, $note);
            return response()->json([
                'status' => 1,
                'message' => $this->message['default']['status']['success']
            ]);            
        }
    }
}
