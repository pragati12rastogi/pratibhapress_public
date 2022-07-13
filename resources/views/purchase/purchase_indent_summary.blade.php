@extends($layout)

@section('title', __('purchase/indent.title1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>{{__('purchase/indent.title1')}}</a></li> 
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
          "ajax": "/purchase/requisition/list/api/paper",
          "columns": [
            {"data":"indent_num"},
              {"data":"item_for"},
              {"data":"item_category"}, 
              {"data":"item_name"}, 
              {"data":"item_qty"}, 
              {"data":"uom_name"}, 
              {"data":"item_req_date"}, 
              {"data":"a"}, 
              {"data":"purchase_req_number", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"qty", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, }, 
                  {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return  "<a href='/purchase/template/req/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp" +
                    '<a href="/purchase/requisition/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              
               
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
          "ajax": "/purchase/requisition/list/api/inks",
          "columns": [
            {"data":"indent_num"},
            {"data":"item_for"},
              {"data":"item_category"}, 
              {"data":"item_name"}, 
              {"data":"item_qty"}, 
              {"data":"uom_name"}, 
              {"data":"item_req_date"}, 
              {"data":"a"}, 
              {"data":"purchase_req_number", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"qty", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, }, 
                  {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return  "<a href='/purchase/template/req/"+data+"'target='_blank' ><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp"+
                    '<a href="/purchase/requisition/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              
               
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
          "ajax": "/purchase/requisition/list/api/plate",
          "columns": [
            {"data":"indent_num"},
            {"data":"item_for"},
              {"data":"item_category"}, 
              {"data":"item_name"}, 
              {"data":"item_qty"}, 
              {"data":"uom_name"}, 
              {"data":"item_req_date"}, 
              {"data":"a"}, 
              {"data":"purchase_req_number", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"qty", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, }, 
                  {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return  "<a href='/purchase/template/req/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp"+
                    '<a href="/purchase/requisition/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              
               
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
          "ajax": "/purchase/requisition/list/api/misc",
          "columns": [
            {"data":"indent_num"},
            {"data":"item_for"},
              {"data":"item_category"}, 
              {"data":"item_name"}, 
              {"data":"item_qty"}, 
              {"data":"uom_name"}, 
              {"data":"item_req_date"}, 
              {"data":"a"}, 
              {"data":"purchase_req_number", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"qty", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, }, 
                  {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return  "<a href='/purchase/template/req/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp"+
                    '<a href="/purchase/requisition/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              
               
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
             @section('titlebutton') 
            <a href="/export/data/purchase/indent"><button class="btn btn-sm btn-primary">Export Purchase Indent</button></a>
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
                    <button class="nav-link1 chal"  onclick="getpaper()">{{__('purchase/indent.paper')}}</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal1" onclick="getinks()">{{__('purchase/indent.ink')}}</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal2" onclick="getplate()">{{__('purchase/indent.plate')}}</button>
                  </li>
                  <li class="nav-item">
                        <button class="nav-link1 chal3" onclick="getmisc()">{{__('purchase/indent.misc')}}</button>
                      </li>
                </ul><br><br>
                <div id="paper" style="display:none">
                    <table id="paper_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>{{__('purchase/indent.Requisition_num')}}</th>
                                 <th>{{__('purchase/indent.req')}}</th>
                                <th>{{__('purchase/indent.item')}}</th>
                                <th>{{__('purchase/indent.name')}}</th>
                                <th>{{__('purchase/indent.item_qty')}}</th>
                                <th>{{__('purchase/indent.unit')}}</th>
                                <th>{{__('purchase/indent.item_date')}}</th>
                                <th>{{__('purchase/indent.for')}}</th>
                                <th>{{__('purchase/indent.pr')}}</th>
                                <th>{{__('purchase/indent.qty')}}</th>
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
                            <th>{{__('purchase/indent.Requisition_num')}}</th>
                                    <th>{{__('purchase/indent.req')}}</th>
                                <th>{{__('purchase/indent.item')}}</th>
                                <th>{{__('purchase/indent.name')}}</th>
                                <th>{{__('purchase/indent.item_qty')}}</th>
                                <th>{{__('purchase/indent.unit')}}</th>
                                <th>{{__('purchase/indent.item_date')}}</th>
                                <th>{{__('purchase/indent.for')}}</th>
                                <th>{{__('purchase/indent.pr')}}</th>
                                <th>{{__('purchase/indent.qty')}}</th>
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
                            <th>{{__('purchase/indent.Requisition_num')}}</th>
                                    <th>{{__('purchase/indent.req')}}</th>
                                    <th>{{__('purchase/indent.item')}}</th>
                                    <th>{{__('purchase/indent.name')}}</th>
                                    <th>{{__('purchase/indent.item_qty')}}</th>
                                    <th>{{__('purchase/indent.unit')}}</th>
                                    <th>{{__('purchase/indent.item_date')}}</th>
                                    <th>{{__('purchase/indent.for')}}</th>
                                    <th>{{__('purchase/indent.pr')}}</th>
                                    <th>{{__('purchase/indent.qty')}}</th>
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
                                <th>{{__('purchase/indent.Requisition_num')}}</th>
                                        <th>{{__('purchase/indent.req')}}</th>
                                <th>{{__('purchase/indent.item')}}</th>
                                <th>{{__('purchase/indent.name')}}</th>
                                <th>{{__('purchase/indent.item_qty')}}</th>
                                <th>{{__('purchase/indent.unit')}}</th>
                                <th>{{__('purchase/indent.item_date')}}</th>
                                <th>{{__('purchase/indent.for')}}</th>
                                <th>{{__('purchase/indent.pr')}}</th>
                                <th>{{__('purchase/indent.qty')}}</th>
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