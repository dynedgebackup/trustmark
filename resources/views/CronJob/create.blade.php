<form method="POST" action="{{ route('cron-job.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">
    <input type="hidden" name="schedule_val" id="schedule_val" value="{{ $data->schedule_value }}">
    <input type="hidden" name="h_day" id="h_day" value="{{ $data->day }}">
    <input type="hidden" name="h_hours" id="h_hours" value="{{ $data->hours }}">
    <div class="modal-body">
        <div class="row">
        <div class="col-md-6">
                <div class="form-group">
                    <label for="department" class="form-label">{{ __('Department') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('department') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="department" id="department" class="form-control" maxlength="50" required value="{{ old('department', $data->department) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_department"></span>
                </div>
            </div>  
            <div class="col-md-6">
                <div class="form-group" id="schedule_type_parrent">
                    <label for="schedule_type" class="form-label">{{ __('Schedule Type') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('schedule_type') }}</span>
                    <div class="form-icon-user">
                        <select name="schedule_type" id="schedule_type" class="form-control select3" required style="width:100%;">
                            @foreach($scheduleType as $key => $label)
                                <option value="{{ $key }}" {{ old('schedule_type', $data->schedule_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="validate-err" id="err_schedule_type"></span>
                </div>
            </div> 
            </div>
            <div class="row" id="divSchduleValue"> 

            </div>
            <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="url" class="form-label">{{ __('URL') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('url') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="url" id="url" class="form-control" required value="{{ old('url', $data->url) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_url"></span>
                </div>
            </div>  
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description" class="form-label">{{ __('Description') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('description') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="description" id="description" class="form-control" required value="{{ old('description', $data->description) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_description"></span>
                </div>
            </div>  
            <div class="col-md-6">
                <div class="form-group">
                    <label for="remarks" class="form-label">{{ __('Remarks') }}</label>
                    <span class="validate-err">{{ $errors->first('remarks') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="remarks" id="remarks" class="form-control" value="{{ old('remarks', $data->remarks) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_remarks"></span>
                </div>
            </div>   
            
            <div class="col-md-2">
                <div class="form-group" id="parrent_status">
                    <label for="status" class="form-label">{{ __('Status') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('status') }}</span>
                    <div class="form-icon-user">
                    <select name="status" id="status" class="form-control select3" required style="width:100%;">
                        <option value="1" {{ old('status', $data->status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $data->status) == '2' ? 'selected' : '' }}>Cancel</option>
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
    var DIR = "{{ url('/') }}/";
    $(document).ready(function () {
        $(".timepicker").datetimepicker({
            datepicker:false,
            format:'H:i'
        }); 
        $("#schedule_type").select3({dropdownAutoWidth : false,dropdownParent: $("#schedule_type_parrent")});
        $('#schedule_type').on('change', function() {
        $('#schedule_val').val("");
            loadScheduleValue();
            
        });
        loadScheduleValue();
    });
    function loadScheduleValue() {
        var filtervars = {
            schedule_type_id:$("#schedule_type").val(),
            schedule_val:$("#schedule_val").val(),
            h_hours:$("#h_hours").val(),
            h_day:$("#h_day").val(),
        "_token": $("#_csrf_token").val()
        }; 
        $.ajax({
        type: "POST",
        url: DIR+'setting/cron-job/getScheduleVal',
        data: filtervars,
        dataType: "html",
        success: function(data){
                $("#divSchduleValue").html(data);
                $(".timepicker").datetimepicker({
                    datepicker:false,
                    format:'H:i'
                }); 
                $("#day").select3({dropdownAutoWidth : false,dropdownParent: $("#divSchduleValue")});
                $("#schedule_value").select3({dropdownAutoWidth : false,dropdownParent: $("#divSchduleValue")});
                
        }
        });
    }

</script>
