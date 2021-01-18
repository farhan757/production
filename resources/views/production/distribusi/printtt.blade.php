@extends('layouts.print')

@section('content')
<div class="row">
      <div class="col-12">
        <h2 class="page-header">
            <img src="{{ asset('img') }}/logowhite.png">
                    <div class="mx-auto">Berita acara serah terima</div>
                    PT. Tata Layak Prawira
          <small class="float-right" id="date"></small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">

      <div class="col-sm-6 invoice-col">
        <dl class="row dl-horizontal">
          <dt class="col-4">Customer</dt>
          <dd class="col-8">{{ $data->customer_name }}</dd>
          <dt class="col-4">Project</dt>
          <dd class="col-8">{{ $data->project_name }}</dd>
          <dt class="col-4">Cycle/Part</dt>
          <dd class="col-8">{{ $data->cycle }}/{{ $data->part }}</dd>
        </dl>
      </div>
      <div class="col-sm-6 invoice-col">
        #{{ $data->no_manifest }}
        <p style = "font-family:'Libre Barcode 39', cursive;font-size: 50px">
            *{{ $data->no_manifest}}*
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
        <div class="panel panel-default" style="height: 120px;width: 2  00px">
          <div class="panel-body">TT Menyerahkan
          
          </div>
          <div style="position: absolute;bottom: 10px; right: 10;" class="panel-body">{{ $name ?? ''}}</div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="panel panel-default" style="height: 120px;width: 2  00px">
          <div class="panel-body">TT Penerima</div>
          <div style="position: absolute;bottom: 10px; right: 10;" class="panel-body">{{ $name_kurir ?? ''}}</div>
        </div>
      </div>
      <!-- /.col -->
    </div>
    <script type="text/javascript">
        Date.prototype.toShortFormat = function() {

            let monthNames =["Januari","Februari","Maret","April",
                              "Mei","Juni","Juli","Agustus",
                              "September", "Oktober","November","Desember"];
            
            let day = this.getDate();
            
            let monthIndex = this.getMonth();
            let monthName = monthNames[monthIndex];
            
            let year = this.getFullYear();
            
            return `${day} ${monthName} ${year}`;  
        }

        n =  new Date();
        y = n.getFullYear();
        m = n.getMonth() + 1;
        d = n.getDate();
        document.getElementById("date").innerHTML = d + "/" + m + "/" + y;
        //document.getElementById("date2").innerHTML ="Jakarta, " + d + "/" + m + "/" + y;
        document.getElementById("date2").innerHTML ="Jakarta, " + n.toShortFormat();

        

    </script>
@endsection