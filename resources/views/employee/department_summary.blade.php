@extends($layout)

@section('title', 'Department List')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Department List</a></li> 
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
          "ajax": "/master/department/list/api",
          "columns": [
              {"data":"id"},
              {"data":"department"},
                   {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/master/department/edit/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" //+ 
                    //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 2 }
            
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
                    @section('titlebutton')
                    <a href="{{url('/master/department')}}"><button class="btn btn-primary">Create Department</button></a>
                      @endsection
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                       
                      <th>Id</th>
                      <th>Department</th>
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