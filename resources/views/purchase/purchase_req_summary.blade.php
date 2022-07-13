@extends($layout)

@section('title', __('purchase/purchase_req.title1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>{{__('purchase/purchase_req.title1')}}</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
  .nav1>li>button {
    position: relative;
    display: block;
    padding: 10px 34px;
    background-color: white;
    margin-left: 10px;
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

    function getcio(){
        $('#dsio').hide();
        $('#im').hide();
        $('#cio').show();
        $('.chal').css("background-color","#87CEFA");
        $('.chal1').removeAttr('style');
        $('.chal2').removeAttr('style');
        if(dataTable)
            dataTable.destroy();

        dataTable = $('#cio_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/purchase/indent/list/api/cio",
          "columns": [
              {"data":"purchase_req_number"},
              {"data":"item_req_for"}, 
              {"data":"io_number"}, 
              {"data":"required_date"}, 
              {"data":"name"}, 
              {"data":"item_desc"}, 
              {"data":"item_qty"}, 
              {"data":"uom_name"}, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return  "<a href='/purchase/template/indent/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp" +
                    '<a href="/purchase/indent/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit</button></a>' ;
                    ;
                  }
              }
            ],
            "columnDefs": [
              
              { "orderable": false, "targets": 8 },
            
            ]
          
        });
    }
    function getdsio(){
        $('#im').hide();
        $('#cio').hide();
        $('#dsio').show();
        $('.chal1').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal2').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#dsio_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/purchase/indent/list/api/dsio",
          "columns": [
              {"data":"purchase_req_number"},
              {"data":"item_req_for"}, 
              {"data":"io_number"}, 
              {"data":"required_date"}, 
              {"data":"name"}, 
              {"data":"item_desc"}, 
              {"data":"item_qty"}, 
              {"data":"uom_name"}, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return  "<a href='/purchase/template/indent/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp "  +
                    '<a href="/purchase/indent/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit</button></a>' ;
                    ;
                  }
              }
            ],
            "columnDefs": [
              
              { "orderable": false, "targets": 8 },
            
            ]
          
        });
    
    }
    function getim(){
        $('#im').show();
        $('#cio').hide();
        $('#dsio').hide();
        $('.chal2').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal1').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#im_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/purchase/indent/list/api/im",
          "columns": [
              {"data":"purchase_req_number"},
              {"data":"item_req_for"}, 
              {"data":"name"}, 
              {"data":"item_desc"}, 
              {"data":"item_qty"}, 
              {"data":"uom_name"}, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return  "<a href='/purchase/template/indent/"+data+"' target='_blank' ><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp"+
                    '<a href="/purchase/indent/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit</button></a>' ; 
                    ;
                  }
              }
            ],
            "columnDefs": [
              
              { "orderable": false, "targets": 6 },
            
            ]
          
        });
    
    }

    $(document).ready(function() {
        getcio();
    });


  </script>
@endsection

@section('main_section')
    <section class="content">
        <div id="app">
            @include('sections.flash-message')
            @yield('content')
            @section('titlebutton')
            {{-- <a href="{{url('/hsn/create')}}"><button class="btn btn-primary">{{__('hsn.hsn_create_btn')}}</button></a>
            <a href="/import/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_import_btn')}}</button></a>
            <a href="/export/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_export_btn')}}</button></a> --}}

            <a href="/export/data/purchase/req"><button class="btn btn-sm btn-primary">Export Purchase Requisition</button></a>

            @endsection
            
        </div>
    <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
            <div class="box-body">
                
                {{-- <div class="row">
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-primary" onclick="getDeliveryChallan()">{{__('waybill.delivery_challan')}}</button>
                    </div>
                    <div class="col-md-2">
                            <button class="btn btn-sm btn-primary" onclick="getTaxInvoice()">{{__('waybill.tax_invoice')}}</button>
                    </div>
                </div> --}}

                <ul class="nav nav1 nav-pills">
                  <li class="nav-item">
                    <button class="nav-link1 chal"  onclick="getcio()">{{__('purchase/purchase_req.cio')}}</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal1" onclick="getdsio()">{{__('purchase/purchase_req.dsio')}}</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal2" onclick="getim()">{{__('purchase/purchase_req.im')}}</button>
                  </li>
                </ul><br><br>
                <div id="cio" style="display:none">
                    <table id="cio_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                 <th>{{__('purchase/purchase_req.num')}}</th>
                                <th>{{__('purchase/purchase_req.item')}}</th>
                                <th>{{__('purchase/purchase_req.io')}}</th>
                                <th>{{__('purchase/purchase_req.date')}}</th>
                                <th>{{__('purchase/purchase_req.req')}}</th>
                                <th>{{__('purchase/purchase_req.desc')}}</th>
                                <th>{{__('purchase/purchase_req.req_item')}}</th>
                                <th>{{__('purchase/purchase_req.req_item_unit')}}</th>
                                <th>{{__('waybill.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>               
                    </table>
                </div>
                <div id="dsio">
                    <table id="dsio_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                    <th>{{__('purchase/purchase_req.num')}}</th>
                                    <th>{{__('purchase/purchase_req.item')}}</th>
                                    <th>{{__('purchase/purchase_req.io')}}</th>
                                    <th>{{__('purchase/purchase_req.date')}}</th>
                                    <th>{{__('purchase/purchase_req.req')}}</th>
                                    <th>{{__('purchase/purchase_req.desc')}}</th>
                                    <th>{{__('purchase/purchase_req.req_item')}}</th>
                                    <th>{{__('purchase/purchase_req.req_item_unit')}}</th>
                                    <th>{{__('waybill.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>    
                        </tbody>
                    
                    </table>
                </div>
                <div id="im">
                    <table id="im_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                    <th>{{__('purchase/purchase_req.num')}}</th>
                                    <th>{{__('purchase/purchase_req.item')}}</th>
                                    <th>{{__('purchase/purchase_req.req')}}</th>
                                    <th>{{__('purchase/purchase_req.desc')}}</th>
                                    <th>{{__('purchase/purchase_req.req_item')}}</th>
                                    <th>{{__('purchase/purchase_req.req_item_unit')}}</th>
                                    <th>{{__('waybill.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>    
                        </tbody>
                    
                    </table>
                </div>
                    <!-- /.box-body -->
            </div>
        </div>
        <!-- /.box -->
    </section>
@endsection