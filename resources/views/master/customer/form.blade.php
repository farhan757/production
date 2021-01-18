<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">              
          <h4 class="modal-title">Form Customer</h4>
        </div>
        <form method="POST" name='form-item' id='form-item'>
            <input type="hidden" name="id" id="id">
            <div class="modal-body">
                
                @csrf

                @foreach($forms as $key=>$value)
                    <div class="form-group row">
                        <label for="{{ $value['field'] }}" class="col-md-{{ $value['mdf'] }} col-form-label text-md-right">{{ $value['desc'] }}</label>

                        <div class="col-md-{{ $value['mdi'] }}">
                            <input id="{{ $value['field'] }}" type="{{ $value['type'] }}" class="form-control{{ $errors->has($value['field']) ? ' is-invalid' : '' }}" name="{{ $value['field'] }}" value=""
                            @if($value['required'])
                                required
                            @endif
                            >
                            
                            <div id="{{ $value['field'] }}-error" class="invalid-feedback">
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
                </button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
<!-- /.modal-dialog -->
</div>
