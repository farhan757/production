<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">

    <div class="overlay-wrapper"> 
       <div class="overlay dark" id="vload" ><i class="fas fa-5x fa-sync-alt fa-spin"></i>&nbsp; &nbsp; &nbsp;<div class="text-bold pt-2" type="hide">Loading...</div></div>        
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="modal-title">Form Customer</h4>
            </div>
                <form action="" method="post" name='form-distribusi' id='form-distribusi'>
                {{ csrf_field() }}
                <input type="hidden" name="no_manifest" id="no_manifest">
                <div class="modal-body">
                        <div class="form-group row">
                            <label for="tgl_kirim" class="col-sm-2 control-label">Tanggal kirim</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                  
                                  <input type="text" class="form-control pull-right" name="tgl_kirim" id="tgl_kirim" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tgl_kirim" class="col-sm-2 control-label">Nama Kurir Pickup</label>
                            <div class="col-sm-8">
                                <div class="input-group text">
                                  
                                  <input type="text" class="form-control pull-right" name="nama_kurir" id="nama_kurir" required>
                                </div>
                            </div>
                        </div>                        
                </div>
                <!-- /.box-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
                </button>
            </div>
            </form>
        </div>
    </div>
    </div>
</div>

