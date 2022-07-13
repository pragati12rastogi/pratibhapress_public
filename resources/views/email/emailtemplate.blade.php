<!DOCTYPE html>
<html lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    .page-break {
            page-break-inside: avoid;       
    }
    @page {
        margin-top:80px;
        footer: page-footer;
        font-family: "Times New Roman", Times, serif;
        font-size: 10px;
}
body{
     margin: auto 15%;
}
table, th, td {
    font-size: 12px !important;
  border: 1px solid black;
  border-collapse: collapse;
}

    </style>
     <script>
            
    </script>
       
</head>
<body>

    <div style="text-align: center;margin-bottom: 20px">
        <h3>Daily Report ({{date("d-m-Y",strtotime($sel_date))}})</h3>
    </div>
    <div>
        <h4>Total Sales For The Financial Year Till Date </h4>
        <table style="width: 100%">
            <tr>
                <td style="font-weight:bold;">With Tax :</td>
                <td>{{$tstax}}</td>
                <td style="font-weight:bold;">Without Tax :</td>
                <td>{{$tsnotax}}</td>
            </tr>
        </table>
    </div>
    <div>
        <h4>Total Sales For The Month Till Date </h4>
        <table style="width: 100%">
            <tr>
                <td style="font-weight:bold;">With Tax :</td>
                <td>{{$tsmonth}}</td>
                <td style="font-weight:bold;">Without Tax :</td>
                <td>{{$tsmonth_notax}}</td>
            </tr>
        </table>
    </div>
    <!-- Internal order list -->
    <div>
        <h4>Internal Orders Engaged Today </h4>
        <table style="width: 100%">
            <tr>
                
                    <th>IO Number</th>
                    <th>Reference Name</th>
                    <th>Item Name</th>
                    <th>IO Qunatity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                
            </tr>
            <tbody>
                @foreach($total_IO as $io)
                <tr>
                    <td>{{$io['io_number']}}</td>
                    <td>{{$io['referencename']}}</td>
                    <td>{{$io['item_name']}}</td>
                    <td>{{$io['IO_qty']}}</td>
                    <td>{{$io['rate_per_qty']}}</td>
                    <td>{{number_format($io['amount'],2)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- IO list End -->
    <div>
        <h4>Tax Invoices Generated Today</h4>
        <table style="width: 100%">
            <tr>
                
                    <th>Invoice Number</th>
                    <th>Party Name</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>Waybill Number</th>
            </tr>
            <tbody>
                @foreach($total_tax as $tt)
                <tr>
                    <td>{{$tt['invoice_number']}}</td>
                    <td>{{$tt['partyname']}}</td>
                    <td>{{$tt['item_name']}}</td>
                    <td>{{$tt['tot_qty']}}</td>
                    <td>{{$tt['tot_rate']}}</td>
                    <td>{{number_format($tt['total_amount'],2)}}</td>
                    <td style="background: {{$tt['wa_s']>50000 && $tt['waybill_status']== 1 ?'#f64343':''}}" >{{$tt['waybill_number']}}</td>
                    
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <h4>Material Received Today</h4>
        <table style="width: 100%">
            <tr>
                <th>Material Inward Number</th>
                <th>Company</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Recieved By</th>
                <th>Purchase Grn No</th>
            </tr>
            <tbody>
                @foreach($mir_today as $mir)
                <tr>
                    <td>{{$mir['material_inward_number']}}</td>
                    <td>{{$mir['company']}}</td>
                    <td>{{$mir['item_name']}}</td>
                    <td>{{$mir['qty']}}</td>
                    <td>{{$mir['received_by']}}</td>
                    <td style="background: {{empty($mir['pur_grn_no'])?'#f64343':''}}">{{$mir['pur_grn_no']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <h4>Material Dispatched Today</h4>
        <table width="100%" >
            <tr>
              <th width="15%">DCN No</th>
              <th width="12%">IDC No</th>
              <th width="12%">MOR No</th>
              <th width="12%">MGP/RGP No</th>
              <th width="15%">Party Name</th>
              <th width="15%">Item Name</th>
              <th width="10%">Qty</th>
              <th width="15%">Waybill No</th>
              
            </tr>
            <tbody>
                @foreach($mat_dispatch_dc as $dc)
                <tr>
                    <td >@php  echo str_replace(",","<br>",$dc['challan_number']);  @endphp
                    </td>
                    <td ></td>
                    <td >@php  echo str_replace(",","<br>",$dc['material_outward_number']);  @endphp</td>
                    <td > @php  echo str_replace(",","<br>",$dc['gatepass_number']);  @endphp</td>
                    <td >{{$dc['partyname']}}</td>
                    <td >@php  echo str_replace(",","<br>",$dc['good_desc']);  @endphp</td>
                    <td >{{$dc['good_qty']}}</td>
                    <td style="background: {{$dc['wa_s']>50000 && $dc['waybill_status']==1 ?'#f64343':''}}" >{{$dc['waybill_number']}}</td>
                </tr>
                @endforeach

                @foreach($mat_idc_today as $idc)
                <tr>
                    <td></td>
                    <td>@php  echo str_replace(",","<br>",$idc['challan_number']);  @endphp</td>
                    <td>@php  echo str_replace(",","<br>",$idc['material_outward_number']);  @endphp</td>
                    <td>@php  echo str_replace(",","<br>",$idc['gatepass_number']);  @endphp</td>
                    <td>{{$idc['partyname']}}</td>
                    <td> @php  echo str_replace(",","<br>",$idc['good_desc']);  @endphp</td>
                    <td>{{$idc['good_qty']}}</td>
                    <td  ></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Production Done for the day -->
    <div>
        <h4>Daily Plate Report</h4>
        <table style="width: 100%">
            <tr>
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
            <tbody>
                @foreach($pre_press as $pre)
                <tr>
                    <td>{{$pre['job_number']}}</td>
                    <td>{{$pre['referencename']}}</td>
                    <td>{{$pre['item_name']}}</td>
                    <td> {{$pre['creative_name']}}</td>
                    <td>{{$pre['element_name']}}</td>
                    <td>{{$pre['e_plate_size']}}</td>
                    <td>{{$pre['total_plates']}}</td>
                    <td>{{$pre['planned_plates']}}</td>
                    <td>{{$pre['actual']}}</td>
                    <td>{{$pre['wastage']}}</td>
                    <td>{{$pre['reason']}}</td>
                   
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>

    <div>
        <h4>Daily Production Report</h4>
        <table style="width: 100%">
            <tr>
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
            <tbody>
                @foreach($dailyprocess as $dp)
                <tr>
                    <td>{{$dp['job_number']}}</td>
                    <td>{{$dp['referencename']}}</td>
                    <td>{{$dp['item_name']}}</td>
                    <td>{{$dp['creative_name']}}</td>
                    <td>{{$dp['element_name']}}</td>
                    <td>{{$dp['total_plates']}}</td>
                    <td>{{$dp['planned_plates']}}</td>
                    <td>{{$dp['actual']}}</td>
                    <td>{{$dp['actual_11am']}}</td>
                    <td>{{$dp['actual_2pm']}}</td>
                    <td>{{$dp['actual_6pm']}}</td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>

    <div>
        <h4>Purchase Order Raised Today</h4>
        <table style="width: 100%">
            <tr>
              <th>PO No</th>
              <th>PR No</th>
              <!-- <th>Indent No</th> -->
              <th>Vendor Name</th>
              <th> Item Name</th>
              <th>Qty</th>
              <th>Rate</th>
              <th>Amount</th>
              
            </tr>
            <tbody>
                @foreach($pur as $dp)
                <tr>
                    <td>{{$dp['po_number']}}</td>
                    <td>{{$dp['pr_no']}}</td>
                    <td>{{$dp['vendor']}}</td>
                    <td>{{$dp['item_name']}}</td>
                   <td> {{$dp['qty']}}</td>
                    <td>{{$dp['rate']}}</td>
                   <td> {{number_format($dp['amount'],2)}}</td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>

    <div>
        <h4>Design Orders Engaged Today</h4>
        <table style="width: 100%">
            <tr>
                
                    <th>DO No</th>
                    <th>Reference Name</th>
                    <th>IO Number</th>
                    <th>Item Name</th>
                    <th>No Of Pages</th>
                    <th>Work Allotted to</th>
                    
                    
            </tr>
            <tbody>
                @foreach($do as $d)
                <tr>
                    <td>{{$d['do_number']}}</td>
                    <td>{{$d['referencename']}}</td>
                    <td>{{$d['io_number']}}</td>
                    <td>{{$d['itemname']}}</td>
                    <td>{{$d['no_pages']}}</td>
                    <td>{{$d['alloted']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
</body>

</html>