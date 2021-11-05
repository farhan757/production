<div class="modal fade" id="modal-form-cancel">
    <div class="modal-dialog modal-lg">
    <div class="overlay-wrapper"> 
       
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="modal-title-cancel">Update Status Production</h4>
            </div>
            <form action="" name='form-status' id='form-status' method="post" class="form-horizontal">
                {{ csrf_field() }}
                <input type="hidden" name="id" id="id">
                <div class="modal-body">                    
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Note</label>

                        <div class="col-sm-13">
                            <textarea type="text" class="form-control" id="note" name="note" rows="5"  placeholder="Notes" required></textarea>
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
