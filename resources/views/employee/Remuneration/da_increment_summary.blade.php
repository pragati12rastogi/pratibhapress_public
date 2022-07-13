@extends($layout)

@section('title', 'DA Increment History ')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>DA Increment History </a></li> 
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
.md_label{
  display: inline;
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
      emp_wise();
    });

    function emp_wise(){
    // debugger;
      
        if(dataTable){
          dataTable.destroy();
        }
        dataTable = $('#da_history').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/da/salary/summary/api",
          "columns": [
            {"data":"month_name"},
              {"data":"da_cat"},
              {"data":"sal_cat"},
              {"data":"amount_inc"}
             
            
            ],
            "columnDefs": [
              {  }
            
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

                        
                         <div class="row" >
                           <table id="da_history" class="table table-bordered table-striped" >
                              <thead>
                                <tr>
                                 <th>Month applicable from</th> 
                                  <th>DA Category</th>
                                  <th>Salary Category</th>
                                  <th>Increment amount</th>
                                  
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                         
                          </table>
                         </div>
                    
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection