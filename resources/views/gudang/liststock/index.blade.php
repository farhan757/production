@extends('adminlte::page')

@section('title', 'List PO Material')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Data Material</h3>
      </div>
        <div class="card-body">

        <div class="row">
                        <div class="col-lg-3 col-xs-6">
                          <!-- small box -->
                          <div class="small-box bg-blue">
                            <div class="inner">
                              <h6 id="b_balance">0</h6>

                              <p>Beginning Balance</p>
                            </div>

                          </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                          <!-- small box -->
                          <div class="small-box bg-red">
                            <div class="inner">
                              <h6 id="u_balance">0</h6>

                              <p>Balance used</p>
                            </div>

                          </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                          <!-- small box -->
                          <div class="small-box bg-yellow">
                            <div class="inner">
                              <h6 id="in_balance">0</h6>

                              <p>Incoming Balance</p>
                            </div>

                          </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                          <!-- small box -->
                          <div class="small-box bg-green">
                            <div class="inner">
                              <h6 id="c_balance">0</h6>

                              <p>Current Balance</p>
                            </div>

                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                          </div>
                        </div>
                        <!-- ./col -->
                      </div>

          <form  method="get" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row" style="padding-bottom: 15px">
              <div class="col-sm-4">
                    <input type="text" class="form-control pull-right" name="code" id="code" value="{{ $code ?? '' }}" placeholder="Kode Material">
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-default" name="submit" id="submit" value="submit"><i class="fas fa-search"></i></button> 
                
                <button type="button" class="btn btn-danger" onclick="clearFilter()"><i class="fa fa-trash"></i></button>
                <button type="submit" class="btn btn-warning" name="export" id="export" value="export"><i class="fas fa-download"></i></button> 
              </div>
            </div>
          </form>
          <div class="card-body table-responsive p-0" style="height: 400px;">
            <table  class="table table-bordered table-head-fixed">
              <tr>
                <th style="width: 10px">#</th>
                <th>Group</th>
                <th>Code</th>
                <th>Name</th>
                <th>Stock</th>
                <th>Status</th>
              </tr>
              @foreach($list as $index=>$value)
              <tr>
                <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                <td>{{ $value->cust_name }}</td>
                <td>{{ $value->code }}</td>
                <td>{{ $value->name }}</td>
                <td>{{ $value->stock }}</td>
                <td>
                @if($value->stock >= 300)
                  <span class="badge bg-success">Aman</span>
                @else
                  <span class="badge bg-danger">Segera PO</span>
                @endif
                </td>
              </tr>
              @endforeach
          </table>
          {{ $list }}
          </div>
          
        </div>
    </div>
</div>  

@stop

@section('css')

@stop

@section('js')
<script type="text/javascript">
    var rootUrl = 'gudang';
    var curentId;

    function clearFilter() {
      $('#code').val('');
    }

    function reload() {
      document.location.reload();
    }
    loadlink();
    function loadlink() {
        $.get( "{{route('SaldoAwal')}}", function( data ) {
          $("#b_balance").html(converToRp(data.saldoawal));
        });

        $.get( "{{route('SaldoPakai')}}", function( data ) {
          $("#u_balance").html(converToRp(data.saldopakai));
        });

        $.get( "{{route('SaldoMasuk')}}", function( data ) {
          $("#in_balance").html(converToRp(data.saldomasuk));
        });

        $.get( "{{route('SaldoAkhir')}}", function( data ) {
          $("#c_balance").html(converToRp(data.saldoakhir));
        });
    }

    function converToRp(bilangan){      		
      var	reverse = bilangan.toString().split('').reverse().join('').replace('.00',''),
        ribuan 	= reverse.match(/\d{1,3}/g);
        ribuan	= ribuan.join(',').split('').reverse().join('');  
        return 'Rp. ' +ribuan;    
    }
    $(function() {
      
      setTimeout(reload, 100000);

      $('#tglpo').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd"
      });
    });
</script>
@endsection