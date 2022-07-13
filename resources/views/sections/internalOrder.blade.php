
@extends($layout)
@section('title', __('internal_order.mytitle'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('internal_order.mytitle')}}</i></a></li>

@endsection

@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="css/party.css">
  <style>
      .help-block{
          color:red;
      }
  </style>
@endsection

@section('js')
<!-- jquery-validater -->
<!-- end of jquery-validater -->
<script src="js/views/create-order.js"></script>
<script>
var message="{{Session::get('internal')}}";
if(message=="successfull"){
    document.getElementById("popup_message1").click();
}
$(".old_dc").keydown(function(e) {
    var oldvalue=$(this).val();
    var field=this;
    var set="{{$settings.'/'}}";
    setTimeout(function () {
        if(field.value.indexOf(set) !== 0) {
            $(field).val(oldvalue);
        } 
    }, 1);
});
// $('.job_date').change(function(e){ 
//     var date = new Date($('.job_date').val().split('-').reverse().join('-'));
//     var dd = date.getDate();
//     var mm = date.getMonth() + 1;
//     var yy = date.getFullYear();
//     var day = dd + "-" + mm + "-" + yy;
//     var y= ('' + date.getFullYear()).substr(2);

//     if(mm >= 4){
//         var financial_year=y+'-'+(parseInt(y)+parseInt(1));
//     }
//     else{
//         var financial_year=(parseInt(y)-parseInt(1))+'-'+(y);
//     }
//     alert(financial_year);
//     var x='^'+'{{$settings}}'+'/'+financial_year+'/';
//     var val=$('input[type=radio][class=challan_number_status]:checked').val();
//     if(val=="Old"){
//         $(".old_io").validate({
//   rules: {
//     contact: {
//       required: true,
//      regex:x
//     }
//   }
// });
//     }
//     alert(val);
// });
</script>
@endsection



@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            </div>        <!-- Default box -->

            @if(in_array(1, Request::get('userAlloweds')['section']))
            
            <div class="box-header with-border internalorder">
                <div class='box box-default'>  <br>
                    <div class="container-fluid">
                        <form action="/internal/insert" method="POST" id="form" class=" {{ $errors->has('name') ? 'has-error' : ''}}">
                            @csrf
                            <div class="row">
                                <div class="form_step1">
                                    <h3 class="box-title" style="font-size: 28px;">{{__('internal_order.IO')}}</h3><br><br><br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>{{__('internal_order.ref_name')}}<sup>*</sup></label>
                                            <select   value="{{ old('reference_name') }}"   class="form-control select2 party"  data-placeholder="" style="width: 100%;" name="reference_name">
                                            <option value="default">Select Reference Name</option>
                                                @foreach($party as $key)
                                                    <option value="{{$key->id}}" {{ old('reference_name') == $key->id ? 'selected="selected"' : ''}} >{{$key->referencename}}</option>
                                                @endforeach
                                            </select>

                                            {!! $errors->first('party_name', '<p class="help-block">:message</p>') !!}
                                        </div><!--col-md-4-->
                                        
                                        <div class="col-md-6">
                                            <label class="item_label_er">{{__('internal_order.item')}}<sup>*</sup></label>
                                            {!! $errors->first('item', '<p class="help-block">:message</p>') !!}

                                            <select  name="item"  class="form-control input-css item_id item select2">
                                                    <option value="default">Select Item</option>
                                                    @foreach ($item as $key)
                                                <option value="{{$key->id}}" {{ old('item') == $key->id ? 'selected="selected"' : ''}}>{{$key->name}}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" placeholder="Item Name" class="form-control input-css other_name" name="other_item_name" />
                                        </div><!--col-md-4-->
                                    </div><!--row-->
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            {{-- <a href="#" class="btn btn-primary btn_continue" id="form_btn1">Continue</a> --}}
                                        </div>
                                    </div>
                                    <div class="row">
                               
                               <div class="col-md-4">
                                     <label>Is Internal Order New Or Old ?<sup>*</sup></label>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input autocomplete="off" type="radio"   checked class="challan_number_status" value="New" name="io_number_status">New</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input     autocomplete="off" type="radio"  class="challan_number_status" value="Old" name="io_number_status">Old</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 old_delivery" style="display:none">
                                    <div class="col-md-4" >
                                    <label>Old IO Number<sup>*</sup></label>
                                            <input type="text" name="old_io" id="" class="input-css old_dc" value="{{$settings.'/'}}" required placeholder="Enter Internal Order Number">
                                    </div>
                                 
                                </div>
                           
                           </div>
                          
                          
                       </div>
                                </div>

                                {{-------------------------------  form 2 starts -------------------------------}}

                                <div class="form_step2" >
                                    <h3 class="box-title" style="font-size: 28px;">{{__('internal_order.job')}}</h3><br><br><br>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.type')}}<sup>*</sup></label>
                                            <select      class="form-control select2"  data-placeholder=""style="width: 100%;" name="io_type">
                                            <option value="default">Select I.O Type</option>
                                            @foreach($io_type as $key)
                                            <option value="{{$key->id}}" {{ old('io_type') == $key->id ? 'selected="selected"' : ''}}>{{$key->name}}</option>
                                            @endforeach
                                            </select>
                                            {!! $errors->first('io_type', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-3-->
                                        <div class="col-md-4"><!--job date-->
                                            <label>{{__('internal_order.date')}}<sup>*</sup></label>
                                            <input   value="{{ old('job_date') }}"   type="text" class="form-control datepicker1 input-css job_date" id="datepicker" name="job_date">
                                            {!! $errors->first('job_date', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->
                                        <!--job date end-->
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.HSN')}} <sup>*</sup></label>
                                            <select   value="{{ old('hsn') }}"   class="form-control select2 hsn"  data-placeholder=""style="width: 100%;" name="hsn">
                                                    <option value="default" {{ old('hsn') == $key->id ? 'selected="selected"' : ''}}>Select HSN</option>
                                                    @foreach($hsn as $key)
                                                    <option value="{{$key->id}}">{{$key->name." - ".$key->hsn."-".$key->gst_rate."%"}}</option>
                                                    @endforeach
                                                    </select>
                                            {!! $errors->first('hsn', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->
                                    </div><!--end of row-->

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.delivery date')}}<sup>*</sup></label>
                                            <input   value="{{ old('delivery_date') }}"   type="text" class="form-control datepicker1 input-css" id="datepicker1" name="delivery_date">
                                            {!! $errors->first('delivery_date', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-3-->

                                        <div class="col-md-4">
                                            <label>{{__('internal_order.qty')}}<sup>*</sup></label>
                                            <input   value="{{ old('job_qty') }}"   type="number" class="form-control input-css" name="job_qty" >
                                            {!! $errors->first('job_qty', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->

                                        <div class="col-md-4">
                                            <label>{{__('internal_order.qty1')}}<sup>*</sup></label>
                                            <select   value="{{ old('job_qty_unit') }}"   class="select2 form-control input-css" name="job_qty_unit" style="width:100%">
                                                    <option value="default">Select Unit of Measurement</option>
                                                    @foreach($uom as $key)
                                                    <option value="{{$key->id}}" {{ old('job_qty_unit') == $key->id ? 'selected="selected"' : ''}}>{{$key->uom_name}}</option>
                                                    @endforeach
                                            </select>    
                                            {!! $errors->first('job_qty_unit', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->
                                        </div><!--end of row-->

                                        <div class="row">
                                        <div class="col-md-4">
                                          
                                                <label>{{__('internal_order.job size')}}<sup>*</sup></label>
                                            <input   value="{{ old('job_size') }}"   type="text" class="form-control input-css" name="job_size" >
                                            {!! $errors->first('job_size', '<p class="help-block">:message</p>') !!}
                                           
                                        </div><!--end of col-md-4-->
                                        <div class="col-md-4">
                                                <label>{{__('internal_order.dimension')}}<sup>*</sup></label>
                                                <select   value="{{ old('dimension') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="dimension">
                                                        <option value="default">Select Dimension</option>
                                                        <option value="m" {{ old('dimension') =='m' ? 'selected="selected"' : ''}}>Metre</option>
                                                        <option value="mm" {{ old('dimension') =='mm' ? 'selected="selected"' : ''}}>Millimeter</option>
                                                        <option value="cm" {{ old('dimension') =='cm' ? 'selected="selected"' : ''}}>Centimeter</option>
                                                        <option value="km" {{ old('dimension') =='km' ? 'selected="selected"' : ''}}>Kilometer</option>
                                                        <option value="in" {{ old('dimension') =='in' ? 'selected="selected"' : ''}}>Inch</option>
                                                        <option value="ft" {{ old('dimension') =='ft' ? 'selected="selected"' : ''}}>Foot</option>
                                                        <option value="ton" {{ old('dimension') =='ton' ? 'selected="selected"' : ''}}>Ton</option>
                                                        <option value="doz" {{ old('dimension') =='doz' ? 'selected="selected"' : ''}}>Dozen</option>
                                                        <option value="kg" {{ old('dimension') =='kg' ? 'selected="selected"' : ''}}>Kilogram</option>
                                                        <option value="g" {{ old('dimension') =='g' ? 'selected="selected"' : ''}}>Grams</option>
                                                </select>
                                            {!! $errors->first('dimension', '<p class="help-block">:message</p>') !!}
                                           </div>
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.job rate')}}<sup>*</sup></label>
                                            <input step="any"  value="{{ old('job_rate') }}"   type="number" class="form-control input-css" name="job_rate">
                                            {!! $errors->first('job_rate', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-4-->

                                   
                                        
                                    </div><!--end of row-->

                                    <div class="row">
                                            <div class="col-md-4">
                                                    <label>{{__('internal_order.details')}} <sup>*</sup></label><br>
                                                    <input   value="{{ old('details') }}"   type="text" class="form-control input-css" name="details" name="job_details">
                                                    {!! $errors->first('job_details', '<p class="help-block">:message</p>') !!}
                                                </div><!--end of col-md-3-->
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.front color')}}<sup>*</sup></label>
                                            <select   value="{{ old('front_color') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="front_color">
                                                    <option value="default">Select Front Color</option>
                                                    <option value="0" {{ old('front_color') =='0' ? 'selected="selected"' : ''}}>0</option>
                                                    <option value="1" {{ old('front_color') =='1' ? 'selected="selected"' : ''}}>1</option>
                                                    <option value="2" {{ old('front_color') =='2' ? 'selected="selected"' : ''}}>2</option>
                                                    <option value="3" {{ old('front_color') =='3' ? 'selected="selected"' : ''}}>3</option>
                                                    <option value="4" {{ old('front_color') =='4' ? 'selected="selected"' : ''}}>4</option>
                                                    <option value="5" {{ old('front_color') =='5' ? 'selected="selected"' : ''}}>5</option>
                                                    <option value="6" {{ old('front_color') =='6' ? 'selected="selected"' : ''}}>6</option>
                                                    <option value="7" {{ old('front_color') =='7' ? 'selected="selected"' : ''}}>7</option>
                                                    <option value="8" {{ old('front_color') =='8' ? 'selected="selected"' : ''}}>8</option>
                                            </select>
                                            {!! $errors->first('front_color', '<p class="help-block">:message</p>') !!}
                                        </div><!--end of col-md-3-->

                                        <div class="col-md-4">
                                            <label>{{__('internal_order.back color')}}<sup>*</sup></label>
                                            <select   value="{{ old('back_color') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="back_color">
                                                <option value="default">Select Back Color</option>
                                                <option value="0" {{ old('back_color') =='0' ? 'selected="selected"' : ''}}>0</option>
                                                    <option value="1" {{ old('back_color') =='1' ? 'selected="selected"' : ''}}>1</option>
                                                    <option value="2" {{ old('back_color') =='2' ? 'selected="selected"' : ''}}>2</option>
                                                    <option value="3" {{ old('back_color') =='3' ? 'selected="selected"' : ''}}>3</option>
                                                    <option value="4" {{ old('back_color') =='4' ? 'selected="selected"' : ''}}>4</option>
                                                    <option value="5" {{ old('back_color') =='5' ? 'selected="selected"' : ''}}>5</option>
                                                    <option value="6" {{ old('back_color') =='6' ? 'selected="selected"' : ''}}>6</option>
                                                    <option value="7" {{ old('back_color') =='7' ? 'selected="selected"' : ''}}>7</option>
                                                    <option value="8" {{ old('back_color') =='8' ? 'selected="selected"' : ''}}>8</option>
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
                                                            <option value="{{$key->id}}" {{ old('market') == $key->id ? 'selected="selected"' : ''}}>{{$key->name}}</option>
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
                                                    <label><input     autocomplete="off" {{ old('paper') == 'Party' ? 'checked="checked"' : ''}} type="radio" class="" value="Party" name="paper">Party</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 na">
                                                <div class="radio">
                                                    <label><input     autocomplete="off" {{ old('paper') == 'Press' ? 'checked="checked"' : ''}} type="radio" class="" value="Press" name="paper">Press</label>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="col-md-2 na">
                                                <div class="radio">
                                                    <label><input    autocomplete="off" {{ old('paper') == 'NA' ? 'checked="checked"' : ''}} type="radio" class="na" value="NA" name="paper">NA</label>
                                                </div>
                                            </div>
                                        </div> <!--col-md-6-->

                                        <div class="col-md-6">
                                            <label  class="plates_label_er">{{__('internal_order.plates')}}<sup></sup></label>
                                            {!! $errors->first('plates', '<p class="help-block">:message</p>') !!}
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input    autocomplete="off" type="radio" {{ old('plates') == 'Party' ? 'checked="checked"' : ''}}class="" value="Party" name="plates">Party</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input autocomplete="off" type="radio"{{ old('plates') == 'Press' ? 'checked="checked"' : ''}} class="" value="Press" name="plates">Press</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input  autocomplete="off" type="radio"{{ old('plates') == 'OldPlates' ? 'checked="checked"' : ''}} class="" value="OldPlates" name="plates">OldPlates</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 na">
                                                <div class="radio">
                                                    <label><input    autocomplete="off" type="radio" class="" {{ old('plates') == 'NA' ? 'checked="checked"' : ''}} value="NA" name="plates">NA</label>
                                                </div>
                                            </div>
                                        </div><!--col-md-6-->
                                    </div><!--end of row-->

                                    <div class="row">
                                            <h3 class="box-title" style="font-size: 28px;">{{__('internal_order.charges')}}</h3><br><br><br>
                                        
                                        <div class="col-md-6">
                                            <label>{{__('internal_order.TC')}} <sup>*</sup></label><br>
                                            <input   value="{{ old('transportaion_charges') }}" min="0"  type="number" step="any" class="form-control input-css" name="transportaion_charges">
                                            {!! $errors->first('transportaion_charges', '<p class="help-block">:message</p>') !!}
                                        </div><!--col-md-6-->

                                        <div class="col-md-6">
                                            <label>{{__('internal_order.OC')}} <sup>*</sup></label><br>
                                            <input   value="{{ old('other_charges') }}" min="0"  type="number" step="any" class="form-control input-css" name="other_charges" >
                                            {!! $errors->first('other_charges', '<p class="help-block">:message</p>') !!}
                                        </div> <!--col-md-6-->
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{__('internal_order.remark')}}<sup>*</sup></label><br>
                                                    <textarea value="{{ old('remark') }}"  class="form-control input-css" rows="3" placeholder="Enter remarks ..."name="remark"></textarea>
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
                                                    <label><input    autocomplete="off" type="radio" class="" value="1" name="adv_received">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input  autocomplete="off" type="radio" class="" value="0" name="adv_received">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!--end of col-md-6-->
                                    <div class="row amount_adv" style="display:none">
                                            <h3 class="box-title" style="font-size: 28px;">{{__('internal_order.amt_details')}}</h3><br><br><br>

                                        <div class="col-md-4">
                                            <label>{{__('internal_order.amt')}}</label>
                                            <input     type="number" step="any" name="amount" class="input-css">
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{__('internal_order.MOR')}}<sup>*</sup></label><br>
                                                {!! $errors->first('mode_received', '<p class="help-block">:message</p>') !!}
                                                <div class="col-md-2" style="width: 21.666667%;">
                                                    <div class="radio">
                                                        <label><input     autocomplete="off" type="radio" class="" value="0" name="mode_received">Cash</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" style="width: 21.666667%;">
                                                    <div class="radio">
                                                        <label><input  autocomplete="off" type="radio" class="" value="1" name="mode_received">Cheque</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" style="width: 21.666667%;">
                                                    <div class="radio">
                                                        <label><input  autocomplete="off" type="radio" class="" value="2" name="mode_received">RTGS</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--end of col-md-6-->
                                        <div class="col-md-4">
                                            <label>{{__('internal_order.ARD')}}</label>
                                            <input   type="text" name="amt_received_date" class="datepicker input-css">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            {{-- <a href="#" class="btn btn-success btn_continue" id="go_to_back">Back</a> --}}
                                            <input   type="submit" href="javascript:void(0)" class="btn btn-primary" value="submit">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div><!--end of container-fluid-->
                </div><!------end of box box-default---->
            </div><!--end of box-header with-border-->
            @endif
            <!-- The Modal -->
    </section><!--end of section-->
@endsection