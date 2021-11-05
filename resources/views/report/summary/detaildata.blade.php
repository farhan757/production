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
          <dd>{{ $data->username }}</dd>
          <dt>Last Status</dt>
          <dd>{{ $data->last_status }}</dd>
        </dl>
    </div>
    <div class="row">
    <div class="col-xs-5 table-responsive">
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
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
  </div>
