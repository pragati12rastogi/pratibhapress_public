@extends($layout)

@section('title', __('vendor/vendor.mytitle1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Vendor Summary</a></li> 
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
      dataTable = $('#asn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/vendor/summary/api",
          "columns": [
              { "data": "vendor_name" }, 
              { "data": "address" }, 
              { "data": "state" },
              { "data": "con_person" },
              { "data": "number" },  
              { "data": "email" }, 
              { "data": "payment" }, 
              { "data": "gst" },
              { "data": "pan" },  
              { "data": "level_authority1" }, 
              { "data": "level_authority2" }, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/vendor/list/edit/"+data+"'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;"
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 11 }
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
                      {{-- <a href="{{url('/hsn/create')}}"><button class="btn btn-primary">{{__('hsn.hsn_create_btn')}}</button></a>
                      <a href="/import/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_import_btn')}}</button></a>
                      <a href="/export/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_export_btn')}}</button></a> --}}
    
                      @endsection
                    <table id="asn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>{{__('vendor/vendor.name')}}</th>
                      <th>{{__('vendor/vendor.address')}}</th>
                      <th>{{__('vendor/vendor.state')}}</th>
                      <th> {{__('vendor/vendor.per')}}</th>
                      <th>{{__('vendor/vendor.num')}}</th>

                      <th>{{__('vendor/vendor.email')}}</th>
                      <th>{{__('vendor/vendor.pay')}}</th>
                      <th>{{__('vendor/vendor.gst')}}</th>
                      <th> {{__('vendor/vendor.pan')}}</th>
                      <th>{{__('vendor/vendor.lev1')}}</th>
                      <th>{{__('vendor/vendor.lev2')}}</th>
                      <th>{{__('hsn.hsn_list_Action')}}</th>
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