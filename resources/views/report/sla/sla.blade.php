@extends('adminlte::page')

@section('title', 'SLA')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')

            <!-- LINE CHART -->
<div class="row">
  <div class="col-xs-5 table-responsive">

  <div class="overlay-wrapper"> 
       <div class="overlay dark" id="vload" ><i class="fas fa-5x fa-sync-alt fa-spin"></i>&nbsp; &nbsp; &nbsp;<div class="text-bold pt-2" type="hide">Loading...</div></div>
              <div class="card-body">

                  <div class="row" style="padding-bottom: 5px; padding-left: 5px">
                    <div class="col-sm-2">
                              <select class="form-control select2" name="customer_id" id="customer_id">
                                <option value="0">All</option>
                                @foreach($customers as $index=>$value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                              </select>                
                    </div>
                    <div class="col-sm-2">
                              <select class="form-control select2" name="customer_id" id="customer_id">
                                <option value="0">All</option>
                                @foreach($customers as $index=>$value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                              </select>                
                    </div>                    
                    <div class="col-sm-2">
                              <select class="form-control select2" name="info" id="info">
                                <option value="1">Printing</option>
                                <option value="2">Pendapatan</option>                                                                                                
                              </select>                
                    </div>   
                    <div class="col-sm-3">
                      <div class="input-group date">                                      
                        <input type="text" class="form-control pull-right" name="filterCycle" id="filterCycle" placeholder="From Date">
                        <input type="text" class="form-control pull-right" name="filterCycle2" id="filterCycle2" placeholder="End Date">
                      </div>
                    </div>                                     
                    <div class="col-md-15">
                        <div class="btn-group">
                          <button id="btnDaily" type="button" class="btn btn-default active">Daily</button>
                          <button id="btnMonthly" type="button" class="btn btn-default">Monthly</button>
                          <button id="btnYearly" type="button" class="btn btn-default">Yearly</button>
                        </div>
                        <button type="submit" class="btn btn-info" id="apply"><i class="fas fa-search"></i></button>                                               
                    </div>                    
                  </div>
          
                <div class="chart">
                  <canvas id="printing" style="min-height: 200; height: 400px; max-height: 500px; max-width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->
   </div>         
      </div>

</div>

@stop

@section('css')
  <link rel="stylesheet" href="{{ asset('vendor/chart.js/Chart.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/chart.js/Chart.min.css') }}">
@stop

@section('js')
<script src="{{ asset('vendor/chart.js/Chart.bundle.js') }}"></script>
<script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('vendor/chart.js/Chart.js') }}"></script>
  <script type="text/javascript">  

    setInterval(function(){
      showGrafik();
    }, 500000);
    function hideLoad(){       
       $('#vload').hide();
    }

    function showLoad()
    {
      $('#vload').show();
    }
    function showGrafik()
    {
            showLoad();
            var produk = $('select[name=customer_id] option:selected').val();
            var info = $('select[name=info] option:selected').val();
            var start = $('#filterCycle').val();
            var end = $('#filterCycle2').val();

            var path = "home/getrange/"+method+"/"+encodeURI(produk)+"/"+encodeURI(info);
            if(!(start=='') && !(end==''))
            {
                path = path+"/"+start+"/"+end;
            }
            $.get(path, function(data, status){
                config.data.labels=[];
                config.data.datasets=[];

                var obj = JSON.parse(JSON.stringify(data));


                config.data.labels=obj.labels;

                var backColor = 'rgb(76, 198, 10)'; 
                var newColor = 'rgb(54, 162, 235)';
                if(info == 2){
                  newColor = 'rgb(76, 198, 10)';
                  backColor = 'rgb(54, 162, 235)';
                }
                
                var newDataset = {
                    label: 'Total '+obj.lbl_info+' '+obj.lbl_total,                    
                    backgroundColor: backColor,
                    borderColor: newColor,
                    pointStyle: 'rect',                    
                    data: obj.total,
                    fill: false
                };
                config.data.datasets.push(newDataset);

                window.myLine.update();             /*
                for (var index = 0; index < config.data.labels.length; ++index) {
                    newDataset.data.push(randomScalingFactor());
                }
                */

                //window.myLine.update();
                console.log(data);
            });
            hideLoad();
    }

        var method = "year";
        //var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        var config = {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                legend: {
                  display: true,
                  position: 'top',
                  labels: {
                    boxWidth: 50,
                    fontColor: 'black',
                    fontSize: 14
                  }
                },                
                title: {
                    display: true,
                    text: 'PT. Tata Layak Prawira',
                    fontSize: 16,
                    fontColor: 'black'
                },
                tooltips: {
                    mode: 'index',
                    intersect: true,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Period'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        }
                    }]
                }
            }
        };
        

    $(function(){
            $('#apply').click(function(){              
              showGrafik();
            });

            $('#btnMonthly').click(function() {
                method='month';

            });
            $('#btnDaily').click(function() {
                method='day';
            });
            $('#btnYearly').click(function() {
                method='year';
            }); 

            $('#filterCycle').datepicker({
              autoclose: true,
              format: "yyyy-mm-dd"
            }); 
            $('#filterCycle2').datepicker({
              autoclose: true,
              format: "yyyy-mm-dd"
            });                              
    })

        window.onload = function() {
            var ctx = document.getElementById('printing').getContext('2d');
            window.myLine = new Chart(ctx, config);
            showGrafik();
        };

        var colorNames = Object.keys(window.chartColors);    
</script>@stop

