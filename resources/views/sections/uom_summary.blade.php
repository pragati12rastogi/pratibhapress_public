@extends($layout)

@section('title', __('uom.mytitle'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>UOM</a></li> 
   
@endsection
@section('css')
<style>
  .content{
    padding: 32px;
  }

@media (max-width: 425px)  
  {
    
    .content-header>h1 {
      display: inline-block;
      padding: 8px;
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
      dataTable = $('#uom_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/uom/data",
          "columns": [
              { "data": "id" }, 
              { "data": "uom_name" }, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/uom/update/"+data+"'><button class='btn btn-primary btn-xs'> {{__('uom.uom_list_Edit')}} </button></a> &nbsp;" 
                    //+'<a href="/uom/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("uom.uom_list_Delete")}} </button></a>'
                  ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 2 },
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
        @section('titlebutton')
        <a href="/uom/create" ><button class="btn btn-primary "  >{{__('uom.uom_create_btn')}}</button></a>
        <a href="/import/data/uom" ><button class="btn btn-primary "  >{{__('uom.uom_import_btn')}}</button></a>
        <a href="/export/data/uom" ><button class="btn btn-primary "  >{{__('uom.uom_export_btn')}}</button></a>
        @endsection
        <div class="box">
            <!-- /.box-header -->
                <div class="box-body">

                  <table id="uom_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>{{__('uom.uom_list_Id')}}</th>
                      <th>{{__('uom.uom_list_UOM_Name')}}</th>
                      <th>{{__('uom.uom_list_Action')}}</th>                      
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
