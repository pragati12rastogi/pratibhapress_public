@extends($layout)

@section('title', __('stock/stock.title2'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Stock Master</a></li>
    

@endsection
@section('js')
<script src="/js/stock/stock.js"></script>

@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
        
                    @yield('content')
            </div>
        <form method="POST" action="/stock/update/{{$id}}" id="stock">
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
                   @foreach ($stock as $item)
                       
                   @endforeach
                   <div class="box-header with-border">
                    <div class='box box-default'>  <br>
                            <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('stock/stock.mytitle')}}</h2><br><br><br>
                        <div class="container-fluid wdt">
                            <div class="row">
                                <div class="col-md-12 {{ $errors->has('entry_for') ? 'has-error' : ''}}">
                                    <label>{{__('stock/stock.master')}} {{__('stock/stock.item')}}<sup>*</sup></label>
                                    <div class="col-md-3">
                                        <div class="radio">
                                            <label><input    autocomplete="off" disabled type="radio" class="entry_for" {{ $item->master_cat_id==1? 'checked=checked' :'' }} value="Paper" name="entry_for">{{__('stock/stock.paper')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="radio">
                                            <label><input autocomplete="off" disabled type="radio" class="entry_for" {{ $item->master_cat_id==2 ? 'checked=checked' :'' }} value="Inks & Chemicals" name="entry_for">{{__('stock/stock.ink')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="radio">
                                            <label><input  autocomplete="off" disabled type="radio" class="entry_for" {{ $item->master_cat_id==3 ? 'checked=checked' :'' }}value="Plate" name="entry_for">{{__('stock/stock.plate')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                            <div class="radio">
                                                <label><input  autocomplete="off" disabled type="radio" {{ $item->master_cat_id==4 ? 'checked=checked' :'' }} class="entry_for" value="Miscellaneous" name="entry_for">{{__('stock/stock.misc')}}</label>
                                            </div>
                                    </div>
                                    <input type="hidden" name="entry_for" value="{{$item->master_cat_id}}">
                                </div><!--col-md-6-->
                                {!! $errors->first('entry_for', '<p class="help-block">:message</p>') !!}
                            </div><br><br>
                        </div>
                    </div>
                   </div>
                   <div class="box-header with-border paper" {{ $item->master_cat_id==1 ? 'style=display:block' :'style=display:none'}}>
                        <div class='box box-default'>  <br>
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('stock/stock.paper')}}</h2><br><br><br>
                            <div class="container-fluid wdt">
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('paper_cat') ? 'has-error' : ''}}">
                                        <label>{{__('stock/stock.paper')}}  {{__('stock/stock.item')}}<sup>*</sup></label>
                                        <select style="width:100%" name="paper_cat" id="paper_cat" class="select2 input-css paper_cat">
                                            <option value="">Select Paper Item Category</option>
                                            @foreach ($paper as $key)
                                        <option value="{{$key->id}}" {{$item->sub_cat_id==$key->id? 'selected="selected':''}}>{{$key->name}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('paper_cat', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('paper_item_stan_unit') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.paper')}}  {{__('stock/stock.item_stan')}}<sup>*</sup></label>
                                            <select style="width:100%" name="paper_item_stan_unit" id="paper_item_stan_unit" class="select2 input-css paper_item_stan_unit">
                                                <option value="">Select Paper Standard Packing</option>
                                                @foreach ($stand_paper as $key)
                                        <option value="{{$key->id}}" {{$item->item_stand_pack==$key->id? 'selected="selected':''}}>{{$key->uom_name}}</option>
                                        @endforeach
                                            </select>
                                            {!! $errors->first('paper_item_stan_unit', '<p class="help-block">:message</p>') !!}
                                    </div>
                                   
                                </div><br><br>
                                    <div class="row">
                                      
                                        <div class="col-md-6 {{ $errors->has('paper_item_qty') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.standard')}}<sup>*</sup></label>
                                            <input type="number" min="0" step="none" name="paper_item_qty" value="{{$item->qty_sp}}" id="paper_item_qty" class="input-css paper_item_qty">
                                            {!! $errors->first('paper_item_qty', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('paper_item_unit') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.paper')}}  {{__('stock/stock.unit')}}<sup>*</sup></label>
                                                <select style="width:100%" name="paper_item_unit" id="paper_item_unit" class="select2 input-css paper_item_unit">
                                                    <option value="">Select Unit Of Quantity</option>
                                                    @foreach ($unit_paper as $key)
                                        <option value="{{$key->id}}" {{$item->unit_of_qty==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('paper_item_unit', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>

                                <br><br>
                                <div class="row">
                                      
                                        <div class="col-md-6 {{ $errors->has('paper_sku') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.paper')}}  {{__('stock/stock.sku')}}<sup>*</sup></label>
                                                <select style="width:100%" name="paper_sku" id="paper_sku" required class="select2 input-css paper_sku">
                                                    <option value="">Select Stock Keeping Unit</option>
                                                    @foreach ($sku_paper as $key)
                                        <option value="{{$key->id}}" {{$item->stock_unit==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('paper_sku', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('paper_length') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.length')}}<sup>*</sup></label>
                                                <input type="number" min="0" step="none" name="paper_length" value="{{ $item->length}}" id="paper_length" required class="input-css paper_length">
                                                {!! $errors->first('paper_length', '<p class="help-block">:message</p>') !!}
                                            </div>
                                </div> <br><br>
                                <div class="row">
                                   
                                    <div class="col-md-6 {{ $errors->has('paper_breadth') ? 'has-error' : ''}}">
                                        <label>{{__('stock/stock.breadth')}}<sup>*</sup></label>
                                        <input type="number" min="0" step="none" name="paper_breadth" value="{{ $item->breadth}}" id="paper_breadth" class="input-css paper_breadth">
                                        {!! $errors->first('paper_breadth', '<p class="help-block">:message</p>') !!}
                                    </div> 
                                    <div class="col-md-6 {{ $errors->has('paper_dimension') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.dimension')}}<sup>*</sup></label>
                                            <select style="width:100%" name="paper_dimension" id="paper_dimension" class="select2 input-css paper_dimension">
                                                    <option value="">Select Dimension</option>
                                                    <option value="m" {{$item->dimension=='m' ? 'selected="selected"':''}}>Metre</option>
                                                    <option value="mm" {{$item->dimension=='mm' ? 'selected="selected"':''}}>Millimeter</option>
                                                    <option value="cm" {{$item->dimension=='cm' ? 'selected="selected"':''}}>Centimeter</option>
                                                    <option value="km" {{$item->dimension=='km' ? 'selected="selected"':''}}>Kilometer</option>
                                                    <option value="in" {{$item->dimension=='in' ? 'selected="selected"':''}}>Inch</option>
                                                    <option value="ft" {{$item->dimension=='ft' ? 'selected="selected"':''}}>Foot</option>
                                                    <option value="ton" {{$item->dimension=='ton' ? 'selected="selected"':''}}>Ton</option>
                                                    <option value="doz" {{$item->dimension=='doz' ? 'selected="selected"':''}}>Dozen</option>
                                                    <option value="kg" {{$item->dimension=='kg' ? 'selected="selected"':''}}>Kilogram</option>
                                                    <option value="g" {{$item->dimension=='g' ? 'selected="selected"':''}}>Grams</option>
                                            </select>
                                            {!! $errors->first('paper_dimension', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div> <br><br>
                                    <div class="row">
                                        <div class="col-md-4 {{ $errors->has('paper_gsm') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.gsm')}}<sup>*</sup></label>
                                            <input type="text" name="paper_gsm" value="{{ $item->gsm}}" id="paper_gsm" class="input-css paper_gsm">
                                            {!! $errors->first('paper_gsm', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-4 {{ $errors->has('paper_brand') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.brand')}}<sup>*</sup></label>
                                            <input type="text"  name="paper_brand" value="{{ $item->brand}}" id="paper_brand" class="input-css paper_brand">
                                            {!! $errors->first('paper_brand', '<p class="help-block">:message</p>') !!}
                                        </div> 
                                        <div class="col-md-4 {{ $errors->has('paper_location') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.paper')}}  {{__('stock/stock.location')}}<sup></sup></label>
                                                <input type="text"  name="paper_location" value="{{ $item->item_location}}" id="paper_location"  class="input-css paper_location">
                                                {!! $errors->first('paper_location', '<p class="help-block">:message</p>') !!}
                                        </div> 
                                    </div> <br><br>
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('paper_stock') ? 'has-error' : ''}}">
                                                    <label>{{__('stock/stock.stock')}}<sup></sup></label>
                                                    <input type="number" name="paper_stock" value="{{ $item->opening_stock}}" id="paper_stock" class="input-css paper_stock">
                                                    {!! $errors->first('paper_stock', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('paper_minimum') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.entry')}}<sup></sup></label>
                                                <input type="number" name="paper_minimum" value="{{ $item->min_entry_level}}" id="paper_minimum"  class="input-css paper_minimum">
                                                {!! $errors->first('paper_minimum', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        
                                    </div> <br><br>

                            </div>   
                        </div>
                   </div>
                    <div class="box-header with-border ink" {{ $item->master_cat_id==2 ? 'style=display:block' :'style=display:none' }}>
                        <div class='box box-default'>  <br>
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('stock/stock.ink')}}</h2><br><br><br>
                            <div class="container-fluid wdt">
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('ink_cat') ? 'has-error' : ''}}">
                                        <label>{{__('stock/stock.ink')}}  {{__('stock/stock.item')}}<sup>*</sup></label>
                                        <select style="width:100%" name="ink_cat" id="ink_cat" class="select2 input-css ink_cat">
                                            <option value="">Select Inks & Chemicals Item Category</option>
                                            @foreach ($ink as $key)
                                        <option value="{{$key->id}}" {{$item->sub_cat_id==$key->id ? 'selected="selected"':''}}>{{$key->name}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('ink_cat', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('ink_item_stan_unit') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.ink')}}  {{__('stock/stock.item_stan')}}<sup>*</sup></label>
                                            <select style="width:100%" name="ink_item_stan_unit" id="ink_item_stan_unit" class="select2 input-css ink_item_stan_unit">
                                                <option value="">Select Inks & Chemicals Standard Packing</option>
                                                @foreach ($stand_ink as $key)
                                        <option value="{{$key->id}}" {{$item->item_stand_pack==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                        @endforeach
                                            </select>
                                            {!! $errors->first('ink_item_stan_unit', '<p class="help-block">:message</p>') !!}
                                    </div>
                                  
                                </div><br><br>
                                    <div class="row">
                                       
                                        <div class="col-md-6 {{ $errors->has('ink_item_qty') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.standard')}}<sup>*</sup></label>
                                            <input type="number" min="0" step="none" name="ink_item_qty" value="{{ $item->qty_sp}}" id="ink_item_qty" class="input-css ink_item_qty">
                                            {!! $errors->first('ink_item_qty', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('ink_item_unit') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.ink')}}  {{__('stock/stock.unit')}}<sup>*</sup></label>
                                                <select style="width:100%" name="ink_item_unit" id="ink_item_unit" class="select2 input-css ink_item_unit">
                                                    <option value="">Select Unit Of Quantity</option>
                                                    @foreach ($unit_ink as $key)
                                        <option value="{{$key->id}}" {{$item->unit_of_qty==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('ink_item_unit', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>

                                <br><br>
                                <div class="row">
                                       
                                        <div class="col-md-6 {{ $errors->has('ink_sku') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.ink')}}  {{__('stock/stock.sku')}}<sup>*</sup></label>
                                                <select style="width:100%" name="ink_sku" id="ink_sku" class="select2 input-css ink_sku" required>
                                                    <option value="">Select Stock Keeping Unit</option>
                                                    @foreach ($sku_ink as $key)
                                        <option value="{{$key->id}}" {{$item->stock_unit==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('ink_sku', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('ink_location') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.ink')}}  {{__('stock/stock.location')}}<sup></sup></label>
                                                <input type="text"  name="ink_location" value="{{ $item->item_location}}" id="ink_location" class="input-css ink_location">
                                                {!! $errors->first('paper_location', '<p class="help-block">:message</p>') !!}
                                        </div> 
                                </div> <br><br>
                                <div class="row">
                                       
                                        <div class="col-md-6 {{ $errors->has('ink_brand') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.model')}}<sup></sup></label>
                                            <input type="text"  name="ink_brand" value="{{ $item->brand}}" id="paper_brand" class="input-css paper_brand" >
                                            {!! $errors->first('ink_brand', '<p class="help-block">:message</p>') !!}
                                        </div> 
                                        <div class="col-md-6 {{ $errors->has('ink_color') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.ink')}}  {{__('stock/stock.color')}}<sup></sup></label>
                                                <input type="text"  name="ink_color" value="{{ $item->color}}" id="paper_location" class="input-css paper_location" >
                                                {!! $errors->first('ink_color', '<p class="help-block">:message</p>') !!}
                                        </div> 
                                    </div> <br><br>
                                 
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('ink_stock') ? 'has-error' : ''}}">
                                                    <label>{{__('stock/stock.stock')}}<sup></sup></label>
                                                    <input type="number" name="ink_stock" value="{{ $item->opening_stock}}" id="ink_stock" class="input-css ink_stock">
                                                    {!! $errors->first('ink_stock', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('ink_minimum') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.entry')}}<sup></sup></label>
                                                <input type="number" name="ink_minimum" value="{{ $item->min_entry_level}}" id="ink_minimum" class="input-css ink_minimum">
                                                {!! $errors->first('ink_minimum', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        
                                    </div> <br><br>

                            </div>   
                        </div>
                    </div>
                    <div class="box-header with-border plate" {{ $item->master_cat_id==3 ? 'style=display:block' :'style=display:none' }}>
                        <div class='box box-default'>  <br>
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('stock/stock.plate')}}</h2><br><br><br>
                            <div class="container-fluid wdt">
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('plate_cat') ? 'has-error' : ''}}">
                                        <label>{{__('stock/stock.plate')}}  {{__('stock/stock.item')}}<sup>*</sup></label>
                                        <select style="width:100%" name="plate_cat" id="plate_cat" class="select2 input-css plate_cat">
                                            <option value="">Select Plate Item Category</option>
                                            @foreach ($plate as $key)
                                        <option value="{{$key->id}}" {{$item->sub_cat_id==$key->id ? 'selected="selected"':''}}>{{$key->name}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('plate_cat', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('plate_item_stan_unit') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.plate')}}  {{__('stock/stock.item_stan')}}<sup>*</sup></label>
                                            <select style="width:100%" name="plate_item_stan_unit" id="plate_item_stan_unit" class="select2 input-css plate_item_stan_unit">
                                                <option value="">Select Plate Standard Packing</option>
                                                @foreach ($stand_plate as $key)
                                        <option value="{{$key->id}}" {{$item->item_stand_pack==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                        @endforeach
                                            </select>
                                            {!! $errors->first('plate_item_stan_unit', '<p class="help-block">:message</p>') !!}
                                    </div>
                                  
                                </div><br><br>
                                    <div class="row">
                                       
                                        <div class="col-md-6 {{ $errors->has('plate_item_qty') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.standard')}}<sup></sup></label>
                                            <input type="number" min="0" step="none" name="plate_item_qty" value="{{ $item->qty_sp}}" id="plate_item_qty" class="input-css plate_item_qty">
                                            {!! $errors->first('plate_item_qty', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('plate_item_unit') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.plate')}}  {{__('stock/stock.unit')}}<sup>*</sup></label>
                                                <select style="width:100%" name="plate_item_unit" id="plate_item_unit" class="select2 input-css plate_item_unit">
                                                    <option value="">Select Unit Of Quantity</option>
                                                    @foreach ($unit_plate as $key)
                                        <option value="{{$key->id}}" {{$item->unit_of_qty==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('plate_item_unit', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>

                                <br><br>
                                <div class="row">
                                       
                                        <div class="col-md-6 {{ $errors->has('plate_sku') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.plate')}}  {{__('stock/stock.sku')}}<sup>*</sup></label>
                                                <select style="width:100%" name="plate_sku" id="plate_sku" class="select2 input-css plate_sku">
                                                    <option value="">Select Stock Keeping Unit</option>
                                                    @foreach ($sku_plate as $key)
                                        <option value="{{$key->id}}" {{$item->stock_unit==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('plate_sku', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('plate_size') ? 'has-error' : ''}}">
                                                <label>Plate Stock Size<sup>*</sup></label>
                                                <select style="width:100%" name="plate_size" id="plate_size" class="select2 input-css plate_size" required>
                                                    <option value="">Select Plate Size</option>
                                                    @foreach ($plate_size as $key)
                                        <option value="{{$key->value}}" {{$item->size==$key->value ? 'selected="selected"':''}}>{{$key->value}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('plate_size', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div> <br><br>
                             
                                    <div class="row">
                                            <div class="col-md-4 {{ $errors->has('plate_location') ? 'has-error' : ''}}">
                                                    <label>{{__('stock/stock.plate')}}  {{__('stock/stock.location')}}<sup></sup></label>
                                                    <input type="text"  name="plate_location" value="{{ $item->item_location}}" id="plate_location" class="input-css plate_location">
                                                    {!! $errors->first('paper_location', '<p class="help-block">:message</p>') !!}
                                            </div> 
                                            <div class="col-md-4 {{ $errors->has('plate_stock') ? 'has-error' : ''}}">
                                                    <label>{{__('stock/stock.stock')}}<sup></sup></label>
                                                    <input type="number" name="plate_stock" value="{{ $item->opening_stock}}" id="plate_stock" class="input-css plate_stock">
                                                    {!! $errors->first('plate_stock', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-4 {{ $errors->has('plate_minimum') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.entry')}}<sup></sup></label>
                                                <input type="number" name="plate_minimum" value="{{ $item->min_entry_level}}" id="plate_minimum" class="input-css plate_minimum">
                                                {!! $errors->first('plate_minimum', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        
                                    </div> <br><br>

                            </div>   
                        </div>
                    </div>
                    <div class="box-header with-border misc" {{ $item->master_cat_id==4 ? 'style=display:block' :'style=display:none' }}>
                        <div class='box box-default'>  <br>
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('stock/stock.misc')}}</h2><br><br><br>
                            <div class="container-fluid wdt">
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('misc_cat') ? 'has-error' : ''}}">
                                        <label>{{__('stock/stock.misc')}}  {{__('stock/stock.item')}}<sup>*</sup></label>
                                        <select style="width:100%" name="misc_cat" id="misc_cat" class="select2 input-css misc_cat">
                                            <option value="">Select Miscellaneous Item Category</option>
                                            @foreach ($misc as $key)
                                        <option value="{{$key->id}}" {{$item->sub_cat_id==$key->id ? 'selected="selected"':''}}>{{$key->name}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('misc_cat', '<p class="help-block">:message</p>') !!}
                                    </div>

                                    <div class="col-md-6 {{ $errors->has('misc_item') ? 'has-error' : ''}}">
                                        <label>{{__('stock/stock.misc')}}  {{__('stock/stock.name')}}<sup>*</sup></label>
                                        <input style="width:100%" name="misc_item" id="misc_item"  class="input-css misc_item" value="{{ $item->item_name}}">
                                        {!! $errors->first('misc_item', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                    <div class="row">
                                        <div class="col-md-6 {{ $errors->has('misc_item_stan_unit') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.misc')}}  {{__('stock/stock.item_stan')}}<sup>*</sup></label>
                                                <select style="width:100%" name="misc_item_stan_unit" id="misc_item_stan_unit" class="select2 input-css misc_item_stan_unit">
                                                    <option value="">Select Miscellaneous Standard Packing</option>
                                                    @foreach ($stand_misc as $key)
                                            <option value="{{$key->id}}" {{$item->unit_of_qty==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('misc_item_stan_unit', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('misc_item_qty') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.standard')}}<sup>*</sup></label>
                                            <input type="number" min="0" step="none" name="misc_item_qty" value="{{ $item->qty_sp}}" id="misc_item_qty" class="input-css misc_item_qty">
                                            {!! $errors->first('misc_item_qty', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    
                                    </div>

                                <br><br>
                                <div class="row">
                                        <div class="col-md-6 {{ $errors->has('misc_item_unit') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.misc')}}  {{__('stock/stock.unit')}}<sup>*</sup></label>
                                                <select style="width:100%" name="misc_item_unit" id="misc_item_unit" class="select2 input-css misc_item_unit">
                                                    <option value="">Select Unit Of Quantity</option>
                                                    @foreach ($unit_misc as $key)
                                        <option value="{{$key->id}}" {{$item->unit_of_qty==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('misc_item_unit', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('misc_sku') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.misc')}}  {{__('stock/stock.sku')}}<sup>*</sup></label>
                                                <select style="width:100%" name="misc_sku" id="misc_sku" class="select2 input-css misc_sku">
                                                    <option value="">Select Stock Keeping Unit</option>
                                                    @foreach ($sku_misc as $key)
                                        <option value="{{$key->id}}" {{$item->stock_unit==$key->id ? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                                </select>
                                                {!! $errors->first('misc_sku', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div> <br><br>
                               
                                    <div class="row">
                                      
                                        <div class="col-md-4 {{ $errors->has('misc_location') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.misc')}}  {{__('stock/stock.location')}}<sup></sup></label>
                                                <input type="text"  name="misc_location" value="{{ $item->item_location}}" id="misc_location" class="input-css misc_location">
                                                {!! $errors->first('paper_location', '<p class="help-block">:message</p>') !!}
                                        </div> 
                                        <div class="col-md-4 {{ $errors->has('misc_stock') ? 'has-error' : ''}}">
                                                <label>{{__('stock/stock.stock')}}<sup></sup></label>
                                                <input type="number" name="misc_stock" value="{{ $item->opening_stock}}" id="misc_stock" class="input-css misc_stock">
                                                {!! $errors->first('misc_stock', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-4 {{ $errors->has('misc_minimum') ? 'has-error' : ''}}">
                                            <label>{{__('stock/stock.entry')}}<sup></sup></label>
                                            <input type="number" name="misc_minimum" value="{{ $item->min_entry_level}}" id="misc_minimum" class="input-css misc_minimum">
                                            {!! $errors->first('misc_minimum', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div> <br><br>
                                    

                            </div>   
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-md-12">
                                <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
                            </div>
                    </div>  
               </form>
      </section>
@endsection
