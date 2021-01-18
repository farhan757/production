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
          <div class="col-xs-5 table-responsive">
          <table class="table table-bordered">
              <tr>
                <th style="width: 10px">#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Stock</th>
                <th>Status</th>
              </tr>
              @foreach($list as $index=>$value)
              <tr>
                <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
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
          </div>
          {{ $list }}
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


    $(function() {
      
      setTimeout(reload, 100000);

      $('#tglpo').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd"
      });
    });
</script>
@endsection