<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">              
          <h4 class="modal-title">Register User</h4>
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
                            <span id="{{ $value['field'] }}-error" class="invalid-feedback">
                            </span>
                        </div>
                    </div>
                @endforeach

                <div class="row" style="padding-bottom: 30px">
                    <div class="col-md-12">
                    <strong class="pull-right">Info User</strong>
                    </div>
                </div>
                <div class="form-group row">
                        <label for="customer" class="col-md-4 col-form-label text-md-right">Customer</label>

                        <div class="col-md-6">
                            <select onchange="getProject()" class="form-control" name="customer_id" id="customer_id">
                                <option value="0">Internal</option>
                                @foreach($customers as $index=>$value)
                                <option value="{{ $value->id }}" 
                                    @if(isset($data->customer_id))
                                    @if($data->customer_id===$value->id) 
                                        selected
                                    @endif 
                                    @endif
                                    >{{ $value->name }}</option>
                                @endforeach
                              </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="project" class="col-md-4 col-form-label text-md-right">Project</label>

                        <div class="col-md-6">
                            <select class="form-control" name="project_id" id="project_id" required>                                        
                              </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="level" class="col-md-4 col-form-label text-md-right">Level</label>

                        <div class="col-md-6">
                            <select class="form-control" name="level" id="level" required>
                            <option value="0">Developer</option> 
                            <option value="1">Administrator</option>
                            <option value="2">Super User</option>
                            <option value="3" selected="true">User</option>
                            </select>
                        </div>
                    </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        
                    </div>
                </div>
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
