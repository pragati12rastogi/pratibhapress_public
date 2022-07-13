
@extends($layout)
@section('title', __('jobcard.view'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('jobcard.view')}}</i></a></li>
@endsection

@section('css')
    <style>
        .help-block{
        color:red;
        }
        table.th{
            text-indent: 50px;
        }
</style>
@endsection


@section('main_section')
    <section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
<!-- Default box -->
   
    <div class="box-header with-border">
        <div class='box box-default'><br>
            <div class="container-fluid">
                  
                    <h2>Job Card Table</h2> 
                    @php
                                            if($job['status']=="Open"){echo "<button class='btn btn-success'>".$job['status']."</button>";}
                                            if($job['status']=="Closed"){echo "<button class='btn btn-danger'>".$job['status']."</button>";}
                                    @endphp
                               <br>   <br>    
                           <div class="row">
                                   <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                                <thead>
                                                                  @if ($job['status']=="Closed")
                                                                  <tr style="background-color:thistle"><th>Closed Date : </th>
                                                                     
                                                                     <td>@php
                                                                         if(isset($job['closed_date'])){echo date('d-m-Y h:i:s A',strtotime($job['closed_date']));}
                                                                         @endphp
                                                                     </td>
                                                                     <th>Closed By : </th>
                                                                     <td>@php
                                                                           echo $closed_by;
                                                                            
                                                                     @endphp</td>
                                                                 </tr>
                                                                     <br> 
                                                                  @endif
                                                                  
                                                                 <tr><th>Job Date : </th>
                                                                     
                                                                     <td>@php
                                                                         if(isset($job['created_time'])){echo CustomHelpers::showDate($job['created_time'],'d-m-Y h:i:s A');}
                                                                         else{echo "-";}
                                                                         @endphp
                                                                     </td>
                                                                     <th>Job Number : </th>
                                                                     <td>@php
                                                                             if(isset($job['job_number'])){echo $job['job_number'];}
                                                                             else{echo "-";}
                                                                     @endphp</td>
                                                                 </tr>
                                                              
                                                                 <tr>
                                                                     <th>IO Date</th>
                                                                     <td>
                                                                             @php
                                                                              if(isset($job['io_created_time'])){echo CustomHelpers::showDate($job['io_created_time'],'d-m-Y h:i:s A');}
                                                                             else{echo "-";}
                                                                     @endphp 
                                                                     </td>
                                                                     <th>IO Number : </th>
                                                                     <td>@php
                                                                         if(isset($job['io_number'])){echo $job['io_number'];}
                                                                         else{echo "-";}
                                                                         @endphp</td>
                                 
                                                                        
                                                                 </tr>
                                 
                                                                
                                 
                                                                 <tr>
                                                                         <th>Open Size : </th>
                                                                         <td>@php
                                                                                 if(isset($job['open_size'])){echo $job['open_size'].' '.$job['dimension'];}
                                                                                                     else{echo "-";}
                                                                             @endphp</td>
                                                                         <th>Close Size : </th>
                                                                         <td>@php
                                                                                 if(isset($job['close_size'])){echo $job['close_size'].' '.$job['dimension'];}
                                                                                                    else{echo "-";}
                                                                            @endphp</td>
                                                                            
                                                                 </tr>
                                 
                                                               
                                                                 <tr>
                                                                         <th>Item Category : </th>
                                                                         <td>@php
                                                                                 if(isset($job['name'])){echo $job['name'];}
                                                                                                    else{echo "-";}
                                                                                                   
                                                                         if($job['other_item_name']){echo " : ".$job['other_item_name'];}
                                                                                           
                                                                        
                                                                            @endphp</td>
                                                                     <th>Quantity : </th>
                                                                     <td>
                                                                         @php
                                                                         if(isset($job['job_qty'])){echo $job['job_qty'];}
                                                                                             else{echo "-";}
                                                                             @endphp 
                                                                     </td>
                                                                    
                                                                 </tr>
                                                                
                                                                 <tr>
                                                                     <th>Marketing Person : </th>
                                                                     <td>
                                                                             @php
                                                                             if(isset($job['marketing_name'])){echo $job['marketing_name'];}
                                                                             else{echo "-";}
                                                                     @endphp  
                                                                     </td>
                                                                     <th>Party Name : </th>
                                                                     <td>
                                                                             @php
                                                                             if(isset($job['partyname'])){echo $job['partyname'];}
                                                                             else{echo "-";}
                                                                     @endphp 
                                                                     </td>
                                                                 </tr>
                                                                
                                                                 <tr>
                                                                         <th>Paper By : </th>
                                                                         <td>
                                                                                 @php
                                                                                 if(isset($job['is_supplied_paper'])){echo $job['is_supplied_paper'];}
                                                                                 else{echo "-";}
                                                                         @endphp
                                                                         </td>
                                                                         <th>Plate By</th>
                                                                         <td>
                                                                                 @php
                                                                                             if(isset($job['is_supplied_plate'])){echo $job['is_supplied_plate'];}
                                                                                             else{echo "-";}
                                                                                     @endphp
                                                                         </td>
                                                                     </tr>
                                                                     <tr>
                                                                             <th>Creative Name : </th>
                                                                             <td>
                                                                                     @php
                                                                                     if(isset($job['creative_name'])){echo $job['creative_name'];}
                                                                                     else{echo "-";}
                                                                             @endphp
                                                                             </td>
                                                                         <th>Sample Recieved</th>
                                                                         <td>
                                                                                 @php
                                                                                 if(isset($job['job_sample_received'])  && $job['job_sample_received']==1){echo "Yes";}
                                                                                 else if(isset($job['job_sample_received'])  && $job['job_sample_received']==0){echo "No";}
                                                                                 else{echo "-";}
                                                                         @endphp 
                                                                         </td>
                                                                     </tr>
                                                                     <tr><th style="vertical-align: middle;">Remark : </th>
                                                                     <td>
                                                                             @php
                                                                             if(isset($job['remarks'])){echo $job['remarks'];}
                                                                             else{echo "-";}
                                                                     @endphp
                                                                         </td>
                                                                     <th style="vertical-align: middle;">Packing Instruction Details</th>
                                                                     <td>
                                                                             @php
                                                                             if(isset($job['description'])){echo $job['description'];}
                                                                             else{echo "-";}
                                                                     @endphp
                                                                         </td>
                                                                     </tr>
                                                               </thead>
                                                             </table>
                                   </div>
                           </div>
                         
            </div>
        </div>
    </div>
    <div class="box-header with-border">
            <div class='box box-default'><br>
                <div class="container-fluid">
                       
                            <h3>Element Form Details</h3>        
                               <div class="row">
                                       <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                                <tr> 
                                                                        <th></th>
                                                                        <th>Plate Size</th> 
                                                                        <th>Plate Set</th> 
                                                                        <th>Front Color</th> 
                                                                        <th>Back Color</th>
                                                                        <th>IMP/Plate</th> 
                                                                        <th>Nos. Of Pages </th>
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
                               </div>
                              
                </div>
            </div>
    </div>  
    <div class="box-header with-border">
            <div class='box box-default'><br>
                <div class="container-fluid">
                     
                            <h3>Raw Material Details</h3>        
                      <div class="row">
                              <div class="col-md-12">
                                        <table class="table table-bordered table-striped"> 
                                                @php
                                                $flag1=0;
                                            @endphp
                                            @foreach ($raw as $item)
                                            @if ($item['is_option']=="Paper")
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
                      </div>
                      <div class="row">
                              <div class="col-md-12">
                                        <table class="table table-bordered table-striped"> 
                                                        @php
                                                            $flag=0;
                                                        @endphp
                                                        @foreach ($raw as $item)
                                                        @if ($item['is_option']=="Other")
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
                                                                        @if ($item['is_option']=="Other")
                                                                                @if ($item['element_type_id']==1)
                                                                                <tr>
                                                                                        <td><b>Text</b></td>
                                                                                        <td>{{$item['item_name']}}</td>
                                                                                        <td>{{$item['size'].' '.$item['size_dimension']}}</td>
                                                                                        <td>{{$item['thickness'].' '.$item['thickness_dimension']}}</td>
                                                                                        <td>{{$item['specification']}}</td>
                                                                                </tr>
                                                                                @endif
                                                                      
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
                      </div>
                            
                </div>
            </div>
    </div>  

    <div class="box-header with-border">
                            
                            @php
                            foreach ($bind as $key => $value) {
                                   
                            $myValue = json_decode(json_encode($value['value']));
                            $myRemark = json_decode(json_encode($value['remark']));
                            $val= json_decode($myValue);
                            $rem= json_decode($myRemark); 
                            
                            echo '<div class="box-header with-border"><div class="box box-default"><div class="container-fluid"><h3>Binding Process</h3><div class="row"><h3>'.$value['item_name'].'</h3><div class="col-md-12"><table class="table table-bordered table-striped"><tr><th>Binding Element</th><th>Value</th><th>Remarks</th></tr>';
                            
                                foreach ($val as $key => $value) {
                                   
                                            $item_key=$key;
                                            $item_value=$value;
                                            if($item_value=="Yes"){
                                                $color="#e6fffa";
                                            }
                                            if($item_value=="No"){
                                                $color="#ffcccc";
                                            }
                                   
                                    foreach($rem as $item=>$res){
                                            if($item == $item_key){
                                                    $x=$res;
                                                    echo "<tr style='background-color:".$color."'><td>".$item_key ."</td><td >".$item_value."</td><td>".$x."</td></tr>";
                                                   
                                            } 
                                    }
                            
                                    
                                    //$remark=$rem[$key];
                                   
                            }
                           
                            echo '</table></div></div></div></div></div>';
                            }
                            @endphp     
                              </div>
                              
                              
               
</section>
@endsection
