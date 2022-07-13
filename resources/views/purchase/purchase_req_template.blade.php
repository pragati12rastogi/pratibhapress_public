
<!DOCTYPE html>
<html lang="en">

<head>

        <link rel="stylesheet" href="css/io_templates.css">
        {{-- <style>
        .noBorder {
    border:none !important;
}
.tablestyle1 tr td{
border:none;
border-bottom: none;
font-size: 13px;
}
.tablestyle1 td{
        
}
        </style> --}}
</head>
<body>
                    
    <div class="box">  
        <div class="box-body">
                                <div class="box-header with-border">
                                    <div class="col" style="width:200px;height: 40px; ">
                                   
                                        <img src="./images/logo.jpg"  class="logopp" style="width:80px;">
                                            
                                    </div>
                                   <div  style="float: right; margin-right: 10px;text-align: right;">
                                    <p>&nbsp;</p><p style="margin-left:10px;text-align:center;width:260px"><b><u>PURCHASE INDENT</u></b></p>
                                    {{$return[0]->purchase_req_number}}
                                    </div>
                                    
                            </div>
                                <table class="tablestyle1" style="width:100%;">
                                    <tr>
                                        <th >
                                            Date
                                        </th>
                                        <td>
                                          
                                                @php
                                                $dateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata')); 
                                                echo $dateTime->format("d/m/y  H:i A"); 
                                            @endphp
                                        </td>
                                       
                                    </tr>
                                    <tr>
                                        <th>Requested by</th>
                                        <td>{{$return[0]->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Requirement Type</th>
                                        <td>{{$return[0]->item_req_for}}</td>
                                    </tr>
                                    <tr>
                                        <th>Internal Order Number</th>
                                        <td>
                                            @php
                                                if($return[0]->io_number){
                                                    echo $return[0]->io_number;
                                                }
                                                else{
                                                    echo "----";
                                                }
                                            @endphp
                                            </td>
                                    </tr>
                                    <tr>
                                        <th>Required Date</th>
                                        <td>
                                        @php
                                        if($return[0]->required_date){
                                            echo $return[0]->required_date;
                                        }
                                        else{
                                            echo "----";
                                        }
                                    @endphp
                                    </td>
                                    </tr>
                                    <tr>
                                        <th>To be shipped to</th>
                                        <td>
                                            @if ($return[0]->item_req_for=="Direct Supply Internal Order")
                                                {{$return[0]->reference_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    
                                </table><br>
        </div>
        <!-- /.box-body -->
    
    </div>
<br>
<div class="box">  
    <div class="box-body">
                 <div class="row">
                     <div class="col-md-12">
                        <center><h4 style="text-align:center">ITEM DETAILS</h4></center>
                     </div>
                 </div>
                            <table class="tablestyle1" style="width:100%;">
                                <tr>
                                    <th>
                                        <p>Item Description</p>
                                    </th>
                                    <th>
                                        <p>Quantity </p>
                                    </th>
                                    <th>
                                        <p>Required Item Quantity Unit</p>
                                    </th>
                                   
                                   
                                </tr>
                                
                                    @foreach ($return as $item)
                                    <tr>
                                    <td>{{$item->item_desc}}</td>
                                    <td>{{$item->item_qty}}</td>
                                    <td>{{$item->uom_name}}</td>
                                </tr>
                                    @endforeach
                                   
                                   
                               
                                
                            </table><br><br>

                            
    </div>
    <!-- /.box-body -->

</div>


        <!-- /.box -->
      <br>
    
        <div class="box">
                <div class="box-body">
                        <table class="noBorder" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; border: none; width:100%;border:none">
                            
                            <tr style="border: none;">
                                <th style="text-align:right;border: none;width:100px">Created By
                                </th>
                                        <th style="text-align:right;border: none;">Store In-Charge
                                        </th>
                                       
                                </tr>
                                <tr>
                                    <td style="text-align:center;border: none;">{{$return[0]->created_by}}
                                    </td>
                                           
                                </tr>
                               
                        </table>
                </div>
                <!-- /.box-body -->

        </div>
        <!-- /.box -->
       {{-- <div class="row">
               <div class="col-md-12">
       <div class="col-md-6"style="float:left">
                <h4>Created By</h4>

                <h4>Approved By</h4>
       </div>
               </div>
       </div> --}}




</body>

</html>