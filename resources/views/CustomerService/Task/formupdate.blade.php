@extends('adminlte::page')

@section('htmlheader_title')
	Change Title here!
@endsection


@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-12">

				<div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Update Status Form</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <form class="form-horizontal" method="post">
                              {{ csrf_field() }}
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h2>Request : {{ $data->title }}</h2>
                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                        Message : {{ $data->desc }}
                        </p>
                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                          Jika anda ingin mengupload file silahkan upload di FTP dengan path /uploaded/{{ date('Ymd', strtotime($data->create_dt)) }}/{{ $data->id }}, jika directory tidak ada maka buat lah
                        </p>
                                <div class="form-group">
                                  <label for="selectStatus" class="col-sm-2 control-label">Status</label>
                                  <div class="col-sm-6">
                                      <select class="form-control" id="selectStatus" name="selectStatus">
                                        @foreach($liststatus as $key=>$value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                      </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="inputMessage" class="col-sm-2 control-label">Message</label>

                                  <div class="col-sm-10">
                                    <input type="text" name="inputMessage" class="form-control" id="inputMessage" placeholder="Message">
                                  </div>
                                </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                      <a href="../" class="btn btn-default pull-left">Cancel</a>
                        <button type="submit" class="btn btn-info pull-right">Submit</button>
                    </div>
                </form>
                </div>
			</div>
		</div>
	</div>
@endsection
