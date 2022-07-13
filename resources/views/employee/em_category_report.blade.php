@extends($layout)

@section('title', 'Employee Category Report')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Employee Category Report</a></li> 
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
    
   
    function emp_cat(){ 
      if(dataTable1){
        dataTable1.destroy();
      }   
      dataTable1 = $('#table_pf').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/emp/category/report/all/api",
          "columns": [
            {"data":"emp_name"},
              {"data":"employee_number"},
              {"data":"designation"},
              {"data": function(data,type,full,meta){
                
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes('Offer Letter')){
                  for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                    if(find[0] == "Offer Letter"){
                        return find[2];
                    }
                  }
                }else{
                  return '';
                }
              }},
              {"data": function(data,type,full,meta){
                // debugger;
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes('Offer Letter')){
                  for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                     if(find[0] == "Offer Letter"){
                        return find[6];
                    }
                  }
                }else{
                  return '';
                }
              }},
              {"data": function(data,type,full,meta){
                
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes('Fixed Term Appointment Letter')){
                 for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                     if(find[0]== "Fixed Term Appointment Letter"){
                        return find[2];
                    }
                  }
                }else{
                  return '';
                }
              }},
              {"data": function(data,type,full,meta){
                // debugger;
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes('Fixed Term Appointment Letter')){
                 for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                      if(find[0] == "Fixed Term Appointment Letter"){
                        if(find[4] != 0){
                            return find[4];
                        }else{
                          return '';
                        }
                    }
                  }
                }else{
                  return '';
                }
              }},
              {"data": function(data,type,full,meta){
                
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes('Trainee Appointment Letter')){
                  for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                     if(find[0]== "Trainee Appointment Letter"){
                        return find[2];
                    }
                  }
                }else{
                  return '';
                }
              }},
              {"data": function(data,type,full,meta){
                // debugger;
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes('Trainee Appointment Letter')){
                  for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                      if(find[0] == "Trainee Appointment Letter"){
                        if(find[4] != 0){
                            return find[4];
                        }else{
                          return '';
                        }
                    }
                  }
                }else{
                  return '';
                }
              }},
              {"data": function(data,type,full,meta){
                
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes("Probation Appointment Letter")){
                  for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                    if(find[0] == "Probation Appointment Letter"){
                        return find[2];
                    }
                  }
                }else{
                  return '';
                }
              }},
              {"data": function(data,type,full,meta){
                // debugger;
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes("Probation Appointment Letter")){
                  for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                      if(find[0] == "Probation Appointment Letter"){
                        if(find[4] != 0){
                            return find[4];
                        }else{
                          return '';
                        }
                    }
                  }
                }else{
                  return '';
                }
              }},
              {"data": function(data,type,full,meta){
                
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes('Confirmation Letter')){
                  for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                    if(find[0] == "Confirmation Letter"){
                        return find[2];
                    }
                  }
                }else{
                  return '';
                }
              }},
              {"data": function(data,type,full,meta){
                // debugger;
                var ds = [];
                if(data.list){
                  ds = (data.list).split(',');
                }
                var ct = [];
                if(data.category_name){
                  ct = (data.category_name).split(',');
                }
                if(ct.includes('Confirmation Letter')){
                  for(i=0;i<ds.length;i++){
                    var find = ds[i].split(':');
                      if(find[0] == "Confirmation Letter"){
                        if(find[4] != 0){
                            return find[4];
                        }else{
                          return '';
                        }
                      }
                  }
                }else{
                  return '';
                }
              }}
            
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 5 }
            
            ]
          
        });
    }
   
    // Data Tables
    $(document).ready(function() {
      
     emp_cat();
    
     
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
                  <div class="tab-content"> 
                    <div class="box-header with-border tab-pane fade active in" id="notpf" >
                      <table id="table_pf" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                        <th>Name</th> 
                          <th>Employee Code</th>
                          <th>Designation</th>
                          <th>Offer Letter Date</th>
                          <th>Tenure</th>
                          <th>Fixed Term Appointment Date</th>
                          <th>Tenure</th>
                          <th>Trainee Appointment Date</th>
                          <th>Tenure</th>
                          <th>Probation Appointment Date</th>
                          <th>Tenure</th>
                          <th>Confirmation Appointment Date</th>
                          <th>Tenure</th>
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