@extends($layout)

@section('title', __('purchase/return.title1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Purchase Return Request Summary</a></li> 
  
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
      dataTable = $('#asn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/purchase/return/summary/api",
          "columns": [
              { "data": "return_number" }, 
              { "data": "date" }, 
              { "data": "name" },
              { "data": "po_num" },
              { "data": "grn_number" },  
              { "data": "supp_name" }, 
              { "data": "item_desc" }, 
              { "data": "item_qty_received" },
              { "data": "item_qty_returned" },

              { "data": "uom_name" }, 
              { "data": "reason" },
              { "data": "payment_desc" },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var data=data.id;
                            return "<a href='/purchase/return/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp"
                            +"<a href='/purchase/template/return/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp";
                    }
                },
            ],
            "columnDefs": [
              { "orderable": false, "targets": 8 }
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
                      <a href="/import/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_import_btn')}}</button></a>--}}
                      <a href="/export/data/purchase/return"><button class="btn btn-sm btn-primary">Export Purchase Return Request</button></a>
                      @endsection
                    <table id="asn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                    <th>{{__('purchase/return.rr_number')}}</th>
                      <th>{{__('purchase/return.date')}}</th>
                      <th>{{__('purchase/return.appr')}}</th>
                      <th>{{__('purchase/return.po')}}</th>
                      <th> {{__('purchase/return.grn')}}</th>
                      <th>{{__('purchase/return.supp')}}</th>

                      <th>{{__('purchase/return.desc')}}</th>
                      <th>{{__('purchase/return.rec_item')}}</th>

                      <th>{{__('purchase/return.ret_item')}}</th>
                      <th>{{__('purchase/return.item')}}</th>
                      <th>{{__('purchase/return.reason')}}</th>
                      <th>{{__('purchase/return.payment')}}</th>
            
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