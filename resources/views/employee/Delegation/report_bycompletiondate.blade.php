@extends($layout)

@section('title', 'Delegation Day Wise Report')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Delegation Day Wise Report</a></li> 
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
  
</style>
 
@endsection
@section('js')

<script src="/js/dataTables.responsive.js"></script>


  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {

      if(dataTable){
          dataTable.destroy();
      }
      dataTable = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/delegation/report/completiondate/api",
            "datatype": "json",
                "data": function (data) {
                    var c_date = $('#c_date').val();
                    data.c_date = c_date;
                }
            },
          "columns": [
              {"data":"name","render": function(data,type,full,meta){
              	return data +"("+full.employee_number+")";
              }},
              {"data":"task_detail"},
              {"data":"assign_date"},
              {"data":"deadline","render": function(data,type,full,meta)
                {  
                  if(data == "01-01-1970"){
                    return "";
                  }else{
                    return data;
                  }
                }
              },
              {"data":"requirements"},
              {"data":"completion_date"},
              {"data":"delegation_status"},
              
            ],
            "columnDefs": [
             
              // { "orderable": false, "targets": 7 }
            ]
          
        });
    });

$('#c_date').change(function(){
  dataTable.draw();
})

$("#c_date").datepicker({
    format: 'd-mm-yyyy'
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
                    @section('titlebutton')
                    
                      @endsection
                    <div class="col-md-4 pull-right ">
                        <label>Select Date</label>
                        <input type="text" autocomplete="off" id="c_date" name="c_date" class=" input-css" value="">
                    </div>
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Task</th>
                      <th>Assign Date</th>
                      <th>Deadline</th>
                      <th>Requirement</th>
                      <th>Completion Date</th>
                      <th>Delegation Status</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
               
                  </table>
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection