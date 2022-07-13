@extends($layout)

@section('title', 'Employee Task List')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Employee Task List</a></li> 
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
     
      dataTable = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/checklist/emp/task/list/api",
          "columns": [
              {"data":"name"},
              {"data":"department"},
              {"data":"task_name"},
              {"data":"frequency"},
              {"data":"day"},
              {"data":"task_date", "render": function(data,type,full,meta)
                  {
                        if(data == '1970-01-01'){
                           return "";
                        }else{
                          return data;
                        }
                  }},
              {"data":"month"},
                {
                  "data": function(data,type)
                  {
                    console.log(data);
                    var cd=data.check_created;
                    if(data.check_created){
                      cd = (data.check_created).split(',');
                    }else{
                      cd =[];
                    }
                     debugger;
                    var now = new Date();
                    var form_date =now.toISOString().substr(0,10);
                    var day=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
                    var js_Day =now.getDay();
                    var curr_month = now.toLocaleString('default', { month: 'long' });
                    var future = new Date((new Date()).valueOf() + 1000*3600*24);
                    var future_form_date = future.toISOString().substr(0,10);
                    var future_js_Day = future.getDay();
                    var fortnightly_date = new Date(+ new Date(data.task_date) + 12096e5).toISOString().substr(0,10);

                    console.log(data.month);
                   
                    var holiday=[];
                    if($("#full_holi_dates").text() != null || $("#full_holi_dates").text() != ''){
                      holiday = $("#full_holi_dates").text().split(',');
                    }

                    if(data.frequency =="daily" && data.emp_status == "active"){
                      if(cd.includes(form_date) || js_Day==0 || holiday.includes(form_date) ){
                        return "";
                      }else{
                        return "<a href='/checklist/emp/status/Done/"+data.id+"' ><button class='btn btn-success btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Done/"+data.id+"' ><button class='btn btn-danger btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Required/"+data.id+"' ><button class='btn btn-facebook btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                      }
                    }else if(data.frequency =="weekly" && data.emp_status == "active"){
                      var getweek =weeklist();
                      var present = findCommonDate(getweek,cd);
                      var dayinNo = day.indexOf((data.day).charAt(0).toUpperCase() + (data.day).slice(1));
                      if(present){
                        return "";
                      }else{
                        if(day[js_Day] == data.day || (holiday.includes(future_form_date) && day[future_js_Day] == data.day)|| (holiday.includes(future2_form_date) && day[future2_js_Day] == data.day && day[future_js_Day] == "Sunday"))
                        {
                          if(cd.includes(form_date) || js_Day==0 || holiday.includes(form_date)){
                          return "";
                          }else{
                          return "<a href='/checklist/emp/status/Done/"+data.id+"' ><button class='btn btn-success btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Done/"+data.id+"' ><button class='btn btn-danger btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Required/"+data.id+"' ><button class='btn btn-facebook btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                          }
                        }else if(dayinNo < js_Day && js_Day!=0 && !holiday.includes(form_date)){
                          return "<a href='/checklist/emp/status/Done/"+data.id+"' ><button class='btn btn-success btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Done/"+data.id+"' ><button class='btn btn-danger btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Required/"+data.id+"' ><button class='btn btn-facebook btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                        }else{
                          return "";
                        }
                      }
                    }else if(data.frequency =="monthly" && data.emp_status == "active"){
                      var isMonth = new Array();
                      var isYear = new Array();
                      var endMonth = monthLast();
                      var i=0;
                      for (x of cd) {
                        isMonth[i]= new Date(x).getMonth();
                        isYear[i]= new Date(x).getFullYear();
                        i++;
                      }
                      if(isMonth.includes(now.getMonth()) && isYear.includes(now.getFullYear())){
                        return"";
                      }else{
                        if(now.getDate() == new Date(data.task_date).getDate() || (holiday.includes(future_form_date) && future.getDate() == new Date(data.task_date).getDate())){
                        if(cd.includes(form_date)|| js_Day==0 || holiday.includes(form_date)){
                          return "";
                        }else{
                          return "<a href='/checklist/emp/status/Done/"+data.id+"' ><button class='btn btn-success btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Done/"+data.id+"' ><button class='btn btn-danger btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Required/"+data.id+"' ><button class='btn btn-facebook btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                        }
                      }else if(new Date(data.task_date).getDate()<now.getDate() && endMonth.getDate()>=now.getDate() && js_Day!=0 && !holiday.includes(form_date)){
                          return "<a href='/checklist/emp/status/Done/"+data.id+"' ><button class='btn btn-success btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Done/"+data.id+"' ><button class='btn btn-danger btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Required/"+data.id+"' ><button class='btn btn-facebook btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                      }else{
                        return "";
                        }
                      }
                      
                    }else if (data.frequency =="fortnightly"  && data.emp_status == "active") {
                      if(form_date == data.task_date || form_date == fortnightly_date || (holiday.includes(future_form_date) && future_form_date == data.task_date || future_form_date == fortnightly_date)){
                        if(cd.includes(form_date) || js_Day==0 || holiday.includes(form_date)){
                          return "";
                        }else{
                          return "<a href='/checklist/emp/status/Done/"+data.id+"' ><button class='btn btn-success btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Done/"+data.id+"' ><button class='btn btn-danger btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Required/"+data.id+"' ><button class='btn btn-facebook btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                        }
                      }else{
                        return "";
                      }
                    }else if(data.frequency =="quarterly"  && data.emp_status == "active"){
                      
                      if(now.getDate() == new Date(data.task_date).getDate() && data.month == curr_month.toLowerCase() || (holiday.includes(future_form_date) && future.getDate() == new Date(data.task_date).getDate() && data.month == curr_month.toLowerCase())){
                        if(cd.includes(form_date) || js_Day==0 || holiday.includes(form_date) ){
                          return "";
                        }else{
                          return "<a href='/checklist/emp/status/Done/"+data.id+"' ><button class='btn btn-success btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Done/"+data.id+"' ><button class='btn btn-danger btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Required/"+data.id+"' ><button class='btn btn-facebook btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                        }
                      }else{
                        return "";
                      }
                    }else if(data.frequency =="half yearly" && data.emp_status == "active"){
                      if(now.getDate() == new Date(data.task_date).getDate() && data.month == curr_month.toLowerCase() || (holiday.includes(future_form_date) && future.getDate() == new Date(data.task_date).getDate() && data.month == curr_month.toLowerCase())){
                        if(cd.includes(form_date) || js_Day==0 || holiday.includes(form_date) ){
                          return "";
                        }else{
                          return "<a href='/checklist/emp/status/Done/"+data.id+"' ><button class='btn btn-success btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Done/"+data.id+"' ><button class='btn btn-danger btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Required/"+data.id+"' ><button class='btn btn-facebook btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                        }
                      }else{
                        return "";
                      }
                    }else if(data.frequency =="annually" && data.emp_status == "active"){
                      if(now.getDate() == new Date(data.task_date).getDate() && data.month == curr_month.toLowerCase() || (holiday.includes(future_form_date) && future.getDate() == new Date(data.task_date).getDate() && data.month == curr_month.toLowerCase())){
                        if(cd.includes(form_date) || js_Day==0 || holiday.includes(form_date)){
                          return "";
                        }else{
                          return "<a href='/checklist/emp/status/Done/"+data.id+"' ><button class='btn btn-success btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Done/"+data.id+"' ><button class='btn btn-danger btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/checklist/emp/status/Not Required/"+data.id+"' ><button class='btn btn-facebook btn-xs' onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                        }
                      }else{
                        return "";
                      }
                    }

                  }
                }
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": 7 }
            ]
          
        });
    });
function weeklist(){
  // debugger
  Date.prototype.getWeek = function(start)
  {
          //Calcing the starting point
      start = start || 0;
      var today = new Date(this.setHours(0, 0, 0, 0));
      var day = today.getDay() - start;
      var date = today.getDate() - day;

          // Grabbing Start/End Dates

          var weekdates=new Array();
          for(var i=0;i<=7;i++){
              weekdates[i] = new Date(today.setDate(date + i)); 
          }

      return weekdates;
  }
  var Dates = new Date().getWeek();
  var full_week=[];
  var i=0;
  for (x of Dates) {
    full_week[i]= x.toISOString().substr(0,10);
    i++;
  }
   full_week.shift()
  return full_week;
}

function findCommonDate(array1, array2) { 
  
  // Loop for array1 
  for(let i = 0; i < array1.length; i++) { 
    
    // Loop for array2 
    for(let j = 0; j < array2.length; j++) { 
      
      // Compare the element of each and 
      // every element from both of the 
      // arrays 
      if(array1[i] === array2[j]) { 
      
        // Return if common element found 
        return true; 
      } 
    } 
  } 
  
  // Return if no common element exist 
  return false; 
} 

function monthLast() { 
  var date = new Date();     
  var lastDay =  
     new Date(date.getFullYear(), date.getMonth() + 1, 0); 
       
  return lastDay;
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
                    
                    <p id="full_holi_dates" hidden="">{{$holiday}}</p>
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Department</th>
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