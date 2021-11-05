<div class="modal fade" id="modal-task-form">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal-title-task-form">Form </h4>
      </div>
      <form class="form-horizontal" method="post" name='form-task' id='form-task'>
          {{ csrf_field() }}
          <input type="hidden" name="id-task" id="id-task">
          <div class="modal-body">
          <div class="card-body table-responsive p-0" style="height: 400px;">
            <table  class="table table-bordered table-head-fixed">
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Name</th>
                    <th>Sort</th>
                    <th>Desc</th>
                  </tr>
                  @foreach($tasks as $key=>$value)
                  <tr>
                    <td>
                      <label>
                        <input type="checkbox" name="checkbox[{{ $loop->iteration-1 }}]" value="{{ $value->id }}" id="checkbox-{{ $value->id }}">
                      </label>
                    </td>
                    <td> <i class="fas {{ $value->icon }}"></i>
                      <strong>{{ $value->name }}</strong>
                  </td>
                  <td>
                    <input type="number" name="sort[]" id="sort-checkbox-{{ $value->id }}">
                  </td>
                    <td>{{ $value->desc }} </td>
                  </tr>
                  @endforeach
              </table>
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