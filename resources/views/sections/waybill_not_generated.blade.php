@extends($layout)

@section('title', __('waybill.waybillNotGen'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>{{__('waybill.waybillNotGen')}}</a></li> 
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

    function getTaxInvoice(){
        $('#challan_div').hide();
        $('#invoice_div').show();
        $('.chal1').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        if(dataTable)
            dataTable.destroy();

        dataTable = $('#invoice_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/report/waybillnotgen/api/invoice",
          "columns": [
            {"data":"date", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                {"data":"invoice_number", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"partyname", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"gst_number", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  //{"data":"partyname"}, 
                  
                   
                  {"data":"total_amount", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return (data).toFixed(2);   
                    else  
                      return "";
                  }, },
                  
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var str = data.total_amount; 
                      var res = str.toString().replace('.', '+');
                      var pointer=data.gst_pointer;
                      if(pointer==1)
                            var gst=data.gst_number;
                      else
                            var gst=data.gst_number;
                   
                    return "<a href='/waybill/create/"+data.a+"/Sale/"+gst+"/"+data.date+"/"+res+"/"+data.reference_name+"/"+pointer+"'><button class='btn btn-primary btn-xs'> {{__('waybill.create')}} </button></a> &nbsp;" //+ 
                    //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                    ;
                    }
                }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 2 },
              { "orderable": false, "targets": 4 },
              { "orderable": false, "targets": 5 },
              
            ]
          
        });
    }
    function getDeliveryChallan(){
        $('#invoice_div').hide();
        $('#challan_div').show();
        $('.chal').css("background-color","#87CEFA");
        $('.chal1').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
        dataTable = $('#challan_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/report/waybillnotgen/api/challan",
          "columns": [
            {"data":"date", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                { "data": "challan_number", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"partyname", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"gst", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"total_amount", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return (data).toFixed(2);   
                    else  
                      return "";
                  }, },
                {
                    "targets": [ -1 ],
                    "data": function(data,type,full,meta)
                    {
                      var str = data.total_amount; 
                      var res = str.toString().replace('.', '+');
                      var pointer=data.gst_pointer;
                      if(pointer==1)
                            var gst=data.gst_number;
                      else
                            var gst=data.gst_number;

                      return "<a href='/waybill/create/"+data.a+"/Challan/"+data.gst+"/"+data.date+"/"+res+"/"+data.reference_name+"/"+pointer+"'><button class='btn btn-primary btn-xs'> {{__('waybill.create')}} </button></a> &nbsp;" //+ 
                    ;
                  }
              }
            ],
            "columnDefs": [
              
              { "orderable": false, "targets": 3 },
              { "orderable": false, "targets": 4 },
              { "orderable": false, "targets": 1 },
              { "orderable": false, "targets": 5 },
            
            ]
          
        });
    
    }

    $(document).ready(function() {
        getDeliveryChallan();
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
                    <button class="nav-link1 chal"  onclick="getDeliveryChallan()">{{__('waybill.delivery_challan')}}</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal1" onclick="getTaxInvoice()">{{__('waybill.tax_invoice')}}</button>
                  </li>
                </ul><br><br>
                <div id="invoice_div" style="display:none">
                    <table id="invoice_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{__('waybill.date')}}</th>
                                <th>{{__('waybill.number')}}</th>
                                <th>{{__('waybill.party1')}}</th>
                                <th>{{__('waybill.party')}}</th>
                                <th>{{__('waybill.amount2')}}</th>
                                <th>{{__('waybill.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>               
                    </table>
                </div>
                <div id="challan_div">
                    <table id="challan_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{__('waybill.date1')}}</th>
                                <th>{{__('waybill.number1')}}</th>
                                <th>{{__('waybill.party1')}}</th>
                                <th>{{__('waybill.party')}}</th>
                                <th>{{__('waybill.amount1')}}</th>
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