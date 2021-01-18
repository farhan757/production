<div class="modal fade" id="modal-detail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">              
                <h4 class="modal-title" id="title-detail">Detail Data</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                  <dl class="row dl-horizontal">
                    <dt class="col-4">Cycle/Part</dt>
                    <dd class="col-8" id="cyclePart"></dd>
                    <dt class="col-4">Tanggal pengiriman</dt>
                    <dd class="col-8" id="created_at"></dd>
                    <dt class="col-4">Ekspedisi / Service</dt>
                    <dd class="col-8" id="ekspedisi"></dd>
                    <dt class="col-4">Tgl Kirim</dt>
                    <dd class="col-8" id="tgl_kirim"></dd>
                  </dl>
              </div>
              <div class="row">
                  <table class="table table-bordered">
                      <tr>
                          <th>No Amplop</th>
                          <th>Penerima</th>
                          <th>Ekspedisi/Service</th>
                      </tr>
                      <tbody id="table-body">
                        
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
