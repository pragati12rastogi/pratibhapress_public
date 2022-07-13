@extends($layout)

@section('title', __('Utilities/material_inward.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Material In-Warding Register</a></li>
   
@endsection
@section('js')
<script src="/js/utilities/material_inward.js"></script>
<script>
function myFunction() {
  var checkBox = document.getElementById("myCheck");
  var text = document.getElementById("invo");
  if (checkBox.checked == true){
    text.style.display = "block";
  } else {
     text.style.display = "none";
  }
}
function myFunction1() {
  var checkBox = document.getElementById("myCheck1");
  var text = document.getElementById("challan");
  if (checkBox.checked == true){
    text.style.display = "block";
  } else {
     text.style.display = "none";
  }
}
function myFunction2() {
  var checkBox = document.getElementById("myCheck2");
  var text = document.getElementById("bilty");
  if (checkBox.checked == true){
    text.style.display = "block";
  } else {
     text.style.display = "none";
  }
}
function myFunction3() {
  var checkBox = document.getElementById("myCheck3");
  var text = document.getElementById("other");
  if (checkBox.checked == true){
    text.style.display = "block";
  } else {
     text.style.display = "none";
  }
}
function time(input){
    var time=$(input).attr("id");
    console.log(time);
    $('.'+time).wickedpicker();  
}

</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                   
                    @yield('content')
            </div>
        <!-- Default box -->
        <form action="/material/inwarding/update/{{$id}}" method="POST" id="form" files="true" enctype="multipart/form-data">
        @csrf
        @foreach ($detail as $item)
            
        @endforeach
        <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <div class="container-fluid wdt">
                            <div class="row">
                                    <div class="col-md-3 ">
                                            <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                            <input type="text" name="update_reason" required="" class="input-css" id="update_reason">
                                            {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                        </div><!--col-md-4-->
                            </div>
                            <br><br>
                        <div class="row">
                            
                            <div class="col-md-12 {{ $errors->has('entry_for') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.for')}}<sup>*</sup></label>
                                <div class="col-md-6">
                                    <div class="radio">
                                        <label><input    autocomplete="off" disabled type="radio" class="entry_for"  {{ $item->entry_for=='Material In-Warding'? 'checked=checked' :'' }} value="Material In-Warding" >Material In-Warding</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="radio">
                                        <label><input autocomplete="off"  disabled type="radio" class="entry_for"  {{ $item->entry_for=='Returnable Item'? 'checked=checked' :'' }} value="Returnable Item" >Returnable Item</label>
                                    </div>
                                </div>
                                
                            </div>
                        <input type="hidden" name="entry_for" value="{{$item->entry_for}}">
                            <!--col-md-6-->
                            {!! $errors->first('entry_for', '<p class="help-block">:message</p>') !!}
                            <label id="entry_for-error" class="error" for="entry_for"></label>
                        </div><br><br>
                    </div>
                </div>
            </div>
        <div class="box-header with-border material_in" {{ $item->entry_for=='Material In-Warding'? 'style=display:block' :'style=display:none'}}>
            <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Utilities/material_inward.mytitle')}}</h2><br><br><br>
                <div class="container-fluid wdt">
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('mat_date') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/material_inward.date')}}<sup>*</sup></label>
                            <input value="{{CustomHelpers::showDate($item->date,'d-m-Y')}}" type="text" name="mat_date" id="mat_date" class="mat_date datepicker1 input-css">
                            {!! $errors->first('mat_date', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('mat_vehicle_no') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/material_inward.vehicle_no')}}<sup>*</sup></label>
                            <input value="{{$item->vehicle_no}}" type="text" name="mat_vehicle_no" id="mat_vehicle_no" class="mat_vehicle_no input-css">
                            {!! $errors->first('mat_vehicle_no', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('mat_vehicle_type') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.vehicle_type')}}<sup>*</sup></label>
                                <select name="mat_vehicle_type" id="mat_vehicle_type" class="select2 input-css mat_vehicle_type" style="width:100%">
                                        <option value="">Select Vehicle Type</option>
                                        @foreach ($vehicle as $key)
                                <option value="{{$key->id}}" {{$item->vehicle_type==$key->id? 'selected="selected"':''}}>{{$key->name}}</option>
                                        @endforeach
                                </select>
                                {!! $errors->first('mat_vehicle_type', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('mat_company') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.company')}}<sup>*</sup></label>
                                <input value="{{ $item->company}}"  type="text" name="mat_company" id="mat_company" class="mat_company input-css">
                                {!! $errors->first('mat_company', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('mat_material') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/material_inward.material')}}<sup>*</sup></label>
                            <input value="{{ $item->item_name}}"  type="text" name="mat_material" id="mat_material" class="mat_material input-css">
                            {!! $errors->first('mat_material', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-3 {{ $errors->has('mat_qty') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.qty')}}<sup>*</sup></label>
                                <input value="{{ $item->qty}}" type="number" name="mat_qty" id="mat_qty" class="mat_qty input-css">
                                {!! $errors->first('mat_qty', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-3">
                                <label>UOM<sup>*</sup></label>
                                <select   value="{{ old('dimension') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="mat_dimension">
                                        <option value="default">Select UOM</option>
                                        {{-- <option value="m" {{ $item->dimension=="m" ? 'selected="selected"' : ''}}>Metre</option>
                                        <option value="mm"{{ $item->dimension=="mm" ? 'selected="selected"' : ''}}>Millimeter</option>
                                        <option value="cm"{{ $item->dimension=="cm" ? 'selected="selected"' : ''}}>Centimeter</option>
                                        <option value="km"{{ $item->dimension=="km" ? 'selected="selected"' : ''}}>Kilometer</option>
                                        <option value="in"{{ $item->dimension=="in" ? 'selected="selected"' : ''}}>Inch</option>
                                        <option value="ft"{{ $item->dimension=="ft" ? 'selected="selected"' : ''}}>Foot</option>
                                        <option value="ton"{{ $item->dimension=="ton" ? 'selected="selected"' : ''}}>Ton</option>
                                        <option value="doz"{{ $item->dimension=="doz" ? 'selected="selected"' : ''}}>Dozen</option>
                                        <option value="kg"{{ $item->dimension=="kg" ? 'selected="selected"' : ''}}>Kilogram</option>
                                        <option value="g"{{ $item->dimension=="g" ? 'selected="selected"' : ''}}>Grams</option> --}}
                                        @foreach($uom as $key)
                                            <option value="{{$key->uom_name}}" {{ $item->dimension==$key->uom_name ? 'selected="selected"' : ''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                </select>
                            {!! $errors->first('mat_dimension', '<p class="help-block">:message</p>') !!}
                           </div>
                        <div class="col-md-6 {{ $errors->has('mat_time') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/material_inward.time')}}<sup>*</sup></label>
                            <input value="{{$item->time}}" type="text" name="mat_time" id="mat_time" class="mat_time input-css" onclick="time(this)">
                            {!! $errors->first('mat_time', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div><br><br>
                    @php
                        $doc=explode(',',$item->doc_for);
                        $invoice="";
                        $challan="";
                        $bilty="";
                        $other="";
                        foreach($doc as $vl){
                            if($vl=="Invoice")
                                $invoice="Invoice";
                            if($vl=="Challan")
                                $challan="Challan";
                            if($vl=="Bilty")
                                $bilty="Bilty";
                            if($vl=="Other")
                                $other="Other";    
                        }
                    @endphp
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('doc_for') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.doc')}}<sup>*</sup></label>
                                <input type="hidden" name="doc_for" value="" id="doc_for">
                                <div class="col-md-3">
                                        <div class="radio">
                                            <label><input autocomplete="off" onclick="myFunction()"  {{ $invoice=='Invoice'? 'checked=checked' :'' }} type="checkbox" class="doc_for"  id="myCheck" value="Invoice" name="doc_for['invoice']">Invoice</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="radio">
                                            <label><input autocomplete="off" onclick="myFunction1()"  {{ $challan=='Challan'? 'checked=checked' :'' }} type="checkbox" id="myCheck1" class="doc_for"  value="Challan" name="doc_for['challan']">Challan</label>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-3">
                                            <div class="radio">
                                                <label><input  onclick="myFunction2()"  autocomplete="off"  {{ $bilty=='Bilty'? 'checked=checked' :''}} id="myCheck2" type="checkbox" class="doc_for"  value="Bilty" name="doc_for['bilty']">Bilty</label>
                                            </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="radio">
                                            <label><input autocomplete="off" onclick="myFunction3()"  {{ $other=='Other'? 'checked=checked' :''}} id="myCheck3" type="checkbox" class="doc_for"  value="Other" name="doc_for['other']">Other</label>
                                        </div>
                                    </div>
                                    {!! $errors->first('doc_for', '<p class="help-block">:message</p>') !!}
                                    <label id="doc_for-error" class="error" for="doc_for"></label>           
                        </div>
                        
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-3" id="invo" {{ $invoice=='Invoice'? 'style=display:block' :'style=display:none'}}>
                                    <label>{{__('Utilities/material_inward.invoice')}}<sup>*</sup></label>
                                    <input value="{{ $item->invoice}}" type="text" name="mat_invoice" id="mat_invoice" class="mat_invoice input-css">
                                   
                            </div>  
                            <div class="col-md-3" id="challan" {{ $challan=='Challan'? 'style=display:block' :'style=display:none'}}>
                                    <label>{{__('Utilities/material_inward.challan')}}<sup>*</sup></label> 
                                    <input value="{{ $item->challan}}" type="text" name="mat_challan" id="mat_challan" class="mat_challan input-css">
                                   
                            </div>
                            <div class="col-md-3" id="bilty" {{ $bilty=='Bilty'? 'style=display:block' :'style=display:none' }}>
                                    <label>{{__('Utilities/material_inward.bilty')}}<sup>*</sup></label> 
                                    <input value="{{ $item->bilty}}" type="text" name="mat_bilty" id="mat_bilty" class="mat_bilty input-css">
    
                            </div>
                            <div class="col-md-3" id="other" {{ $other=='Other'? 'style=display:block' :'style=display:none' }}>
                                    <label>{{__('Utilities/material_inward.other')}}<sup>*</sup></label> 
                                    <input value="{{ $item->other}}" type="text" name="mat_other" id="mat_other" class="mat_other input-css">
                            </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('mat_driver') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.driver')}}<sup>*</sup></label> 
                                    <input value="{{ $item->driver_name}}" type="text" name="mat_driver" id="mat_driver" class="mat_driver input-css">
                                    {!! $errors->first('mat_driver', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('mat_driver_no') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.driver_num')}}<sup>*</sup></label> 
                                    <input value="{{ $item->driver_number}}" type="number" name="mat_driver_no" id="mat_driver_no" class="mat_driver_no input-css">
                                    {!! $errors->first('mat_driver_no', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-12 {{ $errors->has('mat_remark') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.remark')}}</label> 
                                    <textarea name="mat_remark" id="mat_remark"  class="mat_remark input-css">{{ $item->remark}}</textarea>
                                    {!! $errors->first('mat_remark', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                </div>
            </div>
        </div>
        <div class="box-header with-border returnable" {{ $item->entry_for=='Returnable Item'? 'style=display:block' :'style=display:none'}}>
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Utilities/material_inward.mytitle1')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <div class="row">
                            <div class="col-md-6 {{ $errors->has('ret_date') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.date')}}<sup>*</sup></label>
                                <input value="{{CustomHelpers::showDate($item->date,'d-m-Y')}}" type="text" name="ret_date" id="ret_date" class="ret_date datepicker1 input-css">
                                {!! $errors->first('ret_date', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('ret_vehicle_no') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.vehicle_no')}}<sup>*</sup></label>
                                <input value="{{ $item->vehicle_no}}" type="text" name="ret_vehicle_no" id="ret_vehicle_no" class="ret_vehicle_no input-css">
                                {!! $errors->first('ret_vehicle_no', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('ret_vehicle_type') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.vehicle_type')}}<sup>*</sup></label>
                                    <select name="ret_vehicle_type" id="ret_vehicle_type" class="select2 input-css ret_vehicle_type" style="width:100%">
                                            <option value="">Select Vehicle Type</option>
                                            @foreach ($vehicle as $key)
                                <option value="{{$key->id}}" {{$item->vehicle_type==$key->id? 'selected="selected"':''}}>{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('ret_vehicle_type', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('ret_material') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.material')}}<sup>*</sup></label>
                                        <input value="{{$item->item_name}}" name="ret_material" id="ret_material" class="input-css ret_material" style="width:100%">
                                        {!! $errors->first('ret_material', '<p class="help-block">:message</p>') !!}
                                    </div>
                        </div><br><br>
                        <div class="row">
                            <div class="col-md-3 {{ $errors->has('ret_qty') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.qty')}}<sup>*</sup></label>
                                    <input value="{{ $item->qty}}" type="number" name="ret_qty" id="ret_qty" class="ret_qty input-css">
                                    {!! $errors->first('ret_qty', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-3">
                                    <label>UOM<sup>*</sup></label>
                                    <select   value="{{ old('dimension') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="ret_dimension">
                                            <option value="default">Select UOM</option>
                                            {{-- <option value="m" {{ $item->dimension=="m" ? 'selected="selected"' : ''}}>Metre</option>
                                            <option value="mm"{{ $item->dimension=="mm" ? 'selected="selected"' : ''}}>Millimeter</option>
                                            <option value="cm"{{ $item->dimension=="cm" ? 'selected="selected"' : ''}}>Centimeter</option>
                                            <option value="km"{{ $item->dimension=="km" ? 'selected="selected"' : ''}}>Kilometer</option>
                                            <option value="in"{{ $item->dimension=="in" ? 'selected="selected"' : ''}}>Inch</option>
                                            <option value="ft"{{ $item->dimension=="ft" ? 'selected="selected"' : ''}}>Foot</option>
                                            <option value="ton"{{ $item->dimension=="ton" ? 'selected="selected"' : ''}}>Ton</option>
                                            <option value="doz"{{ $item->dimension=="doz" ? 'selected="selected"' : ''}}>Dozen</option>
                                            <option value="kg"{{ $item->dimension=="kg" ? 'selected="selected"' : ''}}>Kilogram</option>
                                            <option value="g"{{ $item->dimension=="g" ? 'selected="selected"' : ''}}>Grams</option> --}}
                                            @foreach($uom as $key)
                                            <option value="{{$key->uom_name}}" {{ $item->dimension==$key->uom_name ? 'selected="selected"' : ''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                    </select>
                                {!! $errors->first('mat_dimension', '<p class="help-block">:message</p>') !!}
                               </div>
                            <div class="col-md-6 {{ $errors->has('ret_time') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.time')}}<sup>*</sup></label>
                                <input value="{{ $item->time}}" type="text" name="ret_time" id="ret_time" class="ret_time input-css" onclick="time(this)">
                                {!! $errors->first('ret_time', '<p class="help-block">:message</p>') !!}
                            </div>
                           
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('ret_invoice') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.invoice')}}/{{__('Utilities/material_inward.challan')}}/Bill Number<sup>*</sup></label>
                                        <input value="{{$item->qty}}" type="text" name="ret_invoice" id="ret_invoice" class="ret_invoice input-css">
                                        {!! $errors->first('ret_invoice', '<p class="help-block">:message</p>') !!}
                                </div>  
                                <div class="col-md-6 {{ $errors->has('ret_bill') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.challan')}}/Bill Number<sup>*</sup></label>
                                        <input type="file"  name="ret_bill" id="ret_bill" class="ret_bill">
                                        <input type="hidden" name="hidden_file" value="{{$item->bill_file}}">
                                        {!! $errors->first('ret_bill', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('ret_driver') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.driver')}}<sup>*</sup></label> 
                                        <input value="{{ $item->driver_name}}" type="text" name="ret_driver" id="ret_driver" class="ret_driver input-css">
                                        {!! $errors->first('ret_driver', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('ret_driver_no') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.driver_num')}}<sup>*</sup></label> 
                                        <input value="{{ $item->driver_number}}" type="text" name="ret_driver_no" id="ret_driver_no" class="ret_driver_no input-css">
                                        {!! $errors->first('ret_driver_no', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-12 {{ $errors->has('ret_remark') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.remark')}}<sup>*</sup></label> 
                                        <textarea name="ret_remark" id="ret_remark" class="ret_remark input-css">{{ $item->remark}}</textarea>
                                        {!! $errors->first('ret_remark', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                    </div>
                </div>
        </div>
 
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
