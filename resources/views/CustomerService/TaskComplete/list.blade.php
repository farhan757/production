@extends('adminlte::page')

@section('htmlheader_title')
	Change Title here!
@endsection


@section('main-content')
	<div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-body">
                    <div class="box-header with-border">
                        <h3 class="box-title">List Data</h3>
                        <!-- /.box-tools -->
                    </div>
 
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                              <th style="width: 10px">#</th>
                              <th>Title</th>
                              <th>Status</th>
                              <th>Tanggal</th>
                              <th>User</th>
                              <th>Prioritas</th>
                            </tr>
                            @foreach($list as $index=>$value)
                            <tr>
                              <td>{{ (($list->currentPage() - 1 ) * $list->perPage()) + $loop->iteration }}</td>
                              <td><a href="taskcomplete/showdetail/{{ $value->id }}" data-toggle="modal" data-target="#modal-{{ $value->id }}">{{ $value->title }}</a></td>
                              <td>{{ $value->name }}</td>
                              <td>{{ $value->create_dt }}</td>
                              <td>{{ $value->username }}</td>
                              <td>{{ $value->priority }}</td>

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
    @foreach($list as $index=>$value)
                    <div class="modal fade" id="modal-{{ $value->id }}">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Loading Modal Polis {{ $value->title }}</h4>
                          </div>
                          <div class="modal-body">
                            <img src="{{ asset('img/Gear-2.2s-200px.gif') }}" class="img-responsive center-block">
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                        <!-- /.modal-content -->
                      </div>
                      <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->
                    @endforeach
@endsection
