@extends('layouts.print')

@section('content')
@inject('pt','App\Http\Controllers\Controller')
    <div class="row">
      <div class="col-12">
        <h4 class="page-header">
            <img src="{{ asset('img') }}/logowhite.png" width="35%"><br><br>
                    <div class="mx-auto">Berita Acara Serah Terima</div>
                    {{ $pt->company()->name }}                    
          <small class="float-right" id="date" style = "font-size: 20px"></small>
        </h4>
        <h10>{{ $pt->company()->alamat }}</h10><br>
        <h10>{{ $pt->company()->kota }} {{ $pt->company()->kdpos }}</h10><br>
        <h10>Telp : {{ $pt->company()->notelp }} <strong>|</strong> Website : <a href="https://xptlp.co.id">https://xptlp.co.id</a></h10><br>
        <hr>
        <br>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">

      <div class="col-sm-6 invoice-col">
        <dl class="row dl-horizontal">
          <dt class="col-4" style = "font-size: 20px">Customer</dt>
          <dd class="col-8" style = "font-size: 20px">{{ $data->customer_name }}</dd>
          <dt class="col-4" style = "font-size: 20px">Project</dt>
          <dd class="col-8" style = "font-size: 20px">{{ $data->project_name }}</dd>
          <dt class="col-4" style = "font-size: 20px">Cycle/Part</dt>
          <dd class="col-8" style = "font-size: 20px">{{ $data->cycle }}/{{ $data->part }}/{{ $data->jenis }}</dd>
        </dl>
      </div>
      <div class="col-sm-6 invoice-col" style = "font-size: 20px">
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
          <tr style = "font-size: 20px">
            <th>#</th>
            <th>Deskripsi</th>
            <th>Qty</th>
            <th>Printing</th>
            <th>Inserting</th>
            <th>Kurir</th>
          </tr>
          </thead>
          <tbody>
          <tr style = "font-size: 20px">
            <td>1</td>
            <td>{{ $data->customer_name }} - {{ $data->project_name }} - {{ $data->cycle }} - {{ $data->part }}</td>
            <td>{{ $total }}</td>
            <td>{{ $printing ?? '0' }}</td>
            <td>{{ $inserting ?? '0' }}</td>
            <td>{{ $data->ekspedisi }}/{{ $data->service }}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    @if(isset($rinci) && count($rinci) > 0)
    <!-- Table row -->
      <br>
        <table style="width: 25%; border: 1px solid black; right:10px">
          <thead>
          <tr>
            <th style="border: 1px solid black; left:10px; font-size:20px">Cabang</th>
            <th style="border: 1px solid black; left:10px; font-size:20px">Qty</th>
          </tr>
          </thead>
          <tbody>
          @foreach($rinci as $value)
          <tr>
            <td style="border: 1px solid black; left:10px; font-size:20px">{{ $value->city }}({{ $value->cabang }})</td>
            <td style="border: 1px solid black; left:10px; font-size:20px">{{ $value->total }}</td>
          </tr>
          @endforeach
          </tbody>
        </table>
        <br>
    <!-- /.row -->
    @endif
    <div class="row">
      <div class="col-md-6">
        <div id="date2" style = "font-size: 20px"></div>
      </div>
  </div>
  </br>
  <table style="width:100%">
    <tr style = "font-size: 20px">
    <th >Menyerahkan</th>
    <th >Penerima</th>
    </tr>
    <tbody>
      <tr style = "font-size: 20px">
        <td ></br></br></br>{{ $name ?? ''}}</td>
        <td ></br></br></br>{{ $name_kurir ?? ''}}</td>
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