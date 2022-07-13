@extends($layout)

@section('title', __('Utilities/internal_dc.title1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Internal Delivery Challan Summary</a></li> 
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
          "ajax": "/internal/deliverychallan/list/api",
          "columns": [
              { "data": "for" }, 
              { "data": "idc_number" }, 
              { "data": "outsource_no" },
              { "data": "date" },
              { "data": "item_desc" },  
              { "data": "item_qty" }, 
              { "data": "uom_name" }, 
              { "data": "hs" }, 
              { "data": "rate" }, 
              { "data": "packing_desc" }, 
              { "data": "dispatch_to" },
              { "data": "mode" },  
              { "data": "courier_name" }, 
              { "data": "reason" }, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/internal/deliverychallan/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;"+
                    "<a href='/internal/deliverychallan/template/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print</button></a> &nbsp"
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
                      <a href="/export/data/internaldc" ><button class="btn btn-primary "  >Export Internal DC</button></a>
                      @endsection
                    <table id="asn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>{{__('Utilities/internal_dc.for')}}</th>
                      <th>{{__('Utilities/internal_dc.num')}}</th>
                      <th>{{__('Utilities/internal_dc.order')}}</th>
                      <th> {{__('Utilities/internal_dc.date')}}</th>
                      <th>{{__('Utilities/internal_dc.desc')}}</th>

                      <th>{{__('Utilities/internal_dc.qty')}}</th>
                      <th>Unit Of Measurement</th>
                      <th>HSN/SAC</th>
                      <th>Rate</th>
                      <th>{{__('Utilities/internal_dc.detail')}}</th>
                      <th>{{__('Utilities/internal_dc.dispatch')}}</th>
                      <th> {{__('Utilities/internal_dc.mode')}}</th>
                      <th>{{__('Utilities/internal_dc.carrier')}}</th>
                      <th>{{__('Utilities/internal_dc.reason')}}</th>
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