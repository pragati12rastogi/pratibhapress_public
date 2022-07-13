@extends($layout)

@section('title', __('stock/stock.title1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>{{__('stock/stock.title1')}}</a></li> 
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
table{
  width:100%;
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

    function getpaper(){
        $('#plate').hide();
        $('#inks').hide();
        $('#misc').hide();
        $('#paper').show();
        $('.chal').css("background-color","#87CEFA");
        $('.chal1').removeAttr('style');
        $('.chal2').removeAttr('style');
        $('.chal3').removeAttr('style');
        if(dataTable)
            dataTable.destroy();

        dataTable = $('#paper_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/stock/summary/api/paper",
          "columns": [
              {"data":"fors"},
              {"data":"item_cat"}, 
              {"data":"item_name"}, 
              {"data":"sp"}, 
              {"data":"qty_sp"}, 
              {"data":"uoq"}, 
              {"data":"sku"}, 
              {"data":"item_location"},
              {"data":"opening_stock"},
              {"data":"min_entry_level"},
             
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/stock/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a>";
                  }
              }
            ],
            "columnDefs": [
              
                { "orderable": false, "targets": 7 },
                { "orderable": false, "targets": 8 },
                { "orderable": false, "targets": 10 },
            
            ]
          
        });
    }
    function getinks(){
        $('#plate').hide();
        $('#inks').show();
        $('#misc').hide();
        $('#paper').hide();
        $('.chal1').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal2').removeAttr('style');
        $('.chal3').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#inks_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/stock/summary/api/inks",
          "columns": [
            {"data":"fors"},
              {"data":"item_cat"}, 
              {"data":"item_name"}, 
              {"data":"sp"}, 
              {"data":"qty_sp"}, 
              {"data":"uoq"}, 
              {"data":"sku"}, 
              {"data":"item_location"},
              {"data":"opening_stock"},
              {"data":"min_entry_level"},
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/stock/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a>";
                  }
              }
            ],
            "columnDefs": [
              
                { "orderable": false, "targets": 7 },
                { "orderable": false, "targets": 8 },
              { "orderable": false, "targets": 10 },

            ]
          
        });
    
    }
    function getplate(){
        $('#plate').show();
        $('#inks').hide();
        $('#misc').hide();
        $('#paper').hide();
        $('.chal2').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal1').removeAttr('style');
        $('.chal3').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#plate_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/stock/summary/api/plate",
          "columns": [
            {"data":"fors"},
              {"data":"item_cat"}, 
              {"data":"item"}, 
              {"data":"sp"}, 
              {"data":"qty_sp"}, 
              {"data":"uoq"}, 
              {"data":"sku"}, 
              {"data":"item_location"},
              {"data":"opening_stock"},
              {"data":"min_entry_level"},
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/stock/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a>";
                  }
              }
            ],
            "columnDefs": [
              
                { "orderable": false, "targets": 7 },
                { "orderable": false, "targets": 8 },
              { "orderable": false, "targets": 10 },

            
            ]
          
        });
    
    }
    function getmisc(){
        $('#plate').hide();
        $('#inks').hide();
        $('#misc').show();
        $('#paper').hide();
        $('.chal3').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal1').removeAttr('style');
        $('.chal2').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#misc_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/stock/summary/api/misc",
          "columns": [
            {"data":"fors"},
              {"data":"item_cat"}, 
              {"data":"item"}, 
              {"data":"sp"}, 
              {"data":"qty_sp"}, 
              {"data":"uoq"}, 
              {"data":"sku"}, 
              {"data":"item_location"},
              {"data":"opening_stock"},
              {"data":"min_entry_level"},
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/stock/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a>";
                  }
              }
            ],
            "columnDefs": [
              
              { "orderable": false, "targets": 7 },
              { "orderable": false, "targets": 8 },
              { "orderable": false, "targets": 10 },
            
            ]
          
        });
    
    }


    $(document).ready(function() {
        getpaper();
    });


  </script>
@endsection

@section('main_section')
    <section class="content">
        <div id="app">
            @include('sections.flash-message')
            @yield('content')
            {{-- @section('titlebutton')
                <a href="{{url('/createdispatch')}}"><button class="btn btn-primary">{{__('goods_dispatch.title')}}</button></a>
                <a href="" ><button class="btn btn-primary "  >{{__('goods_dispatch.goods_dispatch_import_btn')}}</button></a>
                <a href="" ><button class="btn btn-primary "  >{{__('goods_dispatch.goods_dispatch_export_btn')}}</button></a>
            @endsection --}}
            
        </div>
    <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                @section('titlebutton')
              <a href="/import/data/stock"><button class="btn btn-sm btn-primary">Import Stock</button></a>
              {{-- <a href="/export/data/taxinvoice"><button class="btn btn-sm btn-primary">{{__('taxinvoice.exporttitle')}}</button></a> --}}
              @endsection
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
                    <button class="nav-link1 chal"  onclick="getpaper()">{{__('stock/stock.paper')}}</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal1" onclick="getinks()">{{__('stock/stock.ink')}}</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal2" onclick="getplate()">{{__('stock/stock.plate')}}</button>
                  </li>
                  <li class="nav-item">
                        <button class="nav-link1 chal3" onclick="getmisc()">{{__('stock/stock.misc')}}</button>
                      </li>
                </ul><br><br>
                <div id="paper" style="display:none">
                    <table id="paper_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                 <th>{{__('purchase/indent.for')}}</th>
                                <th>{{__('stock/stock.item')}}</th>
                                <th>{{__('stock/stock.name')}}</th>
                                <th>{{__('stock/stock.item_stan')}}</th>
                                <th>{{__('stock/stock.standard')}}</th>
                                <th>{{__('stock/stock.unit')}}</th>
                                <th>{{__('stock/stock.sku')}}</th>
                                <th>{{__('stock/stock.location')}}</th>
                                <th>{{__('stock/stock.stock')}}</th>
                                <th>{{__('stock/stock.entry')}}</th>
                                <th>{{__('waybill.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>               
                    </table>
                </div>
                <div id="inks">
                    <table id="inks_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{__('purchase/indent.for')}}</th>
                                <th>{{__('stock/stock.item')}}</th>
                                <th>{{__('stock/stock.name')}}</th>
                                <th>{{__('stock/stock.item_stan')}}</th>
                                <th>{{__('stock/stock.standard')}}</th>
                                <th>{{__('stock/stock.unit')}}</th>
                                <th>{{__('stock/stock.sku')}}</th>
                                <th>{{__('stock/stock.location')}}</th>
                                <th>{{__('stock/stock.stock')}}</th>
                                <th>{{__('stock/stock.entry')}}</th>
                                    <th>{{__('waybill.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>    
                        </tbody>
                    
                    </table>
                </div>
                <div id="plate">
                    <table id="plate_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{__('purchase/indent.for')}}</th>
                                <th>{{__('stock/stock.item')}}</th>
                                <th>{{__('stock/stock.name')}}</th>
                                <th>{{__('stock/stock.item_stan')}}</th>
                                <th>{{__('stock/stock.standard')}}</th>
                                <th>{{__('stock/stock.unit')}}</th>
                                <th>{{__('stock/stock.sku')}}</th>
                                <th>{{__('stock/stock.location')}}</th>
                                <th>{{__('stock/stock.stock')}}</th>
                                <th>{{__('stock/stock.entry')}}</th>
                                    <th>{{__('waybill.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>    
                        </tbody>
                    
                    </table>
                </div>
                <div id="misc">
                        <table id="misc_table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{__('purchase/indent.for')}}</th>
                                <th>{{__('stock/stock.item')}}</th>
                                <th>{{__('stock/stock.name')}}</th>
                                <th>{{__('stock/stock.item_stan')}}</th>
                                <th>{{__('stock/stock.standard')}}</th>
                                <th>{{__('stock/stock.unit')}}</th>
                                <th>{{__('stock/stock.sku')}}</th>
                                <th>{{__('stock/stock.location')}}</th>
                                <th>{{__('stock/stock.stock')}}</th>
                                <th>{{__('stock/stock.entry')}}</th>
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