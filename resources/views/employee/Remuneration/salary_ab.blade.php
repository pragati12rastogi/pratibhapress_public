@extends($layout)

@section('title', 'Salary Summary A & B')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Salary Summary A & B</a></li> 
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
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;
   
    // Data Tables
    $(document).ready(function() {
      dataTable = $('#salary_ab').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/salary/list/a/b/api",
          "columns": [
            {"data":"employee_number"},
            {"data":"name"},
            {"data": function(data,type,full,meta){
                
                var divide = [];
                if(data.total_salary){
                  divide = (data.total_salary).split(',');
                }
                var find_val;
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "a"){
                     find_val = spill[1];
                     break;
                  }
                }

                if(find_val){
                  return find_val;
                }else{
                  return "";
                }
            }},
            {"data": function(data,type,full,meta){
                
                var divide = [];
                if(data.total_salary){
                  divide = (data.total_salary).split(',');
                }
                var find_val ;
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "a"){
                     find_val= spill[2];break;
                  }
                }
                if(find_val){
                  return find_val;
                }else{
                  return "";
                }
            }},
            {"data": function(data,type,full,meta){
                
                var divide = [];
                if(data.total_salary){
                  divide = (data.total_salary).split(',');
                }
                var find_val ;
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "b"){
                     find_val= spill[1]; break;
                  }
                }
                if(find_val){
                  return find_val;
                }else{
                  return "";
                }
            }}, 
            {"data": function(data,type,full,meta){
                
                var divide = [];
                if(data.total_salary){
                  divide = (data.total_salary).split(',');
                }
                var find_val ;
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "b"){
                     find_val= spill[2]; break;
                  }
                }
                if(find_val){
                  return find_val;
                }else{
                  return "";
                }
            }}, 
            {"data":"total_ab"},
            {"data":"total_overtime"}
            ],
            "columnDefs": [
              { 
                // "orderable": false, "targets": 5
                 }
            
            ]
        });
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

                       
                    
                         <div class="row">
                           <table id="salary_ab" class="table table-bordered table-striped" >
                              <thead>
                                <tr>
                                 <th>Employee Code</th> 
                                  <th>Employee Name</th>
                                  <th>Total Salary A</th>
                                  <th>Overtime Eligible on Salary A</th>
                                  <th>Total Salary B</th>
                                  <th>Overtime Eligible on Salary B</th>
                                  <th>Total Salary(A+B)</th>
                                  <th>Total overtime eligible</th>
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