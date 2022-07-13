<?php //print($client);die();?>
@extends($layout)

@section('title', __('waybill.title'))

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> {{__('waybill.title')}}</a></li>
@endsection

@section('css')
<style>
    .row{
    margin-top:20px;
  }
  .error{
   color:red;
   font-style: oblique;
  }
</style>
@endsection

@section('js')
    <script src="/js/views/waybill.js"></script>
    <script>
       var currentDate="{{CustomHelpers::showDate($date,'d-m-Y')}}";
       console.log(currentDate);
       
        //  $('.datepicker1').datepicker('destroy');
        //         $('.datepicker1').datepicker({
        //             format: 'dd-mm-yyyy',
        //               autoclose: true,
        //             //   startDate:currentDate,
        //     });
   
     $('input[type=radio][name=list_available]').change(function() {
        if (this.value == "Sale"){
            $('#sale').show();
            $('#challan').hide();
        }
      
       
        if (this.value == "Challan")
       {
        $('#challan').show();
            $('#sale').hide();
       }
    });
    $('#party').change(function(e) {
          var party = $(e.target).val();
        challan(party);
        });

        $('#party1').change(function(e) {
          var party = $(e.target).val();
          tax(party);
        });
function challan(party){
    $('#ajax_loader_div').css('display','block');

    $.ajax({
              url: "/party/challan/" + party,
              type: "GET",
              success: function(result) {
                 
                  $('#challan_num').empty();
                  $('#challan_num1').empty();

                 if(result!=''){
                    $("#challan_num").empty();
                    $("#challan_num1").empty();
                    for (var i = 0; i < result.length; i++) {
                    $('#challan_num').append('<option value="'+result[i].id+'">'+result[i].challan_number+'</option>');
                    $('#challan_num1').append('<option value="'+result[i].id+'">'+result[i].total_amount+'</option>');
                  }
                 }
                 else{
                     $("#challan_num1").append('<option value="">No Challan</option>')
                 }
                 $('#challan_num').val([{{implode(',',$challan_id)}}]).trigger('change');
                 $('#challan_num1').val([{{implode(',',$challan_id)}}]).trigger('change');
                 $('#ajax_loader_div').css('display','none');

            }
                  
        });

}
  function tax(party){
    $('#ajax_loader_div').css('display','block');

    $.ajax({
              url: "/party/tax/" + party,
              type: "GET",
              success: function(result) {
                 
                  $('#tax').empty();
                  $('#tax_number1').empty();
                 if(result!=''){
                    $("#tax").empty();
                    $('#tax_number1').empty();
                    for (var i = 0; i < result.length; i++) {
                    $('#tax').append('<option value="'+result[i].id+'">'+result[i].invoice_number+'</option>')
                    $('#tax_number1').append('<option value="'+result[i].id+'">'+result[i].total_amount+'</option>')
                  }
                   
                 }
                 else{
                     $("#tax").append('<option value="">No Tax Invoice</option>')
                 }
                 $('#tax').val([{{implode(',',$challan_id)}}]).trigger('change');
                 $('#tax_number1').val([{{implode(',',$challan_id)}}]).trigger('change');
                 $('#ajax_loader_div').css('display','none');

            }
                  
        });
  }  
 
$(document).ready(function(){

    var text="{{$text}}";
if(text=="Sale"){
    var client="{{$client}}";
    console.log(client);
    
    tax(client);
    $('.tax_date').val("{{$date}}");
    $('.tax_amount').val("{{str_replace("+",".",$amount)}}");
    //$('#tax').val([{{implode(',',$challan_id)}}]).trigger('change');
}
if(text=="Challan"){
    var client="{{$client}}";
    challan(client);
    $('.challan_date').val("{{$date}}");
    $('.challan_amount').val("{{str_replace("+",".",$amount)}}");
    //$('#tax').val([{{implode(',',$challan_id)}}]).trigger('change');
}
});
$('#challan_num').on("select2:unselect", function(e){
    var selText1=0;
        var value= e.params.data.id;
        $('#challan_num1 option[value="'+value+'"]').prop('selected',false);
        $('#challan_num1').trigger('change.select2');
        $("#challan_num1 option:selected").each(function () {
            var $this = $(this);
                selText1 = parseFloat(selText1) + parseFloat($this.text());
                
        });
        $('.challan_amount').val(selText1);
        
});
$('#challan_num').on("select2:select", function(e){
    var selText=0;
        var value= e.params.data.id;
        $('#challan_num1 option[value="'+value+'"]').prop('selected',true);
        $('#challan_num1').trigger('change.select2');
        $("#challan_num1 option:selected").each(function () {
            //challan_list[i]=$('#challan_num1').val(challan_select[i]).text();
            var $this = $(this);
                selText = parseFloat(selText) + parseFloat($this.text());
                
        });
        $('.challan_amount').val(selText);
        
});
//tax invoice
$('#tax').on("select2:unselect", function(e){
    var selText2=0;
        var value= e.params.data.id;
        $('#tax_number1 option[value="'+value+'"]').prop('selected',false);
        $('#tax_number1').trigger('change.select2');
        $("#tax_number1 option:selected").each(function () {
            var $this = $(this);
                selText2 = parseFloat(selText2) + parseFloat($this.text());
                
        });
        $('.tax_amount').val(selText2);
        
});
$('#tax').on("select2:select", function(e){
    var selText3=0;
        var value= e.params.data.id;
        $('#tax_number1 option[value="'+value+'"]').prop('selected',true);
        $('#tax_number1').trigger('change.select2');
        $("#tax_number1 option:selected").each(function () {
            //challan_list[i]=$('#challan_num1').val(challan_select[i]).text();
            var $this = $(this);
                selText3 = parseFloat(selText3) + parseFloat($this.text());
                
        });
        $('.tax_amount').val(selText3);
        
});
$("#challan_num1").select2({
    containerCssClass : "show-hide"
});
$(".show-hide").parent().parent().hide();

$("#tax_number1").select2({
    containerCssClass : "show-hide1"
});
$(".show-hide1").parent().parent().hide();
    </script>
@endsection

@section('main_section')
<section class="content">
        <!-- Main content -->
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            </div>
          @if(in_array(1, Request::get('userAlloweds')['section']))
      {{-- <p>Hello</p> --}}
      @endif
      
        <form method="post" action="/waybill/createDb/{{$delivery_id}}/{{$text}}/{{$client}}" id="form">
       @csrf 
       <div class="box box-default">
            <div class="box-header with-border">
            
               
                <div class="box-body">
                    <div class="row">
                     
                            <div class="form-group">      
                                    <label>{{__('waybill.waybill')}}<sup>*</sup></label>
                                <div class="col-md-12 list_available_label_er">
                                    <div class="col-sm-4">		
                                        <div class="radio">
                                            <label style="font-style:bold">
                                                <input name="list_available"  autocomplete="off" {{$text=="Sale" ? 'checked="checked"':''}} type="radio" value="Sale"  id="list_available" > Tax Invoice </label>
                                               </div>
                                    </div>
                                    {{-- <div class="col-sm-4">	
                                            <div class="radio">
                                                <label style="font-style:bold">
                                                    <input name="list_available" autocomplete="off" value="Purchase" type="radio"> Purchase </label>
                                                   </div>
                                    </div> --}}
                                    <div class="col-sm-4">	
                                        <div class="radio">
                                            <label style="font-style:bold">
                                                <input name="list_available" {{$text=="Challan" ? 'checked="checked"':''}} autocomplete="off" value="Challan" type="radio"> Delivery Challan </label>
                                               </div>
                                </div>
                                    {!! $errors->first('list_available', '<p class="help-block">:message</p>') !!}
                                  
                                </div>
                                
                        </div>
                    </div> 
                </div>                               
            </div>
    </div>
      
      <div id="sale" {{$text=="Sale" ? 'style=display:block':'style=display:none'}} >
            <div class="box box-default">
                    <div class="box-header with-border">
                        <h3>{{__('waybill.sale')}}</h3>
                    <div class="box-body"> 
                    <div class="container-fluid">
                          <div class="row">                     
                            <div class="col-md-6">
                                @php
                                    $party_session=Session::get('party');
                                @endphp
                                <label>{{__('waybill.party')}}<sup>*</sup></label>
                                <input value="{{$pointer==0 ? $refer['referencename'].'-'.$client : $client}}" value="{{ old('party_name') }}"  disabled class="form-control input-css party" id="party" data-placeholder="" style="width: 100%;" name="">
                                <div {{$pointer==0 ? 'style=display:block' : 'style=display:none'}}>
                                        <p class="help-block" style="color:green">This is a random GST for Reference Name : {{$refer['referencename']}}</p>
                                </div>  
                                <input type="hidden" name="tax_party_name" value={{$client}}>
                                {!! $errors->first('party_name', '<p class="help-block">:message</p>') !!}
                            </div><!--col-md-4-->
                                              
                          <div class="col-md-6">
                              <label>{{__('waybill.number')}}<sup>*</sup></label>
                              <select  class="form-control select2 tax1" multiple  id="tax" style="width: 100%;" name="number[]">
                                <option value="default" disabled>Select GSTIN First</option>
                                </select>
                              {!! $errors->first('number', '<p class="help-block">:message</p>') !!}
                          </div><!--end of col-md-4-->
                          </div><!--end of div row-->      
                          <select  class="form-control select2 tax_number1" multiple  id="tax_number1" style="width: 100%;" name="hid1[]">
                                <option value="default" disabled>Select GSTIN First</option>
                                </select> 
                          <div class="row">             
                            <div class="col-md-6">
                                <label>{{__('waybill.date')}}<sup>*</sup></label>
                            <input type="text" value="{{CustomHelpers::showDate($date,'d-m-Y')}}" disabled class="form-control tax_date input-css datepicker1" name="">
                                <input type="hidden" class="form-control tax_date input-css datepicker1" name="tax_date">
                                {!! $errors->first('tax_date', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('waybill.amount')}}<sup>*</sup></label>
                            <input type="number" step="any" class="form-control tax_amount input-css" name="tax_amount">
                            {!! $errors->first('tax_amount', '<p class="help-block">:message</p>') !!}
                    </div>
                          </div><!--end of div row--> 
                          <div class="row">             
                            <div class="col-md-6">
                                <label>{{__('waybill.waybill_date')}}<sup>*</sup></label>
                                <input autocomplete="off" type="text" class="form-control waybill_date input-css datepicker1" name="waybill_date">
                                {!! $errors->first('waybill_date', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('waybill.waybill_number')}}<sup>*</sup></label>
                            <input type="text"  class="form-control waybill_number input-css" name="waybill_number">
                            {!! $errors->first('waybill_number', '<p class="help-block">:message</p>') !!}
                    </div>
                          </div><!--end of div row--> 
                          </div><!--end of container-fluid-->
                          </div><!--end of box box-body-->
                          </div><!--end of box-header with-border-->
                          </div><!--end of box box-default--> 
                       <div class="form-group">  
                       </div>  
      </div>
      <div id="challan" {{$text=="Challan" ? 'style=display:block':'style=display:none'}}>
        <div class="box box-default">
                <div class="box-header with-border">
                    <h3>{{__('waybill.challan')}}</h3>
                <div class="box-body"> 
                <div class="container-fluid">
                      <div class="row">                     
                        <div class="col-md-6">
                            <label>{{__('waybill.party')}}<sup>*</sup></label>
                        <input  value="{{$pointer==0 ? $refer['referencename'].'-'.$client : $client}}"    value="{{ old('party_name') }}"  disabled class="form-control input-css party" id="party1" data-placeholder="" style="width: 100%;" name="">
                        <div {{$pointer==0 ? 'style=display:block' : 'style=display:none'}}>
                                <p class="help-block">This is a random GST for Reference Name : {{$refer['referencename']}}</p>
                        </div>    
                        <input type="hidden" name="challan_party_name" value={{$client}}>
                            {!! $errors->first('party_name', '<p class="help-block">:message</p>') !!}
                        </div><!--col-md-4-->
                                          
                      <div class="col-md-6">
                          <label>{{__('waybill.number1')}}<sup>*</sup></label>
                          <select  class="form-control select2 challan" id="challan_num" multiple style="width: 100%;" name="number[]">
                            <option value="default" disabled>Select GSTIN First</option>
                            </select>
                            <label id="challan_num-error" class="error"></label>
                          {!! $errors->first('number', '<p class="help-block">:message</p>') !!}
                      </div><!--end of col-md-4-->
                      </div><!--end of div row-->
                      <div class="row" style="dispaly:none">
                            <select class="select2 challan1" id="challan_num1" multiple style="width: 100%;"  name="hid[]" hidden>
                                    <option value="default" disabled>Select GSTIN First</option>
                                    </select>        
                    </div>             

                    <div class="row">             
                        <div class="col-md-6">
                            <label>{{__('waybill.date1')}}<sup>*</sup></label>
                        <input type="text" value="{{$date}}" disabled  class="form-control input-css datepicker1 challan_date" name="">
                            <input type="hidden" class="form-control input-css datepicker1 challan_date" name="challan_date">
                            {!! $errors->first('challan_date', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="col-md-6">
                        <label>{{__('waybill.amount1')}}<sup>*</sup></label>
                        <input type="number" step="any"  class="form-control input-css challan_amount" name="challan_amount">
                        {!! $errors->first('challan_amount', '<p class="help-block">:message</p>') !!}
                </div>
                      </div><!--end of div row--> 
                      <div class="row">             
                        <div class="col-md-6">
                            <label>{{__('waybill.waybill_date')}}<sup>*</sup></label>
                            <input autocomplete="off" type="text" class="form-control waybill_date1 input-css datepicker1" name="waybill_date1">
                            {!! $errors->first('waybill_date', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="col-md-6">
                        <label>{{__('waybill.waybill_number')}}<sup>*</sup></label>
                        <input type="text"  class="form-control waybill_number1 input-css" name="waybill_number1">
                        {!! $errors->first('waybill_number', '<p class="help-block">:message</p>') !!}
                </div>
                      </div><!--end of div row--> 
                      </div><!--end of container-fluid-->
                      </div><!--end of box box-body-->
                      </div><!--end of box-header with-border-->
                      </div><!--end of box box-default--> 
                   <div class="form-group">  
                   </div>  
  </div>



        


<div class="form-group">
       
         <button type="submit" class="btn btn-primary">Submit</button>  
            
    </div>
 
</div>
 
</div><!--end of box box-default-->

</form>
</section>
@endsection
