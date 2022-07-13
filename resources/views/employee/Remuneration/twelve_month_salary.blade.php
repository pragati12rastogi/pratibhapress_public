<link rel="stylesheet" href="/css/common.css">
   <style>
   .div{
     border:1px solid black;
   }
   </style> 

      
        <div class="row" >
          <table id="" class="table table-bordered table-striped" >
              <thead>
                <tr>
                  <th>April</th>
                  <th>May</th>
                  <th>June</th>
                  <th>July</th>
                  <th>August</th>
                  <th>September</th>
                  <th>October</th>
                  <th>November</th>
                  <th>December</th>
                  <th>January</th> 
                  <th>February</th>
                  <th>March</th>
                </tr>
              </thead>
              <tbody>
                @if($salary)
                  <tr>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "April")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "May")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "June")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "July")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "August")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "September")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "October")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "November")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "December")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "January")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "February")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($salary as $sal)
                        @if($sal['month_name'] == "March")
                           {{$sal['net_salary']}}
                        @endif
                      @endforeach
                    </td>
                  </tr>
                @else
                  <td colspan="12" align="center">No Salary paid</td>
                @endif
              </tbody>
              <!-- <tbody>
                <td>
                  <?php
                    // $apr_tt = 0;
                      // if($da){
                      //     foreach($da as $dearness){
                      //       if($dearness['month_name'] == 'April'){
                      //         $apr_tt += $dearness['amount_inc'];
                      //       }
                      //     }
                      //   }
                      // $apr_inc_tt=0;
                      //   if($increment){
                      //     foreach($increment as $inc){
                      //       if($inc['month_name'] == 'April'){
                      //         if($inc['amount_type'] == "cr"){
                      //           $apr_inc_tt += $inc['amount'];
                      //         }else{
                      //           $apr_inc_tt -= $inc['amount'];
                      //         }
                      //       }
                      //     }
                      //   }
                      // $april_total= $salary['total_sal']+$apr_tt+$apr_inc_tt;
                      // echo $april_total;
                  ?>
                </td>
                <td>
                  <?php
                //     $may_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'May'){
                //               $may_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $may_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'May'){
                //               if($inc['amount_type'] == "cr"){
                //                 $may_inc_tt += $inc['amount'];
                //               }else{
                //                 $may_inc_tt -= $inc['amount'];

                //               }
                //             }
                //           }
                //         }
                //         $may_total = $april_total+$may_tt+$may_inc_tt;
                //         echo $may_total;
                //   ?>
                // </td>
                // <td>
                //   <?php
                //     $jun_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'June'){
                //               $jun_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $jun_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'June'){
                //               if($inc['amount_type'] == "cr"){
                //                 $jun_inc_tt += $inc['amount'];
                //               }else{
                //                 $jun_inc_tt -= $inc['amount'];

                //               }
                //             }
                //           }
                //         }
                //       $jun_total=  $may_total+$jun_tt+$jun_inc_tt;
                //       echo $jun_total;
                  ?>
                </td>
                <td>
                  <?php
                //     $jul_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'July'){
                //               $jul_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $jul_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'July'){
                //               if($inc['amount_type'] == "cr"){
                //                 $jul_inc_tt += $inc['amount'];
                //               }else{
                //                 $jul_inc_tt -= $inc['amount'];

                //               }
                //             }
                //           }
                //         }
                //       $july_total=  $jun_total+$jul_tt+$jul_inc_tt;
                //       echo $july_total;
                //   ?>
                // </td>
                // <td>
                //   <?php
                //     $aug_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'August'){
                //               $aug_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $aug_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'August'){
                //               if($inc['amount_type'] == "cr"){
                //                 $aug_inc_tt += $inc['amount'];
                //               }else{
                //                 $aug_inc_tt -= $inc['amount'];
                //               }
                //             }
                //           }
                //         }
                //       $august_total=  $july_total+$aug_tt+$aug_inc_tt;
                //       echo $august_total;
                //   ?>
                // </td>
                // <td>
                //   <?php
                //     $sep_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'September'){
                //               $sep_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $sep_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'September'){
                //               if($inc['amount_type'] == "cr"){
                //                 $sep_inc_tt += $inc['amount'];
                //               }else{
                //                 $sep_inc_tt -= $inc['amount'];
                //               }
                //             }
                //           }
                //         }
                        
                //       $septem_total= $august_total+$sep_tt+$sep_inc_tt;
                //       echo $septem_total;
                  ?>
                </td>
                <td>
                  <?php
                //     $oct_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'October'){
                //               $oct_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $oct_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'October'){
                //               if($inc['amount_type'] == "cr"){
                //                 $oct_inc_tt += $inc['amount'];
                //               }else{
                //                 $oct_inc_tt -= $inc['amount'];

                //               }
                //             }
                //           }
                //         }
                //       $oct_total=  $septem_total+$oct_tt+$oct_inc_tt;
                //       echo $oct_total;
                //   ?>
                // </td>
                // <td>
                //   <?php
                //     $nov_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'November'){
                //               $nov_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $nov_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'November'){
                //               if($inc['amount_type'] == "cr"){
                //                 $nov_inc_tt += $inc['amount'];
                //               }else{
                //                 $nov_inc_tt -= $inc['amount'];

                //               }
                //             }
                //           }
                //         }
                //       $nov_total=  $oct_total+$nov_tt+$nov_inc_tt;
                //       echo $nov_total;
                  ?>
                </td>
                <td>
                  <?php
                //     $dec_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'December'){
                //               $dec_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $dec_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'December'){
                //               if($inc['amount_type'] == "cr"){
                //                 $dec_inc_tt += $inc['amount'];
                //               }else{
                //                 $dec_inc_tt -= $inc['amount'];

                //               }
                //             }
                //           }
                //         }
                //       $dec_total =  $nov_total+$dec_tt+$dec_inc_tt;
                //       echo($dec_total);
                //   ?>
                // </td>
                // <td>
                //   <?php
                //     $jan_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'January'){
                //               $jan_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $jan_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'January'){
                //               if($inc['amount_type'] == "cr"){

                //                 $jan_inc_tt += $inc['amount'];
                //               }else{
                //                 $jan_inc_tt -= $inc['amount'];

                //               }
                //             }
                //           }
                //         }
                //       $jan_total=  $dec_total+$jan_tt+$jan_inc_tt;
                //       echo($jan_total);
                //   ?>
                // </td>
                // <td>
                //   <?php
                //     $feb_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'February'){
                //               $feb_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $feb_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'February'){
                //               if($inc['amount_type'] == "cr"){

                //                 $feb_inc_tt += $inc['amount'];
                //               }else{
                //                 $feb_inc_tt -= $inc['amount'];

                //               }
                //             }
                //           }
                //         }
                //       $feb_total=  $jan_total+$feb_tt+$feb_inc_tt;
                //       echo($feb_total);
                //   ?>
                // </td>
                // <td>
                //   <?php
                //     $mar_tt = 0;
                //       if($da){
                //           foreach($da as $dearness){
                //             if($dearness['month_name'] == 'March'){
                //               $mar_tt += $dearness['amount_inc'];
                //             }
                //           }
                //         }
                //       $mar_inc_tt=0;
                //         if($increment){
                //           foreach($increment as $inc){
                //             if($inc['month_name'] == 'March'){
                //               if($inc['amount_type'] == "cr"){

                //                 $mar_inc_tt += $inc['amount'];
                //               }else{
                //                 $mar_inc_tt -= $inc['amount'];

                //               }
                //             }
                //           }
                //         }
                //       $mar_total=  $feb_total+$mar_tt+$mar_inc_tt;
                //       echo($mar_total);
                  ?>
                </td> 
              </tbody> -->
          </table>
         </div>
      