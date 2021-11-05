<div class="modal fade" id="modal-detail-inv">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">              
          <h4 class="modal-title" id="title-detail-inv">Detail</h4>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-md-10">
            <dl class="row" class="dl-horizontal">
              <dt class="col-4">No Invoice</dt>
              <dd class="col-8" id="no_inv"></dd>
              <dt class="col-4">Nama Customer</dt>
              <dd class="col-8" id="nm_cust"></dd>
              <dt class="col-4">Nama Project</dt>
              <dd class="col-8" id="nm_pro"></dd>
              <dt class="col-4">Code Project</dt>
              <dd class="col-8" id="kd_pro"></dd> 
              <dt class="col-4">Tgl Generate</dt>
              <dd class="col-8" id="tgl_gen"></dd> 
              <dt class="col-4">Tgl Jatuh Tempo</dt>
              <dd class="col-8" id="tgl_jt"></dd> 
              <dt class="col-4">Tgl Terbayar</dt>
              <dd class="col-8" id="tgl_bayar"></dd>                                                        
            </dl>            
          </div>

        </div>
        <div class="row">
          <div class="col-xs-5 table-responsive">
            File Attach
            <table class="table table-bordered">
              <thead>
                <tr>
                    <th>File Name</th>
                    <th>Note</th>
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