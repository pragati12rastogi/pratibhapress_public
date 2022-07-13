@extends($layout)

@section('title', 'Completed Years Report')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Completed Years Report</a></li> 
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
   

    function oneyear(){
       if(dataTable1){
          dataTable1.destroy();
       }
      dataTable1 = $('#table_one_yr').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/employee/year/completed/report/api",
            "datatype": "json",
                "data": function (data) {
                    var year = $('#year').val();
                    var month = $('#month').val();
                    var year2 = $('#year2').val();
                    var month2 = $('#month2').val();
                    data.from = parseInt(year)+ parseInt(month);
                    data.to = parseInt(year2)+ parseInt(month2);
                }
            },
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
              {"data":"doj"},{"data" :"year", "render": function(data,type,full,meta){
                return full.year+" year "+full.month+' months';
              }
            }
            
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 5 }
            
            ]
          
        });
    }

   
    // Data Tables
    $(document).ready(function() {
      oneyear();
    });
    $('.changetable').change(function() {
        dataTable1.draw();
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
                    
                    <div class="row">
                        <div class="col-md-2 {{ $errors->has('year') ? 'has-error' : ''}}">
                            <label>From Year<sup>*</sup></label>
                            <select id="year" class="select2 changetable" name="year">
                              <option value="0">Select Year</option>
                              <option value="12">One Year</option>
                              <option value="24">Two Year</option>
                              <option value="36">Three Year</option>
                              <option value="48">Four Year</option>
                              <option value="60">Five Year</option>
                              <option value="72">Six Year</option>
                              <option value="84">Seven Year</option>
                              <option value="96">Eight Year</option>
                              <option value="108">Nine Year</option>
                              <option value="120">Ten Year</option>
                            </select>
                            {!! $errors->first('year', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-2 {{ $errors->has('year') ? 'has-error' : ''}}" >
                            <label>From Month<sup>*</sup></label>
                            <select id="month" class="select2
                             changetable" name="month">
                              <option value="0" selected>Select Month</option>
                              <option value="1">One Month</option>
                              <option value="2">Two Month</option>
                              <option value="3">Three Month</option>
                              <option value="4">Four Month</option>
                              <option value="5">Five Month</option>
                              <option value="6">Six Month</option>
                              <option value="7">Seven Month</option>
                              <option value="8">Eight Month</option>
                              <option value="9">Nine Month</option>
                              <option value="10">Ten Month</option>
                              <option value="11">Eleven Month</option>
                            </select>
                            {!! $errors->first('year', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-2 col-md-offset-4 {{ $errors->has('year') ? 'has-error' : ''}}">
                            <label>To Year<sup>*</sup></label>
                            <select id="year2" class="select2 changetable" name="year2">
                              <option value="0">Select Year</option>
                              <option value="12">One Year</option>
                              <option value="24">Two Year</option>
                              <option value="36">Three Year</option>
                              <option value="48">Four Year</option>
                              <option value="60">Five Year</option>
                              <option value="72">Six Year</option>
                              <option value="84">Seven Year</option>
                              <option value="96">Eight Year</option>
                              <option value="108">Nine Year</option>
                              <option value="120">Ten Year</option>
                            </select>
                            {!! $errors->first('year2', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-2 {{ $errors->has('month2') ? 'has-error' : ''}}" >
                            <label>To Month<sup>*</sup></label>
                            <select id="month2" class="select2
                             changetable" name="month2">
                              <option value="0">Select Month</option>
                              <option value="1">One Month</option>
                              <option value="2">Two Month</option>
                              <option value="3">Three Month</option>
                              <option value="4">Four Month</option>
                              <option value="5">Five Month</option>
                              <option value="6">Six Month</option>
                              <option value="7">Seven Month</option>
                              <option value="8">Eight Month</option>
                              <option value="9">Nine Month</option>
                              <option value="10">Ten Month</option>
                              <option value="11">Eleven Month</option>
                            </select>
                            {!! $errors->first('month2', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div><br><br>

                  <table id="table_one_yr" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                    <th>Employee Number</th> 
                      <th>Name</th>
                      <th>Number</th>
                      <th>Department</th>
                      <th>Designation</th>
                      <th>Joining Date</th>
                      <th>year</th>
                     
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