<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="modal-title">Form Upload Approval</h4>
            </div>
                    <form action="" name="form-item" id="form-item" method="post" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="result_id" class="col-sm-2 control-label">Result</label>

                            <div class="col-sm-4">
                                <select class="form-control" name="result_id">
                                    @foreach($results as $index=>$value)
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
                            <label for="name" class="col-sm-2 control-label">Note</label>

                            <div class="col-sm-8">
                                <input type="text" maxlength="50" class="form-control" id="note" name="note" placeholder="Notes">
                            </div>
                        </div>

                        <div class="form-group row" id="file">
                            <label for="method" class="col-sm-2 control-label" >File (Optional)</label>

                            <div class="col-sm-4">
                                <input type="file" id="file" name="file">                              
                            </div>
                            <div class="col-sm-4">
                                <div class="alert alert-light" role="alert">
                                    Max (5MB)
                                </div>
                            </div>
                        </div>                       
                    </div>
                    <!-- /.box-body -->
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
