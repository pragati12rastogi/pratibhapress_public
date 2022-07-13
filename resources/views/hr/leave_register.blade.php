@extends($layout)

@section('title', 'Leave Register')
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Leave Register</a></li> 
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

    function getemployee(){
      if(dataTable){
      dataTable.destroy();
    }
    
      dataTable = $('#holiday').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          
          "createdRow": function( row, data, dataIndex){
                if( data.leaving_date=="-"){
                  
                }
                else{
                  $(row).css('background-color','#73AC80');
                    
                    
                }
            },
            "ajax": {
            "url": "/hr/leave/register/api",
            "datatype": "json",
                "data": function (data) {
                   
                    var year = $('.date').val();
                    data.date = year;

                    var emp = $('.employee').val();
                    data.employee = emp;
                   
                },

            },

            
          "columns": [
              {"data":"name"},
              {"data":"employee_number"},
              {"data":"local_address"},
              {"data":"designation"},
              {"data":"department"},
              {"data":"father_name"},
              { 
                data:function(data, type, full, meta){
                  var total_p=data.total_present;
                  var total_l=parseInt(total_p/20);
                  var total_a=data.absent;
                  var closing_b=parseInt(total_l)-parseInt(total_a);
                  var year = $('#year').val();
                  return parseInt(total_l)+parseInt(data.carried_leave);
                   } ,"orderable": false
            },
             
              {
                "targets": [ -1 ],
                  "data":"emp_id", "render": function(data,type,full,meta)
                  { 
                    var str = ''; 
                    var year = $(".date").val();
                    str +="<button id="+data+" class='job_det btn btn-success btn-xs'>Details</button> &nbsp;"
                    str +="<a href='/hr/leave/register/print/"+data+"/"+year+"' target='_blank'><button  class='btn_print btn btn-info btn-xs'>Print</button></a>";
                    return str;

                  }
              }
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": 3 },
              { "orderable": false, "targets": 5 },
              { "orderable": false, "targets": 7 }
            ]
          
        });

    }
    // Data Tables
    $(document).ready(function() {
    $('.date').datepicker({
    autoclose: true,
      format: 'yyyy',
      viewMode: "years", 
    minViewMode: "years"
}).datepicker("setDate", new Date());

getemployee();
var last_ele = null ;
    var last_tr = null ;
$('#holiday tbody').on('click', 'button.job_det', function () {
        var tr = $(this).parents('tr');
        var row = dataTable.row( tr );
        var data=$(this).attr("id");
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
          if(last_ele)
          {
            //  last_ele.child.hide();     
          }
          $(this).parents('li').children('div').remove();
                
          $(this).parents('li').append('<center><div class="card" ><h5> Processing...</h5></div></center>');
              
          row.child('<center><div class="card" ><h5> Processing...</h5></div></center>').show();
          getdata1(data,row,this)

          last_ele=row;
          last_tr=tr;
          tr.addClass('shown');
        }
    } );
    });

    $('.date').on('change', function () {
      var yr=$('.date').val();
      $('.employee').empty();
      $('.employee').append("<option value='0'>Select Employee</option>");
      getemployee();
      $('#ajax_loader_div').css('display','block');

      $.ajax({
            url: "/get/employee/"+yr,
              type: "GET",
              success: function(result) {
                var emp=result;
                $('.employee').empty();
                $('.employee').append("<option value='0'>Select Employee</option>");
                for (var i = 0; i < emp.length; i++) {
                  
                    $('.employee').append('<option value="'+emp[i].id+'">'+emp[i].name+'</option>')
                    }
                    $('#ajax_loader_div').css('display','none');
              }
      });
      
          });

          $('.employee').on('change', function () {
            getemployee();
          });
          function getdata1(data,ele,button)  {  
      var out;
      $('#ajax_loader_div').css('display','block');
var year = $('.date').val();
                    data.date = year;
      $.ajax({
               type:'get',
               url:"/hr/leave/register/details/"+data+"/"+year,
               timeout:600000,
                   
               success:function(data) {
                $(button).parents('li').children('div').remove();
                $(button).parents('li').children('center').remove();
                
                $(button).parents('li').append(data);
                  ele.child(data).show();
                  $('#ajax_loader_div').css('display','none');

                }

            });

            return out;
    }

    $("#export_anchor").click(function(){
         geturl();
    });
    
    function geturl(){
        var emp = $('.employee').val();
        var year = $('.date').val();
       $("#export_anchor").attr("href", '/export/data/leaveregister?emp='+emp+'&year='+year);
   
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
                        <div class="col-md-10"></div>
                        <div class="col-md-1">
                        <a href="/export/data/leaveregister" id="export_anchor">
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
                    <table id="holiday" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Employee Name</th>
                      <th>Emp Code</th>
                      <th>Address</th>
                      <th>Designation</th>
                      <th>Department</th>
                      <th>Father's Name</th>
                      <th>Opening Leave balance for the year</th>
                      <th>Action</th>
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