
<div class="col-md-4">

<div class="card">
           
              <div class="card-header border-transparent" id="wrapper" style="background: brown;">
              
              <div class="card-tools" id="first">
              <h3 class="col_head">Announcements</h3>
              </div>
              <div class="card-tools" id="second">
              <button type="button" class="btn btn-tool"  data-toggle="collapse" data-target="#multiCollapseExample5" style="background: transparent;color: whitesmoke;">
                    <i class="fa fa-minus"></i>
                  </button>
                  <!-- <a href="/announcements"><img src="/images/calendar-icon.png" style="height:30px"></a> -->
            </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0 scrr" id="multiCollapseExample5" style="background: white;height: 240px;">
                <div class="scrr"style="background: white;height: 247px;">
                  @php $i=0 @endphp
                  @foreach($announcements as $key)
                    <div class="col-md-12 "style="border-bottom: 1px solid #d6d6d6;padding: 7px;">
                      <div class="col-md-3 inline text-center" style="border-right: 1px solid #d4cccc;"><p style="margin-top: 3px;font-size: 20px;"><small class="badge">{{++$i}}</small></p></div>
                      <div class="col-md-9 inline"style="overflow-wrap: break-word;"><span style="color:#c1586c">{{$key['date']}} : </span>{{$key['announcements']}}</div>
                    </div>

                  @endforeach
                </div>
                  
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center" style="background: brown; border-radius: 0px 0px 3px 3px;    height: 16px;">
                <!-- <a href="javascript:void(0)" class="uppercase">View All Products</a> -->
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
</div>  
   