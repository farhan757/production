<div class="modal fade" id="modal-component-form">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal-title-component-form">Form </h4>
      </div>
      <form class="form-horizontal" method="post" name='form-component' id='form-component'>
          {{ csrf_field() }}
          <input type="hidden" name="id-component" id="id-component">
          <div class="modal-body">
                  <table class="table">
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Sort</th>
                    <th>Code</th>
                    <th>Name</th>
                  </tr>
                  @foreach($components as $key=>$value)
                  <tr>
                    <td>
                      <label>
                        <input type="checkbox" name="checkbox[{{ $loop->iteration-1 }}]" value="{{ $value->id }}" id="comp-checkbox-{{ $value->id }}">
                      </label>
                    </td>
                  <td>
                    <input type="number" name="sort[]" id="comp-sort-checkbox-{{ $value->id }}">
                  </td>
                  <td>
                      {{ $value->code }}
                  </td>

                    <td>{{ $value->name }} </td>
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