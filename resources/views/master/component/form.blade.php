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
                        <h3 class="box-title">Form Component</h3>
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
                        <input type="hidden" name="price" value="0">
                        <div class="form-group">
                            <label for="code" class="col-sm-2 control-label">Code</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control" maxlength="15" id="code" name="code" placeholder="Code" value="{{ $data->code ?? '' }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Name</label>

                            <div class="col-sm-8">
                                <input type="text" maxlength="50" class="form-control" id="name" name="name" placeholder="Name" required value="{{ $data->name ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="satuan" class="col-sm-2 control-label">Satuan</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control" maxlength="25" id="pic" name="satuan" placeholder="Satuan" value="{{ $data->satuan ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="group" class="col-sm-2 control-label">Group</label>

                            <div class="col-sm-2">
                                <select name="group" class="form-control">
                                    <option value="jasa" 
                                    @if(isset($data))
                                    @if($data->group=='jasa') 
                                        selected
                                    @endif
                                    @endif
                                        >Jasa</option>
                                    <option value="material"
                                    @if(isset($data))
                                    @if($data->group=='material') 
                                        selected
                                    @endif
                                    @endif
                                    >Material</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <a href="/production/public/setting/master/customers" type="submit" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info">Submit</button>
                    </div>
                    </form>
                </div>
			</div>
		</div>
	</div>
@endsection
