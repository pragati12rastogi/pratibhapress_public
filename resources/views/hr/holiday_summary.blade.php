@extends($layout)

@section('title', 'Holiday Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Holiday Summary</a></li> 
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
      dataTable = $('#holiday').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/holiday/summary/api",
          "columns": [
              {"data":"name"},
              {"data":"sum_date"},
              {"data":"date","render": function(data,type,full,meta)
                {
                  // debugger;
                  var array_date = data.split(',');
                  var days_array = [];
                  var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                  for (var i = 0; i < array_date.length; i++) {
                    var d = new Date(array_date[i]);
                    var dayName = days[d.getDay()];
                    days_array.push(dayName);
                  }
                  // if(days_array.length>1){
                  //   return days_array[0]+" to "+days_array[days_array.length-1];
                  // }else{
                  //   return days_array[0];
                  // }
                  return days_array.toString();
                },
              },
              {
                  "targets": [ -1 ],
                  data : function(data,type,full,meta)
                  {
                    return "<a href='/update/holiday/"+data.id+"' target='_blank'><button class='btn btn-success btn-xs'>Edit</button></a> &nbsp;"
                    ;
                    
                  
                  },
              }
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": 3 }
            ]
          
        });
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
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    <a href="{{url('/create/holiday')}}"><button class="btn btn-primary">Create Holiday</button></a>
                      @endsection
                    <table id="holiday" class="table table-bordered table-striped">
                    <thead>
                    <tr>

                      <th>Holiday Name</th>
                      <th>Date</th>
                      <th>Day</th>
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