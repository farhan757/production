@extends('adminlte::page')

@section('title', 'List Users')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
    <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">List Users</h3>
      </div>
      <div class="card-body">
        <div class="row" style="padding-bottom: 30px">
          <div class="col-md-4">
            <button class="btn btn-warning" onclick="addForm()">
              <i class="fa fa-plus"></i> Add
            </button>
          </div>
        </div>
          <table class="table table-bordered">
              <tr>
                <th style="width: 10px">#</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Group</th>
                <th>Action</th>
              </tr>
              @foreach($list as $index=>$value)
              <tr>
                <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                <td>{{ $value->name }}</td>
                <td>{{ $value->username }}</td>
                <td>{{ $value->email }}</td>
                <td>{{ $value->group }}</td>
                <td>
                  <a href="#" title="view menu" onclick="menuForm({{$value->id}})" class="text-info"><i class="fas fa-bars"></i></a>&nbsp;
                  <a href="#" title="edit" onclick="editForm({{ $value->id }})" class="text-warning"><i class="fas fa-pencil-alt"></i></a>&nbsp;
                  <a href="#" title="delete" onclick="deleteUser({{ $value->id }})" class="text-danger"><i class="fas fa-trash"></i></a>

                </td>
              </tr>
              @endforeach
          </table>

      </div>
      <div class="card-footer">
            {{ $list }}
      </div>
    </div>
  </div>
@include('master.Users.form')
@include('master.Users.menuform')
@stop

@section('css')
@stop

@section('js')
    <script>

    getProject();

    function getProject() {
        var e = document.getElementById("customer_id").value;
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.open( "GET", "../setting/master/project/getJsonByCustomer/"+e, false ); // false for synchronous request
        xmlHttp.send( null );
        obj = JSON.parse(xmlHttp.responseText);
        html = "<option value=0>All</option>";
        for (let i = 0; i < obj.length; i++) {
            html += "<option value=" + obj[i].id  + ">" + obj[i].name + "</option>"
        }
        document.getElementById("project_id").innerHTML = html;
    }

    function hideError() {
      @foreach($forms as $key=>$value)
        $("#{{ $value['field'] }}-error").hide();
      @endforeach
    }

    function unCheckMenu() {
      @foreach($menus as $key=>$value)
        $( "#{{ $value->id }}-menu" ).prop( "checked", false );
        @foreach($value->contents as $key2=>$value2)
          $( "#{{ $value2->id }}-menu" ).prop( "checked", false );
        @endforeach
      @endforeach
    }

    function addForm() {
        save_method = "add";
        hideError();      

        $('input[name=_method]').val('POST');
        $('#modal-form').modal('show');
        $('#modal-form form')[0].reset();
        $('#title-form').text('Add Job');
        $("#password").prop('required',true);
        $("#password_confirmation").prop('required',true);
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
              url: "users" + '/delete/' + id,
              type: "POST",
              data: { "_token": "{{ csrf_token() }}", },
              dataType: "JSON",
              success: function(data) {
                Swal.fire({
                  icon: 'success',
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
          window.location.reload();
        }
      })
    }

    function menuForm(id) {
      save_method = "menu";
      $('#title-menuform').text('Menu');
      $('#modal-menuform form')[0].reset();
      $.ajax({
              url: "users" + '/getmenu/' + id,
              type: "POST",
              data: { "_token": "{{ csrf_token() }}", },
              dataType: "JSON",
              success: function(data) {
                  $('#modal-menuform').modal('show');
                  $('#title-form').text('Menu');
                  $('#id-menuform').val(id);
                  for(var key in data) {
                    var obj = data[key];
                    $( "#"+obj.menu_id+"-menu" ).prop( "checked", true );
                  }

              },
              error : function() {
                  alert("Nothing Data");
              }
        });
    }

    function editForm(id) {
      save_method = "menu";
      hideError();
      $('input[name=_method]').val('PATCH');
      $('#modal-form form')[0].reset();
          $.ajax({
              url: "users" + '/get/' + id,
              type: "POST",
              data: { "_token": "{{ csrf_token() }}", },
              dataType: "JSON",
              success: function(data) {
                  $('#modal-form').modal('show');
                  $('#title-form').text('Edit Job');

                  $('#id').val(data.id);
                  @foreach($forms as $key=>$value)
                    @if($value['type']!='password') 
                      $("#{{ $value['field'] }}").val(data.{{ $value['field'] }})
                    @endif
                  @endforeach
                  $("#password").prop('required',false);
                  $("#password_confirmation").prop('required',false);
                  $('#customer_id').val(data.customer_id);
              },
              error : function() {
                  alert("Nothing Data");
              }
        });
    }

    $(function(){
      $("form[name='form-menu']").validate({        
        submitHandler: function(form) {
          var id = $('#id-menuform').val();
          url = "users/replacemenu/"+id;
          $.ajax({
            url: url,
            type: form.method,
            data: $('#form-menu').serializeArray(),
            success: function(response) {
              //alert(JSON.stringify(response));
              Swal.fire({
                icon: 'success',
                title: response.message,
                onClose: ()=> {

                }
              })
            },
            error: function(response) {
              Swal.fire({
                icon:'error',
                title:'Error',
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
          if (save_method == 'add') url = "users/add";
          else url = "users/save/" + id;

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
              })
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
