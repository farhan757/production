<div class="modal fade" id="modal-detail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">              
              <h4 class="modal-title" id="title-detail">Detail</h4>
          </div>
          <div class="modal-body">
            <div class="row">
                <dl class="row" id="dl-detail">
                  <dt class="col-4">Ticket</dt>
                  <dd class="col-8" id="ticket"></dd>
                  <dt class="col-4">Tanggal Submit</dt>
                  <dd class="col-8" id="created_at"></dd>
                  <dt class="col-4">Project Name</dt>
                  <dd class="col-8" id="project_name"></dd>
                  <dt class="col-4">File Name</dt>
                  <dd class="col-8" id="file_name"></dd>
                  <dt class="col-4">Cycle/Part/Jenis</dt>
                  <dd class="col-8" id="cyc"></dd>
                  <dt class="col-4">Notes</dt>
                  <dd class="col-8" id="info"></dd>
                </dl>
            </div>
          </div>

          <div class="col-xs-5 table-responsive">
              <div class="card-body">
                <div id="accordion">
                  <div class="card card-primary">
                    <div class="card-header">
                      <h4 class="card-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTransf" id="titleCollapsibletransf">
                          Collapsible Group Item #1
                        </a>
                      </h4>
                    </div>
                    <div id="collapseTransf" class="panel-collapse collapse in">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-12">
                            <!-- The time line -->
                            <div class="timeline" id="timelinetransF">
                              <!-- timeline item -->
                              
                              <!-- END timeline item -->
                              
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>

                  <div id="transP">

                  </div>
                  <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                  

                </div>
              </div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          </div>
        </div>
</div>
