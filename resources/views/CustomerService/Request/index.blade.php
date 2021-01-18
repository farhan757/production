@extends('adminlte::page')

@section('htmlheader_title')
	Change Title here!
@endsection


@section('main-content')
	<div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-10">

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="box box-default">
                            <div class="box-header with-border">
                              <h3 class="box-title">Form Request Task</h3>
                            </div>
                            <!-- /.box-header -->
                            @if(isset($data))
                                <div class="alert alert-info alert-dismissible">
                                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                  <h4><i class="icon fa fa-info"></i> Info!</h4>
                                  {{ $data }}
                                </div>
                            @endif

                            @if(isset($error))
                              <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                                {{ $error }}
                              </div>
                            @endif
                            <!-- form start -->
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                              {{ csrf_field() }}
                              <div class="box-body">
                                <div class="form-group">
                                  <label for="inputTitle" class="col-sm-2 control-label">Title</label>

                                  <div class="col-sm-6">
                                    <input type="text" name="inputTitle" class="form-control" id="inputTitle" placeholder="Title">
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label for="selectPriority" class="col-sm-2 control-label">Prioritas</label>
                                  <div class="col-sm-6">
                                      <select class="form-control" id="selectPriority" name="selectPriority">
                                        <option value="0">Default</option>
                                        <option value="1">Middle</option>
                                        <option value="2">High</option>
                                      </select>
                                  </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputNote" class="col-sm-2 control-label">Note</label>
                                  <div class="col-sm-6">
                                    <input type="text" name="inputNote" class="form-control" id="inputNote" placeholder="Note">
                                  </div>                                
                                </div>
                                <div class="form-group">
                                  <label for="input_file"  class="col-sm-2 control-label">File input</label>
                                  <div class="col-sm-6">
                                      <input type="file" id="input_file" name="input_file">

                                      <p class="help-block">file name</p>
                                  </div>
                                </div>

                              <div class="box-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                              </div>                          
                          </form>
                      </div>
                    </div>
                    <!-- /.box-body -->
            </div>
        </div>
    </div>
@endsection
