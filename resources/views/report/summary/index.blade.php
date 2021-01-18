@extends('adminlte::page')

@section('title', 'Report Summary')

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
                        <h3 class="box-title">List Data</h3>
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
                        <table class="table table-bordered">
                            <tr>
                              <th style="width: 10px">#</th>
                              <th>Tanggal</th>
                              <th>Job Ticket</th>
                              <th>Cycle/Part</th>
                              <th>Project</th>
                              <th>Status</th>
                              <th>Jumlah</th>
                              <th>Action</th>
                            </tr>
                            @foreach($list as $index=>$value)
                            <tr>
                              <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                              <td>{{ date('Y-m-d', strtotime($value->created_at)) }}</td>
                              <td>{{ $value->job_ticket }}</td>
                              <td>{{ $value->cycle }} / {{ $value->part }}</td>
                              <td>{{ $value->project_name }}</td>
                              <td>{{ $value->status_name }} / {{ $value->result_name }}</td>
                              <td>{{ $value->jumlah }}</td>
                              <td><a href="" onclick="showDetail({{ $value->id }})" data-toggle="modal" data-target="#modal-{{ $value->id }}" class="text-primary"><i class="fa fa-eye"></i></a></td>
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

@include('partial.detailproduction')

@endsection

@section('js')
<script type="text/javascript">
  function showDetail(id) {
      $.ajax({
          url: '../production/joblist' + '/detail/' + id,
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-detail-production').modal('show');
              $('#title-detail-production').text('Detail '+data.data.job_ticket);

              $('#customer_name').html(data.data.customer_name);
              $('#project_name').html(data.data.project_name);              
              $('#created_at').html(data.data.created_at);
              $('#project_name').html(data.data.project_name);
              $('#usersubmit').html(data.data.username);
              $('#last_status').html(data.data.last_status);
              $('#file_name').html(data.data.file_name);
              $('#cyclePart').html(data.data.cycle+"/"+data.data.part+"/"+data.data.jenis);
              $('#info').html(data.data.info);
              var tbody = '';
              for(var i=0, l = data.list.length; i< l; i++) {
                var obj = data.list[i];
                tbody += `<tr>
                      <td>${obj.barcode_env}</td>
                      <td>${obj.account_no}</td>
                      <td>${obj.penerima}</td>
                      <td>${obj.ekspedisi} ${obj.service}</td>
                      <td>${obj.no_manifest}</td>
                  </tr>`;
              }

              $('#tbody-detail').html(tbody);

              $('#titleCollapsibletransp').html('Track for Job Ticket #'+data.data.job_ticket);               
              var html='';
              for (var i = 0, l = data.transP.length; i < l; i++) {
                var obj = data.transP[i];
                html += `<div>
                                <i class="fas ${obj.status_icon} bg-blue"></i>
                                <div class="timeline-item">
                                  <span class="time"><i class="fas fa-clock-o"></i> ${obj.created_at}</span>
                                  <h3 class="timeline-header"><a href="#">${obj.username}</a> ${obj.status_name}</h3>

                                  <div class="timeline-body">
                                    <p>${obj.note}</p>
                                    <p>Result : ${obj.result_name}</p>
                                  </div>                                  
                                </div>
                              </div>`;
              }
              $('#timelinetransP').html(html);

              html='';
              if (data.transF!=null) {
                var obj = data.transF;
                html+=`<div class="card card-primary">
                    <div class="card-header">
                      <h4 class="card-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTransf${obj.id}" id="titleCollapsibletransf">
                          Track for Ticket #${obj.ticket}
                        </a>
                      </h4>
                    </div>
                    <div id="collapseTransf${obj.id}" class="panel-collapse collapse in">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-12">
                            <!-- The time line -->
                            <div class="timeline" id="timelinetransF">`;
                    for (var j = 0, k = obj.data.length; j < k; j++) {
                      var obj2 = obj.data[j];
                              html+=`<div>
                                <i class="fas ${obj2.status_icon} bg-blue"></i>
                                <div class="timeline-item">
                                  <span class="time"><i class="fas fa-envelope"></i> ${obj2.created_at}</span>
                                  <h3 class="timeline-header"><a href="#">${obj2.username}</a> ${obj2.status_name}</h3>

                                  <div class="timeline-body">
                                    <p>${obj2.note}</p>
                                    <p>Result : ${obj2.result_name}</p>
                                  </div>                                  
                                </div>
                              </div>`
                    }
                             html+= `</div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>`;
              }
              $('#transF').html(html);
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