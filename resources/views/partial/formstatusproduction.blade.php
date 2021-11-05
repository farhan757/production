<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">
    <div class="overlay-wrapper"> 
       <div class="overlay dark" id="vload" ><i class="fas fa-5x fa-sync-alt fa-spin"></i>&nbsp; &nbsp; &nbsp;<div class="text-bold pt-2" type="hide">Loading...</div></div>        
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="modal-title">Update Status Production</h4>
            </div>
            <form action="" name='form-status' id='form-status' method="post" class="form-horizontal">
                {{ csrf_field() }}
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="form-group">
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
                    
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Note</label>

                        <div class="col-sm-8">
                            <input type="text" maxlength="50" class="form-control" id="note" name="note" placeholder="Notes">
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
