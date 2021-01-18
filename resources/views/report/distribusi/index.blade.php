@extends('adminlte::page')

@section('title', 'Report Distribusi')

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
                                <label for="no_manifest" class="col-sm-2 control-label">No Manifest</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                      
                                      <input type="text" class="form-control pull-right" name="no_manifest" id="no_manifest" value="{{ $no_manifest ?? '' }}">
                                    </div>
                                </div>
                            </div>
                          <div class="form-group row">
                                <label for="tgl_kirim" class="col-sm-2 control-label">Tanggal Pickup</label>
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
                            <div class="form-group row">
                                <label for="ekspedisi" class="col-sm-2 control-label">Ekspedisi</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                      
                                      <input type="text" class="form-control pull-right" name="ekspedisi" id="filter-ekspedisi" value="{{ $ekspedisi ?? '' }}">
                                    </div>
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
                        <h3 class="box-title">List Manifest for Distribusi</h3>
                        <!-- /.box-tools -->
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">

                        <table class="table table-bordered">
                            <tr>
                              <th style="width: 10px">#</th>
                              <th>Tanggal</th>
                              <th>No Manifest</th>
                              <th>Cycle/Part</th>
                              <th>Project</th>
                              <th>Ekspedisi/Service</th>
                              <th>Jumlah</th>
                              <th>Action</th>
                            </tr>
                            @foreach($list as $index=>$value)
                            <tr>
                              <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                              <td>{{ date('Y-m-d', strtotime($value->created_at)) }}</td>
                              <td>{{ $value->no_manifest }}</td>
                              <td>{{ $value->cycle }} / {{ $value->part }}</td>
                              <td>{{ $value->customer_name }} - {{ $value->project_name }}</td>
                              <td>{{ $value->ekspedisi }}/{{ $value->service }}</td>
                              <td>{{ $value->jumlah }}</td>
                              <td><a href="" onclick="showDetail('{{ $value->no_manifest }}')" data-toggle="modal" data-target="#modal-{{ $value->no_manifest }}" class="text-primary"><i class="fa fa-eye"></i></a> &nbsp;
                                <a href="../production/distribusi/download/{{ $value->no_manifest }}" class="text-success"><i class="fa fa-download"></i></a>
                              </td>
                            </tr>
                            @endforeach
                        </table>
                        {{ $list }}
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </div>
    @include('production.distribusi.detaildata')
@endsection

@section('js')
<script type="text/javascript">
  function showDetail(id) {
      $.ajax({
          url: '../production/distribusi' + '/detail/' + id,
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {

              $('#modal-detail').modal('show');
              $('#title-detail').text('Detail '+data.data.no_manifest);

              $('#created_at').html(data.data.created_at);
              $('#cyclePart').html(data.data.cycle+"/"+data.data.part+"/"+data.data.jenis);
              $('#ekspedisi').html(data.data.ekspedisi+ "/" + data.data.service);
              $('#tgl_kirim').html(data.data.tgl_kirim);
              var tbody = '';
              for(var i=0, l = data.list.length; i< l; i++) {
                var obj = data.list[i];
                tbody += `<tr>
                      <td>${obj.barcode_env}</td>
                      <td>${obj.penerima}</td>
                      <td>${obj.ekspedisi} ${obj.service}</td>
                  </tr>`;
              }

              $('#table-body').html(tbody);

              
          },
          error : function() {
              alert("Nothing Data");
          }
        });
    }

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