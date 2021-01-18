@extends('layouts.print')

@section('content')
<div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
        	<img src="../../../img/logowhite.png">
		        	<div class="mx-auto">Report Penggunaan Material</div>
		        	PT. Tata Layak Prawira
          <small class="pull-right" id="date"></small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">

      <div class="col-sm-6 invoice-col">
        <dl class="dl-horizontal">
          <dt>Customer</dt>
          <dd>{{ $data->customer_name }}</dd>
          <dt>Project</dt>
          <dd>{{ $data->project_name }}</dd>
          <dt>Cycle/Part</dt>
          <dd>{{ $data->cycle }}/{{ $data->part }}</dd>
        </dl>
      </div>
      <div class="col-sm-6 invoice-col">
        #{{ $data->job_ticket }}
        <p style = "font-family:'Libre Barcode 39', cursive;font-size: 50px">
        	*{{ $data->job_ticket}}*
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
            <th>Kode</th>
            <th>Deskripsi</th>
            <th>Qty</th>
            <th>Satuan</th>
          </tr>
          </thead>
          <tbody>
          	@foreach($material as $key=>$value)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $value->code }}</td>
            <td>{{ $value->name }}</td>
            <td>{{ $value->total }}</td>
            <td>{{ $value->satuan }}</td>
          </tr>
         	@endforeach
          </tbody>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
      <!-- accepted payments column -->
      <div class="col-md-6">
      	<div id="date2"></div>
      </div>
      <!-- /.col -->
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
		document.getElementById("date2").innerHTML ="Jakarta, " + n.toShortFormat();
    </script>
@endsection