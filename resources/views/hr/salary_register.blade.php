@extends($layout)

@section('title', 'Salary Register')
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Salary Register</a></li> 
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
  #holiday {
          width: 100%;
          overflow-x: auto;
        }
        .sun{
    color: #F50057;
  }

  .holy{
    color: #66BB6A;
  }
        .rotation {
         
     -moz-transform: rotate(-90.0deg);  /* FF3.5+ */
      -ms-transform: rotate(-90.0deg);  /* IE9+ */
       -o-transform: rotate(-90.0deg);  /* Opera 10.5 */
  -webkit-transform: rotate(-90.0deg);  /* Safari 3.1+, Chrome */
          transform: rotate(-90.0deg);  /* Standard */
          height: auto;
          width:auto;
          margin-top: 40px;
}

</style>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var dataTable;

    function getemployee(){
      var str = [];
      // str.push({"data":"emp_id"});
     
      str.push({"data":"emp_name"});
      str.push({"data":"employee_number"});
 
      str.push(  { 
                "data":"local_address","render": function(data, type, full, meta){
                 return  "<div class='address'>"+data+"</div><br><br><br>";
                },"orderable": false
            });
      str.push({"data":"designation","orderable": false});
      str.push({"data":"department","orderable": false});
      str.push({"data":"father_name","orderable": false});
      
      var yr=$('.date').val();
      var mm=yr.split('-');

      var dd=getDaysInMonth(mm[0], mm[1]);
      mm[0]=mm[0]-1;
      var date = new Date(mm[1], mm[0], 1);  // 2009-11-10
     
      var month = date.toLocaleString('default', { month: 'short' });
      for(var i = 1 ; i <= dd ; i++)
      {
        if(i<10){
          var j="0"+i;
        }
        else{
          j=i;
        }
          
        str.push({
                "data":j+'_'+month,"render": function(data, type, full, meta){
                    var st=data;
                    if(st==""){
                      return data;
                    }
                    else if(st=="WO"){
                      return "<div class='rotation sun'>Sunday</div>";
                    }
                    else if(st!="P" && st!="A"){
                      return "<div class='rotation holy'>"+data+"</div>"
                    }
                    else{
                      return data;
                    }
                    
                } ,"orderable": false
                  
                  });
                
      }
      str.push({"data":"total_present_current","orderable": false});
                  str.push({"data":"total_absent_current","orderable": false});
                  str.push({"data":"total_salaryC","orderable": false});
                  str.push({"data":"pf_ded","orderable": false});
                  str.push({"data":"esi_ded","orderable": false});
                  str.push({"data":"opening_advance","orderable": false});
                  str.push({"data":"adv_ded","orderable": false});
                  str.push({"data":"balance_advance","orderable": false});
                  str.push({"data":"total_present_current",
                    "render": function(data,type,full,meta) { 
                              var str = '';
                                var year = $(".date").val();
                                str +="<a href='/hr/salary/register/print/"+full.id+"/"+year+"' target='_blank'><button  class='btn_print btn btn-info btn-xs'>Print</button></a>";
                                return str;
                        },
                "orderable": false});
    if(dataTable){
      dataTable.destroy();
    }
        
       dataTable = $('#holiday').DataTable({
         "processing": true,
         "serverSide": true,
          "autoWidth": false,
          "fixedColumns":   {
            "leftColumns": 2
        },
        "scrollY":        "300px",
        "scrollX":        true,
        "scrollCollapse": true,
         "ajax": "",
         "aaSorting": [],
         "pageLength": 10,
         "responsive": false,
          "ajax": {
            "url": "/hr/salary/register/api",
            "datatype": "json",
            "type": "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "data": function (data) {
                   
                    var year = $('.date').val();
                    data.year = year;

                    var emp = $('.employee').val();
                    data.emp = emp;
                   
                },
                  
            },
          "columns": str
       });
  

    }
    var getDaysInMonth = function(month,year) {
 return new Date(year, month, 0).getDate();
};
    // Data Tables
    $(document).ready(function() {
      $('.date').datepicker({
    autoclose: true,
      format: 'mm-yyyy',
      viewMode: "months", 
    minViewMode: "months"
}).datepicker("setDate", new Date());
    });

$('.date').on('change', function () {
      var yr=$('.date').val();
     
          if(dataTable){
          dataTable.destroy();
        }
      var mm=yr.split('-');

      var dd=getDaysInMonth(mm[0], mm[1]);
      mm[0]=mm[0]-1;
      var date = new Date(mm[1], mm[0], 1);  // 2009-11-10
     
      var month = date.toLocaleString('default', { month: 'short' });
      $('.employee').empty();
      $('.employee').append("<option value='0'>Select Employee</option>");
      $('.tb').empty();
        var ls='<table id="holiday" class="table table-bordered table-striped" style="width:100%"><thead>'+
            '<tr>'+
            '<th>Employee Name</th>'+
            '<th>Emp Code</th>'+
            '<th>Address</th>'+
            '<th>Designation</th>'+
            '<th>Department</th>'+
            '<th>Father Name</th>';
            for(var i=1;i<=dd;i++){
            if(i<10){
              var j="0"+i;
            }
            else{
              j=i;
            }
              ls=ls+'<th>'+j+" "+month+'</th>';
            }
        ls=ls+'<th>Total Present days</th>'+
              '<th>Total Absent Days</th>'+
              '<th>Total Salary C paid</th>'+
              '<th>PF Deduction</th>'+
              '<th>ESI Deduction</th>'+
              '<th>Opening advance</th>'+
              '<th>Advance deducted</th>'+
              '<th>Advance balance </th>'+      
              '<th>Action </th>';      
           ls=ls+ '</tr>'+
        '</thead>'+
        '<tbody></tbody></table>';
    $(".tb").append(ls);
      
      $('#ajax_loader_div').css('display','block');

      $.ajax({
            url: "/get/employee/"+yr,
              type: "GET",
              success: function(result) {
                var emp=result;
                console.log(result);
                $('.employee').empty();
                $('.employee').append("<option value='0'>Select Employee</option>");
                for (var i = 0; i < emp.length; i++) {
                  
                    $('.employee').append('<option value="'+emp[i].id+'">'+emp[i].name+'</option>')
                    }
                    $('#ajax_loader_div').css('display','none');
              }
      });
      getemployee();
          });

          $('.employee').on('change', function () {
            getemployee();
          });

          $("#export_anchor").click(function(){
         geturl();
    });
    
    function geturl(){
        var emp = $('.employee').val();
        var year = $('.date').val();
       $("#export_anchor").attr("href", '/export/data/salary/register?emp='+emp+'&year='+year);
   
    }
  </script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                    </div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                   
                      @endsection
                      <div class="row">
                        <div class="col-md-1 pull-right">
                        <a href="/export/data/salary/register" id="export_anchor">
                          <button class="btn btn-info">Export</button>
                        </a>
                      </div>
                      </div>
                      <div class="row">
                      <div class="col-md-6">
                          <label for="">Select Year</label>
                          <input type="text" class="date input-css datepickerss" name="date">
                      </div>
                      <div class="col-md-6">
                       <label for="">Select Employee</label>
                       <select name="employee" id="" class="select2 input-css employee">
                       <option value="0">Select Employee</option>
                       </select>
                      </div>

                      </div>
                    <div class="tb">
                    <table id="holiday" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                    
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