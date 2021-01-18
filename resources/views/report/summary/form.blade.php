@extends('adminlte::page')

@section('htmlheader_title')
	Change Title here!
@endsection


@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-12">

				<div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Form Upload Job List</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    @if(count($errors) > 0)
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        {{ $error }} <br/>
                        @endforeach
                    </div>
                    @endif
                    <!-- /.box-header -->
                    <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="customer_id" value="{{ $data->customer_id }}">
                        <input type="hidden" name="project_id" value="{{ $data->project_id }}">
                        <input type="hidden" name="cycle" value="{{ $data->cycle }}">
                        <input type="hidden" name="part" value="{{ $data->part }}">
                        <input type="jenis" name="jenis" value="{{ $data->jenis }}">
                        <input type="note" name="note" value="{{ $data->note }}">
                        <div class="box-body">
                            <p class="text-muted well well-sm no-shadow">
                                <strong>Summary :</strong><br>
                                Customer : {{ $value->cureo}}
                            </p>
                            <div class="form-group">
                                <label for="note" class="col-sm-2 control-label">Note</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="note" maxlength="50">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="method" class="col-sm-2 control-label" >File</label>

                                <div class="col-sm-4">
                                    <input type="file" id="file" name="file" required>

                                      <p class="help-block">Select File</p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                        Max (5MB)
                                    </p>
                                </div>
                            </div> 
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href=".." class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info">Submit</button>
                        </div>

                    </form>
                </div>
			</div>
		</div>
	</div>
@endsection
