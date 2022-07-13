@extends($layout)

@section('title', __('Utilities/material_inward.title1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Material Out-Warding Register</a></li>
   
@endsection
@section('js')
<script src="/js/utilities/material_outward.js"></script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                        @include('sections.flash-message')
                       
                        @yield('content')
                </div>
       <form action="/material/outwarding/create" method="POST" id="form">
        @csrf
        @php
        $flag=0;
        $start=0;
        @endphp

        @if (empty(session('lastformdata')))            
        @php
          $flag=1;
          $to=1;
        @endphp
         @else
        @php
          $to = 1;
        @endphp
        @endif 
        <div class="box-header with-border returnable">
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Utilities/material_inward.mytitle2')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <div class="row">
                            <div class="col-md-12 {{ $errors->has('date') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.date')}}<sup>*</sup></label>
                                <input type="text" autocomplete="off" value="{{ $errors->has('date')?'': ($flag==1? '': session('lastformdata')['date'])}}" name="date" id="date" class="datepicker1 input-css">
                                {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                            </div>
                           
                        </div><br><br>
                        <div class="row">
                            <div class="col-md-6 {{ $errors->has('gatepass') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.gate')}}<sup>*</sup></label>
                                <select type="text" name="gatepass" id="gatepass" class="select2 gatepass input-css">
                                                <option value="">Select Gate Pass</option>
                                                @foreach ($gatepass as $item)
                                    <option value="{{$item->id}}" {{$errors->has('gatepass') ? '' : ($flag==1?'':(session('lastformdata')['gatepass']==$item->id? 'selected="selected"':''))}}>{{$item->gatepass_number}}</option>
                                            @endforeach
                                </select>
                                {!! $errors->first('gatepass', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('carrier') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.carrier')}}<sup>*</sup></label>
                                    <input type="text" value="{{ $errors->has('carrier')?'': ($flag==1? '': session('lastformdata')['carrier'])}}" name="carrier" id="carrier" class="carrier input-css">
                                    {!! $errors->first('carrier', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div><br><br>
                        <div class="row">
                                
                                <div class="col-md-6 {{ $errors->has('vehicle_type') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.vehicle_type')}}<sup>*</sup></label>
                                    <select name="vehicle_type" id="vehicle_type" class="select2 input-css vehicle_type" style="width:100%">
                                            <option value="">Select Vehicle Type</option>
                                            @foreach ($vehicle as $item)
                                <option value="{{$item->id}}" {{$errors->has('vehicle_type') ? '' : ($flag==1?'':(session('lastformdata')['vehicle_type']==$item->id? 'selected="selected"':''))}}>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('vehicle_type', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('vehicle_no') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.vehicle_no')}}<sup>*</sup></label>
                                        <input type="text" value="{{ $errors->has('vehicle_no')?'': ($flag==1? '': session('lastformdata')['vehicle_no'])}}" name="vehicle_no" id="vehicle_no" class="vehicle_no input-css">
                                        {!! $errors->first('vehicle_no', '<p class="help-block">:message</p>') !!}
                                    </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('trans') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.trans')}}<sup>*</sup></label>
                                        <select name="trans" id="trans" class="select2 input-css trans" style="width:100%">
                                                <option value="">Select Transport Mode</option>
                                                <option value="Company Owned" {{$errors->has('trans') ? '' : ($flag==1?'':(session('lastformdata')['trans']=='Company Owned'? 'selected="selected"':''))}}>Company Owned</option>
                                                <option value="Transporter" {{$errors->has('trans') ? '' : ($flag==1?'':(session('lastformdata')['trans']=='Transporter'? 'selected="selected"':''))}}>Transporter</option>
                                                <option value="Courier" {{$errors->has('trans') ? '' : ($flag==1?'':(session('lastformdata')['trans']=='Courier'? 'selected="selected"':''))}}>Courier</option>
                                                <option value="Pick by Party" {{$errors->has('trans') ? '' : ($flag==1?'':(session('lastformdata')['trans']=='Pick by Party'? 'selected="selected"':''))}}>Pick by Party</option>
                                        </select>
                                        {!! $errors->first('trans', '<p class="help-block">:message</p>') !!}
                                </div>  
                                <div class="col-md-6 {{ $errors->has('dispatch') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.dispatch')}}<sup>*</sup></label>
                                        <input value="{{ $errors->has('dispatch')?'': ($flag==1? '': session('lastformdata')['dispatch'])}}" type="text" name="dispatch" id="dispatch" class="dispatch input-css">
                                        {!! $errors->first('dispatch', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                            <div class="col-md-12 {{ $errors->has('material') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.material')}}<sup>*</sup></label>
                                <select name="material" id="material" class="select2 item_id input-css material" style="width:100%">
                                        <option value="">Select Material/Items</option>
                                       @foreach ($idc as $item)
                                <option value="{{$item->id}}" {{$errors->has('material') ? '' : ($flag==1?'':(session('lastformdata')['material']==$item->id ? 'selected="selected"':''))}}>{{$item->name}}</option>
                                       @endforeach
                                </select><br><br><br>
                                <input type="text" placeholder="Other Item Name" class="input-css other_name" name="other_item_name" style="display:none"/>
                                {!! $errors->first('material', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div><br><br>
                        <div class="row">
                            <div class="col-md-3 {{ $errors->has('qty') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/material_inward.qty')}}<sup>*</sup></label>
                                    <input type="number" value="{{ $errors->has('qty')?'': ($flag==1? '': session('lastformdata')['qty'])}}" name="qty" id="qty" class="qty input-css">
                                    {!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-3">
                                <label>UOM<sup>*</sup></label>
                                <select   value="{{ old('dimension') }}"   class="form-control select2" required data-placeholder="" style="width: 100%;" name="dimension">
                                        <option value="">Select UOM</option>
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
                                        <option value="{{$key->uom_name}}">{{$key->uom_name}}</option>
                                        @endforeach
                                </select>
                                <label id="dimension-error" class="error" for="dimension"></label>
                            {!! $errors->first('dimension', '<p class="help-block">:message</p>') !!}
                           </div>
                            <div class="col-md-6 {{ $errors->has('time') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/material_inward.time1')}}<sup>*</sup></label>
                                <input type="text" value="{{ $errors->has('time')?'': ($flag==1? '': session('lastformdata')['time'])}}" name="time" id="time" class="time timepicker input-css">
                                {!! $errors->first('time', '<p class="help-block">:message</p>') !!}
                            </div>
                           
                        </div><br><br>
                       
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('driver') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.driver')}}<sup>*</sup></label> 
                                        <input type="text"  value="{{ $errors->has('driver')?'': ($flag==1? '': session('lastformdata')['driver'])}}" name="driver" id="driver" class="driver input-css">
                                        {!! $errors->first('driver', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('driver_num') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.driver_num')}}<sup>*</sup></label> 
                                        <input type="number" value="{{ $errors->has('driver_num')?'': ($flag==1? '': session('lastformdata')['driver_num'])}}" name="driver_num" id="driver_num" class="driver_num input-css">
                                        {!! $errors->first('driver_num', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-12 {{ $errors->has('remark') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/material_inward.remark')}}<sup>*</sup></label> 
                                        <textarea name="remark" id="remark" class="remark input-css">{{ $errors->has('remark')?'': ($flag==1? '': session('lastformdata')['remark'])}}</textarea>
                                        {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}
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
