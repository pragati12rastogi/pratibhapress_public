<link rel="stylesheet" href="/css/common.css">
   <style>
   .div{
     border:1px solid black;
   }
   </style>  
   @php 
   $text="2";
   @endphp        
                           
                         <table class="table" border="1">
                             <thead>
                                 <th>Is Option</th>
                                    @if($raw['is_option']=="Paper")
                                    <th>Paper Size</th>
                                    <th>Paper Type</th>
                                    <th>Paper GSM</th>
                                    <th>Paper Mill</th>
                                    <th>Paper Brand</th>
                                    <th>No. Of Sheets</th>
                                    @endif
                                    @if($raw['is_option']=="Other")
                                    <th>Item Name</th>
                                    <th>Item Size</th>
                                    <th>Size Unit</th>
                                    <th>Item Thickness</th>
                                    <th>Thickness Unit</th>
                                    <th>Other Specification</th>
                                    @endif
                                
                             </thead>
                             <tbody>
                               <tr>
                               <td>{{$raw['is_option']}}</td>
                               @if($raw['is_option']=="Paper")
                                    <td>{{$raw['paper_size']}}</td>
                                    <td>{{$raw['name']}}</td>
                                    <td>{{$raw['paper_gsm']}}</td>
                                    <td>{{$raw['paper_mill']}}</td>
                                    <td>{{$raw['paper_brand']}}</td>
                                    <td>{{$raw['no_of_sheets']}}</td>
                            
                                    @endif
                                    @if($raw['is_option']=="Other")
                                    <td>{{$raw['item_name']}}</td>
                                    <td>{{$raw['size']}}</td>
                                    <td>{{$raw['size_dimension']}}</td>
                                    <td>{{$raw['thickness']}}</td>
                                    <td>{{$raw['thickness_dimension']}}</td>
                                    <td>{{$raw['specification']}}</td>
                                    @endif
                               </tr>
                             </tbody>
                         </table>
                           
                    