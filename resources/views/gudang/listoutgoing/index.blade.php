@extends('adminlte::page')

@section('title', 'Form Outgoing Material')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
<div class="col-sm-12">
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">Form Outgoing Material</h3>
    </div>
    <form class="form-horizontal" name="form-item" id="form-item" method="POST" enctype="multipart/form-data">
      {{ csrf_field() }}
      <div class="card-body">

        <div class="form-group row">
          <label for="part" class="col-sm-2 control-label">NO JOB</label>

          <div class="col-sm-4">
            <input type="text" class="form-control pull-right" name="nojob" id="nojob" value="{{ $nojob ?? '' }}" required readonly>
          </div>
          <div class="col-sm-4">
            <a onclick="getNoPO()" href="#" class="btn btn-info">generate NO. JOB</a>
            <a onclick="bersihkan()" href="#" class="btn btn-danger">Clear Form</a>
          </div>
        </div>

        <div class="form-group row">
          <label for="cycle" class="col-sm-2 control-label">Tgl JOB</label>
          <div class="col-sm-4">
            <div class="input-group date">

              <input type="text" class="form-control pull-right" name="tgljob" id="datepicker" value="{{ $tgljob ?? '' }}" required readonly>
            </div>
          </div>
        </div>

        <div class="form-group row">
          <label for="customer" class="col-sm-2 control-label">Project</label>

          <div class="col-sm-4">
            <select class="form-control select2" name="project_id" id="project_id">
              @foreach($project as $index=>$value)
              <option value="{{ $value->id }}" @if(isset($data->id))
                @if($data->id===$value->id)
                selected
                @endif
                @endif
                >{{ $value->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" id="divNote">
          <label for="note" class="col-sm-2 control-label">Note</label>
          <div class="col-sm-6">
            <input class="form-control" rows="3" id="note" name="note" maxlength="500" placeholder="Notes"></textarea>
          </div>
        </div>
      </div>
      <!-- /.box-body -->
      <div class="card-footer">
        <button type="submit" class="btn btn-info" name="submit" id="submit">Save</button>
        <a onclick="batal('{{ $nojob ?? '' }}')" href="#" class="btn btn-danger">Cancel</a>
      </div>

      <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-head-fixed">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Jumlah</th>
              <th>Action</th>
            </tr>
          </thead>

          <input type="hidden" id="sdf" value=0>
          <tbody id="data-input">

          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" align="right"><a href="#" title="add" id="addForm" class="text-success"><i class="fa fa-plus"></i></a>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </form>
  </div>
</div>

@stop



@section('js')


<script>
  var rootUrl = "gudang";

  function bersihkan() {
    window.location.href = "{{ route('createoutmaterial') }}";
  }

  function aftersave() {
    $('#note').val('');
    $('#qty').val('');
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
          url: 'delete/' + id,
          type: "POST",
          data: {
            "_token": "{{ csrf_token() }}",
          },
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
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'Error Delete Data'
            });
          }
        });
      }
    })
  }

  function batal(id) {
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
          url: 'batal/' + id,
          type: "POST",
          data: {
            "_token": "{{ csrf_token() }}",
          },
          dataType: "JSON",
          success: function(data) {
            Swal.fire({
              icon: 'success',
              title: data.message,
              onClose: () => {
                bersihkan();
              }
            });
          },
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'Error Cancel'
            });
          }
        });
      }
    })
  }

  function getNoPO() {
    var proj_id = $('#project_id option:selected').val();
    var urx = "{{ route('generatenojob',':id') }}";
    var url = urx.replace(':id', proj_id);
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, Generate No JOB !'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: url,
          type: "GET",
          success: function(response) {
            $("#nojob").val(response);

          },
          error: function(response) {
            Swal.fire({
              icon: 'error',
              title: response
            });
          }
        });
      }
    })
  }

  function setMethod() {
    var e = document.getElementById("method_id").value;
    var divFile = document.getElementById("divFile");
    var divNote = document.getElementById("divNote");
    var file = document.getElementById("file");
    if (e == "1") {
      divFile.style.display = "block"
      file.setAttribute("required", "");
      file.required = true;
    } else {
      divFile.style.display = "none";
      file.removeAttribute("required");
    }
  }

  $(function() {
    var urutan = 0;
    $('#addForm').on("click", function() {
      data_form = "{{ route('addform') }}";
      //no = parseInt($('#sdf').val());      
      urutan++; //= no + 1;
      ke = 1;
      ke = ke + 1;
      $.get(data_form, {
        id: urutan
      }, function(data) {
        $('#data-input').append(data);
        $('#sdf').val(urutan);
      });
    });

    $('#data-input').on("click", ".hapus-baris", function(e) {
      e.preventDefault();
      $(this).parent('tr').remove();
    });

    $('#datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd"
    });

    //Initialize Select2 Elements
    $('.select2').select2();

    $('#form-item').submit(function(e) {
      e.preventDefault();

      var url = "{{ route('saveout') }}";
      var formData = new FormData($('#form-item')[0]);
      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          //alert(JSON.stringify(response));
          if (response.status == 1) {
            Swal.fire({
              icon: 'success',
              title: response.message,
              onClose: () => {
                var nopo = $("#nojob").val();
                var tglpo = $("#datepicker").val();
                var reidretc = 'createoutmaterial';
                window.location.href = reidretc;
                aftersave();
              }
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: response.message,
              onClose: () => {
                //window.location.href = '{{ route('requestincomingdata') }}';
                aftersave();
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