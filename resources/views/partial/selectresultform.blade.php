<div class="modal fade" id="modal-result-form">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal-title-result-form">Form </h4>
      </div>
      <form class="form-horizontal" method="post" name='form-result' id='form-result'>
          {{ csrf_field() }}
          <input type="hidden" name="id-result" id="id-result">
          <div class="modal-body">
                  <table class="table">
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Name</th>
                    <th>Desc</th>
                  </tr>
                  @foreach($results as $key=>$value)
                  <tr>
                    <td>
                      <label>
                        <input type="checkbox" name="checkbox[{{ $loop->iteration-1 }}]" value="{{ $value->id }}" id="checkbox-{{ $value->id }}">
                      </label>
                    </td>
                    <td>
                      <strong>{{ $value->name }}</strong>
                  </td>
                    <td>{{ $value->desc }} </td>
                  </tr>
                  @endforeach
              </table>
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