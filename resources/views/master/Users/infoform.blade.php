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
                        <h3 class="box-title">Form Info form</h3>
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
                    <form class="form-horizontal" method="POST">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group">
                                <label for="customer" class="col-sm-2 control-label">Customer</label>

                                <div class="col-sm-4">
                                    <select onchange="getProject()" class="form-control" name="customer_id" id="customer_id">
                                        <option value="0">Internal</option>
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
                                <label for="level" class="col-sm-2 control-label">Level</label>

                                <div class="col-sm-4">
                                    <select class="form-control" name="level" id="level" required>
                                        <option value="0">Administrator</option>
                                        <option value="1">Super User</option>
                                        <option value="2">User</option>
                                      </select>
                                </div>
                            </div>


                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
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
        html += "<option value=0>Internal</option>"
        for (let i = 0; i < obj.length; i++) {
            html += "<option value=" + obj[i].id  + ">" + obj[i].name + "</option>"
        }
        document.getElementById("project_id").innerHTML = html;
    }

</script>
@endsection
