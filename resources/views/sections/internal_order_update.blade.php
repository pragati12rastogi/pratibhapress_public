
@extends($layout)
@section('title', __('internal_order.mytitle'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('internal_order.mytitle')}}</i></a></li>

@endsection

@section('css')
  <link rel="stylesheet" href="/css/party.css">
@endsection

@section('js')
<!-- jquery-validater -->

<!-- end of jquery-validater -->
<script src="/js/views/create-order.js"></script>

@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                 
                    @yield('content')
            </div>

            @if($msg!="")
            <div class="alert alert-info alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>	
                    <strong>{{$msg}}</strong>
            </div>
                    @endif
           
           
        <!-- Default box -->
           @foreach($data as $d)
            @endforeach

            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <div class="container-fluid">
                    <form action="/internalorder/update/db/{{$d->id}}" method="POST" id="form" class=" {{ $errors->has('name') ? 'has-error' : ''}}">
                            @csrf
                            <div class="row">
                                <div class="form_step1">
                                    <h3 class="box-title" style="font-size: 28px;">{{__('internal_order.IO')}}</h3><br><br><br>
                                    <div class="row">
                                        <div class="col-md-3 ">
                                            <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                            <input type="text" name="update_reason" required="" class="form-control input-css" id="update_reason" required>
                                            {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                        </div><!--col-md-4-->
     
                                        <div class="col-md-12">
                                            <label>{{__('internal_order.ref_name')}}<sup>*</sup></label>
                                            <select class="form-control select2 reference_name"  data-placeholder="" style="width: 100%;" name="reference_name">
                                            <option value="default">Select Reference Name</option>
                                            @foreach($party as $key)
                                            <option value="{{$key->id}}" {{ $d->reference_name==$key->id ? 'selected="selected"' : ''}} >{{$key->referencename   }}</option>
                                            @endforeach
                                            </select>
                                            {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
                                        </div><!--col-md-4-->
                                    </div><!--end of row-->
                                        <div class="row">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="item_label_er">{{__('internal_order.item')}}<sup>*</sup></label>
                                            {!! $errors->first('item', '<p class="help-block">:message</p>') !!}

                                            <select name="item" id="" class="form-control input-css item_id item select2">
                                                    @foreach ($item as $key)
                                                <option value="{{$key->id}}" {{ $d->item_category_id==$key->id ? 'selected="selected"' : ''}}>{{$key->name}}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" value="{{$d->item_category_id==15?$d->other_item_name:''}}" placeholder="Item Name" class="form-control input-css other_name" name="other_item_name" />
                                        </div><!--col-md-4-->
                                    </div><!--row-->
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                        </div>
                                    </div>
                                </div>
                                {{-------------------------------  form 2 starts -------------------------------}}
                                <div class="form_step2" >
                                    <h3 class="box-title" style="font-size: 28px;">{{__('internal_order.job')}}</h3><br><br><br>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.type')}}<sup>*</sup></label>
                                            <select class="form-control select2" {{$flag==1 ? 'disabled=disabled' : '' }} id="io_type" data-placeholder=""style="width: 100%;" name="io_type">
                                            <option value="default">Select I.O Type</option>
                                            @foreach($io_type as $key)
                                            <option value="{{$key->id}}" {{ $d->io_type_id==$key->id ? 'selected="selected"' : ''}}>{{$key->name}}</option>
                                            @endforeach
                                            </select>
                                        <input type="hidden" name="io_type_old" value="{{$d->io_type_id}}">
                                            {!! $errors->first('io_type', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-3-->
                                        <div class="col-md-4"><!--job date-->
                                            <label>{{__('internal_order.date')}}<sup>*</sup></label>
                                            
                                        <input type="hidden" name="flag" value="{{$flag}}">
                                         
                                            <input type="text" autocomplete="off" class="form-control datepicker1 input-css" value="{{CustomHelpers::showDate($d->job_date,'d-m-Y')}}"  id="datepicker" name="job_date">
                                            {!! $errors->first('job_date', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->
                                        <!--job date end-->
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.HSN')}} <sup>*</sup></label>
                                            <select class="form-control select2 hsn"  data-placeholder=""style="width: 100%;" name="hsn">
                                                    <option value="0">Select I.O Type</option>
                                                    @foreach($hsn as $key)
                                                    <option value="{{$key->id}}" {{ $d->hsn_code==$key->id ? 'selected="selected"' : ''}}>{{$key->name}} - {{$key->hsn}} - {{$key->gst_rate}}</option>
                                                    @endforeach
                                                    </select>
                                            {!! $errors->first('hsn', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->
                                    </div><!--end of row-->

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.delivery date')}}<sup>*</sup></label>
                                            <input type="text" autocomplete="off" class="form-control datepicker1 input-css" id="datepicker" value="{{CustomHelpers::showDate($d->delivery_date,'d-m-Y')}}" name="delivery_date">
                                            {!! $errors->first('delivery_date', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-3-->

                                        <div class="col-md-4">
                                            <label>{{__('internal_order.qty')}}<sup>*</sup></label>
                                  
                                            @if($flag==0)
                                            <input type="number" class="form-control input-css" value="{{$d->qty}}"  min="0" placeholder="Min:0" name="job_qty" >
                                            @elseif($flag==1)
                                            <input type="number" class="form-control input-css" value="{{$d->qty}}"  min="{{$d->qty-$d->left_qty}}" placeholder="Min:{{$d->qty-$d->left_qty }}" name="job_qty" >
                                            @endif
                                        
                                        <input type="hidden"  value="{{$d->qty}}"  name="old_job_qty" >
                                        <input type="hidden"  value="{{$d->left_qty}}"  name="old_leftqty" >
                                            {!! $errors->first('job_qty', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->

                                        <div class="col-md-4">
                                            <label>{{__('internal_order.qty1')}}<sup>*</sup></label>
                                            <select  type="number" step="any" class="select form-control input-css"  name="job_qty_unit">
                                                <option value="default">Select Job Quantity Unit</option>
                                                @foreach($job_qty_unit as $key)
                                                <option value="{{$key->id}}" {{ $d->unit==$key->id ? 'selected="selected"' : ''}} >{{$key->uom_name}}</option>
                                                @endforeach
                                                                                            </select>
                                                {!! $errors->first('job_qty_unit', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->
                                        </div><!--end of row-->

                                        <div class="row">
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.job size')}}<sup>*</sup></label>
                                            <input type="text" class="form-control input-css" value="{{$d->job_size}}" name="job_size" >
                                            {!! $errors->first('job_size', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->
                                        <div class="col-md-4">
                                                <label>{{__('internal_order.dimension')}}<sup>*</sup></label>
                                                <select   value="{{ old('dimension') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="dimension">
                                                        <option value="default">Select Dimension</option>
                                                        <option value="m" {{ $d->dimension=="m" ? 'selected="selected"' : ''}}>Metre</option>
                                                        <option value="mm"{{ $d->dimension=="mm" ? 'selected="selected"' : ''}}>Millimeter</option>
                                                        <option value="cm"{{ $d->dimension=="cm" ? 'selected="selected"' : ''}}>Centimeter</option>
                                                        <option value="km"{{ $d->dimension=="km" ? 'selected="selected"' : ''}}>Kilometer</option>
                                                        <option value="in"{{ $d->dimension=="in" ? 'selected="selected"' : ''}}>Inch</option>
                                                        <option value="ft"{{ $d->dimension=="ft" ? 'selected="selected"' : ''}}>Foot</option>
                                                        <option value="ton"{{ $d->dimension=="ton" ? 'selected="selected"' : ''}}>Ton</option>
                                                        <option value="doz"{{ $d->dimension=="doz" ? 'selected="selected"' : ''}}>Dozen</option>
                                                        <option value="kg"{{ $d->dimension=="kg" ? 'selected="selected"' : ''}}>Kilogram</option>
                                                        <option value="g"{{ $d->dimension=="g" ? 'selected="selected"' : ''}}>Grams</option>
                                                </select>
                                            {!! $errors->first('dimension', '<p class="help-block">:message</p>') !!}
                                           </div>
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.job rate')}}<sup>*</sup></label>
                                            <input type="number" step="any" class="form-control input-css" value="{{$d->rate_per_qty}}" name="job_rate">
                                            {!! $errors->first('job_rate', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->

                                  
                                    </div><!--end of row-->
                                   
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.details')}} <sup>*</sup></label><br>
                                            <input type="text" class="form-control input-css" value="{{$d->details}}" name="details" name="job_details">
                                            {!! $errors->first('job_details', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-3-->

                                        <div class="col-md-4">
                                            <label>{{__('internal_order.front color')}}<sup>*</sup></label>
                                            <select class="form-control select2"  data-placeholder="" style="width: 100%;" name="front_color">
                                                    <option value="">Select Front Color</option>
                                                    <option value="0" {{ $d->front_color==0 ? 'selected="selected"' : ''}}>0</option>
                                                    <option value="1" {{ $d->front_color==1 ? 'selected="selected"' : ''}}>1</option>
                                                    <option value="2" {{ $d->front_color==2 ? 'selected="selected"' : ''}}>2</option>
                                                    <option value="3" {{ $d->front_color==3 ? 'selected="selected"' : ''}}>3</option>
                                                    <option value="4" {{ $d->front_color==4 ? 'selected="selected"' : ''}}>4</option>
                                                    <option value="5" {{ $d->front_color==5 ? 'selected="selected"' : ''}}>5</option>
                                                    <option value="6" {{ $d->front_color==6 ? 'selected="selected"' : ''}}>6</option>
                                                    <option value="7" {{ $d->front_color==7 ? 'selected="selected"' : ''}}>7</option>
                                                    <option value="8" {{ $d->front_color==8 ? 'selected="selected"' : ''}}>8</option>
                                            </select>
                                            {!! $errors->first('front_color', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-3-->

                                        <div class="col-md-4">
                                            <label>{{__('internal_order.back color')}}<sup>*</sup></label>
                                            <select class="form-control select2"  data-placeholder="" style="width: 100%;" name="back_color">
                                                <option value="">Select Back Color</option>
                                                <option value="0"{{ $d->back_color==0 ? 'selected="selected"' : ''}}>0</option>
                                                <option value="1"{{ $d->back_color==1 ? 'selected="selected"' : ''}}>1</option>
                                                <option value="2"{{ $d->back_color==2 ? 'selected="selected"' : ''}}>2</option>
                                                <option value="3"{{ $d->back_color==3 ? 'selected="selected"' : ''}}>3</option>
                                                <option value="4"{{ $d->back_color==4 ? 'selected="selected"' : ''}}>4</option>
                                                <option value="5"{{ $d->back_color==5 ? 'selected="selected"' : ''}}>5</option>
                                                <option value="6"{{ $d->back_color==6 ? 'selected="selected"' : ''}}>6</option>
                                                <option value="7"{{ $d->back_color==7 ? 'selected="selected"' : ''}}>7</option>
                                                <option value="8"{{ $d->back_color==8 ? 'selected="selected"' : ''}}>8</option>
                                            </select>
                                            {!! $errors->first('back_color', '<p class="help-block">:message</p>') !!}
                                        </div><!--col-md-3-->
                                    </div><!--end of row-->
                                    <div class="row">
                                            <div class="col-md-12">
                                                    <label>{{__('internal_order.market')}}<sup>*</sup></label>
                                                    <select   value="{{ old('market') }}"   class="select2 form-control input-css" name="market" style="width:100%">
                                                            <option value="default">Select Marketing person</option>
                                                            @foreach($users as $key)
                                                            <option value="{{$key->id}}" {{ $d->marketing_user_id==$key->id ? 'selected="selected"' : ''}}>{{$key->name}}</option>
                                                            @endforeach
                                                    </select>    
                                                    {!! $errors->first('market', '<p class="help-block">:message</p>') !!}
                                                </div><!--end of col-md-4-->
                                    </div>
                                    <div class="row">
                                        <h3 class="box-title" style="font-size: 28px;">{{__('internal_order.supply by')}}</h3><br><br><br>

                                        <div class="col-md-4">
                                            <label class="paper_label_er">{{__('internal_order.paper')}}<sup></sup></label>
                                            {!! $errors->first('paper', '<p class="help-block">:message</p>') !!}
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input autocomplete="off" lc type="radio" {{ $d->is_supplied_paper=='Party' ? 'checked="checked"' : ''}} class="" value="Party" name="paper">Party</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 na">
                                                <div class="radio">
                                                    <label><input  autocomplete="off" type="radio"  class="" {{ $d->is_supplied_paper=='Press' ? 'checked="checked"' : ''}} value="Press" name="paper">Press</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 na">
                                                <div class="radio">
                                                    <label><input autocomplete="off" type="radio" class="" {{ $d->is_supplied_paper=='NA' ? 'checked="checked"' : ''}} value="NA" name="paper">NA</label>
                                                </div>
                                            </div>
                                        </div> <!--col-md-6-->

                                        <div class="col-md-6">
                                            <label  class="plates_label_er">{{__('internal_order.plates')}}<sup></sup></label>
                                            {!! $errors->first('plates', '<p class="help-block">:message</p>') !!}
                                            <div class="col-md-2 na">
                                                <div class="radio">
                                                    <label><input autocomplete="off" type="radio" class="" {{ $d->is_supplied_plate=='Party' ? 'checked=checked' : ''}} value="Party" name="plates">Party</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 na">
                                                <div class="radio">
                                                    <label><input autocomplete="off" type="radio" class="" {{ $d->is_supplied_plate=='Press' ? 'checked=checked' : ''}} value="Press" name="plates">Press</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 na">
                                                <div class="radio">
                                                    <label><input autocomplete="off" type="radio" class="" {{ $d->is_supplied_plate=='OldPlates' ? 'checked=checked' : ''}} value="OldPlates" name="plates">OldPlates</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 na">
                                                <div class="radio">
                                                    <label><input autocomplete="off" type="radio" class="" {{ $d->is_supplied_plate=='NA' ? 'checked=checked' : ''}} value="NA" name="plates">NA</label>
                                                </div>
                                            </div>
                                        </div><!--col-md-6-->
                                    </div><!--end of row-->

                                    <div class="row">
                                            <h3 class="box-title" style="font-size: 28px;">{{__('internal_order.charges')}}</h3><br><br><br>
                                        <div class="col-md-6">
                                            <label>{{__('internal_order.TC')}} <sup>*</sup></label><br>
                                            <input type="number" step="any" class="form-control input-css" value="{{$d->transportation_charge}}" name="transportaion_charges">
                                            {!! $errors->first('transportaion_charges', '<p class="help-block">:message</p>') !!}
                                        </div><!--col-md-6-->

                                        <div class="col-md-6">
                                            <label>{{__('internal_order.OC')}} <sup>*</sup></label><br>
                                            <input type="number" step="any" class="form-control input-css" value="{{$d->other_charge}}" name="other_charges" >
                                            {!! $errors->first('other_charges', '<p class="help-block">:message</p>') !!}
                                        </div> <!--col-md-6-->
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{__('internal_order.remark')}}<sup>*</sup></label><br>
                                                    <textarea class="form-control input-css" rows="3"  placeholder="Enter remarks ..."name="remark">{{$d->remarks}}</textarea>
                                                    {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}
                                                </div>
                                        </div><!--end of col-md-6-->
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="adv_received_label_er">{{__('internal_order.adv received')}}<sup>*</sup></label><br>
                                            {!! $errors->first('adv_received', '<p class="help-block">:message</p>') !!}
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input autocomplete="off" {{ $d->advanced_received==1 ? 'checked="checked"' : ''}} type="radio" class="" value="1" name="adv_received">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input autocomplete="off" {{ $d->advanced_received==0 ? 'checked="checked"' : ''}} type="radio" class="" value="0" name="adv_received">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!--end of col-md-6-->
                                    <div class="row amount_adv" {{ ($d->advanced_received==0) ? 'style=display:none' : ''}} >
                                            <h3 class="box-title" style="font-size: 28px;">{{__('internal_order.amt_details')}}</h3><br><br><br>

                                        <div class="col-md-4">
                                            <label>{{__('internal_order.amt')}}</label>
                                            <input type="number" step="any" value="{{ $d->advanced_received==1 ? $d->amount : ''}}" name="amount" class="input-css">
                                        </div>
                                        <div class="col-md-4 ">
                                            <div class="form-group">
                                                <label>{{__('internal_order.MOR')}}<sup>*</sup></label><br>
                                                {!! $errors->first('mode_received', '<p class="help-block">:message</p>') !!}
                                                <div class="col-md-2" style="width: 21.666667%;">
                                                    <div class="radio">
                                                        <label><input autocomplete="off" type="radio" class=""  value="0" {{ $d->mode_of_receive==0 ? 'checked="checked"' : ''}} name="mode_received">Cash</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 " style="width: 21.666667%;">
                                                    <div class="radio">
                                                        <label><input autocomplete="off" type="radio" class="" value="1" {{ $d->mode_of_receive==1 ? 'checked="checked"' : ''}} name="mode_received">Cheque</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" style="width: 21.666667%;">
                                                    <div class="radio">
                                                        <label><input autocomplete="off" type="radio" class="" value="2" {{ $d->mode_of_receive==2 ? 'checked="checked"' : ''}} name="mode_received">RTGS</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--end of col-md-6-->
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.ARD')}}</label>
                                        <input type="text"  name="amt_received_date" value="{{ $d->advanced_received==1 ? CustomHelpers::showDate($d->date,'m/d/y') : ''}}"  class="datepicker1 input-css">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                           
                                            {{-- <a href="/internal/list" class="btn btn-success btn_continue" id="go_to_back">Back</a> --}}
                                            <input type="submit" href="javascript:void(0)" class="btn btn-primary" value="submit"  >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!--end of container-fluid-->
                </div><!------end of box box-default---->
            </div><!--end of box-header with-border-->
            <!-- The Modal -->
    </section><!--end of section-->
@endsection
