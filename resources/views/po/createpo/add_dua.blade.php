
    <tr id="row<?php echo $no; ?>">
        <td>
            <select class="form-control select2" name="components_id[]" id="components_id[]" required>
                @foreach($components as $index=>$value)
                <option value="{{ $value->id }}" @if(isset($data->id))
                    @if($data->id===$value->id)
                    selected
                    @endif
                    @endif
                    >{{ $value->code.'-'.$value->name }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="qty[]" id="qty[]" class="form-control"></td>
        <td>
            <a href="#" title="delete" class="text-danger hapus-baris" id="<?php echo $no; ?>"><i class="fas fa-trash"></i></a>
        </td>
    </tr>



<script type="text/javascript">
    jQuery('.datepicker').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });
    $('.select2').select2();
	$('.hapus-baris').on("click",function(){
		id = this.id;

		$('#row'+id).remove();
	});
</script>