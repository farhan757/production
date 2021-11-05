@extends('adminlte::page')

@section('title', 'List Project')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">List Customers</h3>
      </div>
      <div class="card-body">
            <div class="row" style="padding-bottom: 30px">
              <div class="col-md-4">
                <button class="btn btn-warning" onclick="addForm()">
                  <i class="fa fa-plus"></i> Add
                </button>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 400px;">
            <table  class="table table-bordered table-head-fixed" id="proj">
                <thead>
                <tr>
                  
                  <th>Customer</th>
                  <th>Kode</th>
                  <th>Name</th>
                  <th>Action</th>
                </tr>
                </thead>
              <tbody>
              
                @foreach($list as $index=>$value)
                <tr>
                  
                  <td>{{ $value->customer_name }}</td>
                  <td>{{ $value->code }}</td>
                  <td>{{ $value->name }}</td>
                  <td>
                  <a href="#" title="view detail" onclick="showDetail({{ $value->id }})" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;
                    <a href="#" title="edit" onclick="editForm({{ $value->id }})" class="text-warning"><i class="fas fa-pencil-alt"></i></a>&nbsp;
                    <a href="#" title="view task" onclick="taskForm({{ $value->id }})" class="text-warning"><i class="fas fa-tasks"></i></a>&nbsp;
                    <a href="#" title="view component" onclick="componentForm({{ $value->id }})" class="text-warning"><i class="fas fa-shapes"></i></a>&nbsp;                    
                  <a href="#" title="delete" onclick="deleteUser({{ $value->id }})" class="text-danger"><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
            
          </div>
			</div>
		</div>
	</div>

    @include('partial.form')
    @include('partial.detail')
    @include('partial.selecttaskform')
    @include('partial.selectcomponentform')
@stop

@section('css')
  
@stop

@section('js')
  <script>
    var rootUrl = 'projects';

    function hideError() {
      @foreach($forms as $key=>$value)
        @if($value['type']!='select' && $value['type']!='checkbox')
          $("#{{ $value['field'] }}-error").hide();
        @endif
      @endforeach
    }

    function taskForm(id) {
      $('#id-task').val(id);
      @foreach($tasks as $key=>$value)
        $("#checkbox-{{ $value->id }}").prop( "checked", false);
        $("#sort-checkbox-{{ $value->id }}").val(0);
      @endforeach
      $.ajax({
          url: rootUrl + '/gettask/' + id,
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-task-form').modal('show');
              $('#modal-title-task-form').text('Detail Task');

              for (var i = 0, l = data.length; i < l; i++) {
                var obj = data[i];
                $("#checkbox-"+obj.status_id).prop( "checked", true);
                $("#sort-checkbox-"+obj.status_id).val(obj.sort);
              }
         },
          error : function() {
              alert("Nothing Data");
          }
        });
    }

    function componentForm(id) {
      @foreach($tasks as $key=>$value)
        $("#comp-checkbox-{{ $value->id }}").prop( "checked", false);
        $("#comp-sort-checkbox-{{ $value->id }}").val(0);
      @endforeach
      $.ajax({
          url: rootUrl + '/getcomponent/' + id,
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-component-form').modal('show');
              $('#modal-title-component-form').text('Detail Component');
              $('#id-component').val(id);
              for (var i = 0, l = data.length; i < l; i++) {
                var obj = data[i];
                $("#comp-checkbox-"+obj.component_id).prop( "checked", true);
                $("#comp-sort-checkbox-"+obj.component_id).val(obj.sort);
                $("#comp-price-checkbox-"+obj.component_id).val(obj.price_jual);
              }
         },
          error : function() {
              alert("Nothing Data");
          }
        });
    }

    function showDetail(id) {
      $.ajax({
          url: rootUrl + '/get/' + id,
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-detail').modal('show');
              $('#title-detail').text('Detail '+data.name);
              $('#dl-detail').html('');

              @foreach($forms as $key=>$value)
                $('#dl-detail').append("<dt class='col-4'>{{ $value['desc'] }}</dt><dd class='col-8'>"+data.{{ $value['field'] }}+"</dd>");
              @endforeach
              if(data.components.length>0) {
                for (var i = 0, l = data.components.length; i < l; i++) {
                    var obj = data.components[i];
                    if(i==0) {
                      $('#dl-detail').append("<dt class='col-4'>Components</dt>");
                    } else {
                      $('#dl-detail').append("<dt class='col-4'>&nbsp;</dt>");
                    }
                    $('#dl-detail').append("<dd class='col-8'><i class='fas fa-check-circle text-success'></i>&nbsp;"+obj.name+"</dd>");
                }
              }
              if(data.tasks.length>0) {                
                for (var i = 0, l = data.tasks.length; i < l; i++) {
                    var obj = data.tasks[i];
                    if(i==0) {
                      $('#dl-detail').append("<dt class='col-4'>Tasks</dt>");
                    } else {
                      $('#dl-detail').append("<dt class='col-4'>&nbsp;</dt>");
                    }
                    $('#dl-detail').append("<dd class='col-8'><i class='fas fa-check-circle text-success'></i>&nbsp;"+obj.name+"</dd>");
                }
              }
         },
          error : function() {
              alert("Nothing Data");
          }
        });
    }

    function addForm() {
        save_method = "add";
        hideError();      

        $('input[name=_method]').val('POST');
        $('#modal-form').modal('show');
        $('#modal-form form')[0].reset();
        $('#title-form').text('Add Project');
    }

    function deleteUser(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.value) {
          $.ajax({
              url: rootUrl + '/delete/' + id,
              type: "POST",
              data: { "_token": "{{ csrf_token() }}", },
              dataType: "JSON",
              success: function(data) {
                Swal.fire({
                  icon:'success',
                  type: 'success',
                  title: data.message,
                  onClose: () => {
                    window.location.reload();
                  }
                });
              },
              error : function() {
                Swal.fire({
                  icon:'error',
                  title: 'Error Delete Data'
                });
              }
          });
        }
      })
    }

    function editForm(id) {
      save_method = "menu";
      hideError();
      $('input[name=_method]').val('PATCH');
      $('#modal-form form')[0].reset();
          $.ajax({
              url: rootUrl + '/get/' + id,
              type: "POST",
              data: { "_token": "{{ csrf_token() }}", },
              dataType: "JSON",
              success: function(data) {
                  $('#modal-form').modal('show');
                  $('#title-form').text('Edit Job');

                  $('#id').val(data.id);
                  @foreach($forms as $key=>$value)
                    @if($value['type']!='password')
                      @if($value['type']=='checkbox')
                        if(data.{{ $value['field'] }}=='1') {
                          $("#{{ $value['field'] }}").prop("checked", true);
                        } else {
                          $("#{{ $value['field'] }}").prop("checked", false);
                        }
                      @else
                        $("#{{ $value['field'] }}").val(data.{{ $value['field'] }})
                      @endif
                    @endif
                  @endforeach
              },
              error : function() {
                  alert("Nothing Data");
              }
        });
    }

    $(function(){

      var table2 = $('#example').DataTable(
        {
          "bPaginate": false,
        }
      );

      var table = $('#proj').DataTable(
        {
          "bPaginate": true,
        }
      );      

      $('#submit-comp').click(function() { 
        table2.search('').draw();
      });

      $("form[name='form-component']").validate({

        submitHandler: function(form) {
          var id = $('#id-component').val();
          var url = rootUrl + "/savecomponent/" + id;
          table2.search('').draw();         
          $.ajax({
            url: url,
            type: form.method,
            data: $('#form-component').serialize(),
            success: function(response) {
              //alert(JSON.stringify(response));
              Swal.fire({
                icon: 'success',
                title: response.message,
                onClose: () => {
                  window.location.reload();
                }
              });
            },
            error: function(response) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message
              });
            }
          })
        }
      });

      $("form[name='form-task']").validate({
        rules: {
        },
        submitHandler: function(form) {
          var id = $('#id-task').val();
          var url = rootUrl + "/savetask/" + id;

          $.ajax({
            url: url,
            type: form.method,
            data: $('#form-task').serialize(),
            success: function(response) {
              //alert(JSON.stringify(response));
              Swal.fire({
                icon: 'success',
                title: response.message,
                onClose: () => {
                  window.location.reload();
                }
              });
            },
            error: function(response) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message
              });
            }
          })
        }
      });


      $("form[name='form-item']").validate({
        rules: {
          username: "required",
          name:"required"
        },
        submitHandler: function(form) {
          var id = $('#id').val();
          if (save_method == 'add') url = "projects/add";
          else url = "projects/save/" + id;

          $.ajax({
            url: url,
            type: form.method,
            data: $('#form-item').serialize(),
            success: function(response) {
              //alert(JSON.stringify(response));
              Swal.fire({
                icon: 'success',
                title: response.message,
                onClose: () => {
                  window.location.reload();
                }
              });
            },
            error: function(response) {
              var responseJSON = response.responseJSON;
              @foreach($forms as $key=>$value)
                if(responseJSON.errors.hasOwnProperty("{{ $value['field'] }}")) {
                  $("#{{ $value['field'] }}-error").show();
                  $("#{{ $value['field'] }}-error").html(responseJSON.errors.{{ $value['field'] }}[0]);
                }
              @endforeach
            }
          })
        }
      });
     });
  </script>
@stop

