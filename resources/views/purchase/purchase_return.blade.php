@extends($layout)

@section('title', __('purchase/return.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Purchase Return Request</a></li>
   
@endsection

@section('js')
<script src="/js/purchase/return.js"></script>
<script>
var message="{{Session::get('return')}}";
if(message=="successfull"){
    document.getElementById("return").click();
}
</script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
    
                @yield('content')
        </div>
       <form action="/purchase/return/create" method="POST" id="form">
        @csrf
        @php
                $flag=0;
                @endphp
    
                @if (empty(session('lastformdata')))            
                @php
                  $flag=1;
                  $to=1;
                @endphp
                   @endif
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/return.mytitle')}}</h2><br><br><br>
                <div class="container-fluid wdt">
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('date') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.date')}}<sup>*</sup></label>
                            <input autocomplete="off" value="{{ $errors->has('date')?'': ($flag==1? '': session('lastformdata')['date'])}}"   type="text" name="date" id="date" required class="datepicker1 input-css">
                            {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('approved_by') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.appr')}}<sup>*</sup></label>
                            <select name="approved_by" id="approved_by" class="select2 input-css approved_by">
                               
                                <option value="">Select Return Approved By</option>
                                @foreach ($user as $key)
                                     <option value="{{$key->id}}" {{$errors->has('approved_by') ? '' : ($flag==1?'':(session('lastformdata')['approved_by']==$key->id? 'selected="selected"':''))}}>{{$key->name}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('approved_by', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('po') ? 'has-error' : ''}}">
                                <label>{{__('purchase/return.po')}}<sup>*</sup></label>
                                <select name="po" id="po" class="select2 input-css po">
                                        <option value="">Select PO Number</option>
                                        @foreach ($po as $key)
                                        <option value="{{$key->id}}" {{$errors->has('po') ? '' : ($flag==1?'':(session('lastformdata')['po']==$key->id? 'selected="selected"':''))}}>{{$key->po_num}}</option>
                                            @endforeach
                                </select>
                                {!! $errors->first('po', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('grn') ? 'has-error' : ''}}">
                                <label>{{__('purchase/return.grn')}}<sup>*</sup></label>
                                <select name="grn" id="grn" class="select2 input-css grn">
                                        <option value="">Select GRN</option>
                                        @foreach ($grn as $key)
                                        <option value="{{$key->id}}" {{$errors->has('grn') ? '' : ($flag==1?'':(session('lastformdata')['grn']==$key->id? 'selected="selected"':''))}}>{{$key->grn_number}}</option>
                                        @endforeach
                                 </select>
                                {!! $errors->first('grn', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('supp_by') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.supp')}}<sup>*</sup></label>
                            <input value="{{ $errors->has('supp_by')?'': ($flag==1? '': session('lastformdata')['supp_by'])}}"   type="text" name="supp_by" id="supp_by" class="supp_by input-css">
                            {!! $errors->first('supp_by', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('desc') ? 'has-error' : ''}}">
                                <label>{{__('purchase/return.desc')}}<sup>*</sup></label>
                                <input value="{{ $errors->has('desc')?'': ($flag==1? '': session('lastformdata')['desc'])}}"   type="text" name="desc" id="desc" class="desc input-css">
                                {!! $errors->first('desc', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('rec_item') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.rec_item')}}<sup>*</sup></label>
                            <input value="{{ $errors->has('rec_item')?'': ($flag==1? '': session('lastformdata')['rec_item'])}}"   type="number" name="rec_item" id="rec_item" class="rec_item input-css">
                            {!! $errors->first('rec_item', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('ret_item') ? 'has-error' : ''}}">
                                <label>{{__('purchase/return.ret_item')}}<sup>*</sup></label>
                                <input value="{{ $errors->has('ret_item')?'': ($flag==1? '': session('lastformdata')['ret_item'])}}"   type="number" name="ret_item" id="ret_item" class="ret_item input-css">
                                {!! $errors->first('ret_item', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('uom') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.item')}}<sup>*</sup></label>
                            <select name="uom" id="uom" class="select2 input-css uom">
                                    <option value="">Select Item Unit</option>
                                    @foreach ($unit as $key)
                                    <option value="{{$key->id}}" {{$errors->has('uom') ? '' : ($flag==1?'':(session('lastformdata')['uom']==$key->id? 'selected="selected"':''))}}>{{$key->uom_name}}</option>
                                        @endforeach
                            </select>
                            {!! $errors->first('uom', '<p class="help-block">:message</p>') !!}
                        </div>   
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-12 {{ $errors->has('reason') ? 'has-error' : ''}}">
                                    <label>{{__('purchase/return.reason')}}<sup>*</sup></label> 
                                    <textarea name="reason" id="reason" class="reason input-css">{{ $errors->has('reason')?'': ($flag==1? '': session('lastformdata')['reason'])}}</textarea>
                                    {!! $errors->first('reason', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-12 {{ $errors->has('payment') ? 'has-error' : ''}}">
                                    <label>{{__('purchase/return.payment')}}<sup>*</sup></label> 
                                    <textarea name="payment" id="payment" class="payment input-css">{{ $errors->has('payment')?'': ($flag==1? '': session('lastformdata')['payment'])}}</textarea>
                                    {!! $errors->first('payment', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                </div>
            </div>
        </div>
       
 
        <div class="row">
                <div class="col-md-12">
                     <input  type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
