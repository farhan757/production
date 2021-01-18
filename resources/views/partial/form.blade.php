<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="modal-title">Form Customer</h4>
            </div>
            <form method="POST" name='form-item' id='form-item'>
            {{ csrf_field() }}
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                
                @foreach($forms as $key=>$value)
                    @if($value['type']=='select')
                        <div class="form-group row">
                            <label for="{{ $value['field'] }}" class="col-md-{{ $value['mdf'] }} col-form-label text-md-right">{{ $value['desc'] }}</label>

                            <div class="col-md-{{ $value['mdi'] }}">
                                <select class="form-control" name="{{ $value['field'] }}" id="{{ $value['field'] }}">
                                    @foreach($value['data'] as $index=>$value2)
                                    <option value="{{ $value2->id }}">{{ $value2->name }}</option>
                                    @endforeach
                                  </select>
                            </div>
                        </div>
                    @else
                        @if($value['type']=='selectg')
                            <div class="form-group row">
                                <label for="{{ $value['field'] }}" class="col-md-{{ $value['mdf'] }} col-form-label text-md-right">{{ $value['desc'] }}</label>

                                <div class="col-md-{{ $value['mdi'] }}">
                                    <select class="form-control" name="{{ $value['field'] }}" id="{{ $value['field'] }}">
                                        @foreach($value['data'] as $index=>$value2)
                                        <option value="{{ $value2->code }}">{{ $value2->name }}</option>
                                        @endforeach
                                      </select>
                                </div>
                            </div>
                        @else
                            @if($value['type']=='checkbox')
                                <div class="form-group row">
                                  <div class="offset-sm-{{ $value['mdf'] }} col-sm-{{ $value['mdi'] }}">
                                    <div class="checkbox">
                                      <label>
                                        <input id="{{ $value['field'] }}" name="{{ $value['field'] }}" type="checkbox" > {{ $value['desc'] }}
                                      </label>
                                    </div>
                                  </div>
                                </div>
                            @else
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
                            @endif
                        @endif
                    @endif
                @endforeach
                
            </div>
            <!-- /.box-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
                </button>
            </div>
            </form>
		</div>
	</div>
</div>
