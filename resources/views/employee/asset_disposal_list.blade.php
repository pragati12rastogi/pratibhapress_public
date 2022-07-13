@extends($layout)

@section('title', 'Asset Disposal List')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Asset Disposal List</a></li> 
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
          "ajax": "/master/assets/disposal/list/api",
          "columns": [
              {"data":"category_name"},
              {"data":"asset_code"},
              {"data":"asset"},
              {"data":"asset_value"},
              {"data":"employee"},
              {"data":"disposal_on"},
              {"data":"disposal_reason"},
                 
            ],
            "columnDefs": [
             
              
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
                    <a href="{{url('/master/assets/disposal')}}"><button class="btn btn-primary">Disposal Assets</button></a>
                      @endsection
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>

                      <th>Assets Category</th>
                      <th>Assets Code</th>
                      <th>Asset Name</th>
                      <th>Asset Value</th>
                      <th>Disposal To</th>
                      <th>Disposal On</th>
                      <th>Disposal Reason</th>
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