<form method="POST" action="{{ route('municipality.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group" id="parrent_appType">
                    <label for="prov_no" class="form-label">{{ __('Region => Province') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('prov_no') }}</span>
                    <div class="form-icon-user">
                        <select name="prov_no" id="prov_no" class="form-control select3" required style="width:100%;">
                            @foreach($arrapp_code as $key => $label)
                                <option value="{{ $key }}" {{ old('prov_no', $data->prov_no) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="validate-err" id="err_prov_no"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="mun_desc" class="form-label">{{ __('Municipality | City Name') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('mun_desc') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="mun_desc" id="mun_desc" class="form-control" required value="{{ old('mun_desc', $data->mun_desc) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_mun_desc"></span>
                </div>
            </div>  
            <div class="col-md-3">
                <div class="form-group">
                    <label for="mun_no" class="form-label">{{ __('No.') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('mun_no') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="mun_no" id="mun_no" class="form-control" required value="{{ old('mun_no', $data->mun_no) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_mun_no"></span>
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
    select3Ajax("prov_no","parrent_appType","provinceRegionsAjaxList");
});
</script>
