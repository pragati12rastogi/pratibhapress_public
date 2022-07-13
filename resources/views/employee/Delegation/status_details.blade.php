@extends($layout)

@section('title', 'Delegation Status Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="/delegation/summary"><i class=""></i>Delegation Summary</a></li> 
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
</style>
 
@endsection
@section('js')

<script src="/js/dataTables.responsive.js"></script>
<script>
  var dataTable;

  // Data Tables
  $(document).ready(function() {
    done();
    $(".select2").css('width','280px');
  });

  function done(){
    if(dataTable){
      dataTable.destroy();
    }
    dataTable = $('#table_done').DataTable({
      "processing": true,
      "serverSide": true,
      "aaSorting":[],
      "responsive": true,
      "ajax": "/delegation/status/details/summary/api/{{$id}}",
      "columns": [
        {"data":"name","render": function(data,type,full,meta){
          return data +"("+full.employee_number+")";
        }},
        {"data":"task_detail"},
        {"data":"delegation_status"},
        {"data":"completion_date"},
        {"data":"detail"},
        {"data":"job_image","render": function(data,type,full,meta){
          if(data){
            return "<a href='/upload/completed_job_image/"+data+"' target='_blank'>Image</a> &nbsp;";
          }else{
            return "";
          }
        }}
        
      ],
      "columnDefs": [
        // { "orderable": false, "targets": 6 }
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
                    @section('titlebutton')
                    
                      @endsection
                    
                    <table id="table_done" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>Employee</th>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Completion Date</th>
                            <th>Detail</th>
                            <th>Image</th>
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