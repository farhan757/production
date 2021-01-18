  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Detail {{ $data->name }}</h4>
  </div>
  <div class="modal-body">
    <div class="row">
        <dl class="dl-horizontal">
          <dt>Code</dt>
          <dd>{{ $data->code }}</dd>
          <dt>Company Name</dt>
          <dd>{{ $data->name }}</dd>
          <dt>Satuan</dt>
          <dd>{{ $data->satuan }}</dd>
          <dt>Group</dt>
          <dd>{{ $data->group }}</dd>
        </dl>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
  </div>
