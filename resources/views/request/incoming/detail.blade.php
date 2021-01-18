  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Detail {{ $data->ticket }}</h4>
  </div>
  <div class="modal-body">
    <div class="row">
        <dl class="dl-horizontal">
          <dt>Ticket</dt>
          <dd id="ticket"></dd>
          <dt>Tanggal Submit</dt>
          <dd id="created_at">{{ $data->created_at }}</dd>
          <dt>Project Name</dt>
          <dd id="project_name">{{ $data->project_name }}</dd>
          <dt>File Name</dt>
          <dd id="file_name">{{ $data->file_name }}</dd>
          <dt>Method</dt>
          <dd id="method_id">{{ $data->method_id }}</dd>
          <dt>Notes</dt>
          <dd id="info">{{ $data->info }}</dd>
        </dl>
    </div>
  </div>

      <div class="box-group" id="accordion">
          <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
          <div class="panel box box-info">
            <div class="box-header with-border">
              <h4 class="box-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                  Track for Ticket {{ $data->ticket }}
                </a>
              </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse">
              <div class="box-body">
                <ul class="timeline">

                    <!-- timeline item -->
                    @foreach($transF as $key=>$value)
                    <li>
                        <!-- timeline icon -->
                        <i class="fa {{ $value->status_icon }} bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fa fa-clock-o"></i> {{ $value->created_at }}</span>

                            <h3 class="timeline-header"><a href="#">{{ $value->username }}</a> {{ $value->status_name }}</h3>

                            <div class="timeline-body">
                              <p>{{ $value->note }}</p>
                              <p>Result : {{ $value->result_name }}</p>
                            </div>

                            <!--<div class="timeline-footer">
                                <a class="btn btn-primary btn-xs">...</a>
                            </div>-->
                        </div>
                    </li>
                    @endforeach
                </ul>
              </div>
            </div>
          </div>

          @foreach($transP as $key=>$values)
            <div class="panel box box-info">
              <div class="box-header with-border">
                <h4 class="box-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree2">
                    Track Production for Job Ticket {{ $values['job_ticket'] }}
                  </a>
                </h4>
              </div>
              <div id="collapseThree2" class="panel-collapse collapse">
                <div class="box-body">
                  <ul class="timeline">

                      <!-- timeline item -->
                      @foreach($values['data'] as $key=>$value)
                      <li>
                          <!-- timeline icon -->
                          <i class="fa {{ $value->status_icon }} bg-blue"></i>
                          <div class="timeline-item">
                              <span class="time"><i class="fa fa-clock-o"></i> {{ $value->created_at }}</span>

                              <h3 class="timeline-header"><a href="#">{{ $value->username }}</a> {{ $value->status_name }}</h3>

                              <div class="timeline-body">
                                <p>{{ $value->note }}</p>
                                <p>Result : {{ $value->result_name }}</p>
                              </div>

                              <!--<div class="timeline-footer">
                                  <a class="btn btn-primary btn-xs">...</a>
                              </div>-->
                          </div>
                      </li>
                      @endforeach
                  </ul>
                </div>
              </div>
            </div>
          @endforeach
        </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
  </div>
