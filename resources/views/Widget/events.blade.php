<div class="col-md-6">
      <div class="card">
        <div class="card-header border-transparent bg-warning" id="wrapper" >
          <div class="card-tools" id="first">
            <p class="col_head">Events</p>
          </div>
          <div class="card-tools" id="second">
            <button type="button" class="btn btn-tool"  data-toggle="collapse" data-target="#multiCollapseExample4" style="background: transparent;color: whitesmoke;">
              <i class="fa fa-minus"></i>
            </button>
              <!-- <a href="/events"><img src="/images/calendar-icon.png" style="height:30px"></a> -->  
              <!-- </button> -->
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body p-0" id="multiCollapseExample4"style="background: rgb(250, 250, 250);">
          <div class="col-md-6">
              
                <div class="panel panel-default"  >
    
                  <center> 
                  <div class="panel-body" style="width:100%;overflow-y:auto;">
                    {!! $calendar->calendar() !!}
    
                  </div></center>
                </div>
              
          </div>
            <div class="col-md-6">
              <div class="scrr" style="height: 200px;overflow-y:auto;margin-top: 10px;">
                  <table class="table m-0 tableBodyScroll" id="eventTable" style="margin-bottom: 0px;width:100%">
                    <thead style='position: sticky;top: 0px;'class="bg-primary">
                      <tr><th class="text-center">Events</th><th class="text-center">Date</th></tr>
                    </thead>
                    <tbody>
                      @foreach($feve as $key)
                        <tr>
                          <td>{{$key['events']}}</td>
                          <td><span style="color:green">{{$key['date']}}</span></span></td>
                        </tr>
                      @endforeach
                    </tbody>
                    <thead style='position: sticky;top: 0px;'class="bg-danger">
                      <tr><th class="text-center">Birthday</th><th class="text-center">Date</th></tr>
                    </thead>
                    <tbody>
                      @foreach($fbday as $key)
                        <tr>
                          <td>{{$key['empcode']}}</td>
                          <td><span style="color:green">{{$key['dob']}}</span></span></td>
                        </tr>
                      @endforeach
                    </tbody>
                    <thead style='position: sticky;top: 0px;' class="bg-success">
                      <tr><th class="text-center">Service Anniversary</th><th class="text-center">Date</th></tr>
                    </thead>
                    <tbody>
                      @foreach($fann as $key)
                        <tr>
                          <td>{{$key['empcode']}}</td>
                          <td><span style="color:green">{{$key['doj']}}</span></span></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
              </div>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix border-transparent bg-warning" style="border-radius: 0px 0px 3px 3px;    height: 20px;">
        <!-- <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a>
        <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a> -->
      </div>
        <!-- /.card-footer -->
      </div>
</div>

  