@extends('layouts.print')

@section('content')
@inject('pt','App\Http\Controllers\Controller')
    <div class="row">
      <div class="col-12">
        <h4 class="page-header">
            <img src="{{ asset('img') }}/logowhite.png" width="35%"><br><br>
                    <div class="mx-auto">Rekapitulasi Material & Jasa</div>
                    {{ $pt->company()->name }}
          <small class="float-right" id="date"></small>
        </h4>
        <br>
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
          <dd>{{ $data->cycle }}/{{ $data->part }}/{{ $data->jenis }}</dd>
        </dl>
      </div>
      <div class="col-sm-6 invoice-col">
        #{{ $data->job_ticket }}
        <p style = "font-family:'Libre Barcode 39', cursive;font-size: 35px">
        	*{{ $data->job_ticket}}*
        </p>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      
        <table class="table table-striped table-hover">
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

    <br>
    
      <table border="1" @if(count($rinci) > 0) style="float: left; margin-right:10px" @endif>        
        <thead>
          <tr>
            <th>EKSPEDISI</th>
            <th>SERVICE</th>
            <th>QTY</th>
          </tr>
        </thead>
        <tbody>
        @php
          $total = 0;
        @endphp
          @foreach($kurir as $val)
          <tr>
            <td>{{ $val->ekspedisi }}</td>
            <td>{{ $val->service }}</td>
            <td>{{ $val->count_eks }}</td>
          </tr>
            @php
              $total = $total + $val->count_eks;
            @endphp          
          @endforeach
        </tbody>
        <tfoot>
          <td colspan="2" align="center" ><strong>Total Data</strong></td>
          <td>{{ $total }}</td>
        </tfoot>
      </table>

      @if(count($rinci) > 0)
        <table border="1" style="margin-left:10px">
          <thead>
            <tr>
              <th>KOTA</th>
              <th>CABANG</th>
              <th>TOTAL</th>
              <th>EKSPEDISI</th>
              <th>SERVICE</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rinci as $v)
            <tr> 
              <td>{{ $v->city }}</td>
              <td>{{ $v->cabang }}</td>
              <td>{{ $v->total }}</td>
              <td>{{ $v->ekspedisi }}</td>
              <td>{{ $v->service }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    
<br>
    <div class="row">
      <!-- accepted payments column -->
      <div class="col-md-6">
      	<div id="date2"></div>
      </div>
      <!-- /.col -->
    </div>
  
  <table style="width:100%">
    <th >Menyerahkan</th>
    <th >Penerima</th>
    <tbody>
      <tr>
        <td ></br></br></br>{{ $name ?? ''}}</td>
        <td ></br></br></br></td>
      </tr>
    </tbody>
  </table>  
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