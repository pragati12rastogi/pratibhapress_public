@extends($layout)

@section('title', 'Employee Assets Assign Report')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Employee Assets Assign Report</a></li> 
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
      $('#table').hide();
    });

function assets(){
  $('#table').show();

  if(dataTable){
     dataTable.destroy();
  }
  dataTable = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url":"/master/report/employee/asset/api",
            "datatype": "json",
            "data": function (data) {
                var emp = $('#emp').val();
                data.emp = emp;
            }
          },
          "columns": [
              {"data":"employee"},
              {"data":"asset_code"},
              {"data":"category_name"},
              {"data":"name"},
              {"data":"model_number"},
              {"data":"asset_value"},
              {"data":"from_date"},
              {"data":"to_date", "render": function(data,type,full,meta)
                  {
                    console.log(data);
                    if(data == "1970-01-01")
                      return "";
                    else  
                      return data;
                  },},
              {"data":"asset_form", "render": function(data,type,full,meta)
                  {
                    console.log(data);
                    if(data == "")
                      return "";
                    else  
                      return '<a href="/upload/assets/form/'+data+'" target="_blank">See Asset Receipt Form</a>';
                  },},
              
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": 8 }
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
                      <div class="row">
                        <div class="col-md-6 {{ $errors->has('emp') ? 'has-error' : ''}}">
                              <label>Employee<sup>*</sup></label>
                              <select id="emp" class="select2 changetable" name="emp" onchange="assets()">
                                <option value="">Select Employee</option>
                                @foreach($employee as $emp)
                                    <option value="{{$emp->id}}">{{$emp->name}}</option>
                                @endforeach
                              </select>
                              {!! $errors->first('emp', '<p class="help-block">:message</p>') !!}
                          </div>
                      </div><br><br>
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>

                      <th>Employee Name</th>
                      <th>Assets Code</th>
                      <th>Assets Category</th>
                      <th>Assets Name</th>
                      <th>Assets Model Number</th>
                      <th>Asset Value</th>
                      <th>From Date</th>
                      <th>To Date</th>
                      <th>Asset Receipt</th>
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