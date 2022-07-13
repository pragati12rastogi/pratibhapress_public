@extends($layout)

@section('title', 'Leaves Encashment Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Leaves Encashment Summary</a></li> 
@endsection
@section('css')
<style>

</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>

</script>
  <script>
  

   var dataTable;
    function datatablefn() {

    if(dataTable){
      dataTable.destroy();
    }
    var year = $('#year').val();
       dataTable = $('#admin_table').DataTable({
       
         "processing": true,
         "serverSide": true,
         "ajax": "",
         "aaSorting": [],
         "pageLength": 10,
         "responsive": false,
          "ajax": {
            "url": "/hr/leave/enhancement/list/api",
            "datatype": "json",
                "data": function (data) {
                   
                    var year = $('#year').val();
                    console.log(year);
                    
                    data.year = year;
                   
                },

            },
          "columns": [
              {"data":"employee_number"},
              {"data":"name"},
              { 
                data:function(data, type, full, meta){
                  var total_p=data.total_present;
                  var total_l=parseInt(total_p/20);
                    return total_l;
                } ,"orderable": false
            },
            { 
                data:function(data, type, full, meta){
                  var total_p=data.total_present;
                  var total_l=parseInt(total_p/20);
                  var total_a=data.absent;
                  var closing_b=parseInt(total_l)-parseInt(total_a)
                    return total_a;
                } ,"orderable": false
            },
            {"data":"carried_leave"},
            {"data":"paid_leave"},
            {"data":"amount_paid"},
            { 
                data:function(data, type, full, meta){
                  var total_p=data.total_present;
                  var total_l=parseInt(total_p/20);
                  var total_a=data.absent;
                  var closing_b=parseInt(total_l)-parseInt(total_a);
                  var total_sal_a=data.total_sal_a;
                  if(closing_b)
                  
                  console.log(year);
                  
                  if(data.carried_leave=="-"){
                    return '<a ab onclick="cancel_alert_dailog('+data.emp_id+','+closing_b+','+year+','+total_l+','+total_sal_a+')"><button class="btn btn-primary btn-xs"> Adjust Leave Encashment </button></a> &nbsp;';
               
                  }
                  else{
                    return "";
                  }
                   } ,"orderable": false
            },

          ]
       });
  
     
}

   $(document).ready(function() {
    datatablefn();

   
    
   // $( "#admin_table" ).parent().css( "overflow-x", "auto" );

  });
  function cancel_alert_dailog(id,closing_b,year,total_l,total_sal_a)
    {
      $('#modal_div').empty().append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Balance Leaves</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/hr/leave/enhancement/form" enctype="multipart/form-data">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                    '<h5><u>Total Leaves To Be Adjusted : '+closing_b+'</u></h5>'+
                    '<span id="fc_err" style="color:red; display: none;"></span>'+
                      '<div class="row">'+
                        '<div class="col-md-6">'+
                        '<input type="hidden" name="emp_id"  id="emp" class="input-css" value="'+id+'">'+
                        '<input type="hidden" name="total_l"  id="total_l" class="input-css" value="'+total_l+'">'+
                        '<input type="hidden" name="closing"  id="closing" class="input-css" value="'+closing_b+'">'+
                           '<label>Leaves to be carried forward <sup>*</sup></label>'+
                            '<input type="number" name="carried_l" min="0" id="carried_l" class="input-css" autocomplete="off" required>'+
                        '</div>'+
                        '<div class="col-md-6">'+
                          '<label>Leaves to be paid <sup>*</sup></label>'+
                            '<input type="number" name="paid_l" min="0" id="paid_l" class="input-css paid_l"  autocomplete="off" required>'+
                        '</div>'+
                      '</div>'+
                      '<div class="row">'+
                        '<div class="col-md-6">'+
                           '<label>Amount to be paid <b> (leaves to be paid*(Salary A))</b> <sup>*</sup></label>'+
                            '<input type="number" name="amount" min="0" step="any" id="amount" class="amount input-css" autocomplete="off" required>'+
                        '</div>'+
                        '<div class="col-md-6">'+
                          
                            '<input type="hidden" name="year" id="year" value="'+year+'" class="input-css" autocomplete="off" required>'+
                        '</div>'+
                      '</div>'+
                    '</div>'+
                    '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>'+
                      '<a target="_blank"><button type="submit" class="btn btn-success">Submit</button></a>'+
                    '</div>'+
                    '</form>'+
                '</div>'+
              '</div>'+
            '</div>'
      );
          $(document).find('#myModal').modal("show"); 
          $('.paid_l').change(function(){
            var paid_l=$('.paid_l').val();
            
            var tot_amt=parseInt(paid_l)*parseFloat(total_sal_a);
            $('.amount').val(tot_amt);
          });
          // $('#year').datepicker({
          //       format: "yyyy",
          //       weekStart: 1,
          //       orientation: "bottom",
          //       keyboardNavigation: false,
          //       viewMode: "years",
          //       minViewMode: "years",
          //       autoclose: true
          // }).datepicker("setDate", new Date());
        
       
  }
  $('#year').datepicker({
    format: "yyyy",
    weekStart: 1,
    orientation: "bottom",
    keyboardNavigation: false,
    viewMode: "years",
    minViewMode: "years",
    autoclose: true
}).datepicker("setDate", new Date());
  $('#year').on('change', function () {
    datatablefn();
          });
 
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
            <div id="modal_div"></div>
            <div class="box-header with-border">
            <div class="row">
            <div class="col-md-3" >
                            <label>Select Year</label>
                            <input type="text" name="year" id="year" class="input-css" autocomplete="off">
                    </div><br><br>
            </div>
            </div>
            <div class="box-body">
           
              <table id="admin_table" style="width:100%" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                  <th>Employee Code</th>
                    <th>Employee Name</th>
                    <th>Total leaves</th>
                    <th>Leaves Taken</th>
                    <th>Carried Leave</th>
                    <th>Paid Leave</th>
                    <th>Amount Paid</th>
                    <th>Action</th>
                   
                  </tr>
                  </thead>
                  <tbody>
  
                  </tbody>
             
              </table>
            </div>
          </div>
        <!-- /.box -->
      </section>
@endsection