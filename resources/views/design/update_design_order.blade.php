@extends($layout)

@section('title', 'Design Order')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Design Order</a></li>
   
@endsection
@section('js')
<script src="/js/Design/design_order.js"></script>
<script>
var ast="{{$design->io}}";
var io=ast.split(',');
$('.io').val(io).select2();

</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                   
                    @yield('content')
            </div>
        <!-- Default box -->
        <form action="/design/order/update/{{$id}}" method="POST" id="form">
        @csrf
        <div class="box-header with-border returnable">
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">Update Design Order</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <div class="row">
                                <div class="col-md-6 ">
                                        <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                        <input type="text" name="update_reason" required class="input-css" id="update_reason">
                                        {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                    </div><!--col-md-4-->
                                   
                        </div><br><br>
                                <div class="row">
                                        <div class="col-md-6 {{ $errors->has('party') ? 'has-error' : ''}}">
                                            <label>Referencee Name<sup>*</sup></label>
                                            <select name="reference" id="reference" class="select2 reference input-css">
                                                <option value="">Select Referencee</option>
                                                @foreach ($reference as $item)
                                                <option value="{{$item->id}}" {{$design->reference_name == $item->id ? 'selected = selected':''}}>{{$item->referencename}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('reference', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('io') ? 'has-error' : ''}}">
                                            <label>Internal Order</label>
                                            <select name="io" id="io" class="select2 io input-css" >
                                                <option value="" disabled>Select Internal Order</option>
                                                
                                                @foreach ($io as $item)
                                                <option value="{{$item->id}}" {{$design->io==$item->id ? "selected=selected" : ''}}>{{$item->io_number}}</option>
                                                @endforeach
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
                                                <option value="{{$key->id}}" {{$design->item == $key->id ? 'selected=selected':''}}>{{$key->name}}</option>
                                                @endforeach
                                                       
                                            </select><br><br>
                                        <input type="text" placeholder="Other Item Name" value="{{$design->other_item_name}}" class="input-css other_name" name="other_item_name" style="display:none"/>
                                         
                                            {!! $errors->first('item', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-4 {{ $errors->has('no_pages') ? 'has-error' : ''}}">
                                            <label>Number Pages/Degree/Creative <sup>*</sup></label>
                                        <input type="number" name="no_pages" value="{{$design->no_pages}}" min="{{$design->no_pages}}" id="no_pages" class="no_pages input-css"> 
                                        <input type="hidden"  value="{{$design->no_pages}}"  name="old_no_pages" >
                                        <input type="hidden"  value="{{$design->left_pages}}"  name="old_leftpages" >
                                            {!! $errors->first('no_pages', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-4 {{ $errors->has('alloted') ? 'has-error' : ''}}">
                                                <label>Alloted By <sup>*</sup></label>
                                                  <input type="text" name="alloted" value="{{$design->alloted}}" id="alloted" class="alloted input-css" required>
                                                {!! $errors->first('alloted', '<p class="help-block">:message</p>') !!}
                                            </div>
                                    </div><br><br>
                                    <div class="row">
                                            <div class="col-md-4 {{ $errors->has('creative') ? 'has-error' : ''}}">
                                                <label>Creative Name<sup>*</sup></label>
                                                <input type="text" name="creative" id="creative" value="{{$design->creative}}" class="creative input-css">
                                             
                                                {!! $errors->first('creative', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-4 {{ $errors->has('creative_party') ? 'has-error' : ''}}">
                                                <label>Creative received from Client <sup>*</sup></label>
                                                <!--<input type="text" name="creative_party" id="creative_party" value="{{$design->creative_party}}" class="creative_party input-css">-->
                                                <select name="creative_party" id="creative_party" class="select2 party input-css">
                                                <option value="yes" {{$design->creative_party == "yes" ? 'selected = selected':''}}>Yes</option>
                                                <option value="no" {{$design->creative_party == "yes" ? 'selected = selected':''}}>No</option>
                                            </select>
                                                    {!! $errors->first('creative_party', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-4 {{ $errors->has('print') ? 'has-error' : ''}}">
                                                <label>Is Printing Required <sup>*</sup></label>
                                               <select name="print" id="print" class="select2 print input-css">
                                                    <option selected="" disabled="">Select </option>
                                                    <option value="yes" {{$design->print == "yes" ? 'selected = selected':''}}>Yes</option>
                                                    <option value="no" {{$design->print == "no" ? 'selected = selected':''}}>No</option>
                                                </select>
                                                {!! $errors->first('print', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        </div><br><br>
                                        <div class="row">
                                                <div class="col-md-12 {{ $errors->has('remark') ? 'has-error' : ''}}">
                                                        <label>Any {{__('Utilities/material_inward.remark')}}</label> 
                                                        <textarea name="remark" id="remark" class="remark input-css">{{$design->remark}}</textarea>
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
