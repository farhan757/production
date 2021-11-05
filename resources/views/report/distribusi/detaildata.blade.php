  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Detail {{ $data->no_manifest }}</h4>
  </div>
  <div class="modal-body">
    <div class="row">
        <dl class="dl-horizontal">
          <dt>Cycle/Part</dt>
          <dd>{{ $data->cycle }}/{{ $data->part }}</dd>
          <dt>Tanggal pengiriman</dt>
          <dd>{{ $data->created_at }}</dd>
          <dt>Ekspedisi / Service</dt>
          <dd>{{ $data->ekspedisi }} / {{ $data->service }}</dd>
          <dt>Tgl Kirim</dt>
          <dd>{{ $data->tgl_kirim }}</dd>
        </dl>
    </div>
    <div class="row">
    <div class="card-body table-responsive p-0" style="height: 400px;">
            <table  class="table table-bordered table-head-fixed">
            <tr>
                <th>No Amplop</th>
                <th>Penerima</th>
                <th>Ekspedisi/Service</th>
            </tr>
            @foreach($list as $key=>$value)
            <tr>
                <td>{{ $value->barcode_env }}</td>
                <td>{{ $value->penerima }}</td>
                <td>{{ $value->ekspedisi }} / {{ $value->service }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
  </div>
