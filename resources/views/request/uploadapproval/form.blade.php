<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="modal-title">Form Upload Approval</h4>
            </div>
            <form id="form-item" name="form-item" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="note" class="col-sm-2 control-label">Note</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="note" maxlength="50">
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
