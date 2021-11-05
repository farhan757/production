@extends('layouts.print')

@section('content')
@inject('pt','App\Http\Controllers\Controller')
<div class="row">
      <div class="col-12">
        <h4 class="page-header">
            <img src="{{ asset('img') }}/logowhite.png" width="35%"><br><br>
                    <div class="mx-auto">Purchase Order </div>
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
        <dl class="row dl-horizontal">
          <dt class="col-4">Kode Vendor</dt>
          <dd class="col-8">{{ $infopo->code }}</dd>
          <dt class="col-4">Nama Vendor</dt>
          <dd class="col-8">{{ $infopo->name }}</dd>
          <dt class="col-4">Tgl PO</dt>
          <dd class="col-8">{{ $infopo->tgl_po }}</dd>
          <dt class="col-4">Create By</dt>
          <dd class="col-8">{{ $infopo->nama }}</dd>          
        </dl>
      </div>
      <div class="col-sm-6 invoice-col">
        #{{ $infopo->no_po }}
        <p style = "font-family:'Libre Barcode 39', cursive;font-size: 40px">
            *{{ $infopo->no_po}}*
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
          <tbody> @php
                      $cntr=0;
                  @endphp
          @foreach($data as $val)
                @php
                    $cntr++;
                @endphp
          <tr>
            <td>{{ $cntr }}</td>
            <td>{{ $val->code }}</td>
            <td>{{ $val->name }}</td>
            <td>{{ $val->qty_order }}</td>
            <td>{{ $val->satuan }}</td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </br></br>
    <div class="row">
      <div class="col-md-6">
        <div id="date2"></div>
      </div>
  </div>
  </br>
  <table style="width:100%">
    <th >Menyerahkan</th>
    <th >Penerima</th>
    <tbody>
      <tr>
        <td ></br></br></br>{{ $printby ?? ''}}</td>
        <td ></br></br></br>{{ $infopo->pic ?? ''}}</td>
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
        //document.getElementById("date2").innerHTML ="Jakarta, " + d + "/" + m + "/" + y;
        document.getElementById("date2").innerHTML ="Jakarta, " + n.toShortFormat();

        

    </script>
@endsection