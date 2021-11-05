<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>GTV Tracking Printing</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->

  <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <link href="{{ asset('/css/admin_custom.css') }}" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/adminlte') }}/dist/css/adminlte.min.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet"> 

  <style>
.all-browsers {
  margin: 0;
  padding: 5px;
  background-color: lightgray;
}

.all-browsers > h1, .browser {
  margin: 10px;
  padding: 5px;
}

.browser {
  background: white;
}

.browser > h2, p {
  margin: 4px;
  font-size: 90%;
}

footer {
  position: sticky;
  text-align: center;
  padding: 3px;
  bottom: 0px;
  color: black;
}
</style>

</head>
<body>
  
<div class="wrapper">
  <!-- Main content -->
<section class="invoice">
    <!-- title row -->

  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">TRACKING JOB PRINTING</h3>
      </div>
        <div class="card-body">
                <form action="" method="get" class="form-horizontal">
                  {{ csrf_field() }}
                  <div class="row" style="padding-bottom: 15px">
                    <div class="col-sm-4">
                          <input type="text" class="form-control pull-right" name="ticket" id="ticket" value="{{ $ticket ?? '' }}" placeholder="Ticket">
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-default" name="submit" id="submit"><i class="fas fa-search"></i>TRACK</button> 
                      <button type="button" class="btn btn-danger" onclick="clearFilter()"><i class="fa fa-trash"></i>CLEAR</button>
                    </div>
                  </div>
                  </form>
                <div class="col-xs-5 table-responsive">
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
                    @if(!is_null($list))
                      @foreach($list as $index=>$value)
                      <tr>
                        <td>{{ ($list->perPage()*($list->currentPage()-1)) +$loop->iteration }}</td>
                        <td>{{ $value->created_at }}</td>
                        <td>{{ $value->job_ticket }}</td>
                        <td>{{ $value->cycle }} / {{ $value->part }}</td>
                        <td>{{ $value->project_name }}</td>
                        <td>{{ $value->status_name }} / {{ $value->result_name }}</td>
                        <td>
                          <a href="#" title="view detail" onclick="showDetail({{ $value->id }})" class="text-info"><i class="fas fa-eye"></i> DETAIL</a>                      
                          <!--<a href="download/get/{{ $value->id }}" class="text-success"><i class="fa fa-download"></i></a>-->
                        </td>
                      </tr>
                      @endforeach
                    @endif
                </table>
                @if(!is_null($list))
                {{ $list }}
                @endif
              </div>
            </div>
        </div>
    </div>    
    @include('Tracking.detailtrack')
  </div>
  <footer>
  <p>Copyright &copy 2021 GTV<br>
  <a href="#">Printing Tracking System</a></p>
</footer>
<!-- ./wrapper -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

</body>
<script type="text/javascript">
  //window.print();
  //setTimeout(function(){window.close();}, 1);

  var rootUrl = 'tracking';


    getProject();

    function hideError() {

    }

    function hideLoad(){       
      $('#vload').hide();
    }

    function showLoad()
    {
      $('#vload').show();
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

    function clearFilter() {
      $('#ticket').val('');
      $('#cycle').val('');
    }

    function showDetail(id) {
      $.ajax({
          url: rootUrl + '/detail/' + id,
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
              /*var tbody = '';
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

              $('#tbody-detail').html(tbody);*/

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

      $('#filterCycle').datepicker({
        autoclose: true,
        format: "yyyymmdd"
      });
      $('#cycle').datepicker({
        autoclose: true,
        format: "yyyymmdd"
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
            processData:false,
            success: function(response) {
              //alert(JSON.stringify(response));
              hideLoad();
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
</html>
