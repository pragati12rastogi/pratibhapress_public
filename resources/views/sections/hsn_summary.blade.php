@extends($layout)

@section('title', __('hsn.hsn_list'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>HSN Summary</a></li> 
@endsection
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
      dataTable = $('#hsn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/hsn/list/api",
          "columns": [
              { "data": "id" }, 
              { "data": "itid" }, 
              { "data": "gst" }, 
              { "data": "hsn" }, 
              { "data": "itemdesc" }, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/hsn/list/edit/"+data+"'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" //+ 
                    //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 5 }
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
                      <a href="{{url('/hsn/create')}}"><button class="btn btn-primary">{{__('hsn.hsn_create_btn')}}</button></a>
                      <a href="/import/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_import_btn')}}</button></a>
                      <a href="/export/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_export_btn')}}</button></a>
    
                      @endsection
                    <table id="hsn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>{{__('hsn.hsn_list_Id')}}</th>
                      <th>{{__('hsn.hsn_list_Item_Name')}}</th>
                      <th> {{__('hsn.hsn_list_GstRate')}}</th>
                      <th>{{__('hsn.hsn_list_Hsn')}}</th>
                      <th>Item Description</th>
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