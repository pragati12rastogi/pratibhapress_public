
@extends($layout)

@section('title', __('purchase/order.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('purchase/order.title')}}</i></a></li>
 @endsection
@section('css')

@endsection

@section('js')
<script src="/js/purchase/order.js"></script>
<script>


var message="{{Session::get('po')}}";
if(message=="successfull"){
    document.getElementById("po").click();
}
</script>
<script>
        var currentDate = new Date();
    $('.datepickers').datepicker({
        format: 'dd-mm-yyyy',
          autoclose: true,
          endDate:currentDate,
    });
    </script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                   
            </div>
            @if($errors->any())
             <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div> 
        @endif
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
        <form method="POST" action="/purchase/order/create" id="asn_form">
            @csrf
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/order.title')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                            <div class="row">
                                <div class="col-md-6 ">
                                    <label>Purchase Order Date<sup>*</sup></label>
                                    <input type="text" name="purchase_ord" id="purchase_ord" value="" class="input-css purchase_ord datepickers " placeholder="Enter Date">  
                                    {!! $errors->first('purchase_ord', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 ">
                                    <label>P.R Number<sup></sup></label>
                                    <select style="width:100%" name="indent_no" id="indent_no" class="indent_no select2 input-css">

                                        <option value="">Select P.R Number</option>
                                        @foreach($indent as $key ) 
                                            <option value="{{$key['id']}}">{{$key['indent_num']}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('indent_no', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <br><br>
                            <div class="row">
                                {{-- <div class="col-md-6 ">
                                    <label>P.O. number if old P.O.<sup>*</sup></label>
                                    <input type="text" name="po_num" id="po_num" value="" class="input-css po_num " placeholder="Enter PO Number">  
                                    {!! $errors->first('po_num', '<p class="help-block">:message</p>') !!}
                                </div> --}}
                                <div class="col-md-6 ">
                                    <label>Vendor<sup>*</sup></label>
                                    <select style="width:100%" name="vendor" id="vendor" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                        <option value="default">Select Vendor</option>
                                        @foreach($vendor as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['name']}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('vendor', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 ">
                                    <label>Payment terms<sup>*</sup></label>
                                    <select style="width:100%" name="payment_term" id="payment_term" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                        <option value="default">Select Payment Terms</option>
                                        @foreach($payment as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['value']}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('payment_term', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <br><br>
                            <div class="row">   
                                <div class="col-md-6">
                                    <label>Remark<sup></sup></label>
                                    <input type="text" name="remark" id="remark" value="" class="input-css remark " placeholder="Enter remark">  
                                    {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}
                                </div>
                                    <div class="col-md-6 ">
                                        <label>Master Categories<sup>*</sup></label>
                                        <select style="width:100%" name="master_cat" id="master_cat" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                            <option value="default">Select Master categories</option>
                                            @foreach($master_item_cat as $key => $value)
                                                <option value="{{$value['id']}}">{{$value['name']}}</option>
                                            @endforeach
                                        </select>  
                                        {!! $errors->first('master_cat', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            <br><br> 
                              
                            </div>
                </div>
            </div>        
            <div style="display:none;" id="paper_children_count">0</div>
            <div style="display:none;" id="ink_children_count">0</div>
            <div style="display:none;" id="plate_children_count">0</div>
            <div style="display:none;" id="misc_children_count">0</div>
            <div id="paper" style="display:none;">
                <div class="box-header with-border " id="div_paper" >
                    <div class='box box-default paper-form-div' >   <br>
                        <div class="container-fluid" id="div_paper_0">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Sub Categories<sup>*</sup></label>
                                            <select style="width:100%" onchange="change_item(this)" name="sub_cat_paper[]" id="sub_cat_paper_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select Sub Categories</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Item Name<sup>*</sup></label>
                                            <select style="width:100%" name="item_name_paper[]" id="item_name_paper_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select Item Name</option>
                                            </select>   
                                        </div>
                                        
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Item Quantity<sup>*</sup></label>
                                            <input type="number" min="0" step="none" name="item_qty_paper[]" value="" id="item_qty_paper_0" class="input-css item_qty">      
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Unit of Measurment<sup>*</sup></label>
                                            <select style="width:100%" name="uom_paper[]" id="uom_paper_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select UOM</option>

                                                @foreach($unit_paper as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['uom_name']}}</option>
                                                @endforeach
                                            </select>  
                                            
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Tax % Applicable<sup>*</sup></label>
                                            <select style="width:100%" name="tax_percent_paper[]" id="tax_percent_paper_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select Tax</option>

                                                @foreach($taxPercent as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['value']}}</option>
                                                @endforeach
                                            </select>   
                                            
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Delivery Date<sup>*</sup></label>
                                            <input type="text" autocomplete="off" name="delivery_date_paper[]" id="delivery_date_paper_0" value="" autocomplete="off" class="input-css datepicker delivery_date" placeholder="Select Date">
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                                <label>Item rate<sup>*</sup></label>
                                                <input type="number" min="0" step="none" name="item_rate_paper[]" value="" id="item_rate_misc_0" class="input-css item_rate">    
                                        </div>
                                        <div class="col-md-6">
                                            <label>{{__('purchase/grn.job')}}<sup></sup></label>
                                            <select type="text" name="paper_job[]"  id="paper_job_0" class="input-css select2 paper_job" style="width: 100%">
                                            <option value="">Select Job Card</option>
                                            @foreach ($jobcard as $key)
                                            <option value="{{$key->id}}">{{$key->job_number}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                        <br> 
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3" style="float:right";>    
                        <input type="button" class=" btn btn-success" value="Add More" onclick="add_more('paper')">
                </div>
            </div>
            <div id="ink" style="display:none;">
                <div  class="box-header with-border " id="div_ink" >
                    <div class='box box-default ink-form-div' >   <br>
                        <div class="container-fluid" id="div_ink_0">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Sub Categories<sup>*</sup></label>
                                            <select style="width:100%" name="sub_cat_ink[]" onchange="change_item(this)" id="sub_cat_ink_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select Sub Categories</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Item Name<sup>*</sup></label>
                                            <select style="width:100%" name="item_name_ink[]" id="item_name_ink_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select Item Name</option>
                                            </select>   
                                        </div>
                                        
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Item Quantity<sup>*</sup></label>
                                            <input type="number" min="0" step="none" name="item_qty_ink[]" value="" id="item_qty_ink_0" class="input-css item_qty">      
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Unit of Measurment<sup>*</sup></label>
                                            <select style="width:100%" name="uom_ink[]" id="uom_ink_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select UOM</option>

                                                @foreach($unit_ink as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['uom_name']}}</option>
                                                @endforeach
                                            </select>  
                                            
                                        </div>
                                        <!-- <div class="col-md-6 ">
                                            <label>Item rate<sup>*</sup></label>
                                            <input type="number" min="0" step="none" name="item_rate" value="" id="item_rate" class="input-css item_rate">    
                                        </div>  -->
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Tax % Applicable<sup>*</sup></label>
                                            <select style="width:100%" name="tax_percent_ink[]" id="tax_percent_ink_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select Tax</option>

                                                @foreach($taxPercent as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['value']}}</option>
                                                @endforeach
                                            </select>   
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Delivery Date<sup>*</sup></label>
                                            <input type="text" name="delivery_date_ink[]" id="delivery_date_ink_0" value="" class="input-css datepicker delivery_date" placeholder="Select Date">
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                                <label>Item rate<sup>*</sup></label>
                                                <input type="number" min="0" step="none" name="item_rate_ink[]" value="" id="item_rate_ink_0" class="input-css item_rate">    
                                        </div>
                                        <div class="col-md-6" style="display:none">
                                            <label>{{__('purchase/grn.job')}}<sup></sup></label>
                                            <select type="text" name="ink_job[]" id="ink_job_0" class="input-css select2 ink_job" style="width: 100%">
                                            <option value="">Select Job Card</option>
                                            @foreach ($jobcard as $key)
                                            <option value="{{$key->id}}">{{$key->job_number}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                        <br> 
                                </div>
                    </div>
                </div>
                <div class="form-group mt-3" style="float:right";>    
                        <input type="button" class=" btn btn-success" value="Add More" onclick="add_more('ink')">
                </div>
            </div>
            <div id="plate" style="display:none;">
                <div class="box-header with-border " id="div_plate" >
                    <div class='box box-default plate-form-div' >   <br>
                        <div class="container-fluid" id="div_plate_0">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Sub Categories<sup>*</sup></label>
                                            <select style="width:100%" onchange="change_item(this)" name="sub_cat_plate[]" id="sub_cat_plate_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
            
                                                <option value="default">Select Sub Categories</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Item Name<sup>*</sup></label>
                                            <select style="width:100%" name="item_name_plate[]" id="item_name_plate_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
            
                                                <option value="default">Select Item Name</option>
                                            </select>   
                                        </div>
                                        
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Item Quantity<sup>*</sup></label>
                                            <input type="number" min="0" step="none" name="item_qty_plate[]" value="" id="item_qty_plate_0" class="input-css item_qty">      
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Unit of Measurment<sup>*</sup></label>
                                            <select style="width:100%" name="uom_plate[]" id="uom_plate_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
            
                                                <option value="default">Select UOM</option>
            
                                                @foreach($unit_plate as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['uom_name']}}</option>
                                                @endforeach
                                            </select>  
                                            
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Tax % Applicable<sup>*</sup></label>
                                            <select style="width:100%" name="tax_percent_plate[]" id="tax_percent_plate_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
            
                                                <option value="default">Select Tax</option>
            
                                                @foreach($taxPercent as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['value']}}</option>
                                                @endforeach
                                            </select>   
                                            
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Delivery Date<sup>*</sup></label>
                                            <input type="text" name="delivery_date_plate[]" id="delivery_date_plate_0" value="" autocomplete="off" class="input-css datepicker delivery_date" placeholder="Select Date">
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                                <label>Item rate<sup>*</sup></label>
                                                <input type="number" min="0" step="none" name="item_rate_plate[]" value="" id="item_rate_misc_0" class="input-css item_rate">    
                                        </div>
                                        <div class="col-md-6">
                                            <label>{{__('purchase/grn.job')}}<sup></sup></label>
                                            <select type="text" name="plate_job[]" required id="plate_job_0" class="input-css select2 plate_job" style="width: 100%">
                                            <option value="">Select Job Card</option>
                                            @foreach ($jobcard as $key)
                                            <option value="{{$key->id}}">{{$key->job_number}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                        <br> 
                
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3" style="float:right";>    
                        <input type="button" class=" btn btn-success" value="Add More" onclick="add_more('plate')">
                </div>
            </div>
            <div id="misc" style="display:none;">
                <div  class="box-header with-border " id="div_misc">
                    <div class='box box-default misc-from-div' >   <br>
                        <div class="container-fluid" id="div_misc_0">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Sub Categories<sup>*</sup></label>
                                            <select style="width:100%" name="sub_cat_misc[]" onchange="change_item(this)" id="sub_cat_misc_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select Sub Categories</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Item Name<sup>*</sup></label>
                                            <select style="width:100%" name="item_name_misc[]" id="item_name_misc_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
                                                <option value="default">Select Item Name</option>
                                            </select>   
                                        </div>
                                        
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Item Quantity<sup>*</sup></label>
                                            <input type="number" min="0" step="none" name="item_qty_misc[]" value="" id="item_qty_misc_0" class="input-css item_qty">      
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Unit of Measurment<sup>*</sup></label>
                                            <select style="width:100%" name="uom_misc[]" id="uom_misc_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select UOM</option>

                                                @foreach($unit_misc as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['uom_name']}}</option>
                                                @endforeach
                                            </select>  
                                            
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label>Tax % Applicable<sup>*</sup></label>
                                            <select style="width:100%" name="tax_percent_misc[]" id="tax_percent_misc_0" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                                <option value="default">Select Tax</option>

                                                @foreach($taxPercent as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['value']}}</option>
                                                @endforeach
                                            </select>   
                                            
                                        </div>
                                        <div class="col-md-6 ">
                                            <label>Delivery Date<sup>*</sup></label>
                                            <input type="text" name="delivery_date_misc[]" id="delivery_date_misc_0" value="" autocomplete="off" class="input-css datepicker delivery_date" placeholder="Select Date">
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                                <label>Item rate<sup>*</sup></label>
                                                <input type="number" min="0" step="none" name="item_rate_misc[]" value="" id="item_rate_misc_0" class="input-css item_rate">    
                                        </div>
                                        <div class="col-md-6">
                                            <label>{{__('purchase/grn.job')}}<sup></sup></label>
                                            <select type="text" name="misc_job[]" id="misc_job_0" class="input-css select2 misc_job" style="width: 100%">
                                            <option value="">Select Job Card</option>
                                            @foreach ($jobcard as $key)
                                            <option value="{{$key->id}}">{{$key->job_number}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                        <br> 
                                </div>
                    
                    </div>
                </div> 
                <div class="form-group mt-3" style="float:right";>    
                        <input type="button" class=" btn btn-success" value="Add More" onclick="add_more('misc')">
                </div>
            </div>
            <div class="form-group">    
                <input type="submit" class=" btn btn-success">
            </div>     
        </form>   
    </section><!--end of section-->
@endsection


