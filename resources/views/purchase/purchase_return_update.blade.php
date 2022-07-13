@extends($layout)

@section('title', __('purchase/return.title2'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Purchase Return Request</a></li>
   
@endsection
@section('js')
<script src="/js/purchase/return.js"></script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
    
                @yield('content')
        </div>
    <form action="/purchase/return/update/{{$id}}" method="POST" id="form">
        @csrf
        @foreach ($info as $item)
                    
                @endforeach
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/return.mytitle')}}</h2><br><br><br>
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
                        <div class="col-md-6 {{ $errors->has('date') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.date')}}<sup>*</sup></label>
                            <input value="{{$item->date}}"   type="text" name="date" id="date" required class="datepicker1 input-css">
                             {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('approved_by') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.appr')}}<sup>*</sup></label>
                            <select name="approved_by" id="approved_by" class="select2 input-css approved_by">
                                 
                                <option value="">Select Return Approved By</option>
                                @foreach ($user as $key)
                                <option value="{{$key->id}}" {{ $item->approved_by==$key->id ? 'selected="selected"' : ''}}>{{$key->name}}</option>
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
                                        <option value="{{$key->id}}" {{ $item->po_num_id==$key->id ? 'selected="selected"' : ''}}>{{$key->po_num}}</option>
                                            @endforeach
                                </select>
                                 {!! $errors->first('po', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('grn') ? 'has-error' : ''}}">
                                <label>{{__('purchase/return.grn')}}<sup>*</sup></label>
                                <select name="grn" id="grn" class="select2 input-css grn">
                                        <option value="">Select GRN</option>
                                        @foreach ($grn as $key)
                                        <option value="{{$key->id}}" {{ $item->grn_num_id==$key->id ? 'selected="selected"' : ''}}>{{$key->grn_number}}</option>
                                        @endforeach
                                </select>
                                 {!! $errors->first('grn', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('supp_by') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.supp')}}<sup>*</sup></label>
                            <input value="{{$item->supp_name}}"   type="text" name="supp_by" id="supp_by" class="supp_by input-css">
                             {!! $errors->first('supp_by', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('desc') ? 'has-error' : ''}}">
                                <label>{{__('purchase/return.desc')}}<sup>*</sup></label>
                                <input value="{{$item->item_desc}}"   type="text" name="desc" id="desc" class="desc input-css">
                                 {!! $errors->first('desc', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('rec_item') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.rec_item')}}<sup>*</sup></label>
                            <input value="{{$item->item_qty_received}}"   type="number" name="rec_item" id="rec_item" class="rec_item input-css">
                             {!! $errors->first('rec_item', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('ret_item') ? 'has-error' : ''}}">
                                <label>{{__('purchase/return.ret_item')}}<sup>*</sup></label>
                                <input value="{{$item->item_qty_returned}}"   type="number" name="ret_item" id="ret_item" class="ret_item input-css">
                                 {!! $errors->first('ret_item', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('uom') ? 'has-error' : ''}}">
                            <label>{{__('purchase/return.item')}}<sup>*</sup></label>
                            <select name="uom" id="uom" class="select2 input-css uom">
                                    <option value="">Select Item Unit</option>
                                    @foreach ($unit as $key)
                                    <option value="{{$key->id}}" {{ $item->item_unit==$key->id ? 'selected="selected"' : ''}}>{{$key->uom_name}}</option>
                                        @endforeach
                            </select>
                             {!! $errors->first('uom', '<p class="help-block">:message</p>') !!}
                        </div>   
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-12 {{ $errors->has('reason') ? 'has-error' : ''}}">
                                    <label>{{__('purchase/return.reason')}}<sup>*</sup></label> 
                                    <textarea name="reason" id="reason" class="reason input-css">{{$item->reason}}</textarea>
                                     {!! $errors->first('reason', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-12 {{ $errors->has('payment') ? 'has-error' : ''}}">
                                    <label>{{__('purchase/return.payment')}}<sup>*</sup></label> 
                                    <textarea name="payment" id="payment" class="payment input-css">{{$item->payment_desc}}</textarea>
                                     {!! $errors->first('payment', '<p class="help-block">:message</p>') !!}
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
