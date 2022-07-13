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
                                <th>Time</th>
                                <th>Actual</th>
                                <Th>Reason</Th>
                                
                             </thead>
                             <tbody>
                               <tr>
                             
                                    <td>11 AM</td>
                                    <td>{{$dis['actual_11am']}}</td>
                                    <td>{{$dis['reason_11am']}}</td>
                                  
                               </tr>
                               <tr>
                             
                             <td>2 PM</td>
                             <td>{{$dis['actual_2pm']}}</td>
                             <td>{{$dis['reason_2pm']}}</td>
                           
                        </tr>
                        <tr>
                             
                             <td>6 PM</td>
                             <td>{{$dis['actual_6pm']}}</td>
                             <td>{{$dis['reason_6pm']}}</td>
                           
                        </tr>
                             </tbody>
                         </table>
                           
                    