  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Detail {{ $data->no_manifest }}</h4>
  </div>
  <div class="modal-body">
    <div class="row">
        <dl class="dl-horizontal">
          <dt>No Amplop</dt>
          <dd id="barcode_env"></dd>
          <dt>No Akun</dt>
          <dd id="account_no"></dd>
          <dt>Penerima</dt>
          <dd>{{ $data->penerima }}</dd>
          <dt>Tertanggung</dt>
          <dd>{{ $data->tertanggung }}</dd>
          <dt>Alamat</dt>
          <dd>{{ $data->address1 }}</dd>
          <dd>{{ $data->address2 }}</dd>
          <dd>{{ $data->address3 }}</dd>
          <dd>{{ $data->city }} {{ $data->pos }}</dd>
          <dt>Ekspedisi / Service</dt>
          <dd>{{ $data->ekspedisi }} / {{ $data->service }}</dd>
          <dt>No Manifest</dt>
          <dd>{{ $data->no_manifest }} </dd>
          <dt>File Ticket</dt>
          <dd>{{ $file->ticket ?? '' }}</dd>
          <dt>Production Ticket</dt>
          <dd>{{ $prod->job_ticket ?? '' }}</dd>
        </dl>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
  </div>
