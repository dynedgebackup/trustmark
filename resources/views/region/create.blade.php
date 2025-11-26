<form method="POST" action="{{ route('region.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="reg_region" class="form-label">{{ __('Region Name') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('reg_region') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="reg_region" id="reg_region" class="form-control" required value="{{ old('reg_region', $data->reg_region) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_reg_region"></span>
                </div>
            </div>  
            <div class="col-md-6">
                <div class="form-group">
                    <label for="reg_description" class="form-label">{{ __('Description') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('name') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="reg_description" id="reg_description" class="form-control" required value="{{ old('reg_description', $data->reg_description) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_reg_description"></span>
                </div>
            </div>   
            <div class="col-md-4">
                <div class="form-group" id="parrent_status">
                <label for="reg_no" class="form-label">{{ __('Region No.') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('reg_no') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="reg_no" id="reg_no" class="form-control" required value="{{ old('reg_no', $data->reg_no) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_reg_no"></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group" id="parrent_status">
                    <label for="status" class="form-label">{{ __('Status') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('status') }}</span>
                    <div class="form-icon-user">
                    <select name="is_active" id="status" class="form-control select3" required style="width:100%;">
                        <option value="">Select Status</option>
                        <option value="1" {{ old('is_active', $data->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ old('is_active', $data->is_active) == '2' ? 'selected' : '' }}>Cancel</option>
                    </select>
                    </div>
                    <span class="validate-err" id="err_status"></span>
                </div>
            </div>
        <div class="modal-footer" style="margin-top: 28px;">
        <button type="button" class="btn btn-secondary" style="font-family: sans-serif; font-size: 12px;" data-bs-dismiss="modal">Cancel</button>
            <input type="submit" name="submit" value="{{ ($data->id)>0 ? __('Save Changes') : __('Save Changes') }}" class="btn btn-primary" style="color: #fff;font-size: 12px;">
        </div>
    </div>
</form>
<script>
    document.getElementById('amount').addEventListener('input', function (e) {
        let value = this.value;
        let valid = value.match(/^\d+(\.\d{0,2})?$/);

        if (!valid && value !== '') {
            this.value = value.slice(0, -1); // remove last invalid char
        }
    });
</script>
