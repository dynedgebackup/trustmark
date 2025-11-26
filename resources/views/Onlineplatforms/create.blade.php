<form method="POST" action="{{ route('onlineplatforms.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group" id="parrent_fee_id">
                    <label for="base_url" class="form-label">{{ __('Platform Link [URL]') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('base_url') }}</span>
                    <div class="form-icon-user">
                       <input type="text" name="base_url" id="base_url" class="form-control" required value="{{ old('base_url', $data->base_url) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_base_url"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group" id="parrent_fee_id">
                    <label for="platform_name" class="form-label">{{ __('Name') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('platform_name') }}</span>
                    <div class="form-icon-user">
                         <input type="text" name="platform_name" id="platform_name" class="form-control" required value="{{ old('platform_name', $data->platform_name) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_platform_name"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group" style="padding-top: 45px;">
                    
                    <div class="form-icon-user">

                    <input type="checkbox" 
                        name="with_irm" 
                        id="with_irm" 
                        class="form-check-input code" 
                        value="1" 
                        {{ old('with_irm', $data->with_irm) ? 'checked' : '' }}>
                    <label for="with_irm" class="form-check-label" style="color: #000;font-size: 12px;font-weight: bold;">with internal Redress Mechanism</label>
                       
                    </div>
                    <span class="validate-err" id="err_with_irm"></span>
                </div>
            </div>   
            <div class="col-md-6" style="display: none;">
                <div class="form-group" id="parrent_fee_id">
                    <label for="office_name" class="form-label">{{ __('Platform Logo') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('office_name') }}</span>
                    <div class="form-icon-user">
                         <input class="form-control custom-input" type="file"
                                                        id="platform_logo" name="platform_logo" accept=".jpg,.jpeg,.png,.pdf"
                                                        title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB">
                    </div>
                    <span class="validate-err" id="err_platform_logo"></span>
                </div>
            </div>
            <div class="col-md-6" style="display: none;">
                <div class="form-group">
                    
                    <div class="form-icon-user">
                        <img src="{{ asset('storage/' . $data->platform_logo) }}" width="100px" height="100px">
                    </div>
                    <span class="validate-err" id="err_exclude_due_to_bmbe"></span>
                </div>
            </div>   
           
            <div class="col-md-12" style="padding-top: 6px;">
               
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

