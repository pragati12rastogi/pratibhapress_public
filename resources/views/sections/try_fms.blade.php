@extends($layout)

@section('title', 'Order To Collection FMS')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Order To Collection FMS</a></li> 
@endsection
@section('css')

<link rel="stylesheet" href="/css/responsive.bootstrap.css">    

@endsection
@section('js')
{{-- <script src="/js/dataTables.responsive.js"></script> --}}
<meta name="csrf-token" content="{{ csrf_token() }}" />
  <script>
    $(document).ready(function() {
      $('.yellow').parent().addClass('yellow');
      $('.red').parent().addClass('red');
 
    $('#fms_table').DataTable({
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          // "ajax": "/order/to/collection/fms/api",
          "ajax": {
            "url": "/order/to/collection/fms/api",
            "type": "get",
            // "data": {'_token': CSRF_TOKEN},
        },
          "columns": [
            //step1
              {"data":"io_number"},
              {"data":"io_date"},
              {"data":"reference_name"},
              {"data":"item_category"},
              // {"data":"creative_name"},
              {"data":"qty"},
              {"data":"io_type"},
              {"data":"delivery_date"},
              //step2
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      // var dt = data.actual_job_date;
                      var dt = data.planned_job_date;
                     return dt;
                    }
                },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.actual_job_date;
                      var jc=data.job_number;
                      var io_type=data.io_type;
                      return dt;
                     
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.job_number;
                      var io_type=data.io_type;
                      if(io_type=="Scrap Sale" || io_type=="Outsource" || io_type=="Supplies" || io_type=="Services"  ){
                        return "<div class='black'></div>"
                      }
                      else{
                        return jc;
                      }
                        
                      // return dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                    }
                },
              //step 3
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.planned_pr_date;
                      if(jc)
                      {return jc;}
                      else{return "-";}
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var pr=data.pur_req;
                      if(pr){
                        return pr.replace(/,/g,'<br>');;
                      }
                      else{
                        return "-";
                      }
                     
                    }
                },  //dummy data
               
              //step 4
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      // var dt = data.actual_delivery_date;
                      var dt = data.planned_delivery_date;
                      var jc=data.delivery_challan_number;
                      return dt;
                     
                    }
                },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.actual_delivery_date;
                      var dt1 = data.planned_delivery_date;
                      var jc=data.delivery_challan_number;
                    return dt;
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.delivery_challan_number;
                      var io_type=data.io_type;
                      if(io_type=="Services"){
                        return "<div class='black'></div>"
                      }
                      else if(jc){
                        return jc.replace(/,/g,'<br>');;
                      }
                      else{
                        return "-";
                      }
                     
                    }
                },
              // {
              //       "targets": [ -1 ],
              //       "data" : function(data,type,full,meta)
              //       {
              //         var desc=data.delivery_challan_good_desc;
              //         if(desc){
              //           return desc.replace(/,/g,'<br>');;
              //         }
              //         else{
              //           return "-";
              //         }
                    
              //       }
              //   },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.delivery_challan_good_qty;
                      if(desc){
                        return desc.replace(/,/g,'<br>');;
                      }
                      else{
                        return "-";
                      }
                    }
                },
                { //todo
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var sum_qty=data.sum_qty_dc;
                      var desc=data.delivery_date;
                      var qty=data.qty;
                      if(desc){
                        return qty-sum_qty;
                      }
                      else{
                        return "-";
                      }

                    }
                },
              //step 5
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.actual_cpo_date;
                      var dt = data.planned_cpo_date;
                      return dt;
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.actual_cpo_date;
                      var dt1 = data.planned_cpo_date;
                      return dt;
                    }
                }, 
              { 
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var is_po=data.is_po_provided;
                      var po_num=data.po_number;
                      console.log(po_num);
                      
                      if((is_po==1)){
                        return po_num;
                      }
                      else if((is_po==0)){
                        return "Verbal";
                      }
                      else{
                        return '-';
                      }

                    }
                },
              //invoice  step 6

              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      // var dt = data.actual_delivery_date;
                      var dt = data.planned_tax_date;
                      return dt;
                     
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.actual_tax_date;
                      var dt1 = data.planned_tax_date;
                      var jc=data.invoice_number;
                      return dt;
                    }  
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                      if(io_type=="K Sampling" || io_type=="FOC"){
                        return "<div class='black'></div>"
                      }
                      else if(jc){
                        return jc.replace(/,/g,'<br>');;
                      }
                      else{
                        return "-";
                      }
                     
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.tax_qty;
                      if(desc){
                        return desc.replace(/,/g,'<br>');;
                      }
                      else{
                        return "-";
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.tax_amount;
                      if(desc){
                        return desc.replace(/,/g,'<br>');
                      }
                      else{
                        return "-";
                      }
                    }
                },
                { //todo
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var sum_qty=data.sum_qty_tax;
                      var qty=data.qty;
                      if(sum_qty){
                        return qty-sum_qty;
                      }
                      else{
                        return "-";
                      }

                    }
                },
                { //todo
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var sum_qty=data.sum_qty_tax;
                      var desc=data.sum_qty_dispatch;
                      var qty=data.qty;
                      if(!sum_qty){
                        sum_qty=0;
                        desc=0;
                      }
                      if(!desc){
                        sum_qty=0;
                      }
                      if(desc || sum_qty){
                        return sum_qty-desc;
                      }
                      else{
                        return "-";
                      }

                    }
                },
              { //todo
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var waybill=data.tax_waybill;
                      var way_dc=data.challan_waybill;
                      if(waybill && way_dc){
                        var x=way_dc + "," +waybill;
                        x=x.replace(/1/g,'Yes');
                        x=x.replace(/0/g,'No');
                        return x.replace(/,/g,'<br>');
                      }
                      else if(!waybill && way_dc){
                        way_dc=way_dc.replace(/1/g,'Yes');
                        way_dc=way_dc.replace(/0/g,'No');
                        return way_dc.replace(/,/g,'<br>');
                      }
                      else if(waybill && !way_dc){
                        waybill=waybill.replace(/1/g,'Yes');
                        waybill=waybill.replace(/0/g,'No');
                        return waybill.replace(/,/g,'<br>');
                      }
                      else{
                        return "-";
                      }

                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.way_challan_number;
                      var desc1=data.way_tax_number;
                      if(desc && desc1){
                        var x=desc + "," +desc1;
                        return  x.replace(/,/g,'<br>');
                      }
                      else if(desc && !(desc1)){
                        return desc.replace(/,/g,'<br>');
                      }
                      else if(!(desc) && (desc1)){
                        return desc1.replace(/,/g,'<br>');
                      }
                      else{
                        return "-";
                      }
                    }
                },
                //asn grn
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.asn_number;
                      var desc1=data.grn_number;
                      if(desc && desc1){
                        var x=desc + "," +desc1;
                        return  x.replace(/,/g,'<br>');
                      }
                      else if(desc && !(desc1)){
                        return desc.replace(/,/g,'<br>');
                      }
                      else if(!(desc) && (desc1)){
                        return desc1.replace(/,/g,'<br>');
                      }
                      else{
                        return "-";
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var doc=data.docket_number;
                      if(doc){
                        return doc;
                      }
                      else{
                        return "-";
                      }
                        
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "-";
                      
                     
                    }
                },  //dummy data

                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.io_status;
                    
                        return jc;
                      
                     
                    }
                },  //dummy data
            ],
            "columnDefs": [
              {"scrollY": "500px"}, 
              {"scrollX": false},
              
            ],
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
              // $(document).find('.yellow').parent().addClass('yellow');
              // $(document).find('.red').parent().addClass('red').html('sh');
              // console.log("hello");
              // console.log($(document).find('.yellow').parent());

             
              
              
          }
    
          
        });

    });
   // $('.dataTables_scrollHead').css('display','none'); 

   
  </script>
@endsection
<style>

        .hsn_table {
          width: 100%;
          overflow-x: scroll;
        }
        #fms_table {
          width: 100%;
          overflow-x: auto;
        }

        .hsn_table thead tr  td {
          border: 1px solid black;
          text-align: center;
          border-style: solid;
          /* padding: 0!important ; */
        }
        td{
          padding: 0!important ;
          vertical-align: middle !important; 
          line-height: 1 !important;
        }
        .hsn_table thead tr{
          height: 20px;
        }
     
        .hsn_table tbody tr td{
          height: 20px;
        }
        /* .hsn>tbody tr td{
          line-height: 0.8;
        } */
        /* .hsn_table tr:nth-child(even){background-color: #f2f2f2;} */
        
        
        .hsn_table th {
          text-align: top;
          border: 1px solid black;
          
        }
        .hsn_table td {
          text-align: center;
          border: 1px solid black;
          
        }
        .yellow{
          background-color: yellow;
            /* height: 76px;
            width: 176px;
            padding-top: 25px; */
        }
        table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting {
    padding-right: 122px !important;
}
        .red{
          color:red;
            /* height: 10px;
            width: 176px; */
            opacity: .9;
            text-align: center;
            /* padding-top: 25px; */
        }
        .green{
          background-color: yellowgreen;
            /* height: 76px;
            width: 176px;
            padding-top: 25px; */
        }
        .white{
            background-color: white;
            /* height: 76px;
            width: 176px;
            padding-top: 25px; */
        }
        .black{
            background-color: gray;
            /* height: 76px;
            width: 176px;
            padding-top: 25px; */
        }
        .blue{
            background-color: #1E90FF;
        }
        #yellow{
            background-color: #FFD700;
            
        }
        #red{
            background-color: red;
           
        }
        #green{
            background-color: yellowgreen;
           
        }
        #blue{
            background-color: #1E90FF;
        }
        .fms{
            border: 1px solid black; !important;
            font-size:14px;
        }
        .plan{
            background-color: grey;
        }
        .actual{
            background-color: white; 
        }
        /* thead.fixed-header
        {
          display: table;
          width: 100%;
          overflow: auto;
        } */
       /* tbody.fixed-body
       {
         display: block;
         width: 100%;
         height: 400px;
         overflow: auto;
       } */
       /* tbody.fixed-body tr{
        display: table;
          width: 100%;
          table-layout: fixed;
          position: relative;
       }
       tbody.fixed-body tr td {
         
          width: 100%;
         
      } */
        </style>
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->

        <div id="show" ></div>
        <div class="box" style="overflow-x:auto;overflow-y:auto;">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="fms_table" class="table  table-striped hsn_table fms table-fixed">
                    <thead>
                        <tr style="background-color:#87CEFA;">
                            <td></td>
                            <td colspan="6">Step 1</td>
                            <td colspan="3">Step 2</td>
                            <td colspan="3">Step 3</td>
                           
                            <td colspan="5">Step 4</td>
                            <td colspan="3">Step 5</td>
                            <td colspan="8">Step 6</td>
                            <td colspan="1">Step 7</td>
                            <td colspan="3">Step 8</td>
                            <td colspan="3">Step 9</td>
                            <td colspan="3">Step 10</td>
                            {{-- <td colspan="3">Step 11</td>
                            <td colspan="2">Step 12</td>
                            <td colspan="3">Step 13</td> --}}
                            <td colspan="3">Step 14</td>
                        </tr> 
                             <tr>
                                    <td>WHAT</td>
                                    <td colspan="6">Engage Internal Order</td>
                                    <td colspan="3">Creation of Job Card</td>
                                    <td colspan="3">Raise PR to Store Head for material requirement for the job</td>
                                   
                                    <td colspan="5">Creation of Delivery Challan</td>
                                    <td colspan="3">Creation of Client P.O.</td>
                                    <td colspan="8">Creation of invoice</td>
                                    <td colspan="1" id="yellow">Can dispatch happen witdout Waybill?</td>
                                    <td colspan="3" id="red">Waybill Entry</td>
                                    <td colspan="3" class="blue">ASN/GRN</td>
                                    <td colspan="3" id="green">Invoice Dispatch</td>
                                    {{-- <td colspan="3">Follow up for delivery of material & invoice witd tde client & establish payment date</td>
                                    <td colspan="2">Challan receiving from client to be put back in file</td>
                                    <td colspan="3">Follow up for payment till payment received</td> --}}
                                    <td colspan="3">Closing of Order</td>
                                </tr> 
                             <tr>
                                    <td>WHO</td>
                                    <td colspan="6">CRM: Iffat Rehman</td>
                                    <td colspan="3">GM: Arvind Mishra</td>
                                    <td colspan="3">Sachin Kumar Vaish,Binding Head,Production Head</td>
                                    {{-- <td colspan="3">Binding Head</td>
                                    <td colspan="3">Production Head	</td> --}}
                                   
                                    <td colspan="5">Order Facilitator: Kishan Kumar</td>
                                    <td colspan="3">Order Facilitator: Vikas Chandra</td>
                                    <td colspan="8">Kishan Kumar</td>
                                    <td colspan="1" id="yellow">Automated</td>
                                    <td colspan="3" id="red">Kishan Kumar</td>
                                    <td colspan="3" class="blue">Kishan Kumar</td>
                                    <td colspan="3" id="green">Order Facilitator: VIkas Chandra</td>
                                    {{-- <td colspan="3">CRM: Vaishali Chaurasia</td>
                                    <td colspan="2">Order Facilitator: VIkas Chandra</td>
                                    <td colspan="3">CRM: Vaishali Chaurasia</td> --}}
                                    <td colspan="3">PC</td>
                                </tr> 
                                <tr>
                                        <td>HOW</td>
                                        <td colspan="6">IO Google Form</td>
                                        <td colspan="3">Job Card Google Form</td>
                                        <td colspan="3">PR Google Form</td>
                                        {{-- <td colspan="3">PR Google Form		</td>
                                        <td colspan="3">PR Google Form</td> --}}
                                       
                                        <td colspan="5">Delivery Challan Google Form</td>
                                        <td colspan="3">Client P.O. Google Form</td>
                                        <td colspan="8">Invoice Google Form</td>
                                        <td colspan="1" id="yellow">From Bill Issue Register</td>
                                        <td colspan="3" id="red">Waybill Entry Google Form</td>
                                        <td colspan="3" class="blue">ASN/GRN Entry on Google Form</td>
                                        <td colspan="3" id="green">Invoice Google Form</td>
                                        {{-- <td colspan="3">Telephone/Email</td>
                                        <td colspan="2">Manually by hand</td>
                                        <td colspan="3">Email/telephone</td> --}}
                                        <td colspan="3">On Order closed google form</td>
                                    </tr>
                                    <tr>
                                            <td>WHEN</td>
                                            <td colspan="6">On confirmation of order by Sales Team on whatsapp group</td>
                                            <td colspan="3">Within 2 hours of creation of Internal Order</td>
                                            <td colspan="3">Within 3 hours of receiving Job Card</td>
                                            {{-- <td colspan="3">Within 3 hours of receiving Job Card</td>
                                            <td colspan="3">Within 3 hours of receiving Job Card</td> --}}
                                            
                                       
                                            <td colspan="5">on dispatch of material</td>
                                            <td colspan="3">Before 1 Day of Making Challan</td>
                                            <td colspan="8">Within 30 mins of creation of Delivery Challan</td>
                                            <td id="yellow">Within 5 minutes of creation of invoice</td>
                                            <td colspan="3" id="red">Within 15 minutes of issue of Tax Invoice</td>
                                            <td colspan="3" class="blue">Within 1 day of T.I. Issue</td>
                                            <td colspan="3" id="green">on Dispatch of Material/Within 1 day of creation of Tax Invoice		</td>
                                            {{-- <td colspan="3">Within 2 days of Invoice Dispatch</td>
                                            <td colspan="2">Within 1 day of Delivery Challan Creation</td>
                                            <td colspan="3">Every day till payment is received</td> --}}
                                            <td colspan="3">Within 1 day of Payment received</td>
                                    </tr> 
                        <tr style="background-color:#87CEFA;">
                          <td><b>IO No.</b></td>
                          <td><b>IO Date</b></td>
                          <td><b>Party Name</b></td>
                          <td><b>Item Name</b></td>
                          {{-- <td>Creative Name</td> --}}
                          <td><b>Job Quantity</b></td>
                          <td><b>IO Type</b></td>
                          <td><b>Delivery Date</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Job Card No.</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>P.R. Number</b></td>
                          {{-- <td class="plan"><b>Pln</b></td>
                          <td class="plan"><b>Ac</b></td>
                          <td><b>P.R. Number</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>P.R. Number</b></td> --}}
                         
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Challan No.</b></td>
                          {{-- <td>Challan Description</td> --}}
                          <td><b>Challan quantity</b></td>
                          <td><b>Balance quantity</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Client P.O. Number</b></td>
                          <td><b>Pln</b></td>
                          <td><b>Ac</b></td>
                          <td><b>PO No.</b></td>
                          <td><b>Invoice No.</b></td>
                          <td><b>Invoice quantity</b></td>
                          <td><b>Invoice Amount</b></td>
                          <td><b>Balance Unbilled Order Quantity</b></td>
                          <td><b>Balance Unbilled Dispatched Quantity</b></td>
                          <td><b>Yes/NO?</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Waybill No.</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>ASN/GRN No.</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Docket No.</b></td>
                          {{-- <td class="plan">Pln</td>
                          <td class="actual">Ac</td>
                          <td>Payment date</td>
                          <td class="plan">Pln</td>
                          <td class="actual">Ac</td>
                          <td>Payment Status</td>
                          <td>Payment Amount</td>
                          <td>Payment Balanace</td> --}}
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Status</b></td> 
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
               
                  </table>
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection