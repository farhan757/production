@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Home</h1>
@stop

@section('content')
    <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Secondary Card Example</h3>
      </div>
      <div class="card-body">
        The body of the card
      </div>
      <div class="card-footer">
        The footer of the card
      </div>
    </div>
  </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
