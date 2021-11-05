@extends('adminlte::page')

@section('title', 'Change Courier')

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
                                      <input type="text" class="form-control pull-right" name="no_amplop" id="no_amplop" value="{{ $no_amplop ?? '' }}" required>
                                </div>
                            </div>
                            <!--<div class="form-group row">
                                <label for="no_account" class="col-sm-2 control-label">No Account</label>
                                <div class="col-sm-4">
                                      <input type="text" class="form-control pull-right" name="no_account" id="no_account" value="{{ $no_account ?? '' }}">
                                </div>
                            </div>                            
                            <div class="form-group row">
                                <label for="nama" class="col-sm-2 control-label">Nama</label>
                                <div class="col-sm-6">
                                      <input type="text" class="form-control pull-right" name="nama" id="nama" value="{{ $nama ?? '' }}">
                                </div>
                            </div>-->

                            <div class="form-group row">
                                <label for="project" class="col-sm-2 control-label">Project</label>

                                <div class="col-sm-4">
                                    <select class="form-control select2" name="project_id" id="project_id" required>
                                      <option value="">None</option>
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
                                      
                                      <input type="text" class="form-control pull-right" name="cycle" id="cycle" value="{{ $cycle ?? '' }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="part" class="col-sm-2 control-label">Part</label>

                                <div class="col-sm-4">
                                    <select class="form-control select2" name="part" required>
                                        <option value="">None</option>
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
                                    <select class="form-control select2" name="jenis" required>
                                        <option value="">None</option>
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
                        </form>
                        
            </div>
        </div>
    </div>

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
         
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"></h3>
                        <!-- /.box-tools -->
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                    <div class="card-body table-responsive p-0" style="height: 400px;">
                      <table  class="table table-bordered table-head-fixed">
                            <tr>
                              <th style="width: 10px">#</th>
                              <th>Tanggal</th>
                              <th>No Amplop</th>
                              <th>No Account</th>
                              <th>Penerima</th>
                              <th>Action</th>
                            </tr>
                           @if(!is_null($list)) 
                            @foreach($list as $index=>$value)
                            <tr>
                              <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                              <td>{{ date('Y-m-d', strtotime($value->created_at)) }}</td>
                              <td>{{ $value->barcode_env }}</td>
                              <td>{{ $value->account_no }}</td>
                              <td>{{ $value->penerima }}</td>
                              <td>
                              @if(is_null($value->no_manifest))
                                <a href="#" onclick="hideLoad()" data-toggle="modal" data-target="#modal-{{ $value->id }}" class="text-primary"><i class="fa fa-eye"></i></a>
                              @else
                              <span class="badge bg-danger">{{ $value->no_manifest }} {{ '( Not Change )' }}</span>
                              @endif
                              </td>
                            </tr>
                            @endforeach
                          @endif
                        </table>
                        @if(!is_null($list)) 
                          {{ $list }}
                        @endif
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
@if(!is_null($list)) 
  @foreach($list as $index=>$value)
      <div class="modal fade" id="modal-{{ $value->id }}">
        <div class="modal-dialog modal-md">
        <div class="overlay-wrapper"> 
          <div class="overlay dark" id="vload" ><i class="fas fa-5x fa-sync-alt fa-spin"></i>&nbsp; &nbsp; &nbsp;<div class="text-bold pt-2" type="hide">Loading...</div></div>        
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Detail Account {{ $value->account_no }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <form name="form-item" id="form-item" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}              
            </div>
            <input type="hidden" name="id" id="id" value="{{ $value->id }}">
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
                      <dt class="col-4">Change Courier To</dt>
                      <dd class="col-8"><input type="text" class="form-control pull-right" name="courier" id="courier"></dd>
                      <dt class="col-4">Change Service To</dt>
                      <dd class="col-8"><input type="text" class="form-control pull-right" name="service" id="service"></dd>

                    </dl>
                </div>
            </div>
            <div class="modal-footer">              
              <button type="submit" class="btn btn-success pull-left" >Save</button>
            </div>
          </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      </div>
    <!-- /.modal -->
    @endforeach
@endif
@endsection

@section('js')
<script type="text/javascript">

    function hideLoad(){       
       $('#vload').hide();
    }

    function showLoad()
    {
      $('#vload').show();
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
        $('.select2').select2();
        $('#form-item').submit(function(e) {
          e.preventDefault();
          var url = "change-courier";
            var formData = new FormData($('#form-item')[0]);
            showLoad();
            $.ajax({
              url: url,
              type: 'POST',
              data: formData,
              contentType: false,
              processData:false,
              success: function(response) {
                //alert(JSON.stringify(response));
                hideLoad();
                if(response.status==1) {
                    Swal.fire({
                      icon: 'success',
                      title: response.message,
                      onClose: () => {
                        window.location.reload();
                      }
                    });
                } else {
                    if(response.status==2) {
                      var errors = '';
                      for (var i = 0, l = response.error.length; i < l; i++) {
                        errors+="<p>"+response.error[i]+"</p>";
                      }
                      Swal.fire({
                        icon: 'error',
                        title: response.message + " " + response.error,
                        text: errors
                      });
                    } else {
                      Swal.fire({
                        icon: 'error',
                        title: response.message,
                        onClose: () => {
                          //window.location.href = '{{ route('requestincomingdata') }}';
                        }
                      });                    
                    }
                }
              },
              error: function(response) {
                hideLoad();
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                  });
              }
            });
        });        
    });
</script>
@endsection