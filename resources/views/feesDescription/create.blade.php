<form method="POST" action="{{ route('feesDescription.store') }}">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $data->id }}">

    <div class="modal-body">
        <div class="row">
           
            <div class="col-md-10">
                <div class="form-group">
                    <label for="name" class="form-label">{{ __('Fees Description') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('name') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $data->name) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_name"></span>
                </div>
            </div>   
            <div class="col-md-2">
                <div class="form-group" id="parrent_status">
                <label for="amount" class="form-label">{{ __('Amount') }}</label><span class="text-danger">*</span>
                    <span class="validate-err">{{ $errors->first('amount') }}</span>
                    <div class="form-icon-user">
                        <input type="text" name="amount" id="amount" class="form-control" required value="{{ old('amount', $data->amount) }}" style="font-size:12px; padding:9px;">
                    </div>
                    <span class="validate-err" id="err_amount"></span>
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
