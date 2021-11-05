@extends('adminlte::page')

@section('title', 'Form Scan Distribusi')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
<div class="col-sm-12">
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">Filter</h3>
    </div>

    <form action="{{ route('genman') }}" method="post" class="form-horizontal">
      <div class="card-body">
        {{ csrf_field() }}
        <div class="form-group row">
          <label for="no_amplop" class="col-sm-2 control-label">No Ticket</label>
          <div class="col-sm-4" id="divnoTicket">
            <input type="text" class="form-control pull-right" name="no_ticket" id="no_ticket" value="{{ $data->job_ticket }}" readonly>
            <input type="hidden" class="form-control pull-right" name="id" id="id" value="{{ $data->id }}">
          </div>
        </div>

        <div class="form-group row">
          <label for="project" class="col-sm-2 control-label">Customer</label>

          <div class="col-sm-4">
            <input type="text" class="form-control pull-right" value="{{ $data->cust_name }}" readonly>
          </div>
        </div>

        <div class="form-group row">
          <label for="project" class="col-sm-2 control-label">Project</label>

          <div class="col-sm-4">
            <input type="text" class="form-control pull-right" value="{{ $data->pro_name }}" readonly>
          </div>
        </div>

        <div class="form-group row">
          <label for="cycle" class="col-sm-2 control-label">Cycle</label>
          <div class="col-sm-4">
            <div class="input-group date">
              <input type="text" class="form-control pull-right" value="{{ $data->cycle }}" id="inputCycle" name="inputCycle" readonly>
            </div>
          </div>
        </div>

        <div class="form-group row">
          <label for="part" class="col-sm-2 control-label">Part</label>

          <div class="col-sm-4">
            <input type="text" class="form-control pull-right" value="{{ $data->part }}" id="selectPart" name="selectPart" readonly>
          </div>
        </div>

        <div class="form-group row">
          <label for="jenis" class="col-sm-2 control-label">Jenis</label>

          <div class="col-sm-4">
            <input type="text" class="form-control pull-right" value="{{ $data->jen_name }}" readonly>
          </div>
        </div>

        <div class="form-group row" id="divPolis">
          <label for="inputPolis" class="col-sm-2 control-label">Buku Polis</label>

          <div class="col-sm-6">
            <input type="text" name="inputPolis" class="form-control" id="inputPolis" placeholder="Barcode">
          </div>
        </div>
       
        <button type="button" class="btn btn-primary" onclick="submit();">Generate Manifest</button>
        <br>
        <h2>
          <a class="text-light-green" class="divInfo3" id="Info3">info</a> | <a class="text-light-green" class="divInfo4" id="Info4">info</a>
        </h2>
            
        <div class="card-body table-responsive p-0" style="height: 200px;">
          <table class="table table-bordered table-head-fixed" id="tab-pol">
            <thead>
              <th>Nomor Polis</th>
              <th style="text-align: center;">Status Scan</th>
              <th>Tanggal Scan dan Waktu</th>
              <th>User Scan</th>
            </thead>
            <tbody id="body-pol">
            </tbody>
          </table>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
  var jumlah = 0;
  var tot_scan = 0;

  function CheckJobTick(id) {
    $('#no_ticket').ready(function(e) {
      $.get("checknoTick/" + id, function(data, status) {
        var obj = $.parseJSON("[" + JSON.stringify(data) + "]");
        //var jumlah = obj.jumlah;
        //alert("Data: " + obj[0].jumlah + "\nStatus: " + status);
        console.log(data);
        if (data.jumlah == 0) {
          $('#no_ticket').attr('class', 'form-control is-invalid');

          Swal.fire({
            icon: 'error',
            title: 'Job Ticket Not Found',
            onClose: () => {
              //window.location.href = '{{ route('requestincomingdata') }}';
            }
          });
        } else {
          var datapolis = $.parseJSON("[" + JSON.stringify(obj[0].data) + "]");

          list_polis = new Array();
          var obj2 = $.parseJSON(JSON.stringify(obj[0].data));
          //alert(JSON.stringify(obj[0].data));
          var pol = "";
          var i;
          var t_pol = ''; var icon='';
          for (i = 0; i < obj2.length; i++) {
            list_polis.push(obj2[i].barcode_env);
            pol = pol + obj2[i].barcode_env + ';';
            if(obj2[i].scan_distribusi == 1){
              icon = '<a class="text-success"><h3 class="far fa-check-square"></h3></a>';
            }else{
              icon = '<a class="text-danger"><strong><h3>X</h3></strong></a>';
            }
            t_pol += '<tr>' +
              ' <td>' + obj2[i].barcode_env + '</td>' +
              ' <td style="text-align: center;">' + icon + '</td>' +
              ' <td>' + obj2[i].scan_distribusi_at + '</td>' +
              ' <td>' + obj2[i].scan_distribusi_user_id + '</td>' +
              '</tr>';
          }

          jumlah = obj[0].jumlah;
          tot_scan = obj[0].totalscan;

          $('#body-pol').html(t_pol);
          $('#no_ticket').attr('class', 'form-control is-valid');
          $("#inputPolis").prop('disabled', false);
          $('#inputPolis').focus();
          //$('#Info').text(" Polis : " + pol);
          $('#Info3').text(tot_scan + "/" + jumlah);
        }
      });
    });
  }

  $(function() {
    var id = $("#id").val();
    CheckJobTick(id);
    $('#inputPolis').keypress(function(e) {
      var key = e.which;
      if (key == 13) {
        var no_polis = $('#inputPolis').val();
        var res = no_polis.replace(/DE|ce|dn/gi, "");
        var vscan = res.replace(/de|ps|pls|attc|ce|dn/gi, "");
        vscan = vscan.trim();
        var index = list_polis.indexOf(vscan);
        if (index != -1) {

          var nopol = $('#inputPolis').val();
          var id = $('#id').val();

          //list_polis.splice(index, 1);
          /*if (list_polis.length == 0) {

          }*/
          $.get("scandistribusi/scanok/" + id + "/" + nopol, function(data, status) {

            var obj = $.parseJSON("[" + JSON.stringify(data) + "]");
            console.log(obj);
            if (obj[0].status == 1) {
              tot_scan++;
              CheckJobTick(id);
              $('#Info4').text(no_polis + " Found");
              $('#inputPolis').attr('class', 'form-control is-valid');
              $('#inputPolis').val('');
              $('#Info4').text(no_polis + " Success Scan");
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Nomor Polis sudah pernah di Scan pada tanggal ' + obj[0].data[0].scan_distribusi_at,
                onClose: () => {
                  $('#inputPolis').attr('class', 'form-control is-invalid');
                  $('#inputPolis').val('');
                  $('#inputPolis').focus();
                  $('#Info4').text(no_polis + " Scanned");
                }
              });
              //$('#Info').text(no_awb + " update failed");
            }
          });
          //$('#Info').text(" Polis(" + list_polis.length + ") : " + list_polis.join(";"));
          $('#Info3').text(tot_scan + "/" + jumlah);
        } else {
          $('#Info4').text(no_polis + " Not Found");
          $('#inputPolis').attr('class', 'form-control is-invalid');

          Swal.fire({
            icon: 'error',
            title: 'Nomor Polis Not Found',
            onClose: () => {
              //window.location.href = '{{ route('requestincomingdata') }}';
              $('#inputPolis').val('');
              $('#inputPolis').focus();
            }
          });
        }

      }
    });

    $('#start_date').datepicker({
      autoclose: true,
      format: "yyyy/mm/dd"
    });
    $('#end_date').datepicker({
      autoclose: true,
      format: "yyyy/mm/dd"
    });
    $('#cycle').datepicker({
      autoclose: true,
      format: "yyyymmdd"
    });
    $('.select2').select2();
    $('#form-item').submit(function(e) {
      e.preventDefault();
      var url = "change-courier";
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
@endsection