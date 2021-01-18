@extends('adminlte::page')

@section('htmlheader_title')
	Change Title here!
@endsection


@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-12">

				<div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Task {{ $status->name }}</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    @if(isset($info))
                        <div class="alert alert-info alert-dismissible">
                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                          <h4><i class="icon fa fa-info"></i> Info!</h4>
                          {{ $info }}
                        </div>
                    @endif                       
                    <!-- /.box-header -->
                    <form class="form-horizontal" method="post" enctype="multipart/form-data">
                              {{ csrf_field() }}
                    <div class="box-body">
                            <table class="table">
                            <tr>
                              <th style="width: 10px">#</th>
                              <th>Code</th>
                              <th>Desc</th>
                              <th>Sort</th>
                            </tr>
                            @foreach($results as $index=>$value)
                            <tr>
                              <td>
                                <label>
                                  <input type="checkbox" name="checkbox[{{ $loop->iteration-1 }}]" value="{{ $value['id'] }}" {{ $value['check'] }}>
                                </label>
                              </td>
                              <td>{{ $value['code'] }} </td>
                              <td>
                                {{ $value['name'] }}
                            </td>
                            <td>
                              <input type="number" name="sort[]" value="{{ $value['sort'] ?? '' }}">
                            </td>

                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <a href=".." class="btn btn-default pull-left">Cancel</a></button>
                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                    </div>
                    </form>
        </table>
                </div>
			</div>
		</div>
	</div>
@endsection
