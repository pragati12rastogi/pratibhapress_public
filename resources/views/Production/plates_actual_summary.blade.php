@extends($layout)

@section('title', 'Pre Press Summary')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Pre Press Summary</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
 
@media (max-width: 768px)  
  {
    
    .content-header>h1 {
      display: inline-block;
     
    }
  }
  @media (max-width: 425px)  
  {
   
    .content-header>h1 {
      display: inline-block;
      
    }
  }
   input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
</style>

@endsection
@section('js')
<script src="/js/Production/platebypress_creation.js"></script>

<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
      
      fulltable();

       
    });
    
    function fulltable(){
      // if(dataTable){
      //   dataTable.destroy();
      // }
      var jc =<?php echo $jc ?>;
      var ele= <?php echo $ele ?>;
      dataTable = $('#plate').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/production/daily/plate/actual/list/api/"+jc+"/"+ele+"",
          "columns": [
            {"data":"job_number"},
            {"data":"planned_date"},
             { 
            "data": function(data, type, full, meta){
                var dt=data.created_at;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+ 1;
                      var yyyy=dt.getFullYear();
                      var hh=dt.getHours();
                      var mi=dt.getMinutes();
                      var ss=dt.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                      var ac=dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                      return ac;
               }
              
            },
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"creative_name"},
            {"data":"element_name"},
            {"data":"e_plate_size"},
            {"data":"total_plates"},
            {"data":"actual"},
            {"data":"wastage"},
            {"data":"reason"}
           
            ],
            "columnDefs": [
               // { "orderable": false, "targets": 11 }
            
            ]
          
        });
    }
 
</script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    
                    <table id="plate" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Job Card Number</th>
                          <th>Planned Date</th>
                          <th>Created Date</th>
                          <th>Reference Name</th>
                          <th>Item Name</th>
                          <th>Creative Name</th>
                          <th>Element Name</th>
                          <th>Plate Size</th>
                          <th>Total Plate Required</th>
                          <th>Actual Plates</th>
                          <th>Wastage</th>
                          <th>Reason for Wastage</th>
                          
                        </tr>
                      </thead>
                      <tbody >

                      </tbody>
               
                  </table>
                
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
        </div>
      </section>
@endsection