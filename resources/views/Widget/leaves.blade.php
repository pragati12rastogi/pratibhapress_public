<div class="col-md-4">
<div class="card">
              <div class="card-header border-transparent leavediv" id="wrapper">
              
                <div class="card-tools" id="first">
                <p class="col_head">Leaves Summary</p>
              </div>
                <div class="card-tools" id="second" >
                  <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#multiCollapseExample2" style="background: transparent;color: whitesmoke;">
                    <i class="fa fa-minus"></i>
                  </button>
               
              </div>
                </div>
              
              <!-- /.card-header -->
              <div class="card-body p-0 scrr" id="multiCollapseExample2" style="height:240px;">
                <div class="table-responsive">
                  <table class="table m-0 tableBodyScroll" style="margin-bottom: 10px;">
                    <thead style="position: sticky;top: 0px;">
                    <tr>
                      <th>Employee</th>
                      <th>Leave Period</th>
                      <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                   
                     @foreach($leave as $key)
                     <tr>
                         <td>{{$key['emp']}}</td>
                         <td><span style="color:green">{{$key['start_date']." "}}</span>To <span style="color:green">{{" ".$key['end_date']}}</span></td>
                         <td>
                          
                            @if($key['status_level1']=="Pending")
                                   @php 
                                  $x="no";
                                  $i=0;
                                  $id=$key['id'];
                                  foreach($hr as $index=>$value){
                                  if($auth==$value){
                                      $i=$value;
                                      $x="yes";
                                    }
                                }
                                @endphp 
                                @if($x=="yes")
                                    <a  onclick="cancel_alert_dailog({{$auth}},{{$id}})"><button class="btn btn-primary btn-xs" style="float: left;height: 20px;"> Approve L1</button></a><br><br>
                                @else
                                    <p>Not Eligible L1</p>
                                @endif
                            @elseif($key['status_level1']=="Approved")
                              {{"Approved By Level 1"}}<br>
                            
                            @elseif($key['status_level1']=="Rejected")
                             <span style="color:green">{{"Rejected By Level 1"}}<span><br>
                            @else

                            @endif
                         
                          @if($key['status_level2']=="Pending")
                                @if($key['reporting']==$auth)
                                  <a onclick="cancel_alert_dailog1(0,{{$key['id']}},{{$key['reporting']}})"><button class="btn btn-primary btn-xs" style="float: left;height: 20px;"> Approve L2</button></a>
                                @else
                                    <p>Not Eligible L2</p>
                                @endif
                            
                            @elseif($key['status_level2']=="Approved")
                            {{"Approved By Level 2"}}
                            
                            @elseif($key['status_level2']=="Rejected")
                              {{"Rejected By Level 2"}}
                            @else
                          @endif
                        </td>
                         <!-- <td>{{$key['level1']}}</td>
                         <td><span class="badge badge-warning">{{$key['status_level2']}}</span></td>
                         <td>{{$key['level2']}}</td> -->
                     </tr>
                     @endforeach
                   
                    </tbody>
                  </table>
                </div>
                <!-- /.table-responsive -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix leavediv" style="border-radius: 0px 0px 3px 3px;    height: 20px;">
                <!-- <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a> -->
              </div>
              <!-- /.card-footer -->
            </div>
</div>