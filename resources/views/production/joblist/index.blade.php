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
    <div class="card-body">
      <form action="" method="get" class="form-horizontal">
        {{ csrf_field() }}
        <div class="row" style="padding-bottom: 15px">
          <div class="col-sm-4">
            <input type="text" class="form-control pull-right" name="ticket" id="ticket" value="{{ $ticket ?? '' }}" placeholder="Ticket">
          </div>
          <div class="col-sm-2">
            <div class="input-group date">
              <input type="text" class="form-control pull-right" name="filterCycle" id="filterCycle" placeholder="Cycle" value="{{ $filterCycle ?? '' }}">
            </div>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
            <button type="button" class="btn btn-danger" onclick="clearFilter()"><i class="fa fa-trash"></i></button>
          </div>
        </div>
      
      <div class="row" style="padding-bottom:15px">
        <div class="col-md-12">
          @if($customer_id == 0)
          <button type="button" onclick="showFormUpload()" class="btn btn-warning pull-right"><i class="fa fa-plus"></i> Upload data</button>
          @endif
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-head-fixed">
          <tr>
            <th style="width: 10px">#</th>
            <th>Time submit</th>
            <th>Job Ticket</th>
            <th>Cycle/Part</th>
            <th>Project</th>            
            <th>Status</th>
            <th>Total Data</th>
            <th>Action</th>
          </tr>
          @foreach($list as $index=>$value)
          <tr>
            <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
            <td>{{ $value->created_at }}</td>
            <td>{{ $value->job_ticket }}</td>
            <td>{{ $value->cycle }} / {{ $value->part }}</td>
            <td>{{ $value->project_name }}</td>            
            <td>{{ $value->status_name }} / {{ $value->result_name }}</td>
            <td>{{ $value->jml_data }}</td>
            <td>
              <a href="#" title="view detail" onclick="showDetail({{ $value->id }})" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;
              @if($value->status_warehouse == 0)
                <a href="#" title="Cancel Job" onclick="showFormUpdate({{ $value->id }})" class="text-warning"><i class="fa fa-undo"></i></a>&nbsp;
              @endif
              @if($level == 2)
              <a href="#" title="Delete Job" onclick="deleteJob({{ $value->id }})" class="text-danger"><i class="fa fa-trash"></i></a>
              @endif
              
              <!--<a href="download/get/{{ $value->id }}" class="text-success"><i class="fa fa-download"></i></a>-->
            </td>
          </tr>
          @endforeach
        </table>
        {{ $list->withQueryString()->links() }} 
      </div>
      </form>
    </div>
  </div>
</div>

@include('production.joblist.formproject')
@include('partial.detailproduction')
@include('partial.formcancelprod')
@stop

@section('js')
<script type="text/javascript">
  var rootUrl = 'joblist';
  var currentId;
  $('#btn_material').css('visibility', 'visible');

  getProject();

  function hideError() {

  }

  function hideLoad() {
    $('#vload').hide();
  }

  function showLoad() {
    $('#vload').show();
  }

  function deleteJob(id) {
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
              url: "{{ route('deletejoblist') }}",
              type: "POST",
              data: { "_token": "{{ csrf_token() }}", "id":id },
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

  function getProject() {
    var cust_id = $('#customer_id').val();
    var url = "../setting/master/project/getJsonByCustomer/" + cust_id;
    $.ajax({
      url: url,
      type: "GET",
      success: function(response) {
        var html = "";
        for (let i = 0; i < response.length; i++) {
          html += "<option value=" + response[i].id + ">" + response[i].name + "</option>"
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

  function clearFilter() {
    $('#ticket').val('');
    $('#cycle').val('');
  }

  function showFormUpload() {
    hideError();
    hideLoad();
    $('input[name=_method]').val('POST');
    $('#modal-form').modal('show');
    $('#modal-form form')[0].reset();
    $('#title-form').text('Upload Data Job List');
  }

  function showFormUpdate(id) {
     
      $('input[name=_method]').val('POST');
      $('#modal-form-cancel').modal('show');
      $('#modal-form-cancel form')[0].reset();
      $('#modal-title-cancel').text('Form Cancel');
      $('#id').val(id);
    }

  function showDetail(id) {
    currentId = id;
    $.ajax({
      url: rootUrl + '/detail/' + id,
      type: "POST",
      data: {
        "_token": "{{ csrf_token() }}",
      },
      dataType: "JSON",
      success: function(data) {
        $('#modal-detail-production').modal('show');
        $('#title-detail-production').text('Detail ' + data.data.job_ticket);

        $('#customer_name').html(data.data.customer_name);
        $('#project_name').html(data.data.project_name);
        $('#created_at').html(data.data.created_at);
        $('#project_name').html(data.data.project_name);
        $('#usersubmit').html(data.data.username);
        $('#last_status').html(data.data.last_status);
        $('#file_name').html(data.data.file_name);
        $('#cyclePart').html(data.data.cycle + "/" + data.data.part + "/" + data.data.jenis);
        $('#info').html(data.data.info);
        var tbody = '';
        for (var i = 0, l = data.list.length; i < l; i++) {
          var obj = data.list[i];
          tbody += `<tr>
                      <td>${obj.barcode_env}</td>
                      <td>${obj.account_no}</td>
                      <td>${obj.penerima}</td>
                      <td>${obj.ekspedisi} ${obj.service}</td>
                      <td>${obj.no_manifest}</td>
                  </tr>`;
        }

        $('#tbody-detail').html(tbody);

        $('#titleCollapsibletransp').html('Track for Job Ticket #' + data.data.job_ticket);
        var html = '';
        for (var i = 0, l = data.transP.length; i < l; i++) {
          var obj = data.transP[i];
          html += `<div>
                                <i class="fas ${obj.status_icon} bg-blue"></i>
                                <div class="timeline-item">
                                  <span class="time"><i class="fas fa-clock-o"></i> ${obj.created_at}</span>
                                  <h3 class="timeline-header"><a href="#">${obj.username}</a> ${obj.status_name}</h3>

                                  <div class="timeline-body">
                                    <p>${obj.note}</p>
                                    <p>Result : ${obj.result_name}</p>
                                  </div>                                  
                                </div>
                              </div>`;
        }
        $('#timelinetransP').html(html);

        html = '';
        if (data.transF != null) {
          var obj = data.transF;
          html += `<div class="card card-primary">
                    <div class="card-header">
                      <h4 class="card-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTransf${obj.id}" id="titleCollapsibletransf">
                          Track for Ticket #${obj.ticket}
                        </a>
                      </h4>
                    </div>
                    <div id="collapseTransf${obj.id}" class="panel-collapse collapse in">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-12">
                            <!-- The time line -->
                            <div class="timeline" id="timelinetransF">`;
          for (var j = 0, k = obj.data.length; j < k; j++) {
            var obj2 = obj.data[j];
            html += `<div>
                                <i class="fas ${obj2.status_icon} bg-blue"></i>
                                <div class="timeline-item">
                                  <span class="time"><i class="fas fa-envelope"></i> ${obj2.created_at}</span>
                                  <h3 class="timeline-header"><a href="#">${obj2.username}</a> ${obj2.status_name}</h3>

                                  <div class="timeline-body">
                                    <p>${obj2.note}</p>
                                    <p>Result : ${obj2.result_name}</p>
                                  </div>                                  
                                </div>
                              </div>`
          }
          html += `</div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>`;
        }
        $('#transF').html(html);
      },
      error: function() {
        alert("Nothing Data");
      }
    });
  }

  $(function() {

    $('#btn_material').click(function() {
      window.open('printing/material/' + currentId, '_blank');
    });

    $('#filterCycle').datepicker({
      autoclose: true,
      format: "yyyymmdd"
    });
    $('#cycle').datepicker({
      autoclose: true,
      format: "yyyymmdd"
    });

    $('.select2').select2();

    $('#form-status').submit(function(e){
      e.preventDefault();
      var url = "{{ route('canceljoblist') }}";
      var formData = new FormData($('#form-status')[0]);
      showLoad();
      $.ajax({
        url: url,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
          hideLoad();
          if(response.status == 1){
            Swal.fire({
              icon: 'success',
              title: response.message,
              onClose: () => {
                window.location.reload();
              }
            });             
          }else{
            Swal.fire({
              icon: 'warning',
              title: response.message,
              onClose: () => {
                window.location.reload();
              }
            });             
          }
        },
        error: function(){
          Swal.fire({
              icon: 'error',
              title: 'error',
              onClose: () => {
                window.location.reload();
              }
            });          
        }
      });
    });

    $('#form-item').submit(function(e) {
      e.preventDefault();
      var url = rootUrl;
      var formData = new FormData($('#form-item')[0]);
      showLoad();
      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          //alert(JSON.stringify(response));
          console.log(response);
          hideLoad();
          if (response.status == 1) {
            Swal.fire({
              icon: 'success',
              title: response.message,
              onClose: () => {
                window.location.reload();
              }
            });
          } else {
            if (response.status == 2) {
              var errors = '';
              for (var i = 0, l = response.error.length; i < l; i++) {
                errors += "<p>" + response.error[i] + "</p>";
              }
              Swal.fire({
                icon: 'error',
                title: response.message + " " + response.error,
                text: errors
              });
            } else {
              //alert(response.status);
              Swal.fire({
                icon: 'error',
                title: response.message,
                onClose: () => {
                  //window.location.href = '{{ route('requestincomingdata') }}';
                  window.location.reload();
                }
              });
            }
          }
        },
        error: function(response) {
          hideLoad();
          Swal.fire({
            icon: 'error',
            title: 'Error',
          });
        }
      });
    });
  });
</script>
@endsection