
          <div class="col-md-4">
            <!-- small box -->
            <div class="card small-box bg-orange"><!-- bg-warning bg-gray-active-->
              <div ><!-- class="inner"  -->
                <!-- <h4>Photo Gallary</h4> -->
                     <div class="row">
              <center>        
              <main>
                <div id="myCarousel" class="carousel slide" data-ride="carousel" style="height: 230px;">
                  
                  <div class="carousel-inner">
                    @php $i = 1; @endphp
                    @foreach($doc as  $value)
                        @if (file_exists(public_path().'/upload/photos/'.$value['name']))
                          <div class="item {{$i==1?'active':''}}">
                              <a href="/upload/photos/{{$value['name']}}" data-fancybox="images" data-caption="{{$value['name']}}">
                                <img class="d-block w-100" src="/upload/photos/{{$value['name']}}" alt="{{$value['name']}}" style="width:100%;">
                              </a>
                          </div>
                          @php $i++ @endphp
                        @endif
                    @endforeach
                  </div>
                  <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    <span class="sr-only">Previous</span>
                  </a>
                  <a class="right carousel-control" href="#myCarousel" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                    <span class="sr-only">Next</span>
                  </a>
                </div>
              </main></center>
              </div>
              </div>
              <a href="/dashboard/photos/gallary" target="_blank" class="small-box-footer1">Browse Photo Gallery <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
            </div>
          </div>

      
      