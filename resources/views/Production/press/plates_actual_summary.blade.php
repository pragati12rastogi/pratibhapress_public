@extends($layout)

@section('title', 'Press Production Summary')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Press Production Summary</a></li> 
@endsection
@section('css')
<style>
 
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
          "ajax": "/production/press/dailyplanning/actual/list/api/"+jc+"/"+ele+"",
          "columns": [
            {"data":"job_number"},
            {"data":"planned_date"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"creative_name"},
            {"data":"element_name"},
            {"data":"machine"},
            {"data":"total_plates"},
            {"data":"planned_plates"},
            {"data":"actual"},
           
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
                          <th>JC No.</th>
                          <th>Planned Date</th>
                          <th>Reference</th>
                          <th>Item</th>
                          <th>Creative</th>
                          <th>Element</th>
                          <th>Machine</th>
                          <th>Total Imp</th>
                          <th>Planned</th>
                          <th>Done</th>
                         
                          
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