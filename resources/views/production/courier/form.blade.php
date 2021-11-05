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
                        <h3 class="box-title">Form update tanggal kirim : {{ $data->no_manifest }}</h3>
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
                                <label for="tgl_kirim" class="col-sm-2 control-label">Tanggal kirim</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                      
                                      <input type="text" class="form-control pull-right" name="tgl_kirim" id="datepicker" required>
                                    </div>
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

<script type="text/javascript">

    $(function () {
        $('#datepicker').datepicker({
          autoclose: true,
          format: "yyyymmdd"
        });
    });
</script>
@endsection
