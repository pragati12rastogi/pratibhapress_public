@extends($layout)

@section('title', 'Salary C Increment Report ')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Salary C Increment Report </a></li> 
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
            "url":"/increment/salary/c/report/api",
            "datatype": "json",
            "data": function (data) {
                var emp = $('#emp_name').val();
                data.emp = emp;
            }
          },
          "columns": [
            
              {"data":"month_name"},
              {"data":"his_amt"},
              // {"data":"increment_cat"}
              {"data":"increment_adjust_c"}
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