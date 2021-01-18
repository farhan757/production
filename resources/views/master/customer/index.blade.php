@extends('adminlte::page')

@section('title', 'List Customers')

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
            <table class="table table-bordered">
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Kode</th>
                  <th>Nama</th>
                  <th>PIC</th>
                  <th>Action</th>
                </tr>
                @foreach($list as $index=>$value)
                <tr>
                  <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                  <td>{{ $value->code }}</td>
                  <td>{{ $value->name }}</td>
                  <td>{{ $value->pic }}</td>
                  <td>
                    <a href="#" title="view detail" onclick="showDetail({{ $value->id }})" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;
                    <a href="#" title="edit" onclick="editForm({{ $value->id }})" class="text-warning"><i class="fas fa-pencil-alt"></i></a>&nbsp;
                  <a href="#" title="delete" onclick="deleteUser({{ $value->id }})" class="text-danger"><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
                @endforeach
            </table>
            {{ $list }}
      </div>
                    <!-- /.box-body -->
    </div>
	</div>

    @include('master.customer.form')
    @include('partial.detail')
@stop

@section('css')
@stop

@section('js')
    <script>
    function hideError() {
      @foreach($forms as $key=>$value)
        $("#{{ $value['field'] }}-error").hide();
      @endforeach
    }

    function showDetail(id) {
      $.ajax({
          url: "customers" + '/get/' + id,
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-detail').modal('show');
              $('#title-detail').text('Detail '+data.name);
              $('#dl-detail').html('');

              @foreach($forms as $key=>$value)
                $('#dl-detail').append("<dt class='col-4'>{{ $value['desc'] }}</dt><dd class='col-8'>"+data.{{ $value['field'] }}+"</dd>")
              @endforeach
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
              url: "customers" + '/delete/' + id,
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
        }
      })
    }

    function editForm(id) {
      save_method = "menu";
      hideError();
      $('input[name=_method]').val('PATCH');
      $('#modal-form form')[0].reset();
          $.ajax({
              url: "customers" + '/get/' + id,
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
              },
              error : function() {
                  alert("Nothing Data");
              }
        });
    }

    $(function(){
      $("form[name='form-item']").validate({
        rules: {
          username: "required",
          name:"required"
        },
        submitHandler: function(form) {
          var id = $('#id').val();
          if (save_method == 'add') url = "customers/add";
          else url = "customers/save/" + id;

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
