@extends('layouts.print')

@section('content')
<div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
            <img src="../../../img/logowhite.png">
                    <div class="mx-auto">Berita acara serah terima</div>
                    PT. Tata Layak Prawira
          <small class="pull-right" id="date"></small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">

      <div class="col-sm-8 invoice-col">
        <dl class="dl-horizontal">
          <dt>Customer</dt>
          <dd>{{ $data->customer_name }}</dd>
          <dt>Project</dt>
          <dd>{{ $data->project_name }}</dd>
          <dt>Cycle/Part</dt>
          <dd>{{ $data->cycle }}/{{ $data->part }}</dd>
        </dl>
      </div>
      <div class="col-sm-4 invoice-col">
        #{{ $data->no_manifest }}
        <p style = "font-family:'Libre Barcode 39', cursive;font-size: 40px">
            {{ $data->no_manifest}}
        </p>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>#</th>
            <th>Deskripsi</th>
            <th>Qty</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>1</td>
            <td>{{ $data->customer_name }} - {{ $data->project_name }} - {{ $data->cycle }} - {{ $data->part }}</td>
            <td>{{ $total }}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
      <div class="col-md-6">
        <div id="date2"></div>
      </div>
  </div>
    <div class="row" style="padding-top: 25px">
      <!-- accepted payments column -->
      <div class="col-md-3">
        <div class="panel panel-default" style="height: 100px;width: 2  00px">
          <div class="panel-body">TT Menyerahkan</div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="panel panel-default" style="height: 100px;width: 2  00px">
          <div class="panel-body">TT Penerima</div>
        </div>
      </div>
      <!-- /.col -->
    </div>
    <script type="text/javascript">
        n =  new Date();
        y = n.getFullYear();
        m = n.getMonth() + 1;
        d = n.getDate();
        document.getElementById("date").innerHTML = m + "/" + d + "/" + y;
        document.getElementById("date2").innerHTML ="Jakarta, " + m + "/" + d + "/" + y;
    </script>
@endsection