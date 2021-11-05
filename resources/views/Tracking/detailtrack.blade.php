<div class="modal fade" id="modal-detail-production">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">              
          <h4 class="modal-title" id="title-detail-production">Detail</h4>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-md-10">
            <dl class="row" class="dl-horizontal">
              <dt class="col-4">Customer</dt>
              <dd class="col-8" id="customer_name"></dd>
              <dt class="col-4">Project</dt>
              <dd class="col-8" id="project_name"></dd>
              <dt class="col-4">Cycle/Part</dt>
              <dd class="col-8" id="cyclePart"></dd>
              <dt class="col-4">Waktu Submit</dt>
              <dd class="col-8" id="created_at"></dd>
              <dt class="col-4">Submit by</dt>
              <dd class="col-8" id="usersubmit"></dd>
              <dt class="col-4">Last Status</dt>
              <dd class="col-8" id="last_status"></dd>
            </dl>            
          </div>

        </div>

      <div class="card-body">
        <div id="accordion">
          
          <div id="transF">

          </div>

          <div class="card card-primary">
            <div class="card-header">
              <h4 class="card-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTransp" id="titleCollapsibletransp">
                  Collapsible Group Item #1
                </a>
              </h4>
            </div>
            <div id="collapseTransp" class="panel-collapse collapse in">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <!-- The time line -->
                    <div class="timeline" id="timelinetransP">
                      <!-- timeline item -->
                      
                      <!-- END timeline item -->
                      
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
          

        </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
