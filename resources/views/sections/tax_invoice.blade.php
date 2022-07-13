@extends($layout)

@section('title', __('taxinvoice.title'))

{{-- TODO: fetch from auth --}}
@section('user',Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Tax Invoice</a></li>
@endsection
@section('css')
{{-- for All Css --}}
{{-- <link rel="stylesheet" href="css/all.css"> --}}
<style>
#delivery{
    border:none;
    border-bottom: 2px solid #D1C4E9;
    width: 100%;
}
</style>
@endsection

@section('js')
<script src="/js/views/taxinvoice.js"></script>
<script>
        var currentDate = new Date();
    $('.datepickers').datepicker({
        format: 'dd-mm-yyyy',
          autoclose: true,
          endDate:currentDate,
    });
var party_ids=0;
var details;
var dc;
var val=0;
var tran=new Array();
var other=new Array();
var idss=0;
var type="ins";
$(document).ready(function() {
        $('#party').change(function(e) {
          var party = $(e.target).val();
          party_ids=$(e.target).val();
          $('#list').empty();
          $('#ajax_loader_div').css('display','block');
          $.ajax({
              url: "/party/delivery/" + party + "/"+type + "/"+idss,
              type: "GET",
              success: function(result) {
                  $('#consignee').empty();
                  $('#delivery').empty();
                  $('#ios').empty();
                  var party=result.party;
                  var ios=result.io;
                  console.log(ios);
                  
                  var consignees=result.consignee;
                //  console.log(consignees);
                 for (var i = 0; i < consignees.length; i++) {
                    $('#consignee').append('<option value="'+consignees[i].id+'">'+consignees[i].consignee_name+'</option>')
                    }
                 if(party!=''){
                    $("#list").empty();
                  for (var i = 0; i < party.length; i++) {
                   
                    $('#delivery').append('<option value="'+party[i].id+'">'+party[i].challan_number+'</option>')
                  }
                 }
                 else{
                     $("#list").append('<div class="box box-default"><h3>Delivery Challan for this Client is not available or Tax Invoice for this Client has been created..</h3><br><br><br></div>')
                 }
                 if(ios!=''){
                    $('#io_ser').css('display','block');
                    for (var i = 0; i < ios.length; i++) {
                    $('#ios').append('<option value="'+ios[i].id+'">'+ios[i].io_number+'</option>')
                  }
                 
                 }
                 if(ios==''){
                      $('#io_ser').css('display','none');
                  }
                 $('#ajax_loader_div').css('display','none');

            }
                  
        });
    });
        $('#delivery').on("select2:select", function(e){
          var id = e.params.data.id;
          console.log(id);
          if(document.getElementById(id) == null){
            $('#ajax_loader_div').css('display','block');

            $.ajax({
              url: "/delivery/" + id + "/ins",
              type: "GET",
              success: function(result) {
                  console.log(result);
                    for (var i = 0; i < result.length; i++) {
                      val++;
                      var is_po=0;
                      var io_type=0;
                      var pay="";
                        tran[val]=result[i].transportation_charge;
                        other[val]=result[i].other_charge;
                    if(result[i].io_type==5  ||  result[i].io_type==6){
                        io_type=0;
                    }
                    else{
                        io_type=1;
                    }
                        if(result[i].party_name && result[i].is_po_provided==1){
                            var party_po=result[i].party_name.split(',');
                            var pay_po=result[i].payment_po.split(',');
                            for(var k=0;k<party_po.length;k++){
                                if(party_po[k]==result[i].party_id){
                                    is_po=1;
                                    pay=pay_po[k];
                                }
                            } 
                        }
                        else if(result[i].is_po_provided==0 || result[i].io_type==9){
                           is_po=1; 
                           
                        }
                        else{
                            is_po=0; 
                            pay="";
                        }
                    
                    if(result[i].is_po_provided==0){
                    var info="PO is Verbal for this Internal Order";
                    var discount="";
                    var po_qty=""; 
                    var per="";  
                    var hsn="";  
                    var payment="";
                    var rate="";  
                    var desc="";
                  } 
                  else{
                      var info="The PO Number For this Internal Order is: " + result[i].po_number;
                      var discount="Discount % entered in PO is :  " + result[i].po_discount;
                      var po_qty = "Quantity selected in client PO is:  "  + result[i].po_qty ;
                      var per = "Unit of Measurement selected in client PO is:  "  + result[i].po_uom_name ;
                      var hsn = "HSN Code selected in client PO is:  "  + result[i].po_hsn_name +"<br>"+ "Gst Rate is "+ result[i].po_gst_rate+"%" ;
                      var payment="Payment Term in Client PO is: "+ pay ;
                      var rate="Rate per price in Client PO is : "+ result[i].per_unit_price;
                      var desc="Item Description in Client PO is : " + result[i].item_desc;
                  }
                    
                  var ls='<div class="box box-default upperBox '+result[i].id+'" id="add_'+val+'">';
                ls=ls+'<h3 style="margin-left: 23px;">Delivery Challan No. : '+result[i].challan_number+'</h3>'+
                  '<p style="font-style:italic;margin-left: 23px;">'+info+'<p>'+
                '<div class="box-body">'+
                  '<div class="row">'+
                      '<div class="col-md-4">'+
                              '<div class="form-group"> '+ 
                                      '<label>{{__('taxinvoice.io')}} <sup>*</sup></label>'+
                                      '<select   required   class="form-control select2 io_id input-css" name="internal_'+result[i].id+'[]"  id="io_id'+1+'" data-placeholder="">';
                               ls=ls + '<option value="'+result[i].io_id+'">'+result[i].ionumber+'</option>'+
                                     '</select>'+
                                     '<label id="internal_'+result[i].id+'[]-error" class="error"></label>'+
                              '</div>'+
                          '</div> '+

                         

                          '<div class="col-md-4"> '+    
                              '<label>{{__('taxinvoice.qty')}} <sup>*</sup></label>'+
                              '<input  required   type="text" min="0" name="qty_'+result[i].id+'[]" id="qty_'+result[i].id+'_'+val+'" value="'+result[i].good_qty+'" max="'+(result[i].good_qty+result[i].left_qty)+'" placeholder="max : '+(result[i].good_qty+result[i].left_qty)+'" class="form-control input-css qty qty_io_'+result[i].io+'">'+  
                              '<input type="hidden" value="'+result[i].good_qty+'" id="hidden_max_val_'+val+'">'+
                              '<p style="font-size:12px;color:green">'+po_qty+'</p>'+
                              '<label id="qty_'+result[i].id+'[]-error" class="error"></label>'+
                              '</div>'+
                          '<div class="col-md-4">'+  
                              '<label>{{__('taxinvoice.rate')}} <sup>*</sup></label>'+
                             ' <input   required  type="number" min="0" step="any" disabled id="rate'+result[i].id+val+'"  onchange="change_rate_fn(this,'+result[i].io_id+')"  value="'+result[i].rate_per_qty+'" class="form-control input-css rate rate_'+result[i].io_id+'">'+ 
                             ' <input     type="hidden" min="0" step="any" name="rate_'+result[i].id+'[]" id="rate'+result[i].id+val+'"  onchange="change_rate_fn(this,'+result[i].io_id+')"  value="'+result[i].rate_per_qty+'" class="form-control input-css rate rate_'+result[i].io_id+'">'+ 
                             '<p style="font-size:12px;color:green">'+rate+'</p>'+ 
                             '<label id="rate_'+result[i].id+'[]-error" class="error" ></label>'+
                             '</div>'+ 
                              
                      '</div><br>'+
                      '<div class="row">'+
                        '<div class="col-md-12">'+
                                  '<label>{{__('taxinvoice.goods')}} <sup>*</sup></label>'+
                                  '<textarea  required    name="goods_'+result[i].id+'[]"  id="good'+result[i].id+val+'" onchange="change_desc_fn(this,'+result[i].io_id+')" class="form-control input-css goods desc_'+result[i].io_id+'">'+result[i].good_desc+'</textarea>'+
                                  '<p style="font-size:12px;color:green">'+desc +'</p>'+
                                  '<label id="goods_'+result[i].id+'[]-error" class="error"></label>'+
                                  '</div>'+ 
                        '</div>'+
                      '<div class="row"> '+
                              '<div class="col-md-4">   '+  
                                  '<label>{{__('taxinvoice.per')}} <sup>*</sup></label>'+
                                  '<select  required name="per_'+result[i].id+'[]" id="per'+result[i].id+val+'" class="form-control select2 input-css per"> '+ 
                                        
                                        '<option value="'+result[i].io_uom_id+'">'+result[i].io_uom_name+'</option>'+
                                                    @foreach($uom as $key)
                                                    '<option value="{{$key->id}}">{{$key->uom_name}}</option>'+
                                                    @endforeach
                                    '</select>'+   
                                    '<p style="font-size:12px;color:green">'+per+'</p>'+              
                                  '<label id="per_'+result[i].id+'[]-error" class="error"></label>'+
                                  '</div>'+
                             ' <div class="col-md-4">  '+
                                  '<label>{{__('taxinvoice.discount')}} <sup>*</sup></label>'+
                                 ' <input   required  type="number" step="any" min="0" name="discount_'+result[i].id+'[]" value="'+result[i].discount+'" onchange="change_disc_fn(this,'+result[i].io_id+')" id="discount'+result[i].id+val+'" class="form-control input-css discount discount_'+result[i].io_id+'">'+
                                 '<p style="font-size:12px;color:green"> '+discount+'</p>'+  
                                 '<label id="discount_'+result[i].id+'[]-error" class="error" ></label>'+
                             '</div>'+ 
                             ' <div class="col-md-4">  '+   
                                  '<label>{{__('taxinvoice.hsn')}} <sup>*</sup></label>'+
                                      '<select  required    class="input-css select2 hsn"  id="hsn'+result[i].id+val+'" data-placeholder="" name="hsn_'+result[i].id+'[]">'+
                                              '<option value="'+result[i].io_hsn+'">'+result[i].item_id+" - "+result[i].io_hsn_name+" - "+result[i].io_gst_rate+'</option>'+
                                          @foreach($hsn as $key)
                                              '<option value="{{$key->id}}">{{$key->name." - ".$key->hsn." - ".$key->gst_rate."%"}}</option>'+
                                          @endforeach
                                      '</select>'+   
                                      '<input type="hidden" value="'+result[i].io_gst_rate+'" name="io_gst_'+result[i].id+'[]">'+
                                      '<p style="font-size:12px;color:green">'+hsn+'</p>'+ 
                                      '<label id="hsn_'+result[i].id+'[]-error" class="error"></label>'+ 
                              '</div>'+ 

                          '</div>'+
                          '<div class="row">'+
                                    '<div class="col-md-4"> '+
                                            '<label>{{__('taxinvoice.trans')}} <sup>*</sup></label>'+
                                                '<input required    min="0"    type="number" step="any" name="transportation_'+result[i].id+'[]" id="transportation" class="form-control input-css transportation" value="'+result[i].transportation_charge+'">'+
                                                '<label id="transportation_'+result[i].id+'[]-error" class="error"></label>'+ 
                                    '</div> '+
                                    '<div class="col-md-4"> '+
                                            '<label>{{__('taxinvoice.other')}} <sup>*</sup></label>'+
                                                '<input required    min="0"    type="number" step="any" name="other_'+result[i].id+'[]" id="other" class="form-control input-css other" value="'+result[i].other_charge+'">'+
                                                '<label id="other_'+result[i].id+'[]-error" class="error"></label>'+ 
                                    '</div>  '+ 
                                    '<div class="col-md-4">'+
                                            '<label>{{__('taxinvoice.payment')}} <sup>*</sup></label>'+
                                            '<select  required name="payment_'+result[i].id+'[]" id="payment'+result[i].id+val+'" class="form-control select2 input-css payment"> '+ 
                                            '<option value="'+result[i].party_payment_id+'">'+result[i].party_payment_term+'</option>'+
                                                    @foreach($payment as $key)
                                                    '<option value="{{$key->id}}">{{$key->value}}</option>'+
                                                    @endforeach
                                            '</select>'+  
                                        '<p style="font-size:12px;color:green">'+payment+'</p>'+ 
                                      '<label id="payment_'+result[i].id+'[]-error" class="error"></label>'+  
                                    '</div>'+
                                '</div>'+
              '</div>'+
      '</div>';
      $('select').select2('destroy');
        console.log(is_po);
        console.log(io_type);
        if(is_po==1 && io_type==1){
            $('#list').append(ls);
        }
        else{
            if(is_po==0)
                alert('No Client Po Exist for this Internal Order');
            if(io_type==0)
                alert('No Tax Invoice can be created for IO Type K-Sampling and FOC');
        var wanted_option = $('#delivery option[value="'+ id +'"]');
        wanted_option.prop('selected', false);
            // $('#selectTwo').trigger('change.select2');
        }
    $('.qty').change(function(e)
    {
        var class1 =  $(this).attr('class').split(' ')[3];
        var newval = parseInt($(this).val());
        var id = $(this).attr('id');
        var diff = newval - parseInt($(this).siblings('input').val());
        $(this).siblings('input').val(newval);
        var no_ele = document.getElementsByClassName(class1).length;
        if ( no_ele >1)
        {
            for(i=0;i<no_ele;i++)
            {
                if(document.getElementsByClassName(class1)[i].id!=id)
                {
                    var elem =  document.getElementsByClassName(class1)[i];
                    //  var left = parseInt($(elem).siblings('input').val().split('_')[1])
                    var left = parseInt(document.getElementsByClassName(class1)[i].max);
                    var val = left - parseInt(diff);
                    document.getElementsByClassName(class1)[i].max=val;
                    document.getElementsByClassName(class1)[i].placeholder='max : '+val;
                }
            }
        } 
    });
    var trans_add=0;
    var other_add=0;
    $('.transportation').each(function(e) {
        var x=$(this).val();
        trans_add=parseFloat(trans_add)+parseFloat(x);
    });
    $('.other').each(function(e) {
                var y=$(this).val();
                other_add=parseFloat(other_add)+parseFloat(y);
    });
    $('#transportation_add').val(trans_add);
    $('#other_add').val(other_add);
      $('select').select2();
        }
        $('#ajax_loader_div').css('display','none');
}
            
                  
        });
       
         }

  });
  function change_desc_fn(data,io){
    var e= data.value;
    $('.desc_'+io).val(e);   
}
function change_rate_fn(data,io){
    var e= data.value;
    $('.rate_'+io).val(e);   
}
function change_disc_fn(data,io){
    var e= data.value;
    $('.discount_'+io).val(e);   
}
  
  $('#delivery').on("select2:unselect", function(e){
    var trans_add=0;
    var other_add=0;
        var value= e.params.data.id;
            //alert(tran);
            //var data=$("#add_"+val).remove();
             $('.'+value).each(function(e) {
                $("."+value).remove();
               
            });
            $('.transportation').each(function(e) {
                        
                        var x=$(this).val();
                        trans_add=parseFloat(trans_add)+parseFloat(x);
                        
                    
            });
            $('.other').each(function(e) {
                        
                        var y=$(this).val();
                        other_add=parseFloat(other_add)+parseFloat(y);
                        
                    
            });
            $('#transportation_add').val(trans_add);
            $('#other_add').val(other_add);
            //alert(trans_add);
            }).trigger('change');
      
});

</script>
<script>
        $('#ios').on("select2:select", function(e){
          var id = e.params.data.id;
          console.log(id);
          if(document.getElementById(id) == null){
            $('#ajax_loader_div').css('display','block');

            $.ajax({
              url: "io/" + id + "/ins" + "/" + 0,
              type: "GET",
              success: function(result) {
                  console.log(result);
                    for (var i = 0; i < result.length; i++) {
                      val++;
                      var is_pos=0;
                      var pays="";
                      var po='';
                        tran[val]=result[i].transportation_charge;
                        other[val]=result[i].other_charge;
                        if(result[i].party_name && result[i].is_po_provided==1){
                            var party_po=result[i].party_name.split(',');
                            var pay_po=result[i].payment_po.split(',');
                            var paypo=result[i].party_pay_terms.split(',');
                            for(var k=0;k<party_po.length;k++){
                                if(party_po[k]==party_ids){
                                  
                                    is_pos=1;
                                    pays=pay_po[k];
                                    po=paypo[k];
                                }
                            } 
                        }
                        else if(result[i].is_po_provided==0){
                           is_pos=1; 
                        }
                        else{
                            is_pos=0; 
                            pays="";
                        }
                   
                    if(result[i].is_po_provided==0){
                    var info="PO is Verbal for this Internal Order";
                    var discount="";
                    var po_qty=""; 
                    var per="";  
                    var hsn="";  
                    var payment="";
                    var rate="";  
                    var desc="";
                  } 
                  else{
                      var info="The PO Number For this Internal Order is: " + result[i].po_number;
                      var discount="Discount % entered in PO is :  " + result[i].po_discount;
                      var po_qty = "Quantity selected in client PO is:  "  + result[i].po_qty ;
                      var per = "Unit of Measurement selected in client PO is:  "  + result[i].po_uom_name ;
                      var hsn = "HSN Code selected in client PO is:  "  + result[i].po_hsn_name +"<br>"+ "Gst Rate is "+ result[i].po_gst_rate+"%" ;
                      var payment="Payment Term in Client PO is: "+ pays ;
                      var rate="Rate per price in Client PO is : "+ result[i].per_unit_price;
                      var desc="Item Description in Client PO is : " + result[i].item_desc;
                  }
                    
                  var ls='<div class="box box-default upperBox '+result[i].id+'" id="add_'+val+'">';
                ls=ls+'<h3 style="margin-left: 23px;">No Delivery Challan</h3>'+
                  '<p style="font-style:italic;margin-left: 23px;">'+info+'<p>'+
                '<div class="box-body">'+
                  '<div class="row">'+
                      '<div class="col-md-4">'+
                              '<div class="form-group"> '+ 
                                      '<label>{{__('taxinvoice.io')}} <sup>*</sup></label>'+
                                      '<select   required style="width:100%"  class="form-control select2 io_id input-css" name="internal_io_'+result[i].id+'"  id="io_id'+1+'" data-placeholder="">';
                               ls=ls + '<option value="'+result[i].id+'">'+result[i].ionumber+'</option>'+
                                     '</select>'+
                                     '<label id="internal_io_'+result[i].id+'[]-error" class="error"></label>'+
                              '</div>'+
                          '</div> '+

                         

                          '<div class="col-md-4"> '+    
                              '<label>{{__('taxinvoice.qty')}} <sup>*</sup></label>'+
                              '<input  required   type="text" min="0" name="qty_io_'+result[i].id+'" id="qty_'+result[i].id+'_'+val+'" value="'+result[i].left_qty+'" max="'+(result[i].left_qty)+'" placeholder="max : '+(result[i].left_qty)+'" class="form-control input-css qty qty_io_'+result[i].id+'">'+  
                              '<input type="hidden" value="'+result[i].left_qty+'" id="hidden_max_val_'+val+'" name="orig_qty_left_'+result[i].id+'">'+
                              '<p style="font-size:12px;color:green">'+po_qty+'</p>'+
                              '<label id="qty_io_'+result[i].id+'[]-error" class="error"></label>'+
                              '</div>'+
                          '<div class="col-md-4">'+  
                              '<label>{{__('taxinvoice.rate')}} <sup>*</sup></label>'+
                             ' <input   required  type="number" min="0" step="any" disabled id="rate_'+result[i].id+val+'" value="'+result[i].rate_per_qty+'" class="form-control input-css rate">'+ 
                             ' <input   required  type="hidden" min="0" step="any" name="rate_io_'+result[i].id+'" id="rate_'+result[i].id+val+'" value="'+result[i].rate_per_qty+'" class="form-control input-css rate">'+   
                             '<p style="font-size:12px;color:green">'+rate+'</p>'+ 
                             '<label id="rate_io_'+result[i].id+'[]-error" class="error" ></label>'+
                             '</div>'+ 
                              
                      '</div><br>'+
                      '<div class="row">'+
                        '<div class="col-md-12">'+
                                  '<label>{{__('taxinvoice.goods')}} <sup>*</sup></label>'+
                                  '<textarea  required    name="goods_io_'+result[i].id+'"  id="good'+result[i].id+val+'"  class="form-control input-css goods">'+result[i].good_desc+'</textarea>'+
                                //   '<textarea  required    name="goods_io_'+result[i].id+'"   id="good_'+result[i].id+val+'" class="form-control input-css goods">'+result[i].good_desc+'<textarea>'
                                  '<p style="font-size:12px;color:green">'+desc +'</p>'+
                                  '<label id="goods_io_'+result[i].id+'[]-error" class="error"></label>'+
                                  '</div>'+ 
                        '</div>'+
                      '<div class="row"> '+
                              '<div class="col-md-4">   '+  
                                  '<label>{{__('taxinvoice.per')}} <sup>*</sup></label>'+
                                  '<select  required name="per_io_'+result[i].id+'" id="per'+result[i].id+val+'" class="form-control select2 input-css per"> '+ 
                                        
                                        '<option value="'+result[i].io_uom_id+'">'+result[i].io_uom_name+'</option>'+
                                                    @foreach($uom as $key)
                                                    '<option value="{{$key->id}}">{{$key->uom_name}}</option>'+
                                                    @endforeach
                                    '</select>'+   
                                    '<p style="font-size:12px;color:green">'+per+'</p>'+              
                                  '<label id="per_io_'+result[i].id+'[]-error" class="error"></label>'+
                                  '</div>'+
                             ' <div class="col-md-4">  '+
                                  '<label>{{__('taxinvoice.discount')}} <sup>*</sup></label>'+
                                 ' <input   required  type="number" step="any" min="0" name="discount_io_'+result[i].id+'" value="'+result[i].discount+'" id="discount'+result[i].id+val+'" class="form-control input-css discount">'+
                                 '<p style="font-size:12px;color:green"> '+discount+'</p>'+  
                                 '<label id="discount_io_'+result[i].id+'[]-error" class="error" ></label>'+
                             '</div>'+ 
                             ' <div class="col-md-4">  '+   
                                  '<label>{{__('taxinvoice.hsn')}} <sup>*</sup></label>'+
                                      '<select  required    class="input-css select2 hsn"  id="hsn'+result[i].id+val+'" data-placeholder="" name="hsn_io_'+result[i].id+'">'+
                                              '<option value="'+result[i].io_hsn+'">'+result[i].item_id+" - "+result[i].io_hsn_name+" - "+result[i].io_gst_rate+"%"+'</option>'+
                                          @foreach($hsn as $key)
                                              '<option value="{{$key->id}}">{{$key->name." - ".$key->hsn." - ".$key->gst_rate."%"}}</option>'+
                                          @endforeach
                                      '</select>'+   
                                      '<input type="hidden" value="'+result[i].io_gst_rate+'" name="io_io_gst_'+result[i].id+'">'+
                                      '<p style="font-size:12px;color:green">'+hsn+'</p>'+ 
                                      '<label id="hsn_io_'+result[i].id+'[]-error" class="error"></label>'+ 
                              '</div>'+ 

                          '</div>'+
                          '<div class="row">'+
                                    '<div class="col-md-4"> '+
                                            '<label>{{__('taxinvoice.trans')}} <sup>*</sup></label>'+
                                                '<input   required   min="0" type="number" step="any" name="transportation_io_'+result[i].id+'" id="transportation_'+result[i].id+val+'" class="form-control input-css transportation" value="'+result[i].transportation_charge+'">'+
                                                '<label id="transportation_io_'+result[i].id+'[]-error" class="error"></label>'+ 
                                    '</div> '+
                                    '<div class="col-md-4"> '+
                                            '<label>{{__('taxinvoice.other')}} <sup>*</sup></label>'+
                                                '<input required    min="0"    type="number" step="any" name="other_io_'+result[i].id+'" id="other" class="form-control input-css other" value="'+result[i].other_charge+'">'+
                                                '<label id="other_io_'+result[i].id+'[]-error" class="error"></label>'+ 
                                    '</div>  '+ 
                                    '<div class="col-md-4">'+
                                            '<label>{{__('taxinvoice.payment')}} <sup>*</sup></label>'+
                                            '<select  required name="payment_io_'+result[i].id+'" id="payment'+result[i].id+val+'" class="form-control select2 input-css payment"> '+ 
                                            '<option value="'+po+'">'+pays+'</option>'+
                                                    @foreach($payment as $key)
                                                    '<option value="{{$key->id}}">{{$key->value}}</option>'+
                                                    @endforeach
                                            '</select>'+  
                                        '<p style="font-size:12px;color:green">'+payment+'</p>'+ 
                                      '<label id="payment_io_'+result[i].id+'[]-error" class="error"></label>'+  
                                    '</div>'+
                                '</div>'+
              '</div>'+
      '</div>';
      $('select').select2('destroy');
        if(is_pos==1){
            $('#list_io_ser').append(ls);
        }
        else{
             alert('No Client Po Exist for this Internal Order');
            var wanted_option = $('#ios option[value="'+ id +'"]');
            wanted_option.prop('selected', false);
        }
           
            // $('#selectTwo').trigger('change.select2');
    $('.qty').change(function(e)
    {
        var class1 =  $(this).attr('class').split(' ')[3];
        var newval = parseInt($(this).val());
        var id = $(this).attr('id');
        var diff = newval - parseInt($(this).siblings('input').val());
        $(this).siblings('input').val(newval);
        var no_ele = document.getElementsByClassName(class1).length;
        if ( no_ele >1)
        {
            for(i=0;i<no_ele;i++)
            {
                if(document.getElementsByClassName(class1)[i].id!=id)
                {
                    var elem =  document.getElementsByClassName(class1)[i];
                    //  var left = parseInt($(elem).siblings('input').val().split('_')[1])
                    var left = parseInt(document.getElementsByClassName(class1)[i].max);
                    var val = left - parseInt(diff);
                    document.getElementsByClassName(class1)[i].max=val;
                    document.getElementsByClassName(class1)[i].placeholder='max : '+val;
                }
            }
        } 
    });
    var trans_add=0;
    var other_add=0;
    $('.transportation').each(function(e) {
        var x=$(this).val();
        trans_add=parseFloat(trans_add)+parseFloat(x);
    });
    $('.other').each(function(e) {
                var y=$(this).val();
                other_add=parseFloat(other_add)+parseFloat(y);
    });
    $('#transportation_add').val(trans_add);
    $('#other_add').val(other_add);
      $('select').select2();
        }
        $('#ajax_loader_div').css('display','none');
    }
            
                  
        });
       
         }

         $('#ios').on("select2:unselect", function(e){
        var trans_add=0;
        var other_add=0;
        var value= e.params.data.id;
            //alert(tran);
            //var data=$("#add_"+val).remove();
             $('.'+value).each(function(e) {
                $("."+value).remove();
               
            });
            $('.transportation').each(function(e) {
                        
                        var x=$(this).val();
                        trans_add=parseFloat(trans_add)+parseFloat(x);
                        
                    
            });
            $('.other').each(function(e) {
                        
                        var y=$(this).val();
                        other_add=parseFloat(other_add)+parseFloat(y);
                        
                    
            });
            $('#transportation_add').val(trans_add);
            $('#other_add').val(other_add);
            //alert(trans_add);
            }).trigger('change');

  });
</script>
<script>
var message="{{Session::get('message')}}";
if(message=="tax"){
    document.getElementById("popup_message2").click();
}
</script>
@endsection

@section('main_section')
   <!-- Main content -->
    <section class="content">
        <!-- Default box -->
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
            @if(in_array(1, Request::get('userAlloweds')['section']))
            {{-- <p>Hello</p> --}}
            @endif

        <form id="taxform" action="/taxInsert" method="POST">
            @csrf
            <div class="box box-default">
                <div class="box-header with-border">
                        <h3>{{__('taxinvoice.mytitle')}}</h3>
                    <div class="box-body">
                            {{-- <div class="row">
                         
                                <div class="col-md-6">
                                    <div class="form-group">  
                                            <label>{{__('taxinvoice.terms')}} <sup>*</sup></label>
                                            <input         type="text" name="terms" id="terms" class="form-control input-css terms">
                                    </div>
                                </div>   

                            </div> --}}
                            <div class="row">
                                <div class="col-md-3">
                                        <label>{{__('taxinvoice.party')}} <sup>*</sup></label>
                                        <select required name="party" class="select2 input-css" id="party">
                                        <option value="">Select Client</option>
                                            @foreach($party as $key)
                                            <option value="{{$key->id}}">{{$key->partyname}}</option>
                                            @endforeach
                                        </select> 
                                        {!! $errors->first('party', '<p class="help-block">:message</p>') !!}  
                                </div>
                                <div class="col-md-3">
                                        <label>{{__('taxinvoice.consignee')}} <sup>*</sup></label>
                                        <select required name="consignee" class="select2 input-css" id="consignee">
                                                <option value="">Select Client First</option>
            
                                        </select>   
                                        {!! $errors->first('consignee', '<p class="help-block">:message</p>') !!}
                                </div>
                               <div class="col-md-3"> 
                                    <label>{{__('taxinvoice.delivery')}} <sup></sup></label>
                               <select   value="{{old('delivery[]')}}" class="select2 input-css" name="delivery[]" multiple="multiple" id="delivery">
                                    <option value="default" disabled>Select Delivery Challan No</option>
                                    </select>
                                    {!! $errors->first('delivery', '<p class="help-block">:message</p>') !!}
                                    <label id="delivery-error" class="error" for="delivery"></label>
                                </div> 
                                <div class="col-md-3" id="io_ser"> 
                                    <label>{{__('taxinvoice.io')}} <sup></sup></label>
                               <select   value="{{old('io[]')}}" class="select2 input-css" name="io[]" multiple="multiple" id="ios">
                                    <option value="default" disabled>Select Internal Order</option>
                                    </select>
                                    {!! $errors->first('io', '<p class="help-block">:message</p>') !!}
                                </div> 

                            </div><br>
                            <div class="row">
                                    <div class="col-md-3"> 
                                            <label>{{__('taxinvoice.terms')}} <sup>*</sup></label>
                                                <input required   type="text" name="terms" id="terms" class="form-control input-css terms">
                                                {!! $errors->first('terms', '<p class="help-block">:message</p>') !!}
                                        </div> 
                                    <div class="col-md-3">
                                        <label>{{__('taxinvoice.gst')}} <sup>*</sup></label>
                                            <select required class="select2 input-css gst" name="gst" id="gst">
                                                    <option value="default">Select GST Type</option>
                                                <option value="IGST">IGST</option>
                                                <option value="CGST/SGST">CGST/SGST</option>
                                                 </select>  
                                                 {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                            <label id="gst-error" class="error"></label>
                                    </div>
                                    <div class="col-md-3"> 
                                            <label>{{__('taxinvoice.trans')}} <sup>*</sup></label>
                                                <input required type="number" min="0" step="any" name="transportation" id="transportation_add" class="form-control input-css transportation_add">
                                                {!! $errors->first('transportation', '<p class="help-block">:message</p>') !!}
                                    </div> 
                                    <div class="col-md-3"> 
                                            <label>{{__('taxinvoice.other')}} <sup>*</sup></label>
                                                <input required  required  min="0"  type="number" step="any" name="other" id="other_add" class="form-control input-css other_add">
                                                {!! $errors->first('other', '<p class="help-block">:message</p>') !!}
                                    </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Tax Date<sup>*</sup></label>
                                    <input type="text" autocomplete="off" name="created_at" class="input-css datepickers" id="" required>
                                </div>
                            </div>
                    </div> <!-- /.box-body -->
                </div>  
                <!-- /.box --> 
            </div> 
            <div id="list">
            </div>
            <div id="list_io_ser">
            </div>
            <div class="row">
                    <div class="col-md-12">
                            <button type="submit" style="float:right" class="btn btn-primary ">Submit</button>  
                    </div>
            </div>
        </form>
    </section>
@endsection

