<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="modal-title">Form Upload Job List</h4>
            </div>
            
            <form name="form-item" id="form-item" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="customer_id" id="customer_id">
                <input type="hidden" name="project_id" value="{{ $data->project_id }}">
                <input type="hidden" name="cycle" value="{{ $data->cycle }}">
                <input type="hidden" name="part" value="{{ $data->part }}">
                <input type="jenis" name="jenis" value="{{ $data->jenis }}">
                <input type="note" name="note" value="{{ $data->note }}">
                <div class="box-body">
                    <p class="text-muted well well-sm no-shadow">
                        <strong>Summary :</strong><br>
                        Customer : {{ $value->cureo}}
                    </p>
                    <div class="form-group">
                        <label for="note" class="col-sm-2 control-label">Note</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="note" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
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