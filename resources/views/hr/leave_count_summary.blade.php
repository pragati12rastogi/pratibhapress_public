@extends($layout)

@section('title', 'Leaves Calculation Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Leaves Calculation Summary</a></li> 
@endsection
@section('css')
<style>

</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>

</script>
  <script>
    var selected=[];
     var col_data = 'd';
    //  console.log(month);
     

   var dataTable;
    function datatablefn() {

      var str = [];
      // str.push({"data":"emp_id"});
      str.push({"data":"employee_number"});
      str.push({"data":"name"});
      str.push(  { 
                data:function(data, type, full, meta){
                  
                  var total_p=data.total_present;
                  var total_l=parseInt(total_p/20);
                  var total_a=data.absent;
                  var closing_b=parseInt(total_l)-parseInt(total_a)
                    return data.carried_leave;
                } ,"orderable": false
            });
     
      for(var i = 1 ; i <= 12 ; i++)
      {
          
        str.push({"data": col_data+i,
                  "class":"center light-red",
                  "orderable": false});
      }
        str.push({"data":"present_current","orderable": false});
      str.push(  { 
                data:function(data, type, full, meta){
                  var total_p=data.total_present;
                  var total_l=parseInt(total_p/20);
                    return total_l;
                } ,"orderable": false
            });
            str.push(  { 
                data:function(data, type, full, meta){
                  var total_p=data.total_present;
                  var total_l=parseInt(total_p/20);
                  var total_c=data.carried_leave;
                  var closing_b=parseInt(total_l)+parseInt(total_c)
                    return closing_b;
                } ,"orderable": false
            });
      // console.log(str);
    
      
    

    if(dataTable){
      dataTable.destroy();
    }
        
       dataTable = $('#admin_table').DataTable({
        "scrollX": true,
         "processing": true,
         "serverSide": true,
         "ajax": "",
         "aaSorting": [],
         "pageLength": 10,
         "responsive": false,
          "ajax": {
            "url": "/hr/leave/count/list/api",
            "datatype": "json",
                "data": function (data) {
                   
                    var year = $('#year').val();
                    data.year = year;
                   
                },

            },
          "columns": str
       });
  
     
}

   $(document).ready(function() {
    datatablefn();

   
    
   // $( "#admin_table" ).parent().css( "overflow-x", "auto" );

  });
  $('#year').datepicker({
    format: "yyyy",
    weekStart: 1,
    orientation: "bottom",
    keyboardNavigation: false,
    viewMode: "years",
    minViewMode: "years",
    autoclose: true
}).datepicker("setDate", new Date());
  $('#year').on('change', function () {
    datatablefn();
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
            <div id="modal_div"></div>
            <div class="box-header with-border">
            <div class="row">
            <div class="col-md-3" >
                            <label>Select Year</label>
                            <input type="text" name="year" id="year" class="input-css" autocomplete="off">
                    </div><br><br>
            </div>
            </div>
            <div class="box-body">
           
              <table id="admin_table"  class="table table-bordered table-striped">
                  <thead>
                  <tr>
                  <th>Employee Code</th>
                    <th>Employee Name</th>
                    <th>Closing leave balance for the year</th>
                    <!-- <th colspan=""> Days present </th> -->
                        <th>Jan</th>
                        <th>Feb</th>
                        <th>Mar</th>
                        <th>Apr</th>
                        <th>May</th>
                        <th>Jun</th>
                        <th>Jul</th>
                        <th>Aug</th>
                        <th>Sep</th>
                        <th>Oct</th>
                        <th>Nov</th>
                        <th>Dec</th>
                   
                    <th>Total days present in current Year</th>
                    <th>No of leaves calculated for the year</th>
                    <th>Total leaves(Closing balance for the year+No of leaves calculated for the year)</th>
                   
                  </tr>
                  </thead>
                  <tbody>
  
                  </tbody>
             
              </table>
            </div>
          </div>
        <!-- /.box -->
      </section>
@endsection