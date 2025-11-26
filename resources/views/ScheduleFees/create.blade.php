<form method="POST" action="{{ route('scheduleFees.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group" id="parrent_appType">
                    <label for="office_name" class="form-label">{{ __('Application Type') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('office_name') }}</span>
                    <div class="form-icon-user">
                        <select name="app_code" id="app_code" class="form-control select3" required style="width:100%;">
                            <option value="">-- Select Application Type --</option>
                            @foreach($arrapp_code as $key => $label)
                                <option value="{{ $key }}" {{ old('app_code', $data->app_code) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="validate-err" id="err_app_code"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group" id="parrent_fee_id">
                    <label for="office_name" class="form-label">{{ __('Fee Description') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('office_name') }}</span>
                    <div class="form-icon-user">
                        <select name="fee_id" id="fee_id" class="form-control select3" required style="width:100%;">
                            <option value="">Application Type</option>
                            @foreach($arrfee_id as $key => $label)
                                <option value="{{ $key }}" {{ old('fee_id', $data->fee_id) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="validate-err" id="err_fee_id"></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="amount" class="form-label">{{ __('Standard Amount') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('amount') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="amount" id="amount" class="form-control" required value="{{ old('amount', $data->amount) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_amount"></span>
                </div>
            </div>   
            <div class="col-md-2">
                <div class="form-group" id="parrent_status">
                    <label for="status" class="form-label">{{ __('Status') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('status') }}</span>
                    <div class="form-icon-user">
                    <select name="status" id="status" class="form-control select3" required style="width:100%;">
                        <option value="">Select Status</option>
                        <option value="1" {{ old('status', $data->status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ old('status', $data->status) == '2' ? 'selected' : '' }}>Cancel</option>
                    </select>
                    </div>
                    <span class="validate-err" id="err_status"></span>
                </div>
            </div>
            
            <div class="col-md-4" style="padding-top: 6px;">
                <div class="form-group">
                    
                    <div class="form-icon-user">

                    <input type="checkbox" 
                        name="exclude_due_to_bmbe" 
                        id="exclude_due_to_bmbe" 
                        class="form-check-input code" 
                        value="1" 
                        {{ old('exclude_due_to_bmbe', $data->exclude_due_to_bmbe) ? 'checked' : '' }}>
                    <label for="exclude_due_to_bmbe" class="form-check-label" style="color: #000;font-size: 12px;font-weight: bold;">BMBE: Payment Exclusion</label>
                       
                    </div>
                    <span class="validate-err" id="err_exclude_due_to_bmbe"></span>
                </div>
            </div>
            <div class="col-md-8" style="padding-top: 6px;">
                <div class="form-group">
                    
                    <div class="form-icon-user">

                    <input type="checkbox" 
                        name="is_application_fee" 
                        id="is_application_fee" 
                        class="form-check-input code" 
                        value="1" 
                        {{ old('is_application_fee', $data->is_application_fee) ? 'checked' : '' }}>
                    <label  class="form-check-label" style="color: #000;font-size: 12px;font-weight: bold;">Application Fee</label>
                       
                    </div>
                    <span class="validate-err" id="err_is_application_fee"></span>
                </div>
            </div>
            @if($data->is_application_fee == 1) 
            <div class="col-md-12" style="padding-top: 6px;">
                <table style="width: 100%;border: 1px solid #ccc;font-size: 14px;">
                    <tr>
                        <th style="width:70%;color: #fff;background: #09325d;padding:5px;border-right: 1px solid #fff;">Category</th>
                        <th style="width:20%;color: #fff;background: #09325d;padding:5px;border: 1px solid #fff;">Amount</th>
                        <th style="width:10%;color: #fff;background: #09325d;padding:5px;border: 1px solid #fff;">Default</th>
                    </tr>
                    @foreach($application_fee_category as $fee)
                        <tr>
                            <td style="color:#000;padding:5px;border:1px solid #ccc;">
                                {{ $fee->busn_category_name ?? '' }}
                                <input type="hidden" name="categoryamountfee_id[]" value="{{ $fee->id }}">
                                <input type="hidden" name="busn_category_id[]" value="{{ $fee->busn_category_id }}">
                            </td>
                            <td style="color:#000;padding:5px;border:1px solid #ccc;">
                            <input type="text" name="categoryamount[]" 
                                value="{{ old('categoryamount.' . $loop->index, $fee->amount ?? '') }}"
                                class="form-control numeric-only"
                                style="width:100%; padding:4px; border:1px solid #ccc; border-radius:4px;">
                            
                            </td>
                            <td style="color:#000; padding:5px; border:1px solid #ccc;text-align: center;">
                                <input type="radio" 
                                    name="is_default" 
                                    value="{{ $fee->id }}" 
                                    class="form-check-input" style="border: 1px solid #09325d;"
                                    {{ old('is_default', $fee->is_default ?? 0) == $fee->id || $fee->is_default == 1 ? 'checked' : '' }} >
                            </td>

                        </tr>
                    @endforeach
                </table>
            </div>
            @endif
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
    $("#status").select3({dropdownAutoWidth : false,dropdownParent: $("#parrent_status")});
    select3Ajax("app_code","parrent_appType","AppcodeAjaxList");
    select3Ajax("fee_id","parrent_fee_id","feesAjaxList");
});
</script>
<script>
document.querySelectorAll('.numeric-only').forEach(el => {
    el.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
});
</script>
<script>
// $(document).ready(function() {
//     function toggleApplicationFee() {
//         const isExcluded = $('#exclude_due_to_bmbe').is(':checked');
//         const appFeeCheckbox = $('#is_application_fee');
        
//         if (isExcluded) {
//             appFeeCheckbox.prop('checked', false); 
//             appFeeCheckbox.prop('disabled', true);
//         } else {
//             appFeeCheckbox.prop('disabled', false);
//         }
//     }
//     toggleApplicationFee();
//     $('#exclude_due_to_bmbe').on('change', toggleApplicationFee);
// });
</script>