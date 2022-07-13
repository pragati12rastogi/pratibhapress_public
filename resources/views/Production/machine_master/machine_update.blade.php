@extends($layout)

@section('title', 'Machine')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="{{url('/master/machine/list')}}"><i class=""></i>Machine List</a></li>
    <li><a href="#"><i class=""></i>Update Machine</a></li>
   
@endsection
@section('css')
<style>
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
</style>
@endsection
@section('js')
<script src="/js/Production/machine.js"></script>

<script>
   $(".m_date").datepicker({
        endDate:'today',
        format: 'd-m-yyyy'
    });
</script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
       <form action="/master/machine/update/{{$machine->id}}" method="POST" id="machineform" enctype="multipart/form-data">
        @csrf

       <div class="box box-header">
           <br>
        
        <div class="row" >
                <div class="col-md-6 {{ $errors->has('m_cat') ? ' has-error' : ''}}">
                    <label for="">Machine Category<sup>*</sup></label>
                    <select name="m_cat" id="m_cat" class="m_cat input-css select2" required=''>
                        <option value="">Select Machine</option>
                        <option value="Pre-Press" {{$machine->category=="Pre-Press" ? 'selected=selected' : ''}}>Pre-Press</option>
                        <option value="Press" {{$machine->category=="Press" ? 'selected=selected' : ''}}>Press</option>
                        <option value="Post-Press" {{$machine->category=="Post-Press" ? 'selected=selected' : ''}}>Post-Press</option>
                    </select>
                    {!! $errors->first('m_cat', '<p class="help-block">:message</p>') !!} 
                </div>
                <div class="col-md-6 {{ $errors->has('ps_value') ? ' has-error' : ''}}">
                    <label for="">Machine Name<sup>*</sup></label>
                    <input type="text" name="ps_value" id="" value="{{$machine->name}}" class="ps_value input-css" maxlength="50" required=''>
                    {!! $errors->first('ps_value', '<p class="help-block">:message</p>') !!} 
                </div>
        </div><br><br>
        <div class="row" >
                <div class="col-md-6 {{ $errors->has('m_brand') ? ' has-error' : ''}}">
                    <label for="">Machine Brand<sup>*</sup></label>
                    <input type="text" name="m_brand" value="{{$machine->brand}}" id="m_brand" class="m_brand input-css" maxlength="50" required=''>
                    {!! $errors->first('m_brand', '<p class="help-block">:message</p>') !!} 
                </div>
                <div class="col-md-6 {{ $errors->has('m_size') ? ' has-error' : ''}}">
                    <label for="">Machine Size<sup>*</sup></label>
                    <input type="text" name="m_size" value="{{$machine->size}}" id="m_size" class="m_size input-css" maxlength="50" required=''>
                    {!! $errors->first('m_size', '<p class="help-block">:message</p>') !!} 
                </div>
        </div><br><br>
        <div class="row" >
                <div class="col-md-6 {{ $errors->has('m_date') ? ' has-error' : ''}}">
                    <label for="">Machine Purchase Date<sup>*</sup></label>
                    <input type="text" name="m_date" value="{{CustomHelpers::showDate($machine->purchase_date,'d-m-Y')}}" id="m_date" class=" m_date input-css" required=''>
                    {!! $errors->first('m_date', '<p class="help-block">:message</p>') !!} 
                </div>
                <div class="col-md-6 {{ $errors->has('m_bill_no') ? ' has-error' : ''}}">
                    <label for="">Machine Bill Number<sup>*</sup></label>
                    <input type="text" name="m_bill_no" value="{{$machine->bill_no}}" id="m_bill_no" class="m_bill_no input-css" required=''>
                    {!! $errors->first('m_bill_no', '<p class="help-block">:message</p>') !!} 
                </div>
        </div><br><br>
        <div class="row" >
               
                <div class="col-md-6 {{ $errors->has('m_photo') ? ' has-error' : ''}}">
                    <label for="">Machine Photo Upload </label>
                    <input type="file" accept="image/x-png,image/gif,image/jpeg"  name="m_photo" id="m_photo" class="m_photo ">
                    <input type="hidden" name="hidden_pic" value="{{$machine->photo}}">
                    {!! $errors->first('m_photo', '<p class="help-block">:message</p>') !!} 
                </div>
                <div class="col-md-6 {{ $errors->has('m_bill_upload') ? ' has-error' : ''}}">
                <label for="">Machine Bill Upload </label>
                <input type="file" accept="image/x-png,image/jpeg,application/pdf" name="m_bill_upload" id="m_bill_upload" class="m_bill_upload ">
                <input type="hidden" name="hidden_bill" value="{{$machine->bill_upload}}">
                {!! $errors->first('m_bill_upload', '<p class="help-block">:message</p>') !!} 
            </div>
        </div><br><br>
        
       </div>
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
