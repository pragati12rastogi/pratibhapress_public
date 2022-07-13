@extends($layout)
@section('title', __('taxinvoice.updatetitle'))
@section('user',Auth::user()->name)
@section('breadcrumb')
    <li><a href="#"><i >{{__('taxinvoice.updatetitle')}}</i></a></li>
 @endsection
@section('css')
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
var details;
var party_ids="{{$invoice->party_id}}";
var con_ids="{{$invoice->consignee_id}}";
// 

var dc;
var val=0;
@if(count($tax)>0)
var tx="{{$tax[0]->id}}";
@endif
var dc_not=new Array();
var io_not=new Array();
var tran=new Array();
var other=new Array();
$(document).ready(function() {
    console.log( {{implode(',',$dc)}});
    
   
    @if(count($tax)>0)
       $('#party').val(party_ids).trigger('change');
        party(party_ids,'update',tx);
        @foreach($tax as $t)
        @if($t->io_id!='' && $t->id1=='')
                select2_add_new_io({{$t->io_id}},'update',tx);
            @endif
        @endforeach
        select2_add_new_challan(tx,'update');
    @else 
        $('#party').val(party_ids).trigger('change');
        party(party_ids,'ins',0);
    @endif
    
   
    function party(partyid,type,idss){
        var party = partyid;
        party_ids=partyid;
          $('#list').empty();
          $('#ajax_loader_div').css('display','block');

          $.ajax({
            url: "/party/delivery/" + party + "/"+type + "/"+idss,
              type: "GET",
              success: function(result) {
                  $('#consignee').empty();
                  $('#delivery').empty();
                  $('#delivery1').empty();
                  $('#ios').empty();
                  $('#ios1').empty();
                  var party=result.party;
                  var ios=result.io;
                  var consignees=result.consignee;
                //  console.log(consignees);
                 for (var i = 0; i < consignees.length; i++) {
                    $('#consignee').append('<option value="'+consignees[i].id+'">'+consignees[i].consignee_name+'</option>')
                    }
                    $('#consignee').val(con_ids).trigger('change');
                 if(party!=''){
                    $("#list").empty();
                  for (var i = 0; i < party.length; i++) {
                   
                    $('#delivery').append('<option value="'+party[i].id+'">'+party[i].challan_number+'</option>');
                    $('#delivery1').append('<option value="'+party[i].id+'">'+party[i].challan_number+'</option>');
                  }
                 }
                 else{
                     $("#list").append('<div class="box box-default"><h3>Delivery Challan for this Client is not available or Tax Invoice for this Client has been created..</h3><br><br><br></div>')
                 }
                 if(ios!=''){
                    $('#io_ser').css('display','block');
                    for (var i = 0; i < ios.length; i++) {
                    $('#ios').append('<option value="'+ios[i].id+'">'+ios[i].io_number+'</option>');
                    $('#ios1').append('<option value="'+ios[i].id+'">'+ios[i].io_number+'</option>')
                  }
                 
                 }
                 if(ios==''){
                      $('#io_ser').css('display','none');
                  }
                  $('#delivery').val([
                  {{implode(',',$dc)}}
                ]).trigger('change');
                $('#delivery1').val([
                  {{implode(',',$dc)}}
                ]).trigger('change');
                $('#ios').val([
                    @foreach($tax as $t)
                    @if($t->io_id!='' && $t->id1=='')
                        {{$t->io_id}},
                    @endif
                    @endforeach
                ]).trigger('change');
                $('#ios1').val([
                    @foreach($tax as $t)
                    @if($t->io_id!='' && $t->id1=='')
                        {{$t->io_id}},
                    @endif
                    @endforeach
                ]).trigger('change');
           
                 $('#ajax_loader_div').css('display','none');

            }
                  
        });
    }

    $('#party').change(function(e) {
            var type="ins";
            var idss=0;
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
//    $('#delivery option[value='+dc_not+']').prop('disabled',true);
        $('#ios').on("select2:select", function(e){
            var id = e.params.data.id;          
            select2_add_new_io(id,'ins',0);
        });
    
    $('#delivery').on("select2:select", function(e){
        var id = e.params.data.id;          
        select2_add_new_challan(id,'ins');
      
    });
   
    function select2_add_new_challan(id,type)
    { 
        // console.log(1);
        // console.log(id);
        // console.log(type);
        
        if(document.getElementById(id) == null)
        {
            var party_tax=$('#party').val();
           
            
            $('#ajax_loader_div').css('display','block');
            $.ajax({
                url: "/delivery/" + id +"/"+type,
                type: "GET",
                success: function(result) 
                {
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
                                if(party_po[k]==party_tax){
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
                        else
                        {
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
                            '<select   required   class="form-control select2 io_id input-css" name="internal_'+result[i].id+'[]"  id="io_'+result[i].id+'_'+val+'" data-placeholder="">';
                    ls=ls + '<option value="'+result[i].io_id+'">'+result[i].ionumber+'</option>'+
                            '</select>'+
                            '<label id="internal_'+result[i].id+'[]-error" class="error"></label>'+
                            '</div>'+
                            '</div> '+
                           

                            '<div class="col-md-4"> '+    
                            '<label>{{__('taxinvoice.qty')}} <sup>*</sup></label>'+
                            '<input  required   type="number" min="0" name="qty_'+result[i].id+'[]" id="qty_'+result[i].id+'_'+val+'" value="'+result[i].good_qty+'" max="'+(result[i].good_qty+result[i].left_qty)+'" placeholder="max : '+(result[i].good_qty+result[i].left_qty)+'" class="form-control input-css qty qty_io_'+result[i].io+'">'+  
                            '<input type="hidden" value="'+result[i].good_qty+'" id="hidden_max_val_'+val+'">'+
                            '<p style="font-size:12px;color:green">'+po_qty+'</p>'+
                            '<label id="qty_'+result[i].id+'[]-error" class="error"></label>'+
                            '</div>'+
                            '<div class="col-md-4">'+  
                            '<label>{{__('taxinvoice.rate')}} <sup>*</sup></label>'+
                            ' <input   required  type="number" min="0" step="any" disabled  id="rate_'+result[i].id+'_'+val+'" onchange="change_rate_fn(this,'+result[i].io_id+')" value="'+result[i].rate_per_qty+'" class="form-control input-css rate rate_'+result[i].io_id+'">'+  
                            ' <input   required  type="hidden" min="0" step="any" name="rate_'+result[i].id+'[]" id="rate_'+result[i].id+'_'+val+'" onchange="change_rate_fn(this,'+result[i].io_id+')" value="'+result[i].rate_per_qty+'" class="form-control input-css rate rate_'+result[i].io_id+'">'+  
     
                            
                            '<p style="font-size:12px;color:green">'+rate+'</p>'+ 
                            '<label id="rate_'+result[i].id+'[]-error" class="error" ></label>'+
                            '</div>'+ 
                            '</div><br>'+
                            '<div class="row">'+
                                '<div class="col-md-12">'+
                            '<label>{{__('taxinvoice.goods')}} <sup>*</sup></label>'+
                            '<textarea name="goods_'+result[i].id+'[]" id="good_'+result[i].id+'_'+val+'" onchange="change_desc_fn(this,'+result[i].io_id+')" class="form-control input-css goods desc_'+result[i].io_id+'" rows="5">'+result[i].good_desc+'</textarea>'+
                            // '<input  required  type="text"  name="goods_'+result[i].id+'[]"  value="'+result[i].good_desc+'" id="good_'+result[i].id+'_'+val+'" onchange="change_desc_fn(this,'+result[i].io_id+')" class="form-control input-css goods desc_'+result[i].io_id+'">'+
                            '<p style="font-size:12px;color:green">'+desc +'</p>'+
                            '<label id="goods_'+result[i].id+'[]-error" class="error"></label>'+
                            '</div>'+ 
                            '</div>'+
                            '<div class="row"> '+
                            '<div class="col-md-4">   '+  
                            '<label>{{__('taxinvoice.per')}} <sup>*</sup></label>'+
                            '<select  required name="per_'+result[i].id+'[]" id="per_'+result[i].id+'_'+val+'" onchange="change_pero_fn(this,'+result[i].io_id+')" class="form-control select2 input-css per per_'+result[i].io_id+'"> '+ 

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
                            ' <input   required  type="number" min="0" step="any" name="discount_'+result[i].id+'[]" onchange="change_disc_fn(this,'+result[i].io_id+')" value="'+result[i].discount+'" id="discount_'+result[i].id+'_'+val+'" class="form-control input-css discount discount_'+result[i].io_id+'">'+
                            '<p style="font-size:12px;color:green"> '+discount+'</p>'+  
                            '<label id="discount_'+result[i].id+'[]-error" class="error" ></label>'+
                            '</div>'+ 
                            ' <div class="col-md-4">  '+   
                            '<label>{{__('taxinvoice.hsn')}} <sup>*</sup></label>'+
                            '<select  required    class="input-css select2 hsn hsn_'+result[i].io_id+'" onchange="change_hsn_fn(this,'+result[i].io_id+')"  id="hsn_'+result[i].id+'_'+val+'" data-placeholder="" name="hsn_'+result[i].id+'[]">'+
                            '<option value="'+result[i].io_hsn+'">'+result[i].item_id+" - "+result[i].io_hsn_name+"-"+result[i].io_gst_rate+'</option>'+
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
                            '<input required    required    type="number" step="any" name="transportation_'+result[i].id+'[]" id="transportation_'+result[i].id+'_'+val+'" class="form-control input-css transportation" value="'+result[i].transportation_charge+'">'+
                            '<label id="transportation_'+result[i].id+'[]-error" class="error"></label>'+ 
                            '</div> '+
                            '<div class="col-md-4"> '+
                            '<label>{{__('taxinvoice.other')}} <sup>*</sup></label>'+
                            '<input required    required    type="number" step="any" name="other_'+result[i].id+'[]" id="other_'+result[i].id+'_'+val+'" class="form-control input-css other" value="'+result[i].other_charge+'">'+
                            '<label id="other_'+result[i].id+'[]-error" class="error"></label>'+ 
                            '</div>  '+ 
                            '<div class="col-md-4">'+
                            '<label>{{__('taxinvoice.payment')}} <sup>*</sup></label>'+
                            '<select  required name="payment_'+result[i].id+'[]" id="payment_'+result[i].id+'_'+val+'" class="form-control select2 input-css payment"> '+ 
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
                      
                        if(is_po==1 && io_type==1){
                            $('#list_io_ser').append(ls);
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
                        var trans_add=0;
                        var other_add=0;
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
    }
    $('#delivery').on("select2:unselect", function(e){
        var trans_add=0;
        var other_add=0;
        var value= e.params.data.id;
      
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
function change_pero_fn(data,io){
    var en= data.value;
}
function change_hsn_fn(data,io){
    var en= data.value;
}
</script>
<script>
function select2_add_new_io(id,type,tax){
    // if(document.getElementById(id) == null){
            $('#ajax_loader_div').css('display','block');
        // alert('xvxvx');
        party_ids=$('#party').val();
        // console.log(party_ids);
        
            $.ajax({
              url: "/io/" + id +"/"+type + "/" + tax,
              type: "GET",
              success: function(result) {
                  
                    for (var i = 0; i < result.length; i++) {
                      val++;
                      var is_pos=0;
                      var io_ids=result[i].id;
                      
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
                   if(type=='ins'){
                       var x=result[i].left_qty;
                   } 
                   if(type=='update'){
                       var x=(result[i].left_qty+result[i].good_qty);
                   }
                  var ls='<div class="box box-default upperBox '+result[i].id+'" id="add_'+val+'">';
                ls=ls+'<h3 style="margin-left: 23px;">No Delivery Challan</h3>'+
                  '<p style="font-style:italic;margin-left: 23px;">'+info+'<p>'+
                '<div class="box-body">'+
                  '<div class="row">'+
                      '<div class="col-md-4">'+
                              '<div class="form-group"> '+ 
                                      '<label>{{__('taxinvoice.io')}} <sup>*</sup></label>'+
                                      '<select   required   class="form-control select2 io_id input-css" name="internal_io_'+result[i].id+'"  id="io_id'+1+'" data-placeholder="">';
                               ls=ls + '<option value="'+result[i].id+'">'+result[i].ionumber+'</option>'+
                                     '</select>'+
                                     '<label id="internal_io_'+result[i].id+'[]-error" class="error"></label>'+
                              '</div>'+
                          '</div> '+

                      

                          '<div class="col-md-4"> '+    
                              '<label>{{__('taxinvoice.qty')}} <sup>*</sup></label>'+
                              '<input  required   type="text" min="0" name="qty_io_'+result[i].id+'" id="qty_'+result[i].id+'_'+val+'" value="'+result[i].good_qty+'" max="'+x+'" placeholder="max : '+x+'" class="form-control input-css qty qty_io_'+result[i].id+'">'+  
                              '<p style="font-size:12px;color:green">'+po_qty+'</p>'+
                              '<label id="qty_io_'+result[i].id+'[]-error" class="error"></label>'+
                              '</div>'+
                          '<div class="col-md-4">'+  
                              '<label>{{__('taxinvoice.rate')}} <sup>*</sup></label>'+
                             ' <input   required  type="number" min="0" step="any" name="rate_io_'+result[i].id+'" id="rate_'+result[i].id+val+'" value="'+result[i].rate_per_qty+'" class="form-control input-css rate">'+  
                             '<p style="font-size:12px;color:green">'+rate+'</p>'+ 
                             '<label id="rate_io_'+result[i].id+'[]-error" class="error" ></label>'+
                             '</div>'+ 
                             '<input type="hidden" value="'+result[i].good_qty+'"  name="old_good_qty_'+result[i].id+'">'+
                              '<input type="hidden" value="'+result[i].left_qty+'"  name="orig_qty_left_'+result[i].id+'">'+ 
                      '</div><br>'+
                      '<div class="row">'+
                        '<div class="col-md-12">'+
                                  '<label>{{__('taxinvoice.goods')}} <sup>*</sup></label>'+
                                  '<textarea required  type="text"  name="goods_io_'+result[i].id+'" id="good_'+result[i].id+val+'" class="form-control input-css goods">'+result[i].good_desc+'</textarea>'+
                                //   '<input  required  type="text"  name="goods_io_'+result[i].id+'"  value="'+result[i].good_desc+'" id="good_'+result[i].id+val+'" class="form-control input-css goods">'+
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
                                                '<input   required    type="number" step="any" name="transportation_io_'+result[i].id+'" id="transportation_'+result[i].id+val+'" class="form-control input-css transportation" value="'+result[i].transportation_charge+'">'+
                                                '<label id="transportation_io_'+result[i].id+'[]-error" class="error"></label>'+ 
                                    '</div> '+
                                    '<div class="col-md-4"> '+
                                            '<label>{{__('taxinvoice.other')}} <sup>*</sup></label>'+
                                                '<input required    required    type="number" step="any" name="other_io_'+result[i].id+'" id="other" class="form-control input-css other" value="'+result[i].other_charge+'">'+
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
       
         
    // }
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
            {{-- <p>Hello</p> --}}
            @if ($errors->any())
            <div class="alert alert-warning">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style=" list-style-type: square;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- SELECT2 EXAMPLE -->

        <form id="taxform" action="/taxinvoice/update/db/{{$invoice->id}}" method="POST" >
            @csrf
            <div class="box box-default">
                <div class="box-header with-border">
                        <h3>{{__('taxinvoice.updatetitle')}}</h3>
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

                                <div class="col-md-6 ">
                                    <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                    <input type="text" name="update_reason" required="" class="form-control input-css " id="update_reason">
                                    {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-3">
                                        <label>{{__('taxinvoice.party')}} <sup>*</sup></label>
                                        <select {{ count($tax)>0 ? 'disabled=true' : ''}}  name="party_id" class="select2 input-css" id="party">
                                        <option value="default">Select Client</option>
                                            @foreach($party as $key)
                                            <option value="{{$key->id}}">{{$key->partyname}}</option>
                                            @endforeach
                                        </select> 
                                        
                                        <input type="hidden" name="party" value="{{$invoice->party_id}}">  
                                </div>
                                <div class="col-md-3">
                                        <label>{{__('taxinvoice.consignee')}} <sup>*</sup></label>
                                        <select name="consignee" class="select2 input-css" id="consignee">
                                                <option value="default">Select Client</option>
                                                @foreach($consignee as $key)
                                                <option value="{{$key->id}}" {{$key->id==$invoice->consignee_id ? 'selected=selected' : ''}}>{{$key->consignee_name}}</option>
                                                @endforeach
                                     
                                        </select>   
                                </div>
                               <div class="col-md-3"> 
                                    <label>{{__('taxinvoice.delivery')}} <sup>*</sup></label>
                                    <select class="select2 input-css" name="delivery[]" multiple="multiple" id="delivery">
                                        <option value="default" disabled>Select Delivery Challan No</option>
                                        @foreach($delivery_id['party'] as $key)
                                        <option value="{{$key->id}}" >{{$key->challan_number}}</option>
                                        @endforeach
                                    </select><br><br>
                                </div> 
                                <div class="col-md-6" style="display:none">
                                    <select class="select2 input-css" name="old_dc[]"  multiple="multiple" id="delivery1">
                                        @foreach($delivery_id['party'] as $key)
                                        <option value="{{$key->id}}" >{{$key->challan_number}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" id="io_ser"> 
                                    <label>{{__('taxinvoice.io')}} <sup></sup></label>
                               <select   value="{{old('io[]')}}" class="select2 input-css io_after" name="io[]" multiple="multiple" id="ios">
                                    <option value="default" disabled>Select Internal Order</option>
                                    </select>
                                    {!! $errors->first('io', '<p class="help-block">:message</p>') !!}
                                </div> 
                                <div class="col-md-6" style="display:none">
                                    <select class="select2 input-css" name="old_io[]"  multiple="multiple" id="ios1">
                                        <option value="default" disabled>Select Delivery Challan No</option>
                                        @foreach($delivery_id['party'] as $key)
                                        <option value="{{$key->id}}" >{{$key->challan_number}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><br>
                            <div class="row">
                                    <div class="col-md-3"> 
                                            <label>{{__('taxinvoice.terms')}} <sup>*</sup></label>
                                    <input required type="text" value="{{$invoice->terms_of_delivery}}" name="terms" id="terms" class="form-control input-css terms">
                                             
                                        </div> 
                                    <div class="col-md-3">
                                        <label>{{__('taxinvoice.gst')}} <sup>*</sup></label>
                                            <select required class="select2 input-css gst" name="gst" id="gst">
                                                    <option value="default">Select GST Type</option>
                                                <option value="IGST" {{$invoice->gst_type=="IGST"?'selected="selected"':''}}>IGST</option>
                                                <option value="CGST/SGST"{{$invoice->gst_type=="CGST/SGST"?'selected="selected"':''}}>CGST/SGST</option>
                                               
                                                 </select>  
                                            <label id="gst-error" class="error"></label>
                                    </div>
                                    <div class="col-md-3"> 
                                            <label>{{__('taxinvoice.trans')}} <sup>*</sup></label>
                                                <input required value="{{$invoice->transportation_charge}}" type="number" min="0" step="any" name="transportation" id="transportation_add" class="form-control input-css transportation_add">
                                             
                                    </div> 
                                    <div class="col-md-3"> 
                                            <label>{{__('taxinvoice.other')}} <sup>*</sup></label>
                                    <input required value="{{$invoice->other_charge}}"  min="0"  type="number" step="any" name="other" id="other_add" class="form-control input-css other_add">
                                             
                                    </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Tax Date<sup>*</sup></label>
                                    <input type="text" autocomplete="off" value="{{$invoice->date}}" name="created_at" class="input-css datepickers" id="" required>
                                </div>
                            </div>
                    </div> <!-- /.box-body -->
                </div>  
                <!-- /.box --> 
            </div> 
            <div id="list">
            </div>
            <div id="list_io_ser"></div>
            <div class="row">
                    <div class="col-md-12">
                           
                            <button type="submit" style="float:right" class="btn btn-primary submit">Submit</button>  
                    </div>
            </div>
        </form>
    </section>
@endsection

