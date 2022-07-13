@extends($layout)

@section('title', 'Increment History ')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Increment History </a></li> 
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
      $('#history').hide();
    });

    function emp_wise(){
    // debugger;
      if($('#emp_name').val() != ""){

        $('#peekaboo').show();
        $('#history').show();

        if(dataTable){
          dataTable.destroy();
        }
        dataTable = $('#history').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url":"/increment/history/list/api",
            "datatype": "json",
            "data": function (data) {
                var emp = $('#emp_name').val();
                data.emp = emp;
            }
          },
          "columns": [
            {"data":"month_name"},
              
              {"data":"his_amt"},
              {"data":"increment_cat"},
              {"data":"increment_adjust_c"},
              {"data": function(data,type,full,meta){
                
                var divide = [];
                if(data.total_sal){
                  divide = (data.total_sal).split(',');
                }
                var find_val;
                //checking total salary of a 
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "a"){
                     find_val = spill[1];
                     break;
                  }
                }

                if(find_val){
                  if(data.added_insalary == 0 && (data.increment_cat == "Salary A Basic")){
                    if(data.amount_type == "cr")
                      return parseInt(find_val) + parseInt(data.amount);
                    else
                      return parseInt(find_val) - parseInt(data.amount);
                  }else{
                    return parseInt(find_val);
                  }
                }else{
                  return "";
                }
                
            }},
            {"data": function(data,type,full,meta){
                
                var divide = [];
                if(data.total_sal){
                  divide = (data.total_sal).split(',');
                }
                var find_val ;
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "b"){
                     find_val= spill[1]; break;
                  }
                }

                if(find_val){
                  //checking if increment was not happen yet
                  if(data.added_insalary == 0 && (data.increment_cat == "Salary B Basic")){
                    if(data.amount_type == "cr")
                      return (parseInt(find_val) + parseInt(data.amount));
                    else
                      return (parseInt(find_val) - parseInt(data.amount));
                  }else{
                    return parseInt(find_val);
                  }
                }else{
                  return "";
                }
            }},
            {"data": function(data,type,full,meta){
                
                var divide = [];
                if(data.total_sal){
                  divide = (data.total_sal).split(',');
                }
                var find_val ;
                //find overtime salary a
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "a"){
                     find_val= spill[2];break;
                  }
                }

                if(find_val){
                  if(data.added_insalary == 0 && (data.increment_cat == "Overtime eligible on salary A")){
                    if(data.amount_type == "cr")
                      return (parseInt(find_val) + parseInt(data.amount));
                    else
                      return (parseInt(find_val) - parseInt(data.amount));
                  }else{
                    return parseInt(find_val);
                  }
                    
                }else{
                  return "";
                }
            }},
            {"data": function(data,type,full,meta){
                
                var divide = [];
                if(data.total_sal){
                  divide = (data.total_sal).split(',');
                }
                var find_val ;
                //find overtime salary b
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "b"){
                     find_val= spill[2];break;
                  }
                }

                if(find_val){
                  if(data.added_insalary == 0 && (data.increment_cat == "Overtime eligible on salary B")){
                    if(data.amount_type == "cr"){
                      return (parseInt(find_val) + parseInt(data.amount));
                    }
                    else{
                      return (parseInt(find_val) - parseInt(data.amount));
                    }
                  }else{
                    return parseInt(find_val);
                  }
                }else{
                  return "";
                }
            }},
            {"data": function(data,type,full,meta){
               // debugger;
                var divide = [];
                if(data.total_sal){
                  divide = (data.total_sal).split(',');
                }
                var find_val_a  ;
                var find_val_b  ;
                //find total salary a
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "a"){
                     find_val_a= spill[1];break;
                  }
                }
                //find total salary b
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "b"){
                     find_val_b= spill[1];break;
                  }
                }
                var add_a;
                var min_a;
                if(find_val_a){
                  if(data.added_insalary == 0 && (data.increment_cat == "Salary A Basic")){
                    add_a= (parseInt(find_val_a) + parseInt(data.amount));
                    min_a= (parseInt(find_val_a) - parseInt(data.amount));
                  }else{
                    add_a=parseInt(find_val_a);
                    min_a=parseInt(find_val_a);
                  }
                }else{
                  add_a =0;
                  min_a =0;
                }
                var min_b;
                var add_b;
                if(find_val_b){
                  if(data.added_insalary == 0 && (data.increment_cat == "Salary B Basic")){
                    add_b= (parseInt(find_val_b) + parseInt(data.amount));
                    min_b= (parseInt(find_val_b) - parseInt(data.amount));
                  }else{
                    add_b=parseInt(find_val_b);
                    min_b=parseInt(find_val_b);
                  }
                }else{
                  add_b =0;
                  min_b=0;
                }
                //after increment a+b
                if(data.amount_type == "cr"){
                  return add_a + add_b;
                }
                else if(data.amount_type == "dr"){
                  return min_a+ min_b;
                }else{
                  return"";
                }
                
            }},
            {"data": function(data,type,full,meta){
                
                var divide = [];
                if(data.total_sal){
                  divide = (data.total_sal).split(',');
                }
                var find_val_a=0 ;
                var find_val_b =0;
                //find total salary a
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "a"){
                     find_val_a= spill[2];break;
                  }
                }
                //find total salary b
                for (var i = 0; i < divide.length; i++) {
                  var spill = (divide[i]).split(':');
                  if(spill[0] == "b"){
                     find_val_b= spill[2];break;
                  }
                }
                var add_a;
                var min_a;
                if(find_val_a){
                  if(data.added_insalary == 0 && (data.increment_cat == "Overtime eligible on salary A")){
                    add_a= (parseInt(find_val_a) + parseInt(data.amount));
                    min_a= (parseInt(find_val_a) - parseInt(data.amount));
                  }else{
                    add_a=parseInt(find_val_a);
                    min_a=parseInt(find_val_a);
                  }
                }else{
                  add_a =0;
                  min_a =0;
                }
                var min_b;
                var add_b;
                if(find_val_b){
                  if(data.added_insalary == 0 && (data.increment_cat == "Overtime eligible on salary B")){
                    add_b= (parseInt(find_val_b) + parseInt(data.amount));
                    min_b =(parseInt(find_val_b) - parseInt(data.amount));
                  }else{
                    add_b=parseInt(find_val_b);
                    min_b=parseInt(find_val_b);
                  }
                }else{
                  add_b =0;
                  min_b=0;
                }
                //after increment overtime a+b
                if(data.amount_type == "cr"){
                  return add_a + add_b;
                }
                else if(data.amount_type == "dr"){
                  return min_a+ min_b;
                }else{
                  return "";
                }
                
            }}
            ],
            "columnDefs": [
              {  }
            
            ]
        });
      }else{
        $('#peekaboo').hide();
        
      }
      
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

                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Employee name <sup>*</sup></label>
                                <select name="emp_name" class="select2 input-css emp_name" id="emp_name" onchange="emp_wise()">
                                    <option value="">Select Employee Name</option>
                                    @foreach($employee as $emp)
                                        <option value="{{$emp->id}}">{{$emp->name."(". $emp->employee_number.")"}}</option>
                                    @endforeach
                                </select>
                                
                            </div>
                          </div><br><br>
                    
                         <div class="row" id="peekaboo">
                           <table id="history" class="table table-bordered table-striped" >
                              <thead>
                                <tr>
                                 <th>Increment applicable from month</th> 
                                  <th>Increment amount</th>
                                  <th>Increment category</th>
                                  <th>Increment Adjustment in C</th>
                                  <th>Total salary A after increment</th>
                                  <th>Total salary B after increment</th>
                                  <th>Overtime eligible on A after increment</th>
                                  <th>Overtime eligible on B after increment</th>
                                  <th>Total A + B salary after increment</th>
                                  <th>Total Overtime eligible after increment</th>
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