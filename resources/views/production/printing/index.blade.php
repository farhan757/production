@extends('adminlte::page')

@section('title', 'Form Upload File Data')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Data Printing</h3>
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
          </form>

          <table class="table table-bordered">
              <tr>
                <th style="width: 10px">#</th>
                <th>Time submit</th>
                <th>Job Ticket</th>
                <th>Cycle/Part</th>
                <th>Project</th>
                <th>Status</th>
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
                <td> 
                <a onclick="showDetail({{ $value->id }})" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;
                <a onclick="showFormUpdate({{ $value->id }})" class="text-warning"><i class="far fa-check-square"></i></a>
                </td>
              </tr>
              @endforeach
          </table>
          {{ $list }}
        </div>
    </div>
</div>
  @include('partial.formstatusproduction')
  @include('partial.detailproduction')
@stop

@section('js')
<script type="text/javascript">
    var rootUrl = 'printing';
    var curentId;
    $('#btn_material').css('visibility', 'visible');

    function hideError() {

    }

    function clearFilter() {
      $('#ticket').val('');
      $('#cycle').val('');
    }

    function showFormUpdate(id) {
      hideError();
      $('input[name=_method]').val('POST');
      $('#modal-form').modal('show');
      $('#modal-form form')[0].reset();
      $('#modal-title').text('Update status printing');
      $('#id').val(id);
    }

    function showDetail(id) {
      currentId = id;
      $.ajax({
          url: 'joblist/detail/' + id,
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-detail-production').modal('show');
              $('#title-detail-production').text('Detail '+data.data.job_ticket);

              $('#customer_name').html(data.data.customer_name);
              $('#project_name').html(data.data.project_name);              
              $('#created_at').html(data.data.created_at);
              $('#project_name').html(data.data.project_name);
              $('#usersubmit').html(data.data.username);
              $('#last_status').html(data.data.last_status);
              $('#file_name').html(data.data.file_name);
              $('#cyclePart').html(data.data.cycle+"/"+data.data.part+"/"+data.data.jenis);
              $('#info').html(data.data.info);
              var tbody = '';
              for(var i=0, l = data.list.length; i< l; i++) {
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

              $('#titleCollapsibletransp').html('Track for Job Ticket #'+data.data.job_ticket);               
              var html='';
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

              html='';
              if (data.transF!=null) {
                var obj = data.transF;
                html+=`<div class="card card-primary">
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
                              html+=`<div>
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
                             html+= `</div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>`;
              }
              $('#transF').html(html);
          },
          error : function() {
              alert("Nothing Data");
          }
        });
    }

    $(function() {
      $('#btn_material').click(function() {
       window.open('printing/material/'+currentId, '_blank');
      });

      $('#filterCycle').datepicker({
        autoclose: true,
        format: "yyyymmdd"
      });
      $('#cycle').datepicker({
        autoclose: true,
        format: "yyyymmdd"
      });

      $('#form-status').submit(function(e) {
        e.preventDefault();
        var url = rootUrl;
          var formData = new FormData($('#form-status')[0]);
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
                      window.location.reload();
                    }
                  });
              } else {
                  if(response.status==2) {
                    var errors = '';
                    for (var i = 0, l = response.error.length; i < l; i++) {
                      errors+="<p>"+response.error[i]+"</p>";
                    }
                    Swal.fire({
                      icon: 'error',
                      title: response.message + " " + response.error,
                      text: errors
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
    });
</script>
@endsection