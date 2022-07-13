@extends($layout)

@section('title', 'Design Work Over But Not Closed Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Design Summary</a></li> 
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
          "ajax": "/design/report/workover/api",
          "columns": [
           
            {"data":"do_number"},
              {"data":"referencename"},
              { 
                "data":"work_alloted_number","render": function(data, type, full, meta){
                  if(data)
                    
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            }, 
              
         
              {"data":"status"},
        
                   {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return '<a href="/design/order/status/'+data+'"><button class="btn btn-success btn-xs">Status </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 4 }
            
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
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                 
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                     
                    <th>DO No.</th> 
                      <th>Client</th>
                      <th>WA No.</th>
                      <th>Status</th>
                
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