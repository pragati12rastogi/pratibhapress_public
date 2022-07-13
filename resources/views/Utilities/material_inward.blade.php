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

</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                   
                    @yield('content')
            </div>
        <!-- Default box -->
       <form action="/material/inwarding/create" method="POST" id="form" files="true" enctype="multipart/form-data">
        @csrf
        @php
        $flag=0;
        $start=0;
        @endphp

        @if (empty(session('lastformdata')))            
        @php
          $flag=1;
          $to=1;
          $dc['invoice']='';
          $dc['challan']='';
          $dc['bilty']='';
          $dc['other']='';
        @endphp
         @else
        @php
          $to = 1;
          $dc['invoice']='';
          $dc['challan']='';
          $dc['bilty']='';
          $dc['other']='';
          if(empty(session('lastformdata')['doc_for'])){
            $x=session('lastformdata')['doc_for'];
          if(count($x)>0){
            foreach ($x as $key => $value) {
              if ($value=='Invoice') {
                  $dc['invoice']=$value;
              }
              if ($value=='Challan') {
                  $dc['challan']=$value;
              }
              if ($value=='Bilty') {
                  $dc['bilty']=$value;
              }
              if ($value=='Other') {
                  $dc['other']=$value;
              }
          }
          }
          }
        //   print_r($x);
        //   print_r($dc);die();
        @endphp
        @endif 
        <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <div class="container-fluid wdt">
                        <div class="row">
                            <div class="col-md-12 {{ $errors->has('entry_for') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.for')}}<sup>*</sup></label>
                                <div class="col-md-6">
                                    <div class="radio">
                                        <label><input    autocomplete="off" type="radio" class="entry_for"  {{ $errors->has('entry_for') ? '' :( $flag==1? '': (session('lastformdata')['entry_for']=='Material In-Warding'? 'checked=checked' :'' ) )}} value="Material In-Warding" name="entry_for">Material In-Warding</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="radio">
                                        <label><input autocomplete="off" type="radio" class="entry_for"  {{ $errors->has('entry_for') ? '' :( $flag==1? '': (session('lastformdata')['entry_for']=='Returnable Item'? 'checked=checked' :'' ) )}} value="Returnable Item" name="entry_for">Returnable Item</label>
                                    </div>
                                </div>
                                
                            </div>
                            <!--col-md-6-->
                            {!! $errors->first('entry_for', '<p class="help-block">:message</p>') !!}
                            <label id="entry_for-error" class="error" for="entry_for"></label>
                        </div><br><br>
                    </div>
                </div>
            </div>
        <div class="box-header with-border material_in" {{ $errors->has('entry_for') ? '' :( $flag==1? 'style=display:none': (session('lastformdata')['entry_for']=='Material In-Warding'? 'style=display:block' :'style=display:none' ) )}}>
            <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Utilities/material_inward.mytitle')}}</h2><br><br><br>
                <div class="container-fluid wdt">
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('mat_date') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/material_inward.date')}}<sup>*</sup></label>
                            <input autocomplete="off" value="{{ $errors->has('mat_date')?'': ($flag==1? '': session('lastformdata')['mat_date'])}}" type="text" name="mat_date" id="mat_date" class="mat_date datepicker1 input-css">
                            {!! $errors->first('mat_date', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('mat_vehicle_no') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/material_inward.vehicle_no')}}<sup>*</sup></label>
                            <input value="{{ $errors->has('mat_vehicle_no')?'': ($flag==1? '': session('lastformdata')['mat_vehicle_no'])}}" type="text" name="mat_vehicle_no" id="mat_vehicle_no" class="mat_vehicle_no input-css">
                            {!! $errors->first('mat_vehicle_no', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('mat_vehicle_type') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.vehicle_type')}}<sup>*</sup></label>
                                <select name="mat_vehicle_type" id="mat_vehicle_type" class="select2 input-css mat_vehicle_type" style="width:100%">
                                        <option value="">Select Vehicle Type</option>
                                        @foreach ($vehicle as $item)
                                <option value="{{$item->id}}" {{$errors->has('mat_vehicle_type') ? '' : ($flag==1?'':(session('lastformdata')['mat_vehicle_type']==$item->id? 'selected="selected"':''))}}>{{$item->name}}</option>
                                        @endforeach
                                </select>
                                {!! $errors->first('mat_vehicle_type', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('mat_company') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.company')}}<sup>*</sup></label>
                                <input value="{{ $errors->has('mat_company')?'': ($flag==1? '': session('lastformdata')['mat_company'])}}"  type="text" name="mat_company" id="mat_company" class="mat_company input-css">
                                {!! $errors->first('mat_company', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('mat_material') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/material_inward.material')}}<sup>*</sup></label>
                            <input value="{{ $errors->has('mat_material')?'': ($flag==1? '': session('lastformdata')['mat_material'])}}"  type="text" name="mat_material" id="mat_material" class="mat_material input-css">
                            {!! $errors->first('mat_material', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-3 {{ $errors->has('mat_qty') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.qty')}}<sup>*</sup></label>
                                <input value="{{ $errors->has('mat_qty')?'': ($flag==1? '': session('lastformdata')['mat_qty'])}}" type="number" name="mat_qty" id="mat_qty" class="mat_qty input-css">
                                {!! $errors->first('mat_qty', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-3">
                                <label>UOM<sup>*</sup></label>
                                <select   value="{{ old('dimension') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="mat_dimension">
                                        <option value="default">Select UOM</option>
                                        {{-- <option value="m">Metre</option>
                                        <option value="mm">Millimeter</option>
                                        <option value="cm">Centimeter</option>
                                        <option value="km">Kilometer</option>
                                        <option value="in">Inch</option>
                                        <option value="ft">Foot</option>
                                        <option value="ton">Ton</option>
                                        <option value="doz">Dozen</option>
                                        <option value="kg">Kilogram</option>
                                        <option value="g">Grams</option> --}}
                                        @foreach($uom as $key)
                                        <option value="{{$key->uom_name}}">{{$key->uom_name}}</option>
                                        @endforeach
                                </select>
                            {!! $errors->first('mat_dimension', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('mat_time') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/material_inward.time')}}<sup>*</sup></label>
                            <input value="{{ $errors->has('mat_time')?'': ($flag==1? '': session('lastformdata')['mat_time'])}}" type="text" name="mat_time" id="mat_time" class="mat_time input-css timepicker">
                            {!! $errors->first('mat_time', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('doc_for') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.doc')}}<sup>*</sup></label>
                                <input type="hidden" name="doc_for" value="" id="doc_for">
                                <div class="col-md-3">
                                        <div class="radio">
                                            <label><input autocomplete="off" onclick="myFunction()"  {{ $errors->has('doc_for') ? '' :( $flag==1? '': ($dc['invoice']=='Invoice'? 'checked=checked' :'' ) )}} type="checkbox" class="doc_for"  id="myCheck" value="Invoice" name="doc_for['invoice']">Invoice</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="radio">
                                            <label><input autocomplete="off" onclick="myFunction1()"  {{ $errors->has('doc_for') ? '' :( $flag==1? '': ($dc['challan']=='Challan'? 'checked=checked' :'' ) )}} type="checkbox" id="myCheck1" class="doc_for"  value="Challan" name="doc_for['challan']">Challan</label>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-3">
                                            <div class="radio">
                                                <label><input  onclick="myFunction2()"  autocomplete="off"  {{ $errors->has('doc_for') ? '' :( $flag==1? '': ($dc['bilty']=='Bilty'? 'checked=checked' :'' ) )}} id="myCheck2" type="checkbox" class="doc_for"  value="Bilty" name="doc_for['bilty']">Bilty</label>
                                            </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="radio">
                                            <label><input autocomplete="off" onclick="myFunction3()"  {{ $errors->has('doc_for') ? '' :( $flag==1? '': ($dc['other']=='Other'? 'checked=checked' :'' ) )}} id="myCheck3" type="checkbox" class="doc_for"  value="Other" name="doc_for['other']">Other</label>
                                        </div>
                                    </div>
                                    {!! $errors->first('doc_for', '<p class="help-block">:message</p>') !!}
                                    <label id="doc_for-error" class="error" for="doc_for"></label>           
                        </div>
                        
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-3" id="invo" {{ $errors->has('doc_for') ? '' :( $flag==1? 'style=display:none': ($dc['invoice']=='Invoice'? 'style=display:block' :'style=display:none' ) )}}>
                                    <label>{{__('Utilities/material_inward.invoice')}}<sup>*</sup></label>
                                    <input value="{{ $errors->has('doc_for')?'': ($flag==1? '': session('lastformdata')['mat_invoice'])}}" type="text" name="mat_invoice" id="mat_invoice" class="mat_invoice input-css">
                                   
                            </div>  
                            <div class="col-md-3" id="challan" {{ $errors->has('doc_for') ? '' :( $flag==1? 'style=display:none': ($dc['challan']=='Challan'? 'style=display:block' :'style=display:none' ) )}}>
                                    <label>{{__('Utilities/material_inward.challan')}}<sup>*</sup></label> 
                                    <input value="{{ $errors->has('doc_for')?'': ($flag==1? '': session('lastformdata')['mat_challan'])}}" type="text" name="mat_challan" id="mat_challan" class="mat_challan input-css">
                                   
                            </div>
                            <div class="col-md-3" id="bilty" {{ $errors->has('doc_for') ? '' :( $flag==1? 'style=display:none': ($dc['bilty']=='Bilty'? 'style=display:block' :'style=display:none' ) )}}>
                                    <label>{{__('Utilities/material_inward.bilty')}}<sup>*</sup></label> 
                                    <input value="{{ $errors->has('doc_for')?'': ($flag==1? '': session('lastformdata')['mat_bilty'])}}" type="text" name="mat_bilty" id="mat_bilty" class="mat_bilty input-css">
    
                            </div>
                            <div class="col-md-3" id="other" {{ $errors->has('doc_for') ? '' :( $flag==1? 'style=display:none': ($dc['other']=='Other'? 'style=display:block' :'style=display:none' ) )}}>
                                    <label>{{__('Utilities/material_inward.other')}}<sup>*</sup></label> 
                                    <input value="{{ $errors->has('doc_for')?'': ($flag==1? '': session('lastformdata')['mat_other'])}}" type="text" name="mat_other" id="mat_other" class="mat_other input-css">
                            </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('mat_driver') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.driver')}}<sup>*</sup></label> 
                                    <input value="{{ $errors->has('mat_driver')?'': ($flag==1? '': session('lastformdata')['mat_driver'])}}" type="text" name="mat_driver" id="mat_driver" class="mat_driver input-css">
                                    {!! $errors->first('mat_driver', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('mat_driver_no') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.driver_num')}}<sup>*</sup></label> 
                                    <input value="{{ $errors->has('mat_driver_no')?'': ($flag==1? '': session('lastformdata')['mat_driver_no'])}}" type="number" name="mat_driver_no" id="mat_driver_no" class="mat_driver_no input-css">
                                    {!! $errors->first('mat_driver_no', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-12 {{ $errors->has('mat_remark') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.remark')}}</label> 
                                    <textarea name="mat_remark" id="mat_remark"  class="mat_remark input-css">{{ $errors->has('mat_remark')?'': ($flag==1? '': session('lastformdata')['mat_remark'])}}</textarea>
                                    {!! $errors->first('mat_remark', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                </div>
            </div>
        </div>
        <div class="box-header with-border returnable" {{ $errors->has('entry_for') ? '' :( $flag==1? 'style=display:none': (session('lastformdata')['entry_for']=='Returnable Item'? 'style=display:block' :'style=display:none' ) )}}>
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Utilities/material_inward.mytitle1')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <div class="row">
                            <div class="col-md-6 {{ $errors->has('ret_date') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.date')}}<sup>*</sup></label>
                                <input value="{{ $errors->has('ret_date')?'': ($flag==1? '': session('lastformdata')['ret_date'])}}" type="text" name="ret_date" autocomplete="off" id="ret_date" class="ret_date datepicker input-css">
                                {!! $errors->first('ret_date', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('ret_vehicle_no') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.vehicle_no')}}<sup>*</sup></label>
                                <input value="{{ $errors->has('ret_vehicle_no')?'': ($flag==1? '': session('lastformdata')['ret_vehicle_no'])}}" type="text" name="ret_vehicle_no" id="ret_vehicle_no" class="ret_vehicle_no input-css">
                                {!! $errors->first('ret_vehicle_no', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('ret_vehicle_type') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.vehicle_type')}}<sup>*</sup></label>
                                    <select name="ret_vehicle_type" id="ret_vehicle_type" class="select2 input-css ret_vehicle_type" style="width:100%">
                                            <option value="">Select Vehicle Type</option>
                                            @foreach ($vehicle as $item)
                                <option value="{{$item->id}}" {{$errors->has('ret_vehicle_type') ? '' : ($flag==1?'':(session('lastformdata')['ret_vehicle_type']==$item->id? 'selected="selected"':''))}}>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('ret_vehicle_type', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('ret_material') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.material')}}<sup>*</sup></label>
                                        <input value="{{ $errors->has('ret_material')?'': ($flag==1? '': session('lastformdata')['ret_material'])}}" name="ret_material" id="ret_material" class="input-css ret_material" style="width:100%">
                                        {!! $errors->first('ret_material', '<p class="help-block">:message</p>') !!}
                                    </div>
                        </div><br><br>
                        <div class="row">
                            <div class="col-md-3 {{ $errors->has('ret_qty') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.qty')}}<sup>*</sup></label>
                                    <input value="{{ $errors->has('ret_qty')?'': ($flag==1? '': session('lastformdata')['ret_qty'])}}" type="number" name="ret_qty" id="ret_qty" class="ret_qty input-css">
                                    {!! $errors->first('ret_qty', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-3">
                                    <label>UOM<sup>*</sup></label>
                                    <select   value="{{ old('dimension') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="ret_dimension">
                                            <option value="default">Select UOM</option>
                                            {{-- <option value="m">Metre</option>
                                            <option value="mm">Millimeter</option>
                                            <option value="cm">Centimeter</option>
                                            <option value="km">Kilometer</option>
                                            <option value="in">Inch</option>
                                            <option value="ft">Foot</option>
                                            <option value="ton">Ton</option>
                                            <option value="doz">Dozen</option>
                                            <option value="kg">Kilogram</option>
                                            <option value="g">Grams</option> --}}
                                            @foreach($uom as $key)
                                            <option value="{{$key->uom_name}}">{{$key->uom_name}}</option>
                                            @endforeach
                                    </select>
                                {!! $errors->first('ret_dimension', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('ret_time') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.time')}}<sup>*</sup></label>
                                <input value="{{ $errors->has('ret_time')?'': ($flag==1? '': session('lastformdata')['ret_time'])}}" type="text" name="ret_time" id="ret_time" class="ret_time input-css timepicker">
                                {!! $errors->first('ret_time', '<p class="help-block">:message</p>') !!}
                            </div>
                           
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('ret_invoice') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.invoice')}}/{{__('Utilities/material_inward.challan')}}/Bill Number<sup>*</sup></label>
                                        <input value="{{ $errors->has('ret_invoice')?'': ($flag==1? '': session('lastformdata')['ret_invoice'])}}" type="text" name="ret_invoice" id="ret_invoice" class="ret_invoice input-css">
                                        {!! $errors->first('ret_invoice', '<p class="help-block">:message</p>') !!}
                                </div>  
                                <div class="col-md-6 {{ $errors->has('ret_bill') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.challan')}}/Bill Number<sup>*</sup></label>
                                        <input type="file"  name="ret_bill" id="ret_bill" class="ret_bill">
                                        {!! $errors->first('ret_bill', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('ret_driver') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.driver')}}<sup>*</sup></label> 
                                        <input value="{{ $errors->has('ret_driver')?'': ($flag==1? '': session('lastformdata')['ret_driver'])}}" type="text" name="ret_driver" id="ret_driver" class="ret_driver input-css">
                                        {!! $errors->first('ret_driver', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('ret_driver_no') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.driver_num')}}<sup>*</sup></label> 
                                        <input value="{{ $errors->has('ret_driver_no')?'': ($flag==1? '': session('lastformdata')['ret_driver_no'])}}" type="text" name="ret_driver_no" id="ret_driver_no" class="ret_driver_no input-css">
                                        {!! $errors->first('ret_driver_no', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-12 {{ $errors->has('ret_remark') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.remark')}}<sup>*</sup></label> 
                                        <textarea name="ret_remark" id="ret_remark" class="ret_remark input-css">{{ $errors->has('ret_remark')?'': ($flag==1? '': session('lastformdata')['ret_remark'])}}</textarea>
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
