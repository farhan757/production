@extends('adminlte::page')

@section('title', 'Report Detail')

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
                                <label for="no_amplop" class="col-sm-2 control-label">No Amplop</label>
                                <div class="col-sm-4">
                                      <input type="text" class="form-control pull-right" name="no_amplop" id="no_amplop" value="{{ $no_amplop ?? '' }}">
                                </div>
                            </div>
                          <div class="form-group row">
                                <label for="no_account" class="col-sm-2 control-label">No Account</label>
                                <div class="col-sm-4">
                                      <input type="text" class="form-control pull-right" name="no_account" id="no_account" value="{{ $no_account ?? '' }}">
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
                                <label for="nama" class="col-sm-2 control-label">Nama</label>
                                <div class="col-sm-6">
                                      <input type="text" class="form-control pull-right" name="nama" id="nama" value="{{ $nama ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="project" class="col-sm-2 control-label">Project</label>

                                <div class="col-sm-4">
                                    <select class="form-control" name="project_id" id="project_id" >
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
                                    <select class="form-control" name="part" >
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
                                    <select class="form-control" name="jenis" >
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
                            <button class="btn btn-info"> Filter</button>
                            <button class="btn btn-info" name="download" value="download"> Download</button>
                        </form>
                        
            </div>
        </div>
    </div>

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
         
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">List Detail Production</h3>
                        <!-- /.box-tools -->
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                      <table class="table table-bordered" >
                            <tr>
                              <th style="width: 10px">#</th>
                              <th>Tanggal</th>
                              <th>No Amplop</th>
                              <th>No Manifest</th>
                              <th>No Account</th>
                              <th>Penerima</th>
                              <th>Action</th>
                            </tr>
                            @foreach($list as $index=>$value)
                            <tr>
                              <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                              <td>{{ date('Y-m-d', strtotime($value->created_at)) }}</td>
                              <td>{{ $value->barcode_env }}</td>
                              <td>{{ $value->no_manifest }}</td>
                              <td>{{ $value->account_no }}</td>
                              <td>{{ $value->penerima }}</td>
                              <td><a href="detail/detail/{{ $value->id }}" data-toggle="modal" data-target="#modal-{{ $value->id }}" class="text-primary"><i class="fa fa-eye"></i></a>
                              </td>
                            </tr>
                            @endforeach
                        </table>
                          {{ $list }}
                    </div>
                </div>
            </div>
        </div>
    </div>    

  @foreach($list as $index=>$value)
      <div class="modal fade" id="modal-{{ $value->id }}">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Detail Account {{ $value->account_no }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                    <dl class="row" class="dl-horizontal">
                      <dt class="col-4">Seq</dt>
                      <dd class="col-8">{{ $value->seq }}</dd>
                      <dt class="col-4">Barcode Amplop</dt>
                      <dd class="col-8">{{ $value->barcode_env }}</dd>
                      <dt class="col-4">Account No</dt>
                      <dd class="col-8">{{ $value->account_no }}</dd>
                      <dt class="col-4">Penerima</dt>
                      <dd class="col-8">{{ $value->penerima }}</dd>
                      <dt class="col-4">Tertanggung</dt>
                      <dd class="col-8">{{ $value->tertanggung }}</dd>
                      <dt class="col-4">Alamat</dt>
                      <dd class="col-8">{{ $value->address1 }}</dd>
                      <dt class="col-4"></dt>
                      <dd class="col-8">{{ $value->address2 }}</dd>
                      <dt class="col-4"></dt>
                      <dd class="col-8">{{ $value->address3 }}</dd>
                      <dt class="col-4"></dt>
                      <dd class="col-8">{{ $value->city }} {{ $value->pos }}</dd>
                      <dt class="col-4">Ekspedisi/Service</dt>
                      <dd class="col-8">{{ $value->ekspedisi }}/{{ $value->service }}</dd>
                      <dt class="col-4">No Manifest</dt>
                      <dd class="col-8">{{ $value->no_manifest }}</dd>
                    </dl>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <!-- /.modal -->
    @endforeach
@endsection

@section('js')
<script type="text/javascript">
    $(function () {
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