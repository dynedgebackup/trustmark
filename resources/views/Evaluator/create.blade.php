<form method="POST" action="{{ route('evaluator.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group" id="parrent_appType">
                    <label for="user_id" class="form-label">{{ __('User') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('office_name') }}</span>
                    <div class="form-icon-user">
                        @if($data->id >0)
                        <select name="user_id" id="user_id1" class="form-control " required style="width:100%;" readonly onmousedown="return false;" onkeydown="return false;">
                            
                            @foreach($users as $key => $label)
                                <option value="{{ $key }}" {{ old('user_id', $data->user_id) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @else
                        <select name="user_id" id="user_id" class="form-control select3" required style="width:100%;">
                            <option value="">-- Select user --</option>
                            @foreach($users as $key => $label)
                                <option value="{{ $key }}" {{ old('user_id', $data->user_id) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <span class="validate-err" id="err_user_id"></span>
                </div>
            </div>
            <div class="col-md-2" style="padding-top: 6px;">
                <div class="form-group">
                    
                    <div class="form-icon-user">

                    <input type="checkbox" 
                        name="is_admin" 
                        id="is_admin" 
                        class="form-check-input code" 
                        value="1" 
                        {{ old('is_admin', $data->is_admin) ? 'checked' : '' }}>
                    <label for="is_admin" class="form-check-label" style="color: #000;font-size: 12px;font-weight: bold;">Admin</label>
                       
                    </div>
                    <span class="validate-err" id="err_is_admin"></span>
                </div>
            </div> 
            <div class="col-md-6" style="padding-top: 6px;">
                <div class="form-group">
                    
                    <div class="form-icon-user">

                    <input type="checkbox" 
                        name="is_evaluator" 
                        id="is_evaluator" 
                        class="form-check-input code" 
                        value="1" 
                        {{ old('is_evaluator', $data->is_evaluator) ? 'checked' : '' }}>
                    <label for="is_evaluator" class="form-check-label" style="color: #000;font-size: 12px;font-weight: bold;">Evaluator</label>
                       
                    </div>
                    <span class="validate-err" id="err_is_evaluator"></span>
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
<script >
$(document).ready(function() {
    select3Ajax("user_id","parrent_appType","userAjaxList");
});
</script>
