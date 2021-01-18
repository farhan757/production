  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Detail {{ $data->job_ticket }}</h4>
  </div>
  <div class="modal-body">
    <div class="row">
        <dl class="dl-horizontal">
          <dt>Customer</dt>
          <dd>{{ $data->customer_name }}</dd>
          <dt>Project</dt>
          <dd>{{ $data->project_name }}</dd>
          <dt>Cycle/Part</dt>
          <dd>{{ $data->cycle }}/{{ $data->part }}</dd>
          <dt>Waktu Submit</dt>
          <dd>{{ $data->created_at }}</dd>
          <dt>Submit by</dt>
          <dd>{{ $data->name }}</dd>
          <dt>Last Status</dt>
          <dd>{{ $data->last_status }}</dd>
        </dl>
    </div>
    <div class="row">
        <table class="table table-bordered">
            <tr>
                <th>No Amplop</th>
                <th>No Polis</th>
                <th>Penerima</th>
                <th>Ekspedisi/Service</th>
                <th>No Manifest</th>
            </tr>
            @foreach($list as $key=>$value)
            <tr>
                <td>{{ $value->barcode_env }}</td>
                <td>{{ $value->account_no }}</td>
                <td>{{ $value->penerima }}</td>
                <td>{{ $value->ekspedisi }} / {{ $value->service }}</td>
                <td>{{ $value->no_manifest }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="row">
            <div class="box-group" id="accordion">
                <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                <div class="panel box box-success">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                        Track for Job Ticket {{ $data->job_ticket }}
                      </a>
                    </h4>
                  </div>
                  <div id="collapseThree" class="panel-collapse collapse">
                    <div class="box-body">
                      <ul class="timeline">

                          <!-- timeline item -->
                          @foreach($transP as $key=>$value)
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
              </div>
            </div>
        </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
  </div>
