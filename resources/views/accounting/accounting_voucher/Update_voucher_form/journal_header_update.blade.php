
<div class="row">
    @if(isset($memo))
        <div class="col-md-4"></div>
    @else
        @if($relation == '1ton')
        <div class="col-md-4">
            <label for="ref_no">Original Invoice No.</label>
            <input type="text" name="ref_no" value="{{$voucher->reference}}" id="ref_no" class="input-css form-control ref_no">
        </div>
        <div class="col-md-4">
            <label for="ref_date">Date</label>
            <input type="text" name="ref_date" value="{{date('d-m-Y',strtotime($voucher->reference_date))}}" id="ref_date" autocomplete="off" class="input-css form-control datepicker ref_date">
        </div>
        @else
            <div class="col-md-4"></div>
        @endif
    @endif
    
</div>
