<div class="modal fade" id="modal-detail-material2">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">              
          <h4 class="modal-title" id="title-detail-material2">Detail</h4>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-md-10">
            <dl class="row" class="dl-horizontal">
              <dt class="col-4">Kode Vendor</dt>
              <dd class="col-8" id="codevendor2"></dd>
              <dt class="col-4">Nama Vendor</dt>
              <dd class="col-8" id="namavendor2"></dd>
              <dt class="col-4">Create by</dt>
              <dd class="col-8" id="createby2"></dd>
              <dt class="col-4">Tanggal PO</dt>
              <dd class="col-8" id="tgl_po2"></dd>              
            </dl>            
          </div>

        </div>
        <div class="row">
        <div class="card-body table-responsive p-0" style="height: 400px;">
            <table  class="table table-bordered table-head-fixed">
              <thead>
                <tr>
                    <th>Kode</th>
                    <th>Deskripsi</th>
                    <th>Qty Order</th>
                    <th>Qty In</th>
                    <th>Satuan</th>
                </tr>            
              </thead>
              <tbody id="tbody-detail2">
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-5 table-responsive">
            File Upload Bukti
            <table class="table table-bordered">
              <thead>
                <tr>
                    <th>File Name</th>
                    <th>Action</th>
                </tr>            
              </thead>
              <tbody id="tbody-bukti">
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