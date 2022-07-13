<div class="col-md-8">
<div class="card">
              <div class="card-header border-transparent leavediv" id="wrapper">
              
                <div class="card-tools" id="first">
                <p class="col_head">Delegation </p>
              </div>
                <div class="card-tools" id="second" >
                  <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#delegation" style="background: transparent;color: whitesmoke;">
                    <i class="fa fa-minus"></i>
                  </button>
               
              </div>
                </div>
              
              <!-- /.card-header -->
              <div class="card-body p-0 scrr" id="delegation" style="height:240px; max-height: 200px;overflow-y: auto;">
                <div class="table-responsive">
                  <table class="table m-0 tableBodyScroll table table-bordered table-striped" style="margin-bottom: 10px;">
                    <thead style="position: sticky;top: 0px;">
                    <tr>
                          <th>Task</th>
                          <th>Assign Date</th>
                          <th>Assign By</th>
                          <th>Deadline</th>
                          <th>Requirement</th>
                          <th>Status</th>
                          <th>Action</th>
                    </tr>                   
                    </thead>
                    <tbody>
                   
                     @foreach($delegation as $key)
                     <tr>
                         <td>{{$key->task_detail}}</td>
                         <td>{{$key->assign_date}}</td>
                         <td>{{$key->ass}}</td>
                         <td>
                          @if($key->deadline == "1970-01-01")
                          @else
                          {{$key->deadline}}
                        @endif
                        </td>
                         <td>{{$key->requirements}}</td>
                         <td>
                          @if($key->dele_stat)
                          @php   $ds = [];
                              if($key->dele_stat){
                                $ds = explode(",", $key->dele_stat);
                              }else{
                                $ds =[];
                              }
                              $final_st = $key->final_stat;
                              if($key->final_stat){
                                $final_st = explode(",", $key->final_stat);
                              }else{
                                $final_st =[];
                              }
                              if($ds[0]=='completed' ){
                                if($final_st[0]=='not completed'){
                                @endphp
                                   Not Completed
                                @php
                                }else{
                                @endphp
                                  Completed
                                  @php
                                }
                              }else{
                              @endphp
                                Not Completed
                                @php
                              }
                      @endphp
                          @endif
                         </td>
                         <td>
                           @php 
                               $cd =$key->completion_date;
                                if($key->completion_date){
                                 $cd = explode(",", $key->completion_date);
                                }else{
                                  $cd = [];
                                }
                                $ds = $key->dele_stat;
                                if($key->dele_stat){
                                  $ds = explode(",", $key->dele_stat);
                                }else{
                                  $ds = [];
                                }
                                $arr_merge = [];
                                $count = count($cd);
                                 for($i=0; $i < $count; $i++){
                                  $arr_merge[$cd[$i]]= $ds[$i];
                                }
                                 $final_st = $key->final_stat;
                                if($key->final_stat){
                                  $final_st = explode(",", $key->final_stat);
                                }else{
                                  $final_st =[];
                                }
                                $now = date('Y-m-d');
                                if($key->completion_date == null && $key->assign_date<=$now || $final_st[0]=='not completed'){
                                @endphp
                                  <button id="{{$key->id}}" onClick="getid({{$key->id}})" class='comple btn btn-primary btn-xs' data-toggle='modal' data-target='#myModal_comp_date'>Add Completion Date</button> &nbsp;
                                  <button id="{{$key->id}}" class="job_det btn btn-openid btn-xs">Details</button>&nbsp;<a href="/delegation/status/details/summary/{{$key->id}}" class="btn btn-xs btn-success">Status Detail</a>
                                 @php
                                }elseif($cd[0] == $now && $ds[0]== 'pending'){
                                @endphp
                                 <button id="{{$key->id}}" onClick = "getid($key->id)" class="stat btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal_comp_stat">Add Status</button> &nbsp;
                                <button id="{{$key->id}}" class="job_det btn btn-openid btn-xs">Details</button>&nbsp;
                                <a href="/delegation/status/details/summary/{{$key->id}}" class="btn btn-xs btn-success">Status Detail</a>
                                @php
                              }else{
                              @endphp
                                <button id="{{$key->id}}" class="job_det btn btn-openid btn-xs">Details</button>&nbsp;
                                <a href="/delegation/status/details/summary/{{$key->id}}" class="btn btn-xs btn-success">Status Detail</a>
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

  <div id="myModal_comp_date" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
        
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add Completion Date</h4>
                            </div>
                            <div class="modal-body">
                                <form action="" method="get" id="first_completion">
                                  <span id="fc_err" style="color:red; display: none;"></span>
                                  <div class="row">
                                    <div class="col-md-6 {{ $errors->has('c_date') ? 'has-error' : ''}}">
                                        <label for="">Completion Date<sup>*</sup></label>
                                        <input type="text" name="c_date" id="c_date" autocomplete="off" 
                                        class="input-css c_date datepickers" required="">
                                        {!! $errors->first('c_date', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <input type="text" name="del_id" id="del_id" hidden="">
                                  </div><br><br>
                                
                                  <!-- return confirm("Are you sure you want to update Completion Date"); -->
                                  <div class="modal-footer">
                                      <input type="submit" value="Update" class="btn btn-primary">&nbsp;&nbsp;
                                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                  </div>
                                </form>
                            </div>
        
                        </div>
                    </div>
                </div>
                <div id="myModal_comp_stat" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                  
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add Status</h4>
                            </div>
                            <div class="modal-body">
                                <form action="" id="completion_st_form" method="post" enctype="multipart/form-data">
                                  @csrf
                                  <span id="fs_err" style="color:red; display: none;"></span>
                                  <input type="text" name="stat_del_id" id="stat_del_id" hidden>
                                  <div class="row">
                                    <div class="col-md-6 {{ $errors->has('status') ? 'has-error' : ''}}">
                                        <label for="">Status<sup>*</sup></label>
                                        <select name="status" id="status" class="input-css select2" onchange="show_as_stat()" required="">
                                          <option value="">Select status</option>
                                          <option value="completed">Completed</option>
                                          <option value="not completed">Not Completed</option>
                                        </select>
                                        {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    
                                  </div><br><br>
                                  <div class="row" id="completed_div" style="display: none;">
                                      <div class="col-md-6 {{ $errors->has('detail') ? 'has-error' : ''}}">
                                         <label for="">Details<sup>*</sup></label>
                                         <textarea id="detail" name="detail" class="detail input-css" ></textarea>
                                      </div>
                                      <div class="col-md-6 {{ $errors->has('img') ? 'has-error' : ''}}">
                                        <label for="">Image of Job Completion<sup>*</sup></label>
                                        <input type="file" accept="image/x-png,image/gif,image/jpeg" name="img" id="img" class="img " >
                                        {!! $errors->first('img', '<p class="help-block">:message</p>') !!}
                                    </div>
                                  </div><br>
                                  <div class="row" id="notcompleted_div" style="display: none;">
                                      <div class="col-md-6 {{ $errors->has('new_c_date') ? 'has-error' : ''}}">
                                         <label for="">New Promised Completion Date<sup>*</sup></label>
                                         <input type="text" name="new_c_date" id="new_c_date" autocomplete="off"
                                        class="input-css new_c_date datepickers" >
                                        {!! $errors->first('new_c_date', '<p class="help-block">:message</p>') !!}
                                      </div>
                                  </div><br>
                                  <div class="modal-footer">
                                      <input type="submit" value="Update" class="btn btn-primary">&nbsp;&nbsp;
                                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                  </div>
                                </form>
                            </div>
        
                        </div>
                    </div>
                </div>