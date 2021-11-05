@extends('adminlte::page')

@section('title', 'List Upload Approval')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">List Upload Approval</h3>
      </div>
      <div class="card-body">
      <div class="card-body table-responsive p-0" style="height: 400px;">
            <table  class="table table-bordered table-head-fixed">
            <tr>
              <th style="width: 10px">#</th>
              <th>Ticket</th>
              <th>Project</th>
              <th>File Name</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
            @foreach($list as $index=>$value)
            <tr>
              <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
              <td>{{ $value->ticket }}</td>
              <td>{{ $value->project_name }}</td>
              <td>{{ $value->file_name }}</td>
              <td>{{ $value->status_name }}</td>
              <td>
                  <a title="view detail" href="#" onclick="showDetail({{ $value->id }})" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;
                  <a title="from upload" href="#" onclick="showFormUpload({{ $value->id }})" class="text-success"><i class="fas fa-upload"></i></a>
              </td>
            </tr>
            @endforeach
        </table>
        {{ $list }}
      </div>
        </div>
    </div>
</div>
  @include('request.uploadapproval.form')
  @include('partial.detailincoming')
@stop

@section('js')
    <script>
    var rootUrl = 'uploadapproval';

    function hideError() {
      
    }

    function showFormUpload(id) {
      hideError();
      $('#id').val(id);
      $('input[name=_method]').val('POST');
      $('#modal-form').modal('show');
      $('#modal-form form')[0].reset();
      $('#title-form').text('Upload Approval');
    }

    function showDetail(id) {
      $.ajax({
          url: 'incoming' + '/detail/' + id,
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-detail').modal('show');
              $('#title-detail').text('Detail '+data.name);

              $('#ticket').html(data.ticket);
              $('#created_at').html(data.created_at);
              $('#project_name').html(data.project_name);
              $('#file_name').html(data.file_name);
              $('#cyc').html(data.cycle+"/"+data.part+"/"+data.jenis);
              $('#info').html(data.info);

              $('#titleCollapsibletransf').html('Track for Ticket '+data.ticket);               
              var html='';
              for (var i = 0, l = data.transf.length; i < l; i++) {
                var obj = data.transf[i];
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
              $('#timelinetransF').html(html);

              html='';
              for (var i = 0, l = data.transp.length; i < l; i++) {
                var obj = data.transp[i];
                html+=`<div class="card card-primary">
                    <div class="card-header">
                      <h4 class="card-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTransp${obj.id}" id="titleCollapsibletransp">
                          Track for Ticket Production #${obj.job_ticket}
                        </a>
                      </h4>
                    </div>
                    <div id="collapseTransp${obj.id}" class="panel-collapse collapse in">
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
              $('#transP').html(html);
          },
          error : function() {
              alert("Nothing Data");
          }
        });
    }

    $(function(){
      $('#form-item').submit(function(e) {
        e.preventDefault();
        var url = 'uploadapproval';
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
                      window.location.reload();
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
    });
  </script>
@stop
