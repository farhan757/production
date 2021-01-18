  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Detail {{ $data->name }}</h4>
  </div>
  <div class="modal-body">
    <div class="row">
        <dl class="dl-horizontal">
          <dt>Status Name</dt>
          <dd>{{ $data->name }}</dd>
          <dt>Icon</dt>
          <dd><i class="fa {{ $data->icon }}"></i> {{ $data->icon }}</dd>
          <dt>Description</dt>
          <dd>{{ $data->desc }}</dd>
          <dt>Result</dt>
          @foreach($results as $key=>$value)
          <dd><i class="fa fa-check" style="color: green;"></i> {{ $value->name }}</dd>
          @endforeach        
        </dl>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
  </div>
