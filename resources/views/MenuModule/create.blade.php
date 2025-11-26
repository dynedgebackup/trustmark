<form method="POST" action="{{ route('MenuModule.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group" id="parrent_appType">
                    <label for="menu_group_id" class="form-label">{{ __('Group') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('menu_group_id') }}</span>
                    <div class="form-icon-user">
                        <select name="menu_group_id" id="menu_group_id" class="form-control select3" required style="width:100%;">
                            @foreach($arrapp_code as $key => $label)
                                <option value="{{ $key }}" {{ old('menu_group_id', $data->menu_group_id) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="validate-err" id="err_menu_group_id"></span>
                </div>
            </div> 
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label">{{ __('Name') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('name') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $data->name) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_name"></span>
                </div>
            </div>  
            <div class="col-md-6">
                <div class="form-group">
                    <label for="code" class="form-label">{{ __('Code') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('code') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="code" id="code" class="form-control" required value="{{ old('code', $data->code) }}" style="font-size:12px; padding:9px;" readonly>
                    </div>
                    <span class="validate-err" id="err_code"></span>
                </div>
            </div>  
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-label">{{ __('Description') }}</label>
                    <span class="validate-err">{{ $errors->first('name') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="description" id="description" class="form-control" value="{{ old('description', $data->description) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_description"></span>
                </div>
            </div>   
            <div class="col-md-10">
                <div class="form-group" id="parrent_status">
                <label for="slug" class="form-label">{{ __('Slug') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('slug') }}</span>
                    <div class="form-icon-user">
                        <input type="slug" name="slug" id="slug" class="form-control" required value="{{ old('slug', $data->slug) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_slug"></span>
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
        <div class="modal-footer" style="margin-top: 28px;">
        <button type="button" class="btn btn-secondary" style="font-family: sans-serif; font-size: 12px;" data-bs-dismiss="modal">Cancel</button>
            <input type="submit" name="submit" value="{{ ($data->id)>0 ? __('Save Changes') : __('Save Changes') }}" class="btn btn-primary" style="color: #fff;font-size: 12px;">
        </div>
    </div>
</form>
<script>
    $(document).ready(function () {
        $('#name').on('input', function () {
            $('#code').val($(this).val());
        });
    });
    $(document).ready(function() {
    $("#status").select3({dropdownAutoWidth : false,dropdownParent: $("#parrent_status")});
    select3Ajax("menu_group_id","parrent_appType","getmenuGroupAjaxList");
});
</script>
