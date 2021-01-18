@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
<div class="content">
                <div class="card-header"><h3>Summary Today</h3></div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                          <!-- small box -->
                          <div class="small-box bg-aqua">
                            <div class="inner">
                              <h3 id="submit_today">0</h3>

                              <p>New Order</p>
                            </div>
                            <div class="icon">
                              <i class="ion ion-bag"></i>
                            </div>
                          </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                          <!-- small box -->
                          <div class="small-box bg-green">
                            <div class="inner">
                              <h3 id="prod_today">0</h3>

                              <p>Production Orders</p>
                            </div>
                            <div class="icon">
                              <i class="fa fa-list-ol"></i>
                            </div>
                          </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                          <!-- small box -->
                          <div class="small-box bg-yellow">
                            <div class="inner">
                              <h3 id="material_today">0</h3>

                              <p>Material Used</p>
                            </div>
                            <div class="icon">
                              <i class="fa fa-file"></i>
                            </div>
                          </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                          <!-- small box -->
                          <div class="small-box bg-red">
                            <div class="inner">
                              <h3 id="deliv_today">0</h3>

                              <p>Distribution</p>
                            </div>
                            <div class="icon">
                              <i class="fa fa-truck"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                          </div>
                        </div>
                        <!-- ./col -->
                      </div>
                </div>
</div>


@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
  <script type="text/javascript">
    loadlink(); // This will run on page load


    setInterval(function(){
        loadlink() // this will run after every 5 seconds
    }, 500000);

    function loadlink() {
        $.get( "dashboard/submit", function( data ) {
          $("#submit_today").html(data);
        });

        $.get( "dashboard/prod", function( data ) {
          $("#prod_today").html(data);
        });

        $.get( "dashboard/material", function( data ) {
          $("#material_today").html(data);
        });

        $.get( "dashboard/deliv", function( data ) {
          $("#deliv_today").html(data);
        });
    }
</script>@stop

