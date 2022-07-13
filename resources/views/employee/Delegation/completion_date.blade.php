<link rel="stylesheet" href="/css/common.css">
   <style>
   .div{
     border:1px solid black;
   }
   </style> 

            @if(isset($completion))
              @php $i=0; @endphp
              
                @foreach($completion as $comp)
                      <div class="row">  
                        <div class="col-md-3" style="border-right: 4px double;">
                            <strong>Week {{++$i}}: </strong> 
                        </div>
                        @php 
                            $str_t_ar_date = explode(',',$comp['completion_date']);
                            $str_t_ar_status = explode(',',$comp['completion_status']);
                            $str_score = explode(',',$comp['completion_score'])
                        @endphp
                        @for($x = 0;$x < count($str_t_ar_date);$x++)
                          <div class="col-md-3" style='border-right:5px solid white;text-align: center;border-left: 5px solid white; <?php 
                            if($str_score[$x]=="1"){
                              echo "background:#92d050";
                            }else if($str_score[$x]=="2"){
                             echo "background: #fdff00;";
                            }else if($str_score[$x]=="3"){
                              echo "background: #fa1001;";
                            }else{
                              echo"";
                            } ?>'>
                            <strong>Date {{$x+1}}: </strong> {{date('d-m-Y',strtotime($str_t_ar_date[$x]))}}
                          </div>

                        @endfor
                            
                </div>        
                @endforeach 
                        
            @endif