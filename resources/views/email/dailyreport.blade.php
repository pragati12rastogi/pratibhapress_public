@extends($layout)

@section('title', 'Daily Report')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

@endsection

@section('css')
<style>
th{
    padding:10px;
    width: 296px;
    height: 42px;
    text-align: center;
}
td{
    text-align: center;
    height: 42px;
}
table{
    width: 100%;
}
</style>
 
@endsection
@section('js')
<script>

    function formatNumber(num) {
            input = num;
            var n1, n2;
            num = num + '' || '';
            // works for integer and floating as well
            n1 = num.split('.');
            n2 = n1[1] || null;
            n1 = n1[0].replace(/(\d)(?=(\d\d)+\d$)/g, "$1,");
            num = n2 ? n1 + '.' + n2 : n1;
            // console.log("Input:",input)
            // console.log("Output:",num)
            return num;
    }

  $(document).ready(function()  {
      var date=$("#date").val();
      var reference_name=$("#reference_name").val();
      var party_name=$("#party_name").val();
      tableGen(reference_name,party_name,date);
    });

    function tableGen(ref,party,date){
        if(date){
            var getdate=date;
        }
        else{
            var getdate="{{date('d-m-Y')}}";
        }
        $('#ajax_loader_div').css('display','block');
              $.ajax({
                type:'get',
                url:"/admin/daily/report/api",
                data:{'ref':ref,'party':party,'date':date},
                contentType: "application/json",
                dataType: "json",
                success:function(result) {
                    $('#ajax_loader_div').css('display','block');
                    $('table[id=io_today] tbody').empty();
                    $('table[id=tax_today] tbody').empty();
                    $('table[id=pay_today] tbody').empty();
                    $('table[id=mir_today] tbody').empty();
                    $('table[id=dis_today] tbody').empty();
                    // $('table[id=prod_today] tbody').empty();
                    $('table[id=dailyprepresslog] tbody').empty();
                    $('table[id=dailyprocesslog] tbody').empty();
                    $('table[id=design_order] tbody').empty();
                    $('table[id=purchase] tbody').empty();

                    $('table[id=io_today] tbody').append('<tr><td colspan="6">No I.O. Engaged Today</td></tr>')
                    $('table[id=tax_today] tbody').append('<tr><td colspan="7">No Party Billed Today</td></tr>')
                    $('table[id=pay_today] tbody').append('<tr><td colspan="6">No Payment Received Today</td></tr>')
                    $('table[id=mir_today] tbody').append('<tr><td colspan="6">No MIR Today</td></tr>')
                    // $('table[id=dis_today] tbody').append('<tr><td colspan="8">No Material Dispatchas Today</td></tr>')
                    // $('table[id=prod_today] tbody').append('<tr><td colspan="6">No Production Done for the day Today</td></tr>');
                    $('table[id=dailyprepresslog] tbody').append('<tr><td colspan="11">No Daily Plate Report</td></tr>');
                    $('table[id=dailyprocesslog] tbody').append('<tr><td colspan="11">No Daily Production Report</td></tr>');
                    $('table[id=design_order] tbody').append('<tr><td colspan="6">No Design Order Today</td></tr>');
                    $('table[id=purchase] tbody').append('<tr><td colspan="7">No Purchase Order Today</td></tr>');


                    $('.with').empty();
                    $('.without').empty();

                    $('.mon_without').empty();
                    $('.mon_with').empty();

                   

                    var with_tax=parseFloat(result.with_tax[0].amount).toFixed(2);
                    var without_tax=parseFloat(result.without_tax[0].amount).toFixed(2);

                    var monthly_with_tax=parseFloat(result.monthly_with_tax[0].amount).toFixed(2);
                    var monthly_without_tax=parseFloat(result.monthly_without_tax[0].amount).toFixed(2);
                    if(with_tax==null){
                        with_tax=0;
                    }
                    if(without_tax==null){
                        without_tax=0;
                    }
                    if(monthly_with_tax==null){
                        monthly_with_tax=0;
                    }
                    if(monthly_without_tax==null){
                        monthly_without_tax=0;
                    }
                    $('.with').append("<p>"+formatNumber(with_tax)+"</p>");//with_tax.toLocaleString('en-US')
                    $('.without').append("<p>"+formatNumber(without_tax)+"</p>");//without_tax.toLocaleString('en-US')

                    $('.mon_with').append("<p>"+formatNumber(monthly_with_tax)+"</p>");//monthly_with_tax.toLocaleString('en-IN')
                    $('.mon_without').append("<p>"+formatNumber(monthly_without_tax)+"</p>");//monthly_without_tax.toLocaleString('en-IN')

                    

                    //io today
                    var io_today=result.io_today;
                    if(io_today.length>0){
                        $('table[id=io_today] tbody').empty();
                        for(var i=0;i<io_today.length;i++){
                            var ls= '<tr>'+   
                                        '<td>'+io_today[i].io_number+'</td>'+
                                        '<td>'+io_today[i].referencename+'</td>'+
                                        '<td>'+io_today[i].item_name+'</td>'+
                                        '<td>'+io_today[i].qty+'</td>'+
                                        '<td>'+io_today[i].rate_per_qty+'</td>'+
                                        '<td>'+formatNumber(parseFloat(io_today[i].amount).toFixed(2))+'</td>'+
                                        // '<td>'+io_today[i].job_size+'</td>'+
                                    '</tr>';
                                $('table[id=io_today] tbody').append(ls);  
                        }
                    }
                        //tax today
                        var tax_today=result.tax_today;
                        if(tax_today.length>0){
                        $('table[id=tax_today] tbody').empty();
                        for(var i=0;i<tax_today.length;i++){
                            var x="";
                            
                            if(tax_today[i].wa_s>50000 && tax_today[i].waybill_status == 1){
                                tax_today[i].waybill_number="Not generated"
                                x="style=background-color:#F08080";
                            }
                            
                            var ls= '<tr>'+   
                                        '<td>'+tax_today[i].invoice_number+'</td>'+
                                        '<td>'+tax_today[i].pname+'</td>'+
                                        '<td>'+tax_today[i].item_name+'</td>'+
                                        // '<td>'+tax_today[i].date+'</td>'+
                                        '<td>'+tax_today[i].tot_qty+'</td>'+
                                        '<td>'+tax_today[i].tot_rate+'</td>'+
                                        '<td>'+formatNumber(parseFloat(tax_today[i].total_amount).toFixed(2))+'</td>'+
                                        '<td '+x+'>'+tax_today[i].waybill_number+'</td>'+
                                    '</tr>';
                                $('table[id=tax_today] tbody').append(ls);  
                        }
                    }

                        //pay today
                        var pay_today=result.pay_today;
                        if(pay_today.length>0){
                        $('table[id=pay_today] tbody').empty();
                        for(var i=0;i<pay_today.length;i++){
                            var ls= '<tr>'+   
                                        '<td>'+pay_today[i].partyname+'</td>'+
                                        '<td>'+pay_today[i].amt_recieved+'</td>'+
                                        '<td>'+pay_today[i].mop+'</td>'+
                                        '<td>'+pay_today[i].advice_upload+'</td>'+
                                        '<td>'+pay_today[i].reason_for_deduction+'</td>'+
                                        '<td>'+pay_today[i].deduction+'</td>'+
                                    '</tr>';
                                $('table[id=pay_today] tbody').append(ls);  
                        }
                    }

                    //mir today
                    var mir_today=result.mir_today;
                        if(mir_today.length>0){
                        $('table[id=mir_today] tbody').empty();
                        for(var i=0;i<mir_today.length;i++){
                            var x="";
                            
                            if(mir_today[i].pur_grn_no==null){
                                mir_today[i].pur_grn_no="No GRN"
                                x="style=background-color:#F08080";
                            }
                            var ls= '<tr>'+   
                                        '<td>'+mir_today[i].material_inward_number+'</td>'+
                                        '<td>'+mir_today[i].company+'</td>'+
                                        // '<td>'+mir_today[i].date+'</td>'+
                                        '<td>'+mir_today[i].item_name+'</td>'+
                                        '<td>'+mir_today[i].qty+'</td>'+
                                        // '<td>'+mir_today[i].driver_name+'</td>'+
                                        '<td>'+mir_today[i].received_by+'</td>'+
                                        '<td '+x+'>'+mir_today[i].pur_grn_no+'</td>'+
                                    '</tr>';
                                $('table[id=mir_today] tbody').append(ls);  
                        }
                    }

                        //mat dis today
                        var mat_dis_today=result.mat_dis_today;
                        // if(mat_dis_today.length>0)
                        //     $('table[id=dis_today] tbody').empty();
                        
                        for(var i=0;i<mat_dis_today.length;i++){
                            var x="";
                            var y="";
                            var w="";
                            if(mat_dis_today[i].gatepass_number==null){
                                mat_dis_today[i].gatepass_number="NO MGP No."
                                x="style=background-color:#F08080";
                            }
                            if(mat_dis_today[i].material_outward_number==null){
                                mat_dis_today[i].material_outward_number="NO MOR No.";
                                y="style=background-color:#F08080";
                            }
                            if(mat_dis_today[i].wa_s>50000 && mat_dis_today[i].waybill_status==1){
                                mat_dis_today[i].waybill_number="Not generated.";
                                w="style=background-color:#F08080";
                            }
                            var ls1= '<tr>'+   
                                        '<td>'+mat_dis_today[i].challan_number+'</td>'+
                                        '<td></td>'+
                                        '<td '+y+'>'+mat_dis_today[i].material_outward_number.replace(/,/g,'<br>')+'</td>'+
                                        '<td '+x+'>'+mat_dis_today[i].gatepass_number.replace(/,/g,'<br>')+'</td>'+
                                        '<td>'+mat_dis_today[i].partyname+'</td>'+
                                        '<td>'+mat_dis_today[i].good_desc+'</td>'+
                                        '<td>'+mat_dis_today[i].good_qty+'</td>'+
                                        '<td '+w+'>'+mat_dis_today[i].waybill_number+'</td>'+
                                    '</tr>';
                                    $('table[id=dis_today] tbody').append(ls1); 
                        }
                        var mat_idc_today=result.mat_idc_today;
                        // if(mat_idc_today.length>0)
                        //     $('table[id=dis_today] tbody').empty();
                        for(var i=0;i<mat_idc_today.length;i++){
                            var x="";
                            var y="";
                            
                            if(mat_idc_today[i].gatepass_number==null){
                                mat_idc_today[i].gatepass_number="NO MGP No."
                                x="style=background-color:#F08080";
                            }
                            if(mat_idc_today[i].material_outward_number==null){
                                mat_idc_today[i].material_outward_number="NO MOR No.";
                                y="style=background-color:#F08080";
                            }
                            
                            var ls1=  '<tr>'+   
                                        '<td></td>'+
                                        '<td>'+mat_idc_today[i].challan_number+'</td>'+
                                        '<td '+y+'>'+mat_idc_today[i].material_outward_number.replace(/,/g,'<br>')+'</td>'+
                                        '<td '+x+'>'+mat_idc_today[i].gatepass_number.replace(/,/g,'<br>')+'</td>'+
                                        '<td>'+mat_idc_today[i].partyname+'</td>'+
                                        '<td>'+mat_idc_today[i].good_desc+'</td>'+
                                        '<td>'+mat_idc_today[i].good_qty+'</td>'+
                                        '<td ></td>'+
                                    '</tr>';
                                    $('table[id=dis_today] tbody').append(ls1); 
                        }
                        
                            //prod today
                    //     var prod_today=result.prod_today;
                    //     if(prod_today.length>0){
                    //     $('table[id=prod_today] tbody').empty();
                    //     for(var i=0;i<prod_today.length;i++){
                    //         var ls= '<tr>'+   
                    //                     '<td>'+prod_today[i].machine+'</td>'+
                    //                     '<td>'+prod_today[i].referencename+'</td>'+
                    //                     '<td>'+prod_today[i].creative_name+'</td>'+
                    //                     '<td>'+prod_today[i].item_name+'</td>'+
                    //                     '<td>'+prod_today[i].actual+'</td>'+
                    //                 '</tr>';
                    //             $('table[id=prod_today] tbody').append(ls);  
                    //     }
                    // }
                    var dailyprepresslog=result.dailyprepresslog;
                        if(dailyprepresslog.length>0){
                            $('table[id=dailyprepresslog] tbody').empty();
                        for(var i=0;i<dailyprepresslog.length;i++){
                            var ls= '<tr>'+   
                                        '<td>'+dailyprepresslog[i].job_number+'</td>'+
                                        '<td>'+dailyprepresslog[i].referencename+'</td>'+
                                        '<td>'+dailyprepresslog[i].item_name+'</td>'+
                                        '<td>'+dailyprepresslog[i].creative_name+'</td>'+
                                        '<td>'+dailyprepresslog[i].element_name+'</td>'+
                                        '<td>'+dailyprepresslog[i].e_plate_size+'</td>'+
                                        '<td>'+dailyprepresslog[i].total_plates+'</td>'+
                                        '<td>'+dailyprepresslog[i].planned_plates+'</td>'+
                                        '<td>'+dailyprepresslog[i].actual+'</td>'+
                                        '<td>'+dailyprepresslog[i].wastage+'</td>'+
                                        '<td>'+dailyprepresslog[i].reason+'</td>'+
                                    '</tr>';
                                $('table[id=dailyprepresslog] tbody').append(ls);  
                        }
                    }
                    var dailyprocesslog=result.dailyprocesslog;
                        if(dailyprocesslog.length>0){
                            $('table[id=dailyprocesslog] tbody').empty();
                        for(var i=0;i<dailyprocesslog.length;i++){
                            var ls= '<tr>'+   
                                        '<td>'+dailyprocesslog[i].job_number+'</td>'+
                                        '<td>'+dailyprocesslog[i].referencename+'</td>'+
                                        '<td>'+dailyprocesslog[i].item_name+'</td>'+
                                        '<td>'+dailyprocesslog[i].creative_name+'</td>'+
                                        '<td>'+dailyprocesslog[i].element_name+'</td>'+
                                        '<td>'+dailyprocesslog[i].total_plates+'</td>'+
                                        '<td>'+dailyprocesslog[i].planned_plates+'</td>'+
                                        '<td>'+dailyprocesslog[i].actual+'</td>'+
                                        '<td>'+dailyprocesslog[i].actual_11am+'</td>'+
                                        '<td>'+dailyprocesslog[i].actual_2pm+'</td>'+
                                        '<td>'+dailyprocesslog[i].actual_6pm+'</td>'+
                                    '</tr>';
                                $('table[id=dailyprocesslog] tbody').append(ls);  
                        }
                    }
                    var design_order=result.design_order;
                        if(design_order.length>0){
                            $('table[id=design_order] tbody').empty();
                        for(var i=0;i<design_order.length;i++){
                            var ls= '<tr>'+   
                                        '<td>'+design_order[i].do_number+'</td>'+
                                        '<td>'+design_order[i].referencename+'</td>'+
                                        '<td>'+design_order[i].io_number+'</td>'+
                                        '<td>'+design_order[i].itemname+'</td>'+
                                        '<td>'+design_order[i].no_pages+'</td>'+
                                        '<td>'+design_order[i].alloted+'</td>'+
                                    '</tr>';
                                $('table[id=design_order] tbody').append(ls);  
                        }
                    }
                    var purchase=result.purchase;
                        if(purchase.length>0){
                            $('table[id=purchase] tbody').empty();


                        for(var i=0;i<purchase.length;i++){

                            if(purchase[i].amount == null){
                                purchase[i].amount =0;
                            }
                            var ls= '<tr>'+   
                                        '<td>'+purchase[i].po_number+'</td>'+
                                        '<td>'+purchase[i].pr_no+'</td>'+
                                        '<td>'+purchase[i].vendor+'</td>'+
                                        '<td>'+purchase[i].item_name+'</td>'+
                                        '<td>'+purchase[i].qty+'</td>'+
                                        '<td>'+purchase[i].rate+'</td>'+
                                        '<td>'+formatNumber(parseFloat(purchase[i].amount).toFixed(2))+'</td>'+
                                    '</tr>';
                                $('table[id=purchase] tbody').append(ls);  
                        }
                    }

                    $('#ajax_loader_div').css('display','none');
                  }
                  
              });
             
    }
$("#reference_name").change(function(){
    var date=$("#date").val();
      var reference_name=$("#reference_name").val();
      var party_name=$("#party_name").val();
      tableGen(reference_name,party_name,date);
      $('#ajax_loader_div').css('display','block');
      $ref = $(this).val();
     
      $.ajax({
            url:"/report/fetch/client/api/"+$ref,
            type: "GET",
            success: function(result) {
              if (result) {
                 
                      $("#party_name").empty();
                      $("#party_name").append('<option value="0">Select Client</option>');
                      $.each(result, function(value,key) {
                          
                          $("#party_name").append('<option value="' + key.id + '">' + key.partyname + '</option>');
                      });
                      $('#ajax_loader_div').css('display','none');
                  }
            }
      });
  });


$("#party_name").change(function(){
    var date=$("#date").val();
      var reference_name=$("#reference_name").val();
      var party_name=$("#party_name").val();
      tableGen(reference_name,party_name,date);
});
$("#date").change(function(){
    var date=$("#date").val();
      var reference_name=$("#reference_name").val();
      var party_name=$("#party_name").val();
      tableGen(reference_name,party_name,date);
});
</script>
@endsection

@section('main_section')
    <section class="content">
        <div class="box-header with-border">
            <div class='box box-default'> <br>
                <div class="container-fluid">
                    <div class="row">
                        <form action="/template/daily/report" method="GET" id="form" enctype="multipart/form-data">
                            @csrf
                                <!-- <div class="col-md-4 {{ $errors->has('reference_name') ? 'has-error' : ''}}">
                                    <label>Reference Name<sup>*</sup></label>
                                    <select name="reference_name" id="reference_name" class="select2">
                                        <option value="0">Select Reference</option>
                                        @foreach($reference as $ref)
                                            <option value="{{$ref['id']}}">{{$ref['referencename']}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-4 {{ $errors->has('party_name') ? 'has-error' : ''}}">
                                    <label>Client Name<sup>*</sup></label>
                                    <select name="party_name" id="party_name" class="select2 party_name">
                                        <option value="0">Select Client</option>
                                        
                                    </select>
                                    {!! $errors->first('party_name', '<p class="help-block">:message</p>') !!}
                                </div> -->
                                <div class="col-md-3 {{ $errors->has('party_name') ? 'has-error' : ''}}">
                                    <label>Date<sup>*</sup></label>
                                    <input type="text" class="datepicker input-css" id="date" name="date">
                                  
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" formtarget="_blank"  style="margin-top: 20px;" id="downloadbtn" class="btn btn-primary downloadbtn">Print</button> 
                                </div>
                    </div>
                </div><br><br>

                <div class="row">
                    <div class="col-md-6">
                        <table>
                            <thead>
                               <tr>
                                    <th ></th>
                                    <th style="border:1px solid black;background-color:mediumturquoise">With Tax</th>
                               </tr>
                               <tr style="border:1px solid black">
                                    <th style="border-right:1px solid black">Total Sales for the Financial Year as on date 
                                        </th>
                                    <th style="border-left:1px solid black" class="with"></th>
                               </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="col-md-6">
                            <table>
                                <thead>
                                   <tr>
                                    
                                        <th style="border:1px solid black;background-color:mediumturquoise">Without Tax</th>
                                   </tr>
                                   <tr style="border:1px solid black">
                                        <th style="border-left:1px solid black" class="without"></th>
                                   </tr>
                                </thead>
                            </table>
                        </div>
                </div><br>
                <div class="row">
                        <div class="col-md-6">
                            <table>
                                <thead>
                            
                                   <tr style="border:1px solid black">
                                        <th style="border-right:1px solid black">Total Sales for the month as
                                                on date </th>
                                        <th style="border-left:1px solid black" class="mon_with"></th>
                                   </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="col-md-6">
                                <table>
                                    <thead>
                                      
                                       <tr style="border:1px solid black">
                                            <th style="border-left:1px solid black" class="mon_without"></th>
                                       </tr>
                                    </thead>
                                </table>
                            </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <table border="1" id="io_today">
                               <thead>
                                    <tr>
                                            <th colspan="6" style="text-align: center;background-color: navajowhite"> I.O. Engaged Today</th>
                                        </tr>
                                        <tr style="background-color: mediumturquoise">
                                            <th>Internal Order Number</th>
                                            <th>Party Name</th>
                                            <th>Item</th>
                                            <th>Job Quantity</th>
                                            <th><!-- Value -->Rate</th>
                                            <th>Amount</th>
                                            <!-- <th>Final Job Size</th> -->
                                        </tr>
                               </thead>
                               <tbody>
                                   <tr>
                                       <td colspan="6" style="text-align: center;">
                                            NO IO Today
                                       </td>
                                   </tr>
                               </tbody>
                            </table>
                        </div>
                    </div><br>
                    <div class="row">
                            <div class="col-md-12">
                                <table border="1" id="tax_today">
                                   <thead>
                                        <tr>
                                                <th colspan="7" style="text-align: center;background-color: navajowhite"> Party Billed Today</th>
                                            </tr>
                                            <tr style="background-color: mediumturquoise">
                                                <th>Tax Invoice No</th>
                                                <th>Client</th>
                                                <th>Item</th>
                                                <!-- <th>Date</th> -->
                                                <th>Qty.</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                                <th>Waybill Number</th>
                                            </tr>
                                   </thead>
                                   <tbody>
                                        <tr>
                                                <td colspan="7" style="text-align: center;">
                                                        NO Party Billed Today
                                                </td>
                                            </tr>
                                   </tbody>
                                </table>
                            </div>
                        </div><br>
                        <!-- <div class="row">
                                <div class="col-md-12">
                                    <table border="1" id="pay_today">
                                       <thead>
                                            <tr>
                                                    <th colspan="6" style="text-align: center;background-color: navajowhite"> Payment Received Today</th>
                                                </tr>
                                                <tr style="background-color: mediumturquoise">
                                                    <th>Client Name</th>
                                                    <th>Amount Received</th>
                                                    <th>Payment Mode</th>
                                                    <th>Advice Status</th>
                                                    <th>Deduction Status (If Any)</th>
                                                    <th>Deduction Amount</th>
                                                </tr>
                                       </thead>
                                       <tbody>
                                            <tr>
                                                    <td colspan="6" style="text-align: center;">
                                                            NO Payment Received Today
                                                    </td>
                                                </tr>
                                       </tbody>
                                    </table>
                                </div>
                            </div><br> -->
                            <div class="row">
                                    <div class="col-md-12">
                                        <table border="1" id="mir_today">
                                           <thead>
                                                <tr>
                                                        <th colspan="6" style="text-align: center;background-color: navajowhite"> Today MIR Done</th>
                                                    </tr>
                                                    <tr style="background-color: mediumturquoise">
                                                        <!-- <th>S.R. Number</th> -->
                                                        <th>Material Inward Number</th>
                                                        <th>Company Name</th>
                                                        <!-- <th>Date</th> -->
                                                        <th>Material/Items Name</th>
                                                        <th>Quantity</th>
                                                        <!-- <th>Driver Name</th> -->
                                                        <th>Recieved By</th>
                                                        <th>Purchase Grn No</th>
                                                    </tr>
                                           </thead>
                                           <tbody>
                                                <tr>
                                                        <td colspan="6" style="text-align: center;">
                                                             NO MIR Today
                                                        </td>
                                                    </tr>
                                           </tbody>
                                        </table>
                                    </div>
                                </div><br>
                                <div class="row">
                                        <div class="col-md-12">
                                            <table border="1" id="dis_today">
                                               <thead>
                                                    <tr>
                                                            <th colspan="8" style="text-align: center;background-color: navajowhite"> Material Dispatch Today</th>
                                                        </tr>
                                                        <tr style="background-color: mediumturquoise">
                                                            <!-- <th>S.R. Number</th>
                                                            <th>Client Name</th>
                                                            <th>Goods Description</th>
                                                            <th>Goods Quantity</th>
                                                            <th>MGP. No.</th>
                                                            <th>MOR No.</th>
                                                            <th>Waybill No</th> -->
                                                            <th>DCN No</th>
                                                              <th>IDC No</th>
                                                              <th>MOR No</th>
                                                              <th>MGP/RGP No</th>
                                                              <th>Party Name</th>
                                                              <th>Item Name</th>
                                                              <th>Qty</th>
                                                              <th>Waybill No</th>
                                                        </tr>
                                               </thead>
                                               <tbody>
                                                    <!-- <tr>
                                                            <td colspan="8" style="text-align: center;">
                                                                    NO Material Dispatch today
                                                            </td>
                                                        </tr> -->
                                               </tbody>
                                            </table>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                            <div class="col-md-12">
                                                <!-- <table border="1" id="prod_today">
                                                   <thead>
                                                        <tr>
                                                                <th colspan="6" style="text-align: center;background-color: yellow"> Production Done for the day</th>
                                                            </tr>
                                                            <tr style="background-color: mediumturquoise">
                                                                <th>Machine name</th>
                                                                <th>Client Name</th>
                                                                <th>Creative</th>
                                                                <th>Item name</th>
                                                                <th>Actual</th>
                                                             
                                                            </tr>
                                                   </thead>
                                                   <tbody>
                                                        <tr>
                                                                <td colspan="6" style="text-align: center;">
                                                                        NO Production Report today
                                                                </td>
                                                            </tr>
                                                   </tbody>
                                                </table> -->
                                                <table border="1" id="dailyprepresslog">
                                                   <thead>
                                                        <tr>
                                                            <th colspan="11" style="text-align: center;background-color: yellow"> Production Done for the day</th>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="11" style="text-align: center;background-color: navajowhite">Daily Plate Report</th>
                                                        </tr>
                                                            <tr style="background-color: mediumturquoise">
                                                                <th>Job Card Number</th>
                                                                <th>Reference Name</th>
                                                                <th>Item Name</th>
                                                                <th>Creative Name</th>
                                                                <th>Element Name</th>
                                                                <th>Plate Size</th>
                                                                <th>Total Plate Required</th>
                                                                <th>Planned Plates</th>
                                                                <th>Actual Plates</th>
                                                                <th>Wastage</th>
                                                                <th>Reason for Wastage</th>
                                                             
                                                            </tr>
                                                   </thead>
                                                   <tbody>
                                                        <!-- <tr>
                                                                <td colspan="11" style="text-align: center;">
                                                                        NO Daily Plate Report
                                                                </td>
                                                            </tr> -->
                                                   </tbody>
                                                </table>
                                                
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table border="1" id="dailyprocesslog">
                                                   <thead>
                                                        <tr>
                                                            <th colspan="11" style="text-align: center;background-color: navajowhite">Daily Production Report</th>
                                                        </tr>
                                                            <tr style="background-color: mediumturquoise">
                                                                <th>JC No.</th>
                                                                <th>Reference</th>
                                                                <th>Item</th>
                                                                <th>Creative</th>
                                                                <th>Element</th>
                                                                <th>Total Impression</th>
                                                                <th>Planned</th>
                                                                <th>Actual</th>
                                                                <th>11am</th>
                                                                <th>2pm</th>
                                                                <th>6pm</th>
                                                            </tr>
                                                   </thead>
                                                   <tbody>
                                                        
                                                   </tbody>
                                                </table>
                                                </div>
                                        </div><br>
                                         <div class="row">
                                            <div class="col-md-12">
                                                <table border="1" id="purchase">
                                                   <thead>
                                                        <tr>
                                                            <th colspan="7" style="text-align: center;background-color: navajowhite">Purchase Order Raised Today</th>
                                                        </tr>
                                                            <tr style="background-color: mediumturquoise">
                                                                <th>PO No</th>
                                                                  <th>PR No</th>
                                                                  <!-- <th>Indent No</th> -->
                                                                  <th>Vendor Name</th>
                                                                  <th> Item Name</th>
                                                                  <th>Qty</th>
                                                                  <th>Rate</th>
                                                                  <th>Amount</th>
                                                            </tr>
                                                   </thead>
                                                   <tbody>
                                                        
                                                   </tbody>
                                                </table>
                                                </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table border="1" id="design_order">
                                                   <thead>
                                                        <tr>
                                                            <th colspan="11" style="text-align: center;background-color: navajowhite">Design Orders Engaged Today</th>
                                                        </tr>
                                                            <tr style="background-color: mediumturquoise">
                                                                <th>DO No</th>
                                                                <th>Reference Name</th>
                                                                <th>IO Number</th>
                                                                <th>Item Name</th>
                                                                <th>No Of Pages</th>
                                                                <th>Work Allotted to</th>
                                                            </tr>
                                                   </thead>
                                                   <tbody>
                                                        
                                                   </tbody>
                                                </table>
                                                </div>
                                        </div><br>
            </div>
        </div>
      </section>
@endsection