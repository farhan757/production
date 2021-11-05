@extends('adminlte::page')

@section('title', 'List PO Material')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">List Invoice</h3>
      </div>
        <div class="card-body">
          <form action="" method="get" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row" style="padding-bottom: 15px">
              <div class="col-sm-4">
                    <input type="text" class="form-control pull-right" name="no_inv" id="no_inv" value="{{ $no_inv ?? '' }}" placeholder="No Invoice">
              </div>
              <div class="col-sm-2">
                  <div class="input-group date">                                      
                    <input type="text" class="form-control pull-right" name="tglgen" id="tglgen" placeholder="Tanggal Invoice" value="{{ $tglgen ?? '' }}">
                  </div>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button> 
                <button type="button" class="btn btn-danger" onclick="clearFilter()"><i class="fa fa-trash"></i></button>
              </div>
            </div>
          </form>
          <div class="card-body table-responsive p-0" style="height: 400px;">
            <table  class="table table-bordered table-head-fixed">
              <tr>
                <th style="width: 10px">#</th>
                <th>No Invoice</th>
                <th>Tanggal Generate</th>
                <th>Customer/Aplikasi</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
              @foreach($listinv as $index=>$value)
              <tr>
                <td>{{ ($listinv->perPage()*($listinv->currentPage()-1)) +$loop->iteration }}</td>
                <td>{{ $value->no_inv }}</td>
                <td>{{ $value->generate_date }}</td>
                <td>{{ $value->nm_cust }} / {{ $value->nm_pro }}</td>
                <td>{{ $value->nm_rs }}</td>
                <td> 
                <a href="#" title="view detail" onclick="showDetail({{ $value->id }})" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;
                <a href="#" title="Update" onclick="showForm({{ $value->id }})"><i class="fa fa-list"></i></a>&nbsp;
                <a href="#" title="Print Invoice" onclick="cetak({{ $value->id }})"><i class="fa fa-print"></i></a>&nbsp;                
                </td>
              </tr>
              @endforeach
          </table>
          </div>
          {{ $listinv }}
        </div>
    </div>
</div>  
@include('adm.listinv.form')
@include('adm.listinv.detaildata')
@stop

@section('js')
<script type="text/javascript">
    var rootUrl = 'po';
    var curentId;
    status();
    function cetak(id){
      alert(id);
      
      $.ajax({
              url:  'cetak/' + id,
              type: "GET",
              data: { "_token": "{{ csrf_token() }}", },
              dataType: "JSON",
              success: function(response) {
                var param = "?period="+response.data.period+"&project_id="+response.data.projects_id+"&tgldari="+response.data.from_date+"&tglsampai="+response.data.until_date+"&pkp="+response.data.pkp+"&b_pkp="+response.data.b_pkp+"&kredit="+response.data.kredit+"&tunai="+response.data.tunai+"&t_materai="+response.data.materai+"&t_ppn="+response.data.ppn+"&no_inv="+response.data.no_inv;      
                        window.open('preview/'+param,'_blank','toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=1000,height=1000')
                        window.open('perincian/'+param,'_blank','toolbar=no,scrollbars=yes,resizable=yes,top=1000,left=1000,width=1000,height=1000')                                                
              },
              error : function() {
                Swal.fire({
                  icon:'error',
                  title: 'Error'
                });
              }
      });
    }

    function clearFilter() {
      $('#no_inv').val('');
      $('#tglgen').val('');
    }

    function status(){
      var id = $('#result_id').val();
      $('#tglbayar-div').hide();
      if(id == 18){
        $('#tglbayar-div').show();
      }
    }

    function showForm(id) {
      status();
        save_method = "add";     
        status();     
        $('#id').val(id);
        $('input[name=_method]').val('POST');
        $('#modal-form').modal('show');
        $('#modal-form form')[0].reset();
        $('#modal-title').text('Form Update');
        status();
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
      var uri = "{{ route('listdetailinv',':currentId') }}";
      $.ajax({
          url: uri.replace(':currentId', id),
          type: "GET",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-detail-inv').modal('show');
              $('#title-detail-inv').text('Detail '+data.data.no_inv);

              $('#no_inv').html(data.data.no_inv);
              $('#nm_cust').html(data.data.nm_cust);              
              $('#nm_pro').html(data.data.nm_pro);
              $('#kd_pro').html(data.data.kd_pro);
              $('#tgl_gen').html(data.data.generate_date);
              $('#tgl_jt').html(data.data.jatuhtempo_date);
              $('#tgl_bayar').html(data.data.pay_date);

              var tbukti = '';
              for(var i=0, l = data.bukti.length; i< l; i++){
                var obj = data.bukti[i];
                tbukti += `<tr>
                      <td>${obj.file_name}</td>
                      <td>${obj.note}</td>
                      <td><a href='download/${obj.file_id}'>Download</a></td>
                  </tr>`;
              }
              $('#tbody-bukti').html(tbukti);
          }
        });
    }

    $(function() {      

      $('#tglgen').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd"
      });

      $('#tglbayar').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd"
      });      

      $('#form-item').submit(function(e) {
        e.preventDefault();
        var url = "{{ route('updatestatusinv') }}";
          var formData = new FormData($('#form-item')[0]);
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