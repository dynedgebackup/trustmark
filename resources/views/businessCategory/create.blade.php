<form method="POST" action="{{ route('businessCategory.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
           
            <div class="col-md-10">
                <div class="form-group">
                    <label for="name" class="form-label">{{ __('Category Name') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('name') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $data->name) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_name"></span>
                </div>
            </div>   
            <div class="col-md-2">
                <div class="form-group" id="parrent_status">
                    <label for="status" class="form-label">{{ __('Status') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('status') }}</span>
                    <div class="form-icon-user">
                    <select name="is_active" id="status" class="form-control select3" required style="width:100%;">
                        <option value="1" {{ old('is_active', $data->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ old('is_active', $data->is_active) == '2' ? 'selected' : '' }}>Cancel</option>
                    </select>
                    </div>
                    <span class="validate-err" id="err_status"></span>
                </div>
            </div>
            <div class="col-md-2" style="padding-top: 6px;">
                <div class="form-group">
                    
                    <div class="form-icon-user">

                    <input type="checkbox" 
                        name="is_others" 
                        id="is_others" 
                        class="form-check-input code" 
                        value="1" 
                        {{ old('is_others', $data->is_others) ? 'checked' : '' }}>
                    <label for="is_others" class="form-check-label" style="color: #000;font-size: 12px;font-weight: bold;">Others</label>
                       
                    </div>
                    <span class="validate-err" id="err_is_others"></span>
                </div>
            </div>
        <div class="modal-footer" style="margin-top: 28px;">
        <button type="button" class="btn btn-secondary" style="font-family: sans-serif; font-size: 12px;" data-bs-dismiss="modal">Cancel</button>
            <input type="submit" name="submit" value="{{ ($data->id)>0 ? __('Save Changes') : __('Save Changes') }}" class="btn btn-primary" style="color: #fff;font-size: 12px;">
        </div>
    </div>
</form>
<script >
$(document).ready(function() {
    $("#status").select3({dropdownAutoWidth : false,dropdownParent: $("#parrent_status")});
});
</script>
