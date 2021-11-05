<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">

       <div class="overlay-wrapper"> 
       <div class="overlay dark" id="vload" ><i class="fas fa-5x fa-sync-alt fa-spin"></i>&nbsp; &nbsp; &nbsp;<div class="text-bold pt-2" type="hide">Loading...</div></div>
        <div class="modal-content">    
            <div class="modal-header">
              <h4 class="modal-title" id="modal-title">Form Upload Job List</h4>              
            </div>        
            <form class="form-horizontal" name="form-item" id="form-item" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card-body">
                <div class="modal-body">                
                    <div class="form-group row">
                        <label for="customer" class="col-sm-2 control-label">Customer</label>

                        <div class="col-sm-8">
                            <select class="form-control select2" onchange="getProject()"  name="customer_id" id="customer_id">
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
                        <label for="project" class="col-sm-2 control-label">Project</label>

                        <div class="col-sm-8">
                            <select class="form-control select2" name="project_id" id="project_id" required>                                        
                              </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cycle" class="col-sm-2 control-label">Cycle</label>
                        <div class="col-sm-4">
                            <div class="input-group date">                              
                              <input type="text" class="form-control pull-right" name="cycle" id="cycle" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="part" class="col-sm-2 control-label">Part</label>

                        <div class="col-sm-4">
                            <select class="form-control" name="part" required>
                                @foreach($parts as $key=>$value)
                                <option value="{{ $value->code }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="jenis" class="col-sm-2 control-label">Jenis</label>

                        <div class="col-sm-4">
                            <select class="form-control" name="jenis" required>
                                @foreach($jenis as $key=>$value)
                                <option value="{{ $value->code }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="method" class="col-sm-2 control-label" >File</label>

                        <div class="col-sm-4">
                            <input type="file" id="file" name="file" required>

                              <p class="help-block">Select File</p>
                        </div>
                        <div class="col-sm-4">
                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                Max (5MB)
                            </p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="note" class="col-sm-2 control-label">Note</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" id="note" name="note" maxlength="500" placeholder="Notes"></textarea>
                        </div>
                    </div>
                </div>
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
</div>
