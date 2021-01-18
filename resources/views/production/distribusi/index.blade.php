@extends('adminlte::page')

@section('title', 'Form Upload File Data')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Form Upload File Data</h3>
      </div>
        <div class="card-body">
          <form action="" method="get" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row" style="padding-bottom: 15px">
              <div class="col-sm-4">
                    <input type="text" class="form-control pull-right" name="ticket" id="ticket" value="{{ $ticket ?? '' }}" placeholder="Ticket">
              </div>
              <div class="col-sm-4">
                    <input type="text" class="form-control pull-right" name="no_manifest" id="filterNoManifest" value="{{ $no_manifest ?? '' }}" placeholder="No Manifest">
              </div>  
              <div class="col-sm-2">
                  <div class="input-group date">                                      
                    <input type="text" class="form-control pull-right" name="filterCycle" id="filterCycle" placeholder="Cycle" value="{{ $filterCycle ?? '' }}">
                  </div>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button> 
                <button type="button" class="btn btn-danger" onclick="clearFilter()"><i class="fa fa-trash"></i></button>
              </div>
            </div>
          </form>                      
              <table class="table table-bordered">
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Tanggal</th>
                    <th>No Manifest</th>
                    <th>Cycle/Part</th>
                    <th>Project</th>
                    <th>Ekspedisi/Service</th>
                    <th>Action</th>
                  </tr>
                  @foreach($list as $index=>$value)
                  <tr>
                    <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                    <td>{{ $value->created_at }}</td>
                    <td>{{ $value->no_manifest }}</td>
                    <td>{{ $value->cycle }} / {{ $value->part }}</td>
                    <td>{{ $value->customer_name }} - {{ $value->project_name }}</td>
                    <td>{{ $value->ekspedisi }}/{{ $value->service }}</td>
                    <td>
                      <a onclick="showDetail({{ $value->no_manifest }})" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;
                      <a href="distribusi/download/{{ $value->no_manifest }}" class="text-success"><i class="fa fa-download"></i></a>&nbsp;
                      @if($value->tgl_kirim)
                        @if($value->print==0)
                          <a onclick="printmanifest('{{ $value->no_manifest }}')" class="text-info"><i class="fa fa-print"></i></a>&nbsp;
                        @endif

                      @else
                      <a onclick="showFormUpdate('{{ $value->no_manifest }}')" class="text-warning"><i class="far fa-calendar-check"></i> tgl kirim</a>
                      @endif
                      
                      
                    </td>
                  </tr>
                  @endforeach
              </table>
              {{ $list }}
            </div>
        </div>
    </div>
  @include('production.distribusi.form')
  @include('production.distribusi.detaildata')
@stop
@section('js')
<script type="text/javascript">
    var rootUrl = 'distribusi';

    function hideError() {

    }

    function printmanifest($no_manifest) {
      window.open('distribusi/print/'+$no_manifest,'print','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,width=500,height=500');
    }

    function clearFilter() {
      $('#ticket').val('');
      $('#filterCycle').val('');
      $('#filterNoManifest').val();
    }

    function showFormUpdate(no_manifest) {

            $('input[name=_method]').val('POST');
            $('#modal-form').modal('show');
            $('#modal-form form')[0].reset();
            $('#modal-title').text('Input Tanggal Kirim dan Kurir Pickup '+ no_manifest);
            $('#no_manifest').val(no_manifest)
    }

    function showDetail(id) {
      $.ajax({
          url: rootUrl + '/detail/' + id,
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

    $(function() {

      $('#filterCycle').datepicker({
        autoclose: true,
        format: "yyyymmdd"
      });
      $('#cycle').datepicker({
        autoclose: true,
        format: "yyyymmdd"
      });

      $(function () {
          $('#tgl_kirim').datepicker({
            autoclose: true,
            format: "yyyymmdd"
          });
      });

      $('#form-distribusi').submit(function(e) {
        e.preventDefault();
        var url = rootUrl+'/'+'update';
          var formData = new FormData($('#form-distribusi')[0]);
          $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData:false,
            success: function(response) {
              //alert(JSON.stringify(response));
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
