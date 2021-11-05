@extends('adminlte::page')

@section('title', 'List Users')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
	@if(isset($info))
    <div class="alert alert-info alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h4><i class="icon fa fa-info"></i> Info!</h4>
      {{ $info }}
    </div>
@endif  

    <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Form Change Password</h3>
      </div>
      <div class="card-body">
                    <!-- /.box-header -->
                    <div class="box-body">
                    <form class="form-horizontal" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                          <label for="current-password" class="col-sm-4 control-label">Old Password</label>
                          <div class="col-sm-6">
                              <input type="password" name="current-password" class="form-control" id="current-password" placeholder="Old Password">
                            </div>
                          </div>
                            
                          <br>
                        <div class="form-group">
                          <label for="password" class="col-sm-4 control-label">New Password</label>
                          <div class="col-sm-6">
                              <input type="password" name="password" class="form-control" id="password" placeholder="New Password">
                            </div>
                          </div>
                        <div class="form-group">
                          <label for="password_confirmation" class="col-sm-4 control-label">Retype New Password</label>
                          <div class="col-sm-6">
                              <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Retype New Password">
                            </div>
                          </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                
                            </div>
                            <div class="col-sm-6">
                              <button type="submit" class="btn btn-default">Save</button>
                            </div>
                      </div>
                    </form>
                    </div>
    </div>
  </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script type="text/javascript"></script>
@stop

