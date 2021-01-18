@extends('adminlte::page')

@section('title', 'List PO Material')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Data PO Material</h3>
      </div>
        <div class="card-body">
          <form action="" method="get" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row" style="padding-bottom: 15px">
              <div class="col-sm-4">
                    <input type="text" class="form-control pull-right" name="nopo" id="nopo" value="{{ $nopo ?? '' }}" placeholder="No PO">
              </div>
              <div class="col-sm-2">
                  <div class="input-group date">                                      
                    <input type="text" class="form-control pull-right" name="tglpo" id="tglpo" placeholder="Tanggal PO" value="{{ $tglpo ?? '' }}">
                  </div>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button> 
                <button type="button" class="btn btn-danger" onclick="clearFilter()"><i class="fa fa-trash"></i></button>
              </div>
            </div>
          </form>
          <div class="col-xs-5 table-responsive">
          <table class="table table-bordered">
              <tr>
                <th style="width: 10px">#</th>
                <th>No PO</th>
                <th>Tanggal PO</th>
                <th>Kode/Vendor</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
              @foreach($listpo as $index=>$value)
              <tr>
                <td>{{ ($listpo->perPage()*($listpo->currentPage()-1)) +$loop->iteration }}</td>
                <td>{{ $value->no_po }}</td>
                <td>{{ $value->tgl_po }}</td>
                <td>{{ $value->code }} / {{ $value->name }}</td>
                <td>
                @if($value->complete == 1)
                  <span class="badge bg-success">Completed</span>
                @else
                  <span class="badge bg-danger">Waiting</span>
                @endif
                </td>
                <td> 
                <a href="#" title="view detail" onclick="showDetail('{{ $value->no_po }}')" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;
                
                <a href="#" title="Print PO" onclick="cetak('{{ $value->no_po }}')"><i class="fa fa-print"></i></a>&nbsp;
                @if($value->print == 0)
                <a href="#" title="delete" onclick="deleteUser('{{ $value->no_po }}')" class="text-danger"><i class="fas fa-trash"></i></a>
                @endif
                </td>
              </tr>
              @endforeach
          </table>
          </div>
          {{ $listpo }}
        </div>
    </div>
</div>  
  @include('po.listpo.detaildata')
@stop

@section('js')
<script type="text/javascript">
    var rootUrl = 'po';
    var curentId;

    function cetak(id){
      var nopo = id;
      window.open('printpo/'+nopo,'_blank');
      window.reload();
    }

    function clearFilter() {
      $('#nopo').val('');
      $('#tglpo').val('');
    }

    function deleteUser(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.value) {
          $.ajax({
              url:  'delete/' + id,
              type: "POST",
              data: { "_token": "{{ csrf_token() }}", },
              dataType: "JSON",
              success: function(data) {
                Swal.fire({
                  icon: 'success',
                  title: data.message,
                  onClose: () => {
                    window.location.reload();
                  }
                });
              },
              error : function() {
                Swal.fire({
                  icon:'error',
                  title: 'Error Delete Data'
                });
              }
          });
        }
      })
    }

    function showDetail(id) {
      currentId = id;      
      var uri = "{{ route('podetail',':currentId') }}";
      $.ajax({
          url: uri.replace(':currentId', currentId),
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-detail-material').modal('show');
              $('#title-detail-material').text('Detail '+data.data.no_po);

              $('#codevendor').html(data.data.code);
              $('#namavendor').html(data.data.name);              
              $('#tgl_po').html(data.data.tgl_po);
              $('#createby').html(data.data.nama);
              var tbody = '';
              for(var i=0, l = data.list.length; i< l; i++) {
                var obj = data.list[i];
                tbody += `<tr>
                      <td>${obj.code}</td>
                      <td>${obj.name}</td>
                      <td>${obj.qty_order}</td>
                      <td>${obj.qty_arrive}</td>
                      <td>${obj.satuan}</td>
                  </tr>`;
              }
              $('#tbody-detail').html(tbody);
          },
          error : function() {
              alert("Nothing Data");
          }
        });
    }

    $(function() {
      $('#btn_material').click(function() {
       window.open('printing/material/'+currentId, '_blank');
      });

      $('#tglpo').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd"
      });

      $('#form-status').submit(function(e) {
        e.preventDefault();
        var url = "{{ route('updateappmaterial') }}";
          var formData = new FormData($('#form-status')[0]);
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