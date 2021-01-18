@extends('adminlte::page')

@section('title', 'List PO Material')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
<div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Data Material Keluar Job</h3>
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
                    <input type="text" class="form-control pull-right" name="filterCycle" id="filterCycle" placeholder="Cycle" value="{{ $cycle ?? '' }}">
                  </div>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button> 
                <button type="button" class="btn btn-danger" onclick="clearFilter()"><i class="fa fa-trash"></i></button>
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
              @foreach($listats as $index=>$value)
              <tr>
                <td>{{ ($listats->perPage()*($listats->currentPage()-1)) +$loop->iteration }}</td>
                <td>{{ $value->created_at }}</td>
                <td>{{ $value->job_ticket }}</td>
                <td>{{ $value->cycle }} / {{ $value->part }}</td>
                <td>{{ $value->project_name }}</td>
                <td>{{ $value->status_name }} / {{ $value->result_name }}</td>
                <td> 
                <a href="#" title="View" onclick="showDetail({{ $value->id }})" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;                
                </td>
              </tr>
              @endforeach
          </table>
          {{ $listats }}
        </div>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Data Material Keluar Test</h3>
      </div>
        <div class="card-body">
          <form action="" method="get" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row" style="padding-bottom: 15px">
              <div class="col-sm-4">
                    <input type="text" class="form-control pull-right" name="nojob" id="nojob" value="{{ $nojob ?? '' }}" placeholder="nojob">
              </div>
              <div class="col-sm-2">
                  <div class="input-group date">                                      
                    <input type="text" class="form-control pull-right" name="filterCycle2" id="filterCycle2" placeholder="Cycle" value="{{ $cycle2 ?? '' }}">
                  </div>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button> 
                <button type="button" class="btn btn-danger" onclick="clearFilter2()"><i class="fa fa-trash"></i></button>
              </div>
            </div>
          </form>
          <div class="col-xs-5 table-responsive">
          <table class="table table-bordered">
              <tr>
                <th style="width: 10px">#</th>
                <th>No JOB</th>
                <th>Tanggal JOB</th>
                <th>Kode/Project</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
              @foreach($listbwh as $index=>$value)
              <tr>
                <td>{{ ($listbwh->perPage()*($listbwh->currentPage()-1)) +$loop->iteration }}</td>
                <td>{{ $value->no_job }}</td>
                <td>{{ $value->tgl_out }}</td>
                <td>{{ $value->code }} / {{ $value->name }}</td>
                <td>                
                  <span class="badge bg-success">Completed</span>
                </td>
                <td> 
                <a href="#" title="view detail" onclick="showDetail2('{{ $value->no_job }}')" class="text-info"><i class="fas fa-eye"></i></a>&nbsp;
                </td>
              </tr>
              @endforeach
          </table>          
          {{ $listbwh }}
          </div>
        </div>
    </div>
</div>
@include('partial.detailproduction')
@include('gudang.listoutgoing.detaildata')
@stop

@section('js')
<script type="text/javascript">
    var rootUrl = 'gudang';
    var curentId;

    function clearFilter() {
      $('#ticket').val('');
      $('#filterCycle').val('');
    }

    function clearFilter2() {
      $('#nojob').val('');
      $('#filterCycle2').val('');
    }    

    function showDetail2(id) {
      currentId = id;      
      var uri = "{{ route('lisgodetail',':currentId') }}";
      $.ajax({
          url: uri.replace(':currentId', currentId),
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", },
          dataType: "JSON",
          success: function(data) {
              $('#modal-detail-material').modal('show');
              $('#title-detail-material').text('Detail '+data.data.no_job);

              $('#codevendor').html(data.data.code);
              $('#namavendor').html(data.data.name);              
              $('#tgl_po').html(data.data.tgl_out);
              $('#createby').html(data.data.nama);
              $('#note').html(data.data.note);
              var tbody = '';
              for(var i=0, l = data.list.length; i< l; i++) {
                var obj = data.list[i];
                tbody += `<tr>
                      <td>${obj.code}</td>
                      <td>${obj.name}</td>
                      <td>${obj.qty_out}</td>                      
                      <td>${obj.satuan}</td>
                  </tr>`;
              }
              $('#tbody-detail2').html(tbody);
          },
          error : function() {
              alert("Nothing Data");
          }
        });
    }

    function showDetail(id) {
      currentId = id;      
      var uri = "{{ route('joblistdetail',':currentId') }}";
      $.ajax({
          url: uri.replace(':currentId', currentId),
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
              url:  'delete/' + id,
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

    $(function() {
      

      $('#filterCycle').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd"
      });
      $('#filterCycle2').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd"
      });
    });
</script>
@endsection