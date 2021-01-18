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
                        <h3 class="box-title">List Group</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                              <th style="width: 10px">#</th>
                              <th>Nama</th>
                              <th>Desc</th>
                              <th>Active</th>
                            </tr>
                            @foreach($list as $index=>$value)
                            <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td>{{ $value->name }}</td>
                              <td>{{ $value->desc }}</td>
                              <td>{{ $value->active }}</td>
                            </tr>
                            @endforeach
                        </table>
                        {{ $list }}
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </div>
@endsection
