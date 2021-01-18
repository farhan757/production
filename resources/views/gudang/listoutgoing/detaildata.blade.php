<div class="modal fade" id="modal-detail-material">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">              
          <h4 class="modal-title" id="title-detail-material">Detail</h4>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-md-10">
            <dl class="row" class="dl-horizontal">
              <dt class="col-4">Kode Project</dt>
              <dd class="col-8" id="codevendor"></dd>
              <dt class="col-4">Nama Project</dt>
              <dd class="col-8" id="namavendor"></dd>
              <dt class="col-4">Create by</dt>
              <dd class="col-8" id="createby"></dd>
              <dt class="col-4">Tanggal Job</dt>
              <dd class="col-8" id="tgl_po"></dd>  
              <dt class="col-4">Note</dt>
              <dd class="col-8" id="note"></dd>                             
            </dl>            
          </div>

        </div>
        <div class="row">
          <div class="col-xs-5 table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                    <th>Kode</th>
                    <th>Deskripsi</th>
                    <th>Qty Order</th>                    
                    <th>Satuan</th>
                </tr>            
              </thead>
              <tbody id="tbody-detail2">
              </tbody>
            </table>
          </div>
        </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>