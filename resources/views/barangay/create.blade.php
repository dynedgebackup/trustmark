<form method="POST" action="{{ route('barangay.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group" id="parrent_appType">
                <label for="mun_no" class="form-label">{{ __('Municipality | City, Province, Region') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('mun_no') }}</span>
                    <div class="form-icon-user">
                        <select name="mun_no" id="mun_no" class="form-control select3" required style="width:100%;">
                            @foreach($arrapp_code as $key => $label)
                                <option value="{{ $key }}" {{ old('mun_no', $data->mun_no) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="validate-err" id="err_mun_no"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="brgy_name" class="form-label">{{ __('Barangay Name') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('brgy_name') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="brgy_name" id="brgy_name" class="form-control" required value="{{ old('brgy_name', $data->brgy_name) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_brgy_name"></span>
                </div>
            </div>  
            <div class="col-md-3">
                <div class="form-group">
                    <label for="brgy_code" class="form-label">{{ __('No.') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('brgy_code') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="brgy_code" id="brgy_code" class="form-control" required value="{{ old('brgy_code', $data->brgy_code) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_brgy_code"></span>
                </div>
            </div>   
            <div class="col-md-3">
                <div class="form-group" id="parrent_status">
                    <label for="status" class="form-label">{{ __('Status') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('status') }}</span>
                    <div class="form-icon-user">
                    <select name="is_active" id="is_active" class="form-control select3" required style="width:100%;">
                        <option value="1" {{ old('is_active', $data->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $data->is_active) == '0' ? 'selected' : '' }}>Cancel</option>
                    </select>
                    </div>
                    <span class="validate-err" id="err_is_active"></span>
                </div>
            </div>
        <div class="modal-footer" style="margin-top: 28px;">
        <button type="button" class="btn btn-secondary" style="font-family: sans-serif; font-size: 12px;" data-bs-dismiss="modal">Cancel</button>
            <input type="submit" name="submit" value="{{ ($data->id)>0 ? __('Save Changes') : __('Save Changes') }}" class="btn btn-primary" style="color: #fff;font-size: 12px;">
        </div>
    </div>
</form>

<script>
    document.getElementById('brgy_code').addEventListener('input', function (e) {
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
    select3Ajax("mun_no","parrent_appType","getBarngayMunProvRegionAjaxList");
});
</script>
