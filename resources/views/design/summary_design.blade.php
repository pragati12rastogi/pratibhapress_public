

<link rel="stylesheet" href="/css/common.css">
   <style>
   .div{
     border:1px solid black;
   }
   </style>          
                              @foreach ($design as $item)
                            <div class="row">
                              <div class="col-md-12">
                              <div class="col-md-3 div" style="background-color:khaki"><strong>WA No.:</strong>{{$item['work_alloted_number']}}</div>
                              <div class="col-md-3 div" style="background-color:lavender"><strong>Employee:</strong>{{$item['name']}}</div>
                              <div class="col-md-2" style="background-color:khaki"><strong>Pages Alloted:</strong>{{$item['no_pages']}}</div>
                              <div class="col-md-2" style="background-color:lavender"><strong>Pages Done:</strong>{{$item['sum_pages']}}</div>
                              <div class="col-md-2" style="background-color:lavender"><strong>Pages Left:</strong>{{$item['no_pages']-$item['sum_pages']}}</div>
                            </div>
                          </div> 
                              @php
                                 $status= explode(',',$item['status']);
                                 $pages=explode(',',$item['pages']);
                                 $date=explode(',',$item['date']);  
                                //  print_r($status);
                              @endphp
                              
                                 
                              @foreach ($status as $key=>$value)
                              <div class="row"> 
                              <div class="col-md-12">
                              <div class="col-md-4 div"><strong>Status:</strong>{{$value}}</div>
                              <div class="col-md-4 div"><strong>Date:</strong>{{$date[$key]}}</div>
                              <div class="col-md-4 div"><strong>Page:</strong>{{$pages[$key]}}</div>
                            </div>
                          </div>
                              @endforeach
                           
                            <br><br>
                              @endforeach
                          
                 

