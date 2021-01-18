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
                        <h3 class="box-title">Form Task Status</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <form action="" method="post" class="form-horizontal">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <input type="hidden" name="id" value="{{ $data->id ?? '' }}">                        
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Status Name</label>

                            <div class="col-sm-8">
                                <input type="text" maxlength="100" class="form-control" id="name" name="name" placeholder="Status Name" required value="{{ $data->name ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="icon" class="col-sm-2 control-label">Is Ok(Next)</label>

                            <div class="col-sm-4">
                                <select class="form-control" name="isok">
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                  </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pic" class="col-sm-2 control-label">Description</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" maxlength="300" id="desc" name="desc" placeholder="Description" value="{{ $data->desc ?? '' }}">
                            </div>
                        </div>                        
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <a href="/production/public/setting/master/taskresult" type="submit" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info">Submit</button>
                    </div>
                    </form>
                </div>
			</div>
		</div>
	</div>
@endsection
