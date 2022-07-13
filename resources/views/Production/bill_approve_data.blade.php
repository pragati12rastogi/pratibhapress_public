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
                                 <th>Level 2</th>
                                 <th>Approval Amount</th>
                                 <th>Approval Date</th>
                                 <th>Level 1</th>
                                 <th>Approval Amount</th>
                                 <th>Approval Date</th>
                                 <th>Action</th>
                                
                             </thead>
                             <tbody>
                                 @foreach ($bind as $item)
                                     <tr {{$item['levels']!=NULL ? 'style=background-color:#90EE90' :'style=background-color:#FFFFE0' }}>
                                         <td>
                                                @if ($item['levelss'])
                                                    {{$item['levelss']}}
                                                @else 
                                                    {{"-"}}
                                                @endif
                                         </td>
                                         <td>
                                                @if ($item['levelss'])
                                                    {{$item['pay_approve2']}}
                                                @else 
                                                    {{"-"}}
                                                @endif
                                         </td>
                                         <td>
                                                @if ($item['levelss'])
                                                    {{$item['pay_approve__date2']}}
                                                @else 
                                                    {{"-"}}
                                                @endif
                                         </td>
                                         <td>
                                                @if ($item['levels'])
                                                    {{$item['levels']}}
                                                @else 
                                                    {{"-"}}
                                                @endif
                                         </td>
                                         <td>
                                                @if ($item['levels'])
                                                    {{$item['pay_approve1']}}
                                                @else 
                                                    {{"-"}}
                                                @endif
                                         </td>
                                         <td>
                                                @if ($item['levels'])
                                                    {{$item['pay_approve__date1']}}
                                                @else 
                                                    {{"-"}}
                                                @endif
                                         </td>
                                                <td>
                                                

                                               @foreach($level1 as $key)
                                               @if($key==$auth)
                                                    @if ($item['levels'])
                                                            {{"-"}}
                                                    @else 

                                                            <a onclick="cancel_alert_dailog({{$item['id']}},{{$bill_act_amt['amount']}},{{$amt['amount_app']}},{{$auth}},{{$text}},{{$item['id']}})"><button class="btn btn-xs btn-dander">Approve against Level2</button></a>
                                                    @endif
                                               @endif
                                               @endforeach
                                                
                                               
                                                </td>
                                     </tr>
                                 @endforeach
                             </tbody>
                         </table>
                           
                    