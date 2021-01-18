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
                        <h3 class="box-title">Form update status balancing : {{ $data->job_ticket }}</h3>
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
                            <label for="result_id" class="col-sm-2 control-label">Result</label>

                            <div class="col-sm-4">
                                <select class="form-control" name="result_id">
                                    @foreach($results as $index=>$value)
                                    <option value="{{ $value->id }}" 
                                        @if(isset($data->customer_id))
                                        @if($data->customer_id===$value->id) 
                                            selected
                                        @endif 
                                        @endif
                                        >{{ $value->name }}</option>
                                    @endforeach
                                  </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Note</label>

                            <div class="col-sm-8">
                                <input type="text" maxlength="50" class="form-control" id="note" name="note" placeholder="Notes">
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <a href=".." type="submit" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
