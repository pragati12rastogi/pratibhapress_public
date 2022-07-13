<div class="row">
    @if(isset($memo))
        <div class="col-md-4"></div>
    @else
        @if($relation == '1ton')
            <div class="col-md-4">
                <label for="ref_no">Original Invoice No.</label>
               {{$voucher->reference}}
            </div>
            <div class="col-md-4">
                <label for="ref_no">Date</label>
                {{$voucher->reference_date}}
            </div>
        @else
            <div class="col-md-4"></div>
        @endif
    @endif
   </div>