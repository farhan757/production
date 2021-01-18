@extends('adminlte::page')

@section('title', 'Form Incoming Material')

@section('content_header')
<!--    <h1>Home</h1>-->
@stop

@section('content')
<script>
  hideLoad();
</script>
  <div class="col-sm-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Form Generate Invoice</h3>
      </div>
      <div class="overlay dark" id="vload"><i class="fas fa-5x fa-sync-alt fa-spin"></i>&nbsp; &nbsp; &nbsp;<div class="text-bold pt-2" type="hide">Loading...</div></div>
            <form class="form-horizontal" name="form-item" id="form-item" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card-body">
                  
                    <div class="form-group row">
                        <label for="cycle" class="col-sm-2 control-label">Tgl Dari</label>
                        <div class="col-sm-4">
                            <div class="input-group date">                              
                              <input type="text" class="form-control pull-right datepicker" name="tgldari" id="tgldari" value="{{ $tgldari ?? '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cycle" class="col-sm-2 control-label">Tgl Sampai</label>
                        <div class="col-sm-4">
                            <div class="input-group date">                              
                              <input type="text" class="form-control pull-right datepicker" name="tglsampai" id="tglsampai" value="{{ $tglsampai ?? '' }}" required>
                            </div>
                        </div>
                    </div>                    

                    <div class="form-group row">
                        <label for="customer" class="col-sm-2 control-label">Customer</label>

                        <div class="col-sm-4">
                            <select onchange="getProject()" class="form-control select2" name="customer_id" id="customer_id">
                                @foreach($customers as $index=>$value)
                                <option value="{{ $value->id }}" 
                                    @if(isset($data->customer_id))
                                    @if($data->customer_id===$value->id) 
                                        selected
                                    @endif 
                                    @endif
                                    >{{ $value->name }}</option>
                                @endforeach
                              </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="project" class="col-sm-2 control-label">Project</label>

                        <div class="col-sm-4">
                            <select class="form-control select2" name="project_id" id="project_id" required>                                        
                              </select>
                        </div>
                     
                    </div>

                    <div class="form-group row">
                        <label for="period" class="col-sm-2 control-label">Periode</label>
                        <div class="col-sm-4">
                          <div class="input-group date">  
                            <input type="text" class="form-control pull-right periode" name="period" id="period" value="{{ $period ?? '' }}" required>
                          </div>
                          </div>
                    </div>                    

                    <div class="form-group row">
                        <div class="col-sm-3">
                          <div class="card card-outline card-success">
                            <div class="card-header">
                              <h3 class="card-title">Keterangan Invoice</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                  <div class="col-sm-5">
                                      <input type="checkbox" id="pkp" name="pkp">
                                      <label for="checkboxSuccess3">
                                        PKP
                                      </label>
                                  </div>   
                                  <div class="col-sm-7">
                                      <input type="checkbox" id="b_pkp" name="b_pkp">
                                      <label for="checkboxSuccess3">
                                        Bukan PKP
                                      </label>
                                  </div>                                                                                               
                                </div>
                                <div class="form-group row">                            
                                  <div class="col-sm-5">
                                      <input type="checkbox" id="tunai" name="tunai">
                                      <label for="checkboxSuccess3">
                                        Tunai
                                      </label>
                                  </div> 
                                  <div class="col-sm-7">
                                      <input type="checkbox" id="kredit" name="kredit">
                                      <label for="checkboxSuccess3">
                                        Kredit
                                      </label>
                                  </div>                                                                                             
                                </div>                            
                            </div>
                          </div>
                        </div>

                        <div class="col-sm-3">
                          <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Info Tambahan</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                  <div class="col-sm-9">
                                      <input type="checkbox" id="ppn" name="ppn" checked>
                                      <label for="checkboxSuccess3">
                                        PPN
                                      </label>
                                      <input type="number" name="t_ppn" id="t_ppn" style="width:50px;" value="10"> %                                                                          
                                  </div>                                                                                                                      
                                </div>     
                                <div class="form-group row">
                                    <div class="col-sm-9">
                                          <input type="checkbox" id="c_materai" name="c_materai"> 
                                          <label for="checkboxSuccess3">
                                            Materai
                                          </label>
                                          <input type="number" name="t_materai" id="t_materai" style="width:70px;" value="0">
                                      </div>                                  
                                </div>                            
                            </div>
                          </div>
                        </div>
                    </div>              
                </div>
                <!-- /.box-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-info" name="submit" id="submit" value="generate">Generate</button>                                       
                    <a href="#" onclick="preview()" class="btn btn-info" name="submit" id="submit" value="preview">Preview</a>                                       
                </div>

            </form>
	</div>
</div>

@stop

@section('css')
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
  <script>
    hideLoad();
  </script>
@stop

@section('js')
<!-- Select2 -->
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
    var rootUrl = "po";
    hideLoad();
    getProject();
    
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

    function hideLoad(){
       
       $('#vload').hide();
    }

    function showLoad()
    {
      //$('#vload').append('<i class="fas fa-5x fa-sync-alt fa-spin"></i>&nbsp; &nbsp; &nbsp;<div class="text-bold pt-2" type="hide">Loading...</div>');
      $('#vload').show();
    }

    function preview(){
      var project_id = $('#project_id').val();
      var pkp = $('#pkp').is(':checked'); 
      var b_pkp = $('#b_pkp').is(':checked'); 
      var kredit = $('#kredit').is(':checked');
      var tunai = $('#tunai').is(':checked');
      var c_ppn = $('#ppn').is(':checked');
      var c_materai = $('#c_materai').is(':checked');
      var tgldari = $('#tgldari').val();
      var tglsampai = $('#tglsampai').val();
      var t_ppn = 0;
      var t_materai = 0;
      if(c_ppn == true){
        t_ppn = $('#t_ppn').val();
      }
      if(c_materai == true){
        t_materai = $('#t_materai').val();
      }

      var param = "?project_id="+project_id+"&tgldari="+tgldari+"&tglsampai="+tglsampai+"&pkp="+pkp+"&b_pkp="+b_pkp+"&kredit="+kredit+"&tunai="+tunai+"&t_materai="+t_materai+"&t_ppn="+t_ppn;      
      
      window.open('preview/'+param,'_blank','toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=1000,height=1000')
      window.open('perincian/'+param,'_blank','toolbar=no,scrollbars=yes,resizable=yes,top=1000,left=1000,width=1000,height=1000')
      //alert(param)
    }

    $(function(){
          //Initialize Select2 Elements
      $('.select2').select2();
      hideLoad();
      $('.datepicker').datepicker({
          autoclose: true,
          format: "yyyy-mm-dd"
        });
        $('.periode').datepicker({
          autoclose: true,
          changeMonth: true,
          changeYear: true,
                   
          format: "MM yyyy"
        });        
      $('#form-item').submit(function(e) {
        e.preventDefault();                        
        showLoad();
        var url = "{{ route('saveinvoice') }}";
        var formData = new FormData($('#form-item')[0]);
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
                      var nopo = $("#nopo").val();
                      var tglpo = $("#datepicker").val();
                      var reidretc = "{{ route('geninv') }}";
                      //window.location.href = reidretc;
                      window.open('generate-invoice','_blank','toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=400,height=400');
                    }
                  });
              } else {
                  Swal.fire({
                    icon: 'error',
                    title: response.message,
                    onClose: () => {
                      var reidretc = "{{ route('geninv') }}";
                      //window.location.href =reidretc;
                      window.open('generate-invoice','_blank','toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=400,height=400');
                    }
                  });

              }
            },
            error: function(response) {
              hideLoad();
                Swal.fire({                    
                    icon: 'error',
                    title: 'Error',
                    onClose: () => {
                      var reidretc = "{{ route('geninv') }}";
                      window.location.href =reidretc;
                    }
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
