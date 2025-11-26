<form method="POST" action="{{ route('ApplicationStatusCannedMessage.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group" id="parrent_appType">
                    <label for="app_status_id" class="form-label">{{ __('Application Status') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('app_status_id') }}</span>
                    <div class="form-icon-user">
                        <select name="app_status_id" id="app_status_id" class="form-control select3" required style="width:100%;">
                            @foreach($arrapp_code as $key => $label)
                                <option value="{{ $key }}" {{ old('app_status_id', $data->app_status_id) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="validate-err" id="err_app_status_id"></span>
                </div>
            </div>
            <div class="col-md-9">
                <div class="form-group">
                    <label for="description" class="form-label">{{ __('Message Description') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('description') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="description" id="description" class="form-control" required value="{{ old('description', $data->description) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_description"></span>
                </div>
            </div>  
           
            <div class="col-md-3">
                <div class="form-group" id="parrent_status">
                    <label for="status" class="form-label">{{ __('Status') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('status') }}</span>
                    <div class="form-icon-user">
                    <select name="status" id="status" class="form-control select3" required style="width:100%;">
                        <option value="1" {{ old('status', $data->status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $data->status) == '0' ? 'selected' : '' }}>Cancel</option>
                    </select>
                    </div>
                    <span class="validate-err" id="err_status"></span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="remarks" class="form-label">{{ __('Remarks') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('remarks') }}</span>
                    <div class="form-icon-user">
                        <textarea name="remarks" id="remarks" class="form-control" rows="6" required style="font-size:12px; padding:9px;">{{ old('remarks', $data->remarks) }}</textarea>
                    </div>
                    <span class="validate-err" id="err_remarks"></span>
                </div>
            </div>
        <div class="modal-footer" style="margin-top: 28px;">
        <button type="button" class="btn btn-secondary" style="font-family: sans-serif; font-size: 12px;" data-bs-dismiss="modal">Cancel</button>
            <input type="submit" name="submit" value="{{ ($data->id)>0 ? __('Save Changes') : __('Save Changes') }}" class="btn btn-primary" style="color: #fff;font-size: 12px;">
        </div>
    </div>
</form>

<script>
    document.getElementById('mun_no').addEventListener('input', function (e) {
        let value = this.value;
        let valid = value.match(/^\d+(\.\d{0,2})?$/);

        if (!valid && value !== '') {
            this.value = value.slice(0, -1); // remove last invalid char
        }
    });
</script>
<script >
$(document).ready(function() {
    $("#is_active").select3({dropdownAutoWidth : false,dropdownParent: $("#parrent_status")});
    select3Ajax("app_status_id","parrent_appType","applicationStatusAjaxList");
});
</script>
