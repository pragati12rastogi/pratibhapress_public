<div class="col-md-4">
<div class="card">
              <div class="card-header border-transparent leavediv" id="wrapper">
              
                <div class="card-tools" id="first">
                <p class="col_head">Checklist</p>
              </div>
                <div class="card-tools" id="second" >
                  <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#checklist" style="background: transparent;color: whitesmoke;">
                    <i class="fa fa-minus"></i>
                  </button>
               
              </div>
                </div>
              
              <!-- /.card-header -->
              <div class="card-body p-0 scrr" id="checklist" style="height:240px; max-height: 200px;overflow-y: auto;">
                <div class="table-responsive">
                  <table class="table m-0 tableBodyScroll" style="margin-bottom: 10px;">
                    <thead style="position: sticky;top: 0px;">
                    <tr>
                      <th>Task</th>
                      <th>Task Date</th>
                      <th>Action</th>
                    </tr>                   
                    </thead>
                    <tbody>
                   
                     @foreach($checklist as $key)
                     <tr>
                         <td>{{$key->task_name}}</td>
                         <td>{{$key->st_date}}</td>
                         <td>
                          @php 
                            $now = date('Y-m-d');
                           // $day = date('N', strtotime($now));
                            //$holiday = [];
                          
                            $weekday = date('l', strtotime($now)); 
                            if($weekday == 'Sunday'){
                              $day = 0;
                            }else{
                              $day = 1;
                            }
                            if($holiday){
                             $holi_data = explode(",", $holiday);
                            }else{
                              $holi_data = [];
                            }
                            if( in_array($now, $holi_data)){
                              $holi = 0;
                            }else{
                              $holi = 1;
                            }

                            if($key->st_date <= $now && $day != 0 && $holi == 1){
                          @endphp
                           <a href="/chklist/emp/status/upd/Done/{{$key->id}}" ><button class='btn btn-success btn-xs' onClick = return confirm(Are you sure you want to update task status as Done?)>Done </button></a> &nbsp;

                           <a href='/chklist/emp/status/upd/Not Done/{{$key->id}}' ><button class='btn btn-danger btn-xs'onClick='return confirm(re you sure you want to update task status as Not Done?);'>Not Done</button></a> &nbsp;

                           <a href="/chklist/emp/status/upd/Not Required/{{$key->id}}" ><button class='btn btn-facebook btn-xs onClick='return confirm(Are you sure you want to update task status as Not Required?);>Not Required</button></a>
                          @php 
                            }
                         @endphp
                         </td>
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