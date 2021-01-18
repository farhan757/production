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
                        <h3 class="box-title">Form Upload Data Production</h3>
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
                        <div class="box-body">
                            <div class="form-group">
                                <label for="customer" class="col-sm-2 control-label">Customer</label>

                                <div class="col-sm-4">
                                    <select onchange="getProject()" class="form-control" name="customer_id" id="customer_id">
                                        @foreach($customers as $index=>$value)
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
                                <label for="project" class="col-sm-2 control-label">Project</label>

                                <div class="col-sm-4">
                                    <select class="form-control" name="project_id" id="project_id" required>                                        
                                      </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cycle" class="col-sm-2 control-label">Cycle</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                      
                                      <input type="text" class="form-control pull-right" name="cycle" id="datepicker" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="part" class="col-sm-2 control-label">Part</label>

                                <div class="col-sm-4">
                                    <select class="form-control" name="part" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="jenis" class="col-sm-2 control-label">Jenis</label>

                                <div class="col-sm-4">
                                    <select class="form-control" name="jenis" required>
                                        <option value="REG">Reguler</option>
                                        <option value="CU">Cetak Ulang</option>
                                        <option value="SS">Susulan</option>
                                    </select>
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

                            <div class="form-group">
                                <label for="note" class="col-sm-2 control-label">Note</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" rows="3" id="note" name="note" maxlength="500" placeholder="Notes"></textarea>
                                </div>
                            </div>                            
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="../joblist" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info">Submit</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">

    getProject();

    function getProject() {
        var e = document.getElementById("customer_id").value;
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.open( "GET", "/production/public/setting/master/project/getJsonByCustomer/"+e, false ); // false for synchronous request
        xmlHttp.send( null );
        obj = JSON.parse(xmlHttp.responseText);
        html = "";
        for (let i = 0; i < obj.length; i++) {
            html += "<option value=" + obj[i].id  + ">" + obj[i].name + "</option>"
        }
        document.getElementById("project_id").innerHTML = html;
    }

    $(function () {
        $('#datepicker').datepicker({
          autoclose: true,
          format: "yyyymmdd"
        });
    });
</script>
@endsection
