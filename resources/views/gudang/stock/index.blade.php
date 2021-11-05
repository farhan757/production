@extends('adminlte::page')

@section('title', 'Form Stock Material')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')

  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Form Stock Material</h3>
        <ol class="breadcrumb float-sm-right">
          <h3 class="card-title pull-right"><strong>STOCK {{ $comp->name ?? '' }} : {{ $comp->stock ?? '0' }}</strong></h3>
        </ol>                               
      </div>          
            <form class="form-horizontal" name="form-item" id="form-item" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card-body">

                    <div class="form-group row">
                        <label for="cycle" class="col-sm-2 control-label">Transaksi Dari</label>
                        <div class="col-sm-4">
                            <div class="input-group date">
                              
                              <input type="text" class="form-control pull-right datepicker" name="dari" id="dari" value="{{ $tgldari ?? '' }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cycle" class="col-sm-2 control-label">Transaksi Sampai</label>
                        <div class="col-sm-4">
                            <div class="input-group date">
                              
                              <input type="text" class="form-control pull-right datepicker" name="sampai" id="sampai" value="{{ $tglsampai ?? '' }}" required>
                            </div>
                        </div>
                    </div>                    

                    <div class="form-group row">
                        <label for="components" class="col-sm-2 control-label">Components</label>

                        <div class="col-sm-6">
                            <select class="form-control select2" name="components_id" id="components_id" required> 
                              @foreach($components as $index=>$value)
                                <option value="{{ $value->id }}" 
                                    @if(isset($nojob))
                                    @if($nojob == $value->id) 
                                        selected
                                    @endif 
                                    @endif
                                    >{{ $value->code.'-'.$value->name }}</option>
                                @endforeach                                                                   
                              </select>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-info" name="submit" id="submit" value="submit">Submit</button>                                       
                    <button type="submit" class="btn btn-danger" name="export" id="export" value="export">Export</button>                                       
                </div>

            </form>
	</div>
</div>


    
        <div class="row">
            <div class="col-md-12">

                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">List Item Material</h3>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <div class="row">
                      </div>
                      <div class="card-body table-responsive p-0" style="height: 400px;">
                        <table  class="table table-bordered table-head-fixed">
                            <tr>
                              <th>Tgl Trans/Cycle/Part</th>
                              <th>No Job</th>
                              <th>Nama Material</th>
                              <th>Code</th>
                              <th>Qty Out</th>
                              <th>Qty In</th>
                            </tr>
                            
                            @if(isset($nojob))
                         
                              <!-- transaksi keluar dari job -->  
                              
                               @foreach($listjob as $value)
                              <tr>
                                <td>{{ $value->tgl_out }}/{{ $value->cycle }}/{{ $value->part }}</td>                                
                                <td>{{ $value->no_job }}</td>
                                <td>{{ $value->nama_material }}</td>
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->qty }}</td>
                                <td>{{ 0 }}</td>
                              </tr>
                              @endforeach
                              <!-- transaksi keluar dari testprint-->
                              @foreach($listtest as $value)
                              <tr>
                                <td>{{ $value->tgl_out }}</td>                                
                                <td>{{ $value->no_job }}</td>
                                <td>{{ $value->nama_material }}</td>
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->qty }}</td>
                                <td>{{ 0 }}</td>
                              </tr>
                              @endforeach  
                              <!-- transaksi masuk po-->
                              @if(isset($listmsk))
                              @foreach($listmsk as $value)
                              <tr>
                                <td>{{ $value->tgl_in ?? 'masuk po' }}</td>                                
                                <td>{{ $value->no_job ?? '' }}</td>
                                <td>{{ $value->nama_material ?? '' }}</td>
                                <td>{{ $value->code ?? ''}}</td>                                
                                <td>{{ 0 }}</td>
                                <td>{{ $value->qty ?? ''}}</td>
                              </tr>
                              @endforeach 
                              @endif                                                         
                            @endif
                        </table>
                      </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    


@stop


@section('js')


    <script>
    var rootUrl = "gudang";

    function bersihkan(){
      window.location.href = '{{ route('createoutmaterial') }}';      
    }

    function aftersave(){
      $('#note').val('');
      $('#qty').val('');      
    }

    function batal(id) {
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
              url:  'batal/' + id,
              type: "POST",
              data: { "_token": "{{ csrf_token() }}", },
              dataType: "JSON",
              success: function(data) {
                Swal.fire({
                  icon: 'success',
                  title: data.message,
                  onClose: () => {
                    bersihkan();
                  }
                });
              },
              error : function() {
                Swal.fire({
                  icon:'error',
                  title: 'Error Cancel'
                });
              }
          });
        }
      })
    }

    $(function(){
      $('.datepicker').datepicker({
          autoclose: true,
          format: "yyyy-mm-dd"
        });

      //Initialize Select2 Elements
      $('.select2').select2();

     });
  </script>
@stop
