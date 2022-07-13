
<!DOCTYPE html>
<html lang="en">

<head>

<link rel="stylesheet" href="/css/jobcard_template.css">
<style>
.page-break {
        page-break-inside: avoid;
}
</style>
</head>

<body>
        <div class="box">
                <div class="box-header with-border">
                        <div class="col" style="width:200px;height: 90px; ">
                       
                            <img src="./images/logo.jpg"  class="logopp" style="width:80px;">
                                
                        </div>
                       <div  style="float: right; margin-right: 10px;text-align: right;">
                        <p>&nbsp;</p>
                         @php
                            if(isset($job['job_number'])){echo $job['job_number']."<br>";}
                                else{echo "-";}
                               echo "Status: ".$job['status'];
                        @endphp
                    
                        </div>
                        
                </div>
                <div class="box-body">
                        <table class="tablestyle" style="width:100%;">
                                        <tr><th colspan="12" style="text-align:center"><h3 class="head">JOB CARD</h3></th></tr>
                                <tr>
                                        <td>
                                                <h4>Job Date </h4>
                                        </td>
                                        <td class="colcenter">
                                                <p id="small"> @php
                                                                if(isset($job['created_time'])){echo CustomHelpers::showDate($job['created_time'],'d-m-Y h:i:s A');}
                                                                else{echo "-";}
                                                        @endphp </p>
                                        </td>
                                        <td>
                                                <h4 id="book"> IO Date </h4>
                                        </td>
                                        <td>
                                                <p id="small"> @php
                                                                if(isset($job['io_created_time'])){echo CustomHelpers::showDate($job['io_created_time'],'d-m-Y h:i:s A');}
                                                                else{echo "-";}
                                                        @endphp </p>
                                        </td>
                                        <td>
                                                <h4 id="book"> IO Number </h4>
                                        </td>
                                        <td> @php
                                                if(isset($job['io_number'])){echo $job['io_number'];}
                                                else{echo "-";}
                                                @endphp</td>
                                        <td>
                                            <h4 id="book"> Delivery Date </h4>
                                    </td>
                                    <td>@php
                                                if(isset($job['delivery_date'])){echo CustomHelpers::showDate($job['delivery_date'],'d-m-Y');}
                                                else{echo "-";}
                                        @endphp</td>
                                    
                                </tr>
                                <tr>
                                        <td><h4>Open Size </h4></td>
                                        <td colspan="1"><p id="small">@php
                                             if(isset($job['open_size'])){echo $job['open_size'].' '.$job['dimension'];}
                                                                else{echo "-";}
                                        @endphp</p></td>
                                        <td><h4> Close Size </h4></td>
                                        <td colspan="1"><p id="small">@php
                                                        if(isset($job['close_size'])){echo $job['close_size'].' '.$job['dimension'];}
                                                                           else{echo "-";}
                                                   @endphp</p></td>
                                        <td><h4> Item Category </h4></td>
                                        <td colspan="1"><p id="small">@php
                                                        if(isset($job['name'])){echo $job['name'];}
                                                                           else{echo "-";}
                                                        if($job['other_item_desc']){echo " : ".$job['other_item_desc'];}
                                                   @endphp</p></td>
                                        <td><h4> Quantity </h4></td>
                                        <td colspan="1"><p id="small">@php
                                                        if(isset($job['job_qty'])){echo $job['job_qty'];}
                                                                           else{echo "-";}
                                                                          
                                                   @endphp</p></td>
                                </tr>
                                <tr>
                                        <td>
                                                <h4> Creative Name </h4>
                                        </td>
                                       
                                        <td colspan="4">
                                                <p>@php
                                                                if(isset($job['creative_name'])){echo $job['creative_name'];}
                                                                else{echo "-";}
                                                        @endphp</p>
                                        </td>
                                        <td>
                                                        <h4> Marketing Person </h4>
                                                </td>
                                        <td colspan="3">
                                                        <p>@php
                                                                        if(isset($job['marketing_name'])){echo $job['marketing_name'];}
                                                                        else{echo "-";}
                                                                @endphp</p>
                                                </td>
                                </tr>
                                <tr>
                                        <td>
                                                <h4>Reference Name</h4>
                                        </td>
                                        <td colspan="7">
                                                <p id="small"> @php
                                                                if(isset($job['partyname'])){echo $job['partyname'];}
                                                                else{echo "-";}
                                                        @endphp
                                                       </p>
                                        </td>
                                </tr>
                                <tr>
                                        <td colspan="1">
                                                <h4>Paper By</h4>
                                        </td>
                                        <td colspan="2">
                                                <p id="small">@php
                                                                if(isset($job['is_supplied_paper'])){echo $job['is_supplied_paper'];}
                                                                else{echo "-";}
                                                        @endphp</p>
                                        </td>
                                        <td colspan="1">
                                                <h4 id="book">Plate By</h4>
                                        </td>
                                        <td colspan="2">
                                                <p id="small">@php
                                                                if(isset($job['is_supplied_plate'])){echo $job['is_supplied_plate'];}
                                                                else{echo "-";}
                                                        @endphp</p>
                                        </td>
                                        <td colspan="1">
                                            <h4 id="book">Sample Recieved</h4>
                                    </td>
                                    <td colspan="1">
                                            <p id="small">@php
                                                        if(isset($job['job_sample_received'])  && $job['job_sample_received']==1){echo "Yes";}
                                                        else if(isset($job['job_sample_received'])  && $job['job_sample_received']==0){echo "No";}
                                                        else{echo "-";}
                                                @endphp</p>
                                    </td>
                                </tr>
                                
                               
                        </table>
                </div>
                <!-- /.box-body -->

        </div>
        <br>
       <div class="box-body">
<table  class="tablestyle" style="width:100%;">
        <tr>
                <td>
                        <h4>Remark</h4>
                </td>
                <td colspan="">
                        <p id="small"> @php
                                        if(isset($job['remarks'])){echo $job['remarks'];}
                                        else{echo "-";}
                                @endphp
                               </p>
                </td>
        </tr>
        <tr>
                        <td>
                                <h4>Packing Instruction Details</h4>
                        </td>
                        <td colspan="">
                                <p id="small"> @php
                                                if(isset($job['description'])){echo $job['description'];}
                                                else{echo "-";}
                                        @endphp
                                       </p>
                        </td>
                </tr>
</table>
       </div>
      {{-- @if (!count($element)==0) --}}
        <h5 class="">ELEMENT PRINTING DETAIL</h5>
        <div class="box-body">
                <table class="tablestyle" style="width:100%;"> 
                        <tr> 
                        <th></th>
                        <th>Plate Size</th> 
                        <th>Plate Set</th> 
                        <th>Front Color</th> 
                        <th>Back Color</th>
                        <th>IMP/Plate</th> 
                        <th>No. Of Pages </th>
                        </tr> 
                 
                        
                       
                        @foreach ($element as $item)
                     @if ($item['elementfeeder_type_id']==1)
                     <tr>
                             <td><b>Text</b></td>
                             <td>{{$item['plate_size']}}</td>
                             <td>{{$item['plate_sets']}}</td>
                             <td>{{$item['front_color']}}</td>
                             <td>{{$item['back_color']}}</td>
                             <td>{{$item['impression_per_plate']}}</td>
                             <td>{{$item['no_of_pages']}}</td>
                     </tr>
                     @endif
                     <tr>
                     @if ($item['elementfeeder_type_id']==2)
                     <tr>
                             <td><b>Cover</b></td>
                             <td>{{$item['plate_size']}}</td>
                             <td>{{$item['plate_sets']}}</td>
                             <td>{{$item['front_color']}}</td>
                             <td>{{$item['back_color']}}</td>
                             <td>{{$item['impression_per_plate']}}</td>
                             <td>{{$item['no_of_pages']}}</td>
                     </tr>
                     @endif
                     @if ($item['elementfeeder_type_id']==3)
                     <tr>
                             <td><b>Posteen</b></td>
                             <td>{{$item['plate_size']}}</td>
                             <td>{{$item['plate_sets']}}</td>
                             <td>{{$item['front_color']}}</td>
                             <td>{{$item['back_color']}}</td>
                             <td>{{$item['impression_per_plate']}}</td>
                             <td>{{$item['no_of_pages']}}</td>
                     </tr>
                     @endif
                     @if ($item['elementfeeder_type_id']==4)
                     <tr>
                             <td><b>Seperator</b></td>
                             <td>{{$item['plate_size']}}</td>
                             <td>{{$item['plate_sets']}}</td>
                             <td>{{$item['front_color']}}</td>
                             <td>{{$item['back_color']}}</td>
                             <td>{{$item['impression_per_plate']}}</td>
                             <td>{{$item['no_of_pages']}}</td>
                     </tr>
                     @endif
                     @if ($item['elementfeeder_type_id']==5)
                     <tr>
                             <td><b>Hard Case Stand</b></td>
                             <td>{{$item['plate_size']}}</td>
                             <td>{{$item['plate_sets']}}</td>
                             <td>{{$item['front_color']}}</td>
                             <td>{{$item['back_color']}}</td>
                             <td>{{$item['impression_per_plate']}}</td>
                             <td>{{$item['no_of_pages']}}</td>
                     </tr>
                     @endif 
                         
                       
                @endforeach
                </table>    
        </div>
      {{-- @else --}}

      {{-- @endif --}}
        <!-- /.box-body -->

    </div>
    <h5 class="">RAW MATERIAL DETAIL</h5>
    <div class="box-body">
                <table class="tablestyle" style="width:100%;"> 
                        @php
                        $flag1=0;
                    @endphp
                    @foreach ($raw as $item)
                    @if ($item['is_option']=="Paper"))
                            @php
                                $flag1=1;
                            @endphp
                    @endif
                    @endforeach
                            
                            @if ($flag1==1)
                            <tr> 
                                <th></th>
                                <th>Material Size</th> 
                                <th>Paper Type</th> 
                                <th>Paper GSM</th>
                                <th>Paper Brand</th> 
                                <th>No. Of Sheets</th>
                                </tr> 
                         
                            @endif  
                                
                                
                                
                               
                        @foreach ($raw as $item)
                                @if ($item['is_option']=="Paper")
                                @if ($item['element_type_id']==1)
                                <tr>
                                        <td><b>Text</b></td>
                                        <td>{{$item['paper_size']}}</td>
                                        <td>{{$item['name']}}</td>
                                        <td>{{$item['paper_gsm']}}</td>
                                        <td>{{$item['paper_brand']}}</td>
                                        <td>{{$item['no_of_sheets']}}</td>
                                </tr>
                                @endif
                                <tr>
                                @if ($item['element_type_id']==2)
                                <tr>
                                        <td><b>Cover</b></td>
                                        <td>{{$item['paper_size']}}</td>
                                        <td>{{$item['name']}}</td>
                                        <td>{{$item['paper_gsm']}}</td>
                                        <td>{{$item['paper_brand']}}</td>
                                        <td>{{$item['no_of_sheets']}}</td>
                                </tr>
                                @endif
                                @if ($item['element_type_id']==3)
                                <tr>
                                        <td><b>Posteen</b></td>
                                        <td>{{$item['paper_size']}}</td>
                                        <td>{{$item['name']}}</td>
                                        <td>{{$item['paper_gsm']}}</td>
                                        <td>{{$item['paper_brand']}}</td>
                                        <td>{{$item['no_of_sheets']}}</td>
                                </tr>
                                @endif
                                @if ($item['element_type_id']==4)
                                <tr>
                                        <td><b>Seperator</b></td>
                                        <td>{{$item['paper_size']}}</td>
                                        <td>{{$item['name']}}</td>
                                        <td>{{$item['paper_gsm']}}</td>
                                        <td>{{$item['paper_brand']}}</td>
                                        <td>{{$item['no_of_sheets']}}</td>
                                </tr>
                                @endif
                                @if ($item['element_type_id']==5)
                                <tr>
                                        <td><b>Hard Case Stand</b></td>
                                        <td>{{$item['paper_size']}}</td>
                                        <td>{{$item['name']}}</td>
                                        <td>{{$item['paper_gsm']}}</td>
                                        <td>{{$item['paper_brand']}}</td>
                                        <td>{{$item['no_of_sheets']}}</td>
                                </tr>
                                @endif
                                @if ($item['element_type_id']==6)
                                <tr>
                                        <td><b>{{$job['other_item_name']}}</b></td>
                                        <td>{{$item['paper_size']}}</td>
                                        <td>{{$item['name']}}</td>
                                        <td>{{$item['paper_gsm']}}</td>
                                        <td>{{$item['paper_brand']}}</td>
                                        <td>{{$item['no_of_sheets']}}</td>
                                </tr>
                                @endif
                                @endif
                                 
                               
                        @endforeach
                        </table>    
    </div>
    <br>
    <div class="box-body">
        <table class="tablestyle" style="width:100%;"> 
                @php
                    $flag=0;
                @endphp
                @foreach ($raw as $item)
                @if ($item['is_option']=="Other"))
                        @php
                            $flag=1;
                        @endphp
                @endif
                @endforeach
                        
                        @if ($flag==1)
                        <tr> 
                                <th></th>
                                <th>Item Name</th> 
                                <th>Item Size</th> 
                                <th>Item Thickness</th>
                                <th>Other Specification</th> 
                                </tr> 
                        @endif
                 
                        
                       
                        @foreach ($raw as $item)
                                @if ($item['is_option']=="Other"))
                                @if ($item['element_type_id']==1)
                                <tr>
                                        <td><b>Text</b></td>
                                        <td>{{$item['item_name']}}</td>
                                        <td>{{$item['size'].' '.$item['size_dimension']}}</td>
                                        <td>{{$item['thickness'].' '.$item['thickness_dimension']}}</td>
                                        <td>{{$item['specification']}}</td>
                                </tr>
                                @endif
                                <tr>
                                @if ($item['element_type_id']==2)
                                <tr>
                                        <td><b>Cover</b></td>
                                        <td>{{$item['item_name']}}</td>
                                        <td>{{$item['size'].' '.$item['size_dimension']}}</td>
                                        <td>{{$item['thickness'].' '.$item['thickness_dimension']}}</td>
                                        <td>{{$item['specification']}}</td>
                                </tr>
                                @endif
                                @if ($item['element_type_id']==3)
                                <tr>
                                        <td><b>Posteen</b></td>
                                        <td>{{$item['item_name']}}</td>
                                        <td>{{$item['size'].' '.$item['size_dimension']}}</td>
                                        <td>{{$item['thickness'].' '.$item['thickness_dimension']}}</td>
                                        <td>{{$item['specification']}}</td>
                                </tr>
                                @endif
                                @if ($item['element_type_id']==4)
                                <tr>
                                        <td><b>Seperator</b></td>
                                        <td>{{$item['item_name']}}</td>
                                        <td>{{$item['size'].' '.$item['size_dimension']}}</td>
                                        <td>{{$item['thickness'].' '.$item['thickness_dimension']}}</td>
                                        <td>{{$item['specification']}}</td>
                                </tr>
                                @endif
                                @if ($item['element_type_id']==5)
                                <tr>
                                        <td><b>Hard Case Stand</b></td>
                                        <td>{{$item['item_name']}}</td>
                                        <td>{{$item['size'].' '.$item['size_dimension']}}</td>
                                        <td>{{$item['thickness'].' '.$item['thickness_dimension']}}</td>
                                        <td>{{$item['specification']}}</td>
                                </tr>
                                @endif
                                @if ($item['rawelement_type_id']==6)
                                <tr>
                                        <td><b>{{$job['other_item_name']}}</b></td>
                                        <td>{{$item['item_name']}}</td>
                                        <td>{{$item['size'].' '.$item['size_dimension']}}</td>
                                        <td>{{$item['thickness'].' '.$item['thickness_dimension']}}</td>
                                        <td>{{$item['specification']}}</td>
                                </tr>
                                @endif
                                @endif   
                @endforeach
                </table>    
</div>
    
    <div class="box page-break">
        <div class="box-header with-border">
            <h5 class="">BINDING PROCESS</h5>
        </div>
        <div class="box-body">
            
                @php
                foreach ($bind as $key => $value) {
                        echo "<h4>".$value['item_name'] ." </h4>";
                $myValue = json_decode(json_encode($value['value']));
                $myRemark = json_decode(json_encode($value['remark']));
                $val= json_decode($myValue);
                $rem= json_decode($myRemark); 
                
                echo '<table class="tablestyle" style="width:100%;"><tr><td><h4>Binding Element</h4></td><td><h4>YES/NO</h4></td><td><h4>Remarks</h4></td></tr>';
                foreach ($val as $key => $value) {
                       
                                $item_key=$key;
                                $item_value=$value;
                       
                        foreach($rem as $item=>$res){
                                if(($item == $item_key && $res!=NULL) || ($item == $item_key && $item_value=="Yes")){
                                        $x=$res;
                                        echo "<tr><td>".$item_key . " </td><td>".$item_value . " </td><td>".$x."</td></tr>";
                                       
                                } 
                        
                }
                        
                        //$remark=$rem[$key];
                       
                }
               
                echo '</table>';
                }
                @endphp 
               
              
              <br><br>
              <table class="noBorder" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; border: none; width:100%;border:none">
                        <tr style="border: none;">
                                <th style="border: none;">CREATED BY:</th>
                                <th style="text-align:right;border: none;">APPROVED BY:</th>
                        </tr>
                        <tr style="border: none;">
                        <td style="border: none;">{{$created['created_by_name']}}</td>
                                <td style="border: none;"></td>
                        </tr>
                </table>
            
        </div>
        <!-- /.box-body -->

    </div>
    <!-- /.box -->
   

    <htmlpagefooter name="page-footer">
	Page {PAGENO} of {nb}
</htmlpagefooter>

</body>

</html>