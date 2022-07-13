@extends($layout)

@section('title', 'Task Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Task Summary</a></li> 
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
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
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
            "url": "/checklist/task/list/api",
            "datatype": "json",
                "data": function (data) {
                    var employee = $('#emp').val();
                    data.employee = employee;
                }
            },
          "columns": [
              {"data":"name"},
              {"data":"task_name"},
              {"data":"frequency"},
              {"data":"day"},
              {"data":"task_date", "render": function(data,type,full,meta)
                {
                  if(data == '0'){
                     return "";
                  }else{
                    return data;
                  }
                }
              },
              {"data":"month"},
              {"data":"emp_status"}
            ],
            "columnDefs": [
             
              // { "orderable": false, "targets": 7 }
            ]
          
        });
    });
function filter() {
  dataTable.draw();
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
                    @section('titlebutton')
                    
                      @endsection
                      
                    <div class="col-md-4 pull-right ">
                        <label>Employee</label>
                        <select id="emp" name="emp" class="input-css select2" onchange="filter()">
                          <option value="">Select Employee</option>
                          @foreach($employee as $emp)
                            <option value="{{$emp->id}}">{{$emp->name}}</option>
                          @endforeach
                        </select>
                    </div>
                    
                   
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Task</th>
                      <th>Frequency</th>
                      <th>Task Day</th>
                      <th>Task Date</th>
                      <th>Task Month</th>
                      <th>Status</th>
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