@extends($layout)

@section('title', 'Birthday and Anniversary Employee List')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Birthday and Anniversary Employee List</a></li> 
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
  .nav-pills>li {
    
    border: 1px solid #a9a0a0;
}
.select2{
  width: 160px;
}
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable1;
    var dataTable2;

    function bday(){
      if(dataTable1){
        dataTable1.destroy();
      }
      dataTable1 = $('#table_bd').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
             "url": "/employee/bday/list/api",
              "datatype": "json",
                "data": function (data) {
                    var bdaymonth = $('#bd_month').val();
                    data.bdaymonth = bdaymonth;
                }
          },
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
              {"data":"dob"}
              //      {
              //     "targets": [ -1 ],
              //     "data":"id", "render": function(data,type,full,meta)
              //     {
              //       return "<a href='/employee/profile/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" //+ 
              //       //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
              //       ;
              //     }
              // }
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 5 }
            
            ]
          
        });
    }

    function anni(){
      if(dataTable1){
        dataTable1.destroy();
      }
       dataTable1 = $('#table_ani').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
             "url": "/employee/anniversary/list/api",
              "datatype": "json",
                "data": function (data) {
                    var annimonth = $('#anni_month').val();
                    data.annimonth = annimonth;
                }
          },
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
              {"data":"doj"}
              //      {
              //     "targets": [ -1 ],
              //     "data":"id", "render": function(data,type,full,meta)
              //     {
              //       return "<a href='/employee/profile/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" //+ 
              //       //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
              //       ;
              //     }
              // }
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 5 }
            
            ]
          
        });
    }
    // Data Tables
    $(document).ready(function() {
      
      bday();
     
    });

    $("#bid").click(function(){
        bday();
    })
    $("#aid").click(function(){
        anni();
    })
$('.date-range-filter').change(function() {
        dataTable1.draw();
});
$('.date-range-filter2').change(function() {
        dataTable1.draw();
});
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
                    <div class="box-header ">
                      <div class="box-header ">
                        <ul class="nav nav1 nav-pills">
                          <li class="nav-item active" id="bid">
                            <a data-toggle="pill" href="#birthday_box">Birthday List</a>
                          </li>
                          <li class="nav-item " id="aid">
                            <a data-toggle="pill" href="#anninversary">Anniversary List</a>
                          </li>
                        </ul>
                      </div>
                  </div>
                  <div class="tab-content"> 
                    <div class="box-header with-border tab-pane fade active in" id="birthday_box" >
                       <div class="col-md-4 pull-right margin">
                          <div class="input-group input-daterange">
                           
                              <select id="bd_month" class="form-control date-range-filter select2">
                                <option value=''>--Select Month--</option>
                                <option value='01'>Janaury</option>
                                <option value='02'>February</option>
                                <option value='03'>March</option>
                                <option value='04'>April</option>
                                <option value='05'>May</option>
                                <option value='06'>June</option>
                                <option value='07'>July</option>
                                <option value='08'>August</option>
                                <option value='09'>September</option>
                                <option value='10'>October</option>
                                <option value='11'>November</option>
                                <option value='12'>December</option>
                              </select> 
                          </div>
                        </div>
                        <table id="table_bd" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                            <th>Employee Number</th> 
                              <th>Name</th>
                              <th>Number</th>
                              <th>Department</th>
                              <th>Designation</th>
                              <th>Birth Date</th>
                             
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                       
                        </table>
                    </div>
                     <div class="box-header with-border tab-pane fade " id="anninversary" >
                         <div class="col-md-4 pull-right margin">
                            <div class="input-group input-daterange">
                                <select id="anni_month" class=" date-range-filter2 select2">
                                  <option value=''>--Select Month--</option>
                                  <option value='01'>Janaury</option>
                                  <option value='02'>February</option>
                                  <option value='03'>March</option>
                                  <option value='04'>April</option>
                                  <option value='05'>May</option>
                                  <option value='06'>June</option>
                                  <option value='07'>July</option>
                                  <option value='08'>August</option>
                                  <option value='09'>September</option>
                                  <option value='10'>October</option>
                                  <option value='11'>November</option>
                                  <option value='12'>December</option>
                                </select> 
                            </div>
                          </div>
                          <table id="table_ani" class="table table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th>Employee Number</th> 
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Joining Date</th>
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                         
                          </table>
                    </div>
                  </div>
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection