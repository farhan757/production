@extends('adminlte::page')

@section('title', 'Report Material')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Filter</h3>
      </div>
        <div class="card-body">
                        <form action="" method="get" class="form-horizontal">
                          {{ csrf_field() }}
                          <div class="form-group row">
                                <label for="code" class="col-sm-2 control-label">Code</label>
                                <div class="col-sm-4">
                                      <input type="text" class="form-control pull-right" name="code" id="code" value="{{ $code ?? '' }}">
                                </div>
                            </div>
                          <div class="form-group row">
                                <label for="job_ticket" class="col-sm-2 control-label">Job Ticket</label>
                                <div class="col-sm-4">
                                      <input type="text" class="form-control pull-right" name="job_ticket" id="job_ticket" value="{{ $job_ticket ?? '' }}">
                                </div>
                            </div>

                          <div class="form-group row">
                                <label for="tgl_kirim" class="col-sm-2 control-label">Tanggal</label>
                                <div class="col-sm-2">
                                    <div class="input-group date">                                      
                                      <input type="text" class="form-control pull-right" name="start_date" id="start_date" value="{{ $start_date ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group date">                                      
                                      <input type="text" class="form-control pull-right" name="end_date" id="end_date" value="{{ $end_date ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="project" class="col-sm-2 control-label">Project</label>

                                <div class="col-sm-4">
                                    <select class="form-control select2" name="project_id" id="project_id" >
                                      <option value="0">All</option>
                                        @foreach($projects as $index=>$value)
                                        <option value="{{ $value->id }}" 
                                            @if(isset($project_id))
                                              @if($project_id==$value->id) 
                                                  selected
                                              @endif 
                                            @endif
                                            >{{ $value->customer_name }} - {{ $value->project_name }}</option>
                                        @endforeach                              
                                      </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cycle" class="col-sm-2 control-label">Cycle</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                      
                                      <input type="text" class="form-control pull-right" name="cycle" id="cycle" value="{{ $cycle ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="part" class="col-sm-2 control-label">Part</label>

                                <div class="col-sm-4">
                                    <select class="form-control select2" name="part" >
                                        <option value="0">All</option>
                                        @foreach($parts as $key=>$value)
                                        <option value="{{ $value->code }}"
                                        @if(isset($part))
                                          @if($part==$value->code)
                                            selected
                                          @endif
                                        @endif
                                        >{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="jenis" class="col-sm-2 control-label">Jenis</label>

                                <div class="col-sm-4">
                                    <select class="form-control select2" name="jenis" >
                                        <option value="0">All</option>
                                        @foreach($jeniss as $key=>$value)
                                        <option value="{{ $value->code }}"
                                        @if(isset($jenis))
                                          @if($jenis==$value->code)
                                            selected
                                          @endif
                                        @endif
                                        >{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-info" name="filter" value="filter"> Filter</button>
                            <button class="btn btn-info" name="download" value="download"> Download</button>
                        </form>
            </div>
        </div>
    </div>
@if(isset($show))
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">List Penggunaan Material</h3>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <div class="row">
                        <form method="get">
                          <div class="form-group">
                          </div>
                        </form>
                      </div>
                      <div class="card-body table-responsive p-0" style="height: 400px;">
                        <table  class="table table-bordered table-head-fixed">
                            <tr>
                              <th style="width: 10px">#</th>
                              <th>Kode</th>
                              <th>Deskripsi</th>
                              <th>Jenis</th>
                              <th>Jumlah</th>
                            </tr>
                            @foreach($list as $index=>$value)
                            <tr>
                              <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                              <td>{{ $value->code }}</td>
                              <td>{{ $value->name }}</td>
                              <td>{{ $value->group }}</td>
                              <td>{{ $value->total }}</td>
                            </tr>
                            @endforeach
                        </table>
                        {{ $list }}
                      </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('js')
<script type="text/javascript">
    $(function () {
      $('.select2').select2();
        $('#start_date').datepicker({
          autoclose: true,
          format: "yyyy/mm/dd"
        });
        $('#end_date').datepicker({
          autoclose: true,
          format: "yyyy/mm/dd"
        });
        $('#cycle').datepicker({
          autoclose: true,
          format: "yyyymmdd"
        });
    });
</script>
@endsection