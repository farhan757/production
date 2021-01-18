@extends('adminlte::page')

@section('title', 'Form Upload File Data')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Form Upload File Data</h3>
      </div>
            <form class="form-horizontal" name="form-item" id="form-item" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card-body">
                    <div class="form-group row">
                        <label for="customer" class="col-sm-2 control-label">Customer</label>

                        <div class="col-sm-4">
                            <select onchange="getProject()" class="form-control select2" name="customer_id" id="customer_id">
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

                    <div class="form-group row">
                        <label for="project" class="col-sm-2 control-label">Project</label>

                        <div class="col-sm-4">
                            <select class="form-control" name="project_id" id="project_id" required>                                        
                              </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cycle" class="col-sm-2 control-label">Cycle</label>
                        <div class="col-sm-4">
                            <div class="input-group date">
                              
                              <input type="text" class="form-control pull-right" name="cycle" id="datepicker" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="part" class="col-sm-2 control-label">Part</label>

                        <div class="col-sm-4">
                            <select class="form-control" name="part" required>
                                @foreach($parts as $key=>$value)
                                <option value="{{ $value->code }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="jenis" class="col-sm-2 control-label">Jenis</label>

                        <div class="col-sm-4">
                            <select class="form-control" name="jenis" required>
                                @foreach($jenis as $key=>$value)
                                <option value="{{ $value->code }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="method" class="col-sm-2 control-label">Method</label>

                        <div class="col-sm-4">
                            <select onchange="setMethod()" class="form-control" name="method_id" id="method_id" required>
                                <option value="1">Upload</option>
                                <option value="2">FTP</option>
                                <option value="3">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" id="divFile">
                        <label for="method" class="col-sm-2 control-label" >File</label>

                        <div class="col-sm-4">
                            <input type="file" id="file" name="file" required>

                              <p class="help-block">Select File</p>
                        </div>
                        <div class="col-sm-4">
                            <div class="alert alert-light" role="alert">
                                Max (5MB)
                            </div>
                        </div>
                    </div>

                    <div class="form-group row" id="divNote">
                        <label for="note" class="col-sm-2 control-label">Note</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="note" name="note" maxlength="500" placeholder="Notes" required></textarea>
                        </div>
                    </div>
                    <div class="alert alert-secondary" role="alert">
                    Tambahkan keterangan Note seperti Host, Path file, nama file jika method menggunakan FTP
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-info">Submit</button>
                </div>

            </form>
	</div>
</div>

@stop

@section('css')

@stop

@section('js')
    <script>
    var rootUrl = "upload";
    
    getProject();

    function getProject() {
        var cust_id = $('#customer_id').val();
        var url = "../setting/master/project/getJsonByCustomer/" + cust_id;
        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                var html = "";
                for (let i = 0; i < response.length; i++) {
                    html += "<option value=" + response[i].id  + ">" + response[i].name + "</option>"
                }
                $("#project_id").html(html);
            },
            error: function(response) {
              Swal.fire({
                icon: 'error',
                title: response
                });
            }
          });

    }

    function setMethod() {
        var e = document.getElementById("method_id").value;
        var divFile = document.getElementById("divFile");
        var divNote = document.getElementById("divNote");
        var file = document.getElementById("file");
        if(e == "1") {
            divFile.style.display="block"
            file.setAttribute("required", "");
            file.required = true;                          
        } else {
            divFile.style.display="none";
            file.removeAttribute("required");  
                    }
    }

    $(function(){      
      $('#datepicker').datepicker({
          autoclose: true,
          format: "yyyymmdd"
        });
      $('#form-item').submit(function(e) {
        e.preventDefault();
        var url = 'upload';
          var formData = new FormData($('#form-item')[0]);
          $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData:false,
            success: function(response) {
              //alert(JSON.stringify(response));
              if(response.status==1) {
                  Swal.fire({
                    icon: 'success',
                    title: response.message,
                    onClose: () => {
                      window.location.href = '{{ route('requestincomingdata') }}';
                    }
                  });
              } else {
                  Swal.fire({
                    icon: 'error',
                    title: response.message,
                    onClose: () => {
                      //window.location.href = '{{ route('requestincomingdata') }}';
                    }
                  });

              }
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                });
            }
          });
      });
/*
      $("form[name='form-item']").validate({
        
        submitHandler: function(form) {
          var url = 'upload';
          var formData = new FormData($('#form-item')[0]);
          $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData:false,
            success: function(response) {
              //alert(JSON.stringify(response));
              Swal.fire({
                icon: 'success',
                title: response.message,
                onClose: () => {
                  //window.location.href = '{{ route('requestincomingdata') }}';
                }
              })
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                });
            }
          });
          alert('test');
        }
      });*/
     });
  </script>
@stop
