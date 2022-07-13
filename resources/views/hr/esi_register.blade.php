@extends($layout)

@section('title', 'ESI Register')

@section('breadcrumb')

    <li><a href="#"><i class=""></i>ESI Register</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
@media (max-width: 768px){
    .content-header>h1 {
      display: inline-block;
    }
  }
  @media (max-width: 425px){   
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
    function daysInMonth (month, year) { 
        return new Date(year, month, 0).getDate(); 
    }
    function create_datatable(){
       if(dataTable){
        dataTable.destroy();
      }
      var year = $('#year').val();
      var month = $('#month').val();
      var days=daysInMonth(month, year);
      dataTable = $('#att_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/hr/recruitment/esi/register/api",
            "datatype": "json",
                "data": function (data) {
                    var year = $('#year').val();
                    data.year = year;
                    var month = $('#month').val();
                    data.month = month;
                },
            },
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"esi_no"},
              {"data":"gross_wages"},
              { 
                data:function(data, type, full, meta){
                  var gross_wages=data.gross_wages;
                  var effective_present=data.effective_present;
                  var effective_absent=data.effective_absent;
                  var eps=parseFloat(parseFloat(gross_wages)/days)*parseInt(effective_present);
                  return eps;
                  
                   } ,"orderable": false
            },
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": false }
            ]
           
      });
    }


    // Data Tables
    $(document).ready(function() {
      create_datatable();
    });
    $('#year').datepicker({
      autoclose: true,
      format: 'yyyy',
      viewMode: "years", 
    minViewMode: "years"
}).datepicker("setDate", new Date());
    $('#month').datepicker({
      autoclose: true,
      format: 'mm',
      viewMode: "months", 
    minViewMode: "months"
}).datepicker("setDate", new Date());
    $('#year').on('change', function () {
      create_datatable();
    });
    $('#month').on('change', function () {
      create_datatable();
    });
 $("#anchor").click(function(){
         geturl();
    });
  function geturl(){
        var month = $('#month').val();
        var year = $('#year').val();
        $("#anchor").attr("href", '/export/data/esiregister?month='+month+'&year='+year);
   
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
                      <div class="col-md-3" >
                        <label>Select Year</label>
                        <input type="text" name="year" id="year" class="input-css" autocomplete="off">
                      </div>
                      <div class="col-md-3" >
                        <label>Select Month</label>
                        <input type="text" name="month" id="month" class="input-css" autocomplete="off">
                      </div>
                      <div class="col-md-1 pull-right">
                        <a href="/export/data/esiregister" id="anchor">
                          <button class="btn btn-info">Export</button>
                        </a>
                      </div>
                    </div><br>
                    <table id="att_table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>Employee Number</th>
                          <th>Employee name</th>
                          <th>ESI Number</th>
                          <th>Wages</th>
                          <th>Total monthly wages</th>
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