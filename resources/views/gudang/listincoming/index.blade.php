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
                  <span class="badge bg-danger">UnComplete</span>
                @endif
                </td>
                <td> 

                @if($value->complete == 0)
                  <a href="" title="Update PO" class="text-info" data-toggle="modal" data-target="#modal-detail-{{ $value->no_po }}"><i class="fas fa-edit"></i></a>&nbsp;                
                @endif
                <a href="#" title="view detail" onclick="showDetail('{{ $value->no_po }}')" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;              
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
            @foreach($listpo as $index=>$value)
            <form enctype="multipart/form-data" class="form-horizontal" method="post" name='form-update-{{ $value->no_po }}' id='form-update-{{ $value->no_po }}' action="{{ route('saveupdate') }}">
            {{ csrf_field() }}
            <div class="modal fade" id="modal-detail-{{ $value->no_po }}">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">              
                      <h4 class="modal-title" id="title-detail">Detail {{ $value->no_po }}</h4>
                  </div>
                  <div class="modal-body">

                    <div class="row">
                      <div class="col-md-10">
                        <dl class="row" class="dl-horizontal">
                          <dt class="col-4">Kode Vendor</dt>
                          <dd class="col-8" id="codevendor">{{ $value->code }}</dd>
                          <dt class="col-4">Nama Vendor</dt>
                          <dd class="col-8" id="namavendor">{{ $value->name }}</dd>
                          <dt class="col-4">Create by</dt>
                          <dd class="col-8" id="createby">{{ $value->nama }}</dd>
                          <dt class="col-4">Tanggal PO</dt>
                          <dd class="col-8" id="tgl_po">{{ $value->tgl_po }}</dd> 
                          <dt class="col-4">Masukan Bukti PO</dt>
                          <dd class="col-8"><input type="file" id="file" name="file"></dd>              
                        </dl>            
                      </div>
                    </div>                                        
                        <input type="hidden" name="nopo" id="nopo" value="{{ $value->no_po }}">                    
                        <div class="row">
                          <div class="col-xs-5 table-responsive">
                            <table class="table table-bordered">
                              <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Deskripsi</th>
                                    <th>Qty Order</th>
                                    <th>Qty Minus</th>
                                    <th>Qty In</th>
                                    <th>Satuan</th>
                                </tr>            
                              </thead>
                              <tbody>
                                  @inject('detail','App\Http\Controllers\Gudang\ListIncPOController')
                                  
                                  @foreach($detail->detail($value->no_po) as $key=>$val)   
                                    <input type="hidden" name="code[]" id="code[]" value="{{ $val->id }}">                                                           
                                    <tr>
                                        <td>{{ $val->code }}</td>
                                        <td>{{ $val->name }}</td>
                                        <td>{{ $val->qty_order }}</td>
                                        <td>{{ $val->qty_minus }}</td>
                                        <input type="hidden" name="qty_order[]" id="qty_order[]" value="{{ $val->qty_order }}">
                                        <td><input type="number" name="qty_arrive[]" id="qty_arrive-{{ $val->qty_arrive }}" value="{{ $val->qty_arrive }}"></td>
                                        <td>{{ $val->satuan }}</td>
                                    </tr> 
                                  @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                          @if($value->complete == 0)
                          <button type="submit" class="btn btn-primary" name="submit" id="submit">
                          {{ __('Save') }}
                          </button>                
                          @endif          
                        </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
            @include('gudang.listincoming.detaildata')
@stop

@section('css')

@stop

@section('js')
<script type="text/javascript">
    var rootUrl = 'gudang';
    var curentId;

    function clearFilter() {
      $('#nopo').val('');
      $('#tglpo').val('');
    }

    function showDetail(id) {
      currentId = id;      
      var uri = "{{ route('listdetail',':currentId') }}";
      $.ajax({
          url: uri.replace(':currentId', currentId),
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-detail-material2').modal('show');
              $('#title-detail-material2').text('Detail '+data.data.no_po);

              $('#codevendor2').html(data.data.code);
              $('#namavendor2').html(data.data.name);              
              $('#tgl_po2').html(data.data.tgl_po);
              $('#createby2').html(data.data.nama);
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
              $('#tbody-detail2').html(tbody);

              var tbukti = '';
              for(var i=0, l = data.bukti.length; i< l; i++){
                var obj = data.bukti[i];
                tbukti += `<tr>
                      <td>${obj.file_name}</td>
                      <td><a href='download/${obj.file_id}'>Download</a></td>
                  </tr>`;
              }
              $('#tbody-bukti').html(tbukti);
          },
          error : function() {
              alert("Nothing Data");
          }
        });
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

    $(function() {
      
      $('#btn_material').click(function() {
       window.open('printing/material/'+currentId, '_blank');
      });

      $('#tglpo').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd"
      });
    });
</script>
@endsection