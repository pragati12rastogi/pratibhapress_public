@extends($layout)

@section('title', 'Design Order')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Design Order</a></li>
   
@endsection
@section('js')
<script src="/js/Design/design_order.js"></script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                   
                    @yield('content')
            </div>
        <!-- Default box -->
       <form action="/design/order/create" method="POST" id="form">
        @csrf
        <div class="box-header with-border returnable">
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">Create Design Order</h2><br><br><br>
                    <div class="container-fluid wdt">
                     
                                <div class="row">
                                        <div class="col-md-6 {{ $errors->has('party') ? 'has-error' : ''}}">
                                            <label>Reference Name<sup>*</sup></label>
                                            <select name="reference" id="reference" class="select2 reference input-css" required>
                                                <option value="">Select Reference Name</option>
                                                @foreach ($reference as $item)
                                                <option value="{{$item->id}}" {{old('party')==$item->id ? 'selected=selected':''}}>{{$item->referencename}}</option>
                                                 @endforeach
                                            </select>
                                            {!! $errors->first('reference', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('io') ? 'has-error' : ''}}">
                                            <label>Internal Order</label>
                                            <select name="io" id="io" class="select2 io input-css" >
                                                <option value="" ></option>
                                            </select>
                                            {!! $errors->first('io', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div><br><br>
                                <div class="row">
                                        <div class="col-md-4 {{ $errors->has('item') ? 'has-error' : ''}}">
                                            <label>Item Category<sup>*</sup></label>
                                            <select name="item" id="item" class="select2 item_id item input-css">
                                                <option value="">Select item category</option>
                                                @foreach ($item_cat as $key)
                                                <option value="{{$key->id}}" {{old('item')==$key->id ? 'selected=selected':''}}>{{$key->name}}</option>
                                                @endforeach                                                 
                                            </select><br><br>
                                        <input type="text" placeholder="Other Item Name" value="{{old('other_item_name')}}" class="input-css other_name" name="other_item_name" style="display:none"/>
                                         
                                            {!! $errors->first('item', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-4 {{ $errors->has('no_pages') ? 'has-error' : ''}}">
                                            <label>Number Pages/Degree/Creative <sup>*</sup></label>
                                              <input type="number" name="no_pages" value="{{old('no_pages')}}" id="no_pages" class="no_pages input-css">
                                                {!! $errors->first('no_pages', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-4 {{ $errors->has('alloted') ? 'has-error' : ''}}">
                                                <label>Alloted By <sup>*</sup></label>
                                                  <input type="text" name="alloted" value="{{old('alloted')}}" id="alloted" class="alloted input-css" required>
                                                {!! $errors->first('alloted', '<p class="help-block">:message</p>') !!}
                                            </div>
                                    </div><br><br>
                                    <div class="row">
                                            <div class="col-md-4 {{ $errors->has('creative') ? 'has-error' : ''}}">
                                                <label>Creative Name<sup>*</sup></label>
                                                <input type="text" name="creative" id="creative" value="{{old('creative')}}" class="creative input-css">
                                             
                                                {!! $errors->first('creative', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-4 {{ $errors->has('creative_party') ? 'has-error' : ''}}">
                                                    <label>Creative received from Client <sup>*</sup></label>
                                                   <!--<input type="text" name="creative_party" id="creative_party" value="{{old('creative_party')}}" class="creative_party input-css">-->
                                                   <select name="creative_party" id="creative_party" class="select2 party input-css">
                                                        <option selected="" disabled="">Select </option>
                                                        <option value="yes" >Yes</option>
                                                        <option value="no" >No</option>
                                                    </select>
                                                    {!! $errors->first('creative_party', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-4 {{ $errors->has('print') ? 'has-error' : ''}}">
                                                    <label>Is Printing Required <sup>*</sup></label>
                                                   <select name="print" id="print" class="select2 print input-css">
                                                        <option selected="" disabled="">Select </option>
                                                        <option value="yes" >Yes</option>
                                                        <option value="no" >No</option>
                                                    </select>
                                                    {!! $errors->first('print', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div><br><br>
                                        <div class="row">
                                                <div class="col-md-12 {{ $errors->has('remark') ? 'has-error' : ''}}">
                                                        <label>Any {{__('Utilities/material_inward.remark')}}</label> 
                                                        <textarea name="remark" id="remark" class="remark input-css">{{old('remark')}}</textarea>
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
