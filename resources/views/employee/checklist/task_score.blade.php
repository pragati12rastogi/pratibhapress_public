@extends($layout)

@section('title', 'Task Score Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Task Score Summary</a></li> 
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
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css">  
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
            "url": "/employee/checklist/task/score/api",
            "datatype": "json",
                "data": function (data) {
                    var week = $('.displayDate').text().replace('-', '');
                    data.week = week;
                }
            },
          "columns": [
              {"data":"name","render": function(data,type,full,meta){
              	return data +"("+full.employee_number+")";
              }},
              {"data":"task_name"},
              {"data":"Done", "render": function(data,type,full,meta)
                  {   
                    console.log(full.WEEK);
                      if(data == ""|| data == null){
                        return '';
                      }else{
                        return parseInt(data) +" %";
                      }
                  }},
              {"data":"NotDone", "render": function(data,type,full,meta)
                  {   
                    
                      if(data == ""|| data == null){
                        return '';
                      }else{
                        return parseInt(data) +" %";
                      }
                  }},
              {"data":"Late", "render": function(data,type,full,meta)
                  {   
                    
                      if(data == ""|| data == null){
                        return '';
                      }else{
                        return parseInt(data) +" %";
                      }
                  }},
              {"data":"N_r", "render": function(data,type,full,meta)
                  {   
                    
                      if(data == ""|| data == null){
                        return '';
                      }else{
                        return parseInt(data) +" %";
                      }
                  }}
              
            ],
            "columnDefs": [
             
              // { "orderable": false, "targets": 7 }
            ]
          
        });
    });

$('.displayDate').on('DOMSubtreeModified',function(){
  dataTable.draw();
})
// $("#hw").on('input', function(){
  
// }) 

  </script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="/js/weekPicker.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
      convertToWeekPicker($("#week"));
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
                        <label>Select Week</label>
                        <span class="displayDate" style="display:none"></span>
                        <input type="text" id="week" name="week" class="input-css" value="" readonly="">
                        <!-- <input type="text" id="hw" value="" hidden> -->
                    </div>
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Task</th>
                      <th>Done</th>
                      <th>Not Done</th>
                      <th>Late</th>
                      <th>Not Required</th>
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