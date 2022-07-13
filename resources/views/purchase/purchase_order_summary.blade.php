@extends($layout)

@section('title', 'Purchase Order Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Purchase Order Summary</a></li> 
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
  div.options > label > input {
	visibility: hidden;
}

div.options > label {
	display: block;
	margin: 0 0 0 -10px;
	padding: 0 0 20px 0;  
	height: 20px;
	width: 150px;
}

div.options > label > img {
	display: inline-block;
	padding: 7px;
	height:34px;
	width:37px;
	background: none;
}

div.options > label > input:checked +img {  
	background: url(http://cdn1.iconfinder.com/data/icons/onebit/PNG/onebit_34.png);
	background-repeat: no-repeat;
	background-position:center center;
	background-size:21px 24px;
}
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;
       
        function not_approv(){
        $('#approv').hide();
        $('#rejected').hide();
        $('#not_approv').show();
        $('.chal').css("background-color","#87CEFA");
        $('.chal1').removeAttr('style');
        $('.chal2').removeAttr('style');
        if(dataTable)
            dataTable.destroy();

            dataTable = $('#not_app').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/purchase/order/list/api/NotApproved",
          "columns": [
                {"data":"po_num"}, 
                {"data":"indent_num"}, 
                // {"data":"po_date"},
                {"data": function(data, type, full, meta){
                var dt=data.po_date;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+1;
                      var yyyy=dt.getFullYear();
                      var ac=dd+'-'+mm+'-'+yyyy;
                      return ac;
                } 
            }, 
                {"data":"master_name"}, 
                {"data":"vendor_name"}, 
                {"data":"py_value"}, 
                {"data":"status"}, 
                {"data": function(data, type, full, meta){
                var dt=data.status_date;
                    if(dt){
                        dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+1;
                      var yyyy=dt.getFullYear();
                      var ac=dd+'-'+mm+'-'+yyyy;
                      return ac;
                    }
                    else{
                        return '';
                    }
                } 
            }, 
                {"data":"username"},  
                {
                    "targets": [ -1 ],
                    "data":"po_id", "render": function(data,type,full,meta)
                    {
                        return "<a href='/purchase/order/update/"+data+"' target='_blank'><button class='btn btn-success btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;"+
                        "<a href='/purchase/order/view/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" //+ 
                        +"<a href='/purchase/template/po/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp"
                        +"<a onclick='approval("+data+")'><button class='btn btn-warning btn-xs'> Approve </button></a> &nbsp"
                        ;
                    },
                    "orderable": false
                }
            ],
            "columnDefs": [
              
                { "orderable": false, "targets": 6 }
            
            ]
          
        });
    }
    function approv(){
        $('#approv').show();
        $('#rejected').hide();
        $('#not_approv').hide();
        $('.chal1').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal2').removeAttr('style');
        if(dataTable)
            dataTable.destroy();

            dataTable = $('#approved').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/purchase/order/list/api/Approved",
          "columns": [
                {"data":"po_num"}, 
                {"data":"indent_num"}, 
                // {"data":"po_date"},
                {"data": function(data, type, full, meta){
                var dt=data.po_date;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+1;
                      var yyyy=dt.getFullYear();
                      var ac=dd+'-'+mm+'-'+yyyy;
                      return ac;
                } 
            }, 
                {"data":"master_name"}, 
                {"data":"vendor_name"}, 
                {"data":"py_value"}, 
                {"data":"status"}, 
                {"data": function(data, type, full, meta){
                var dt=data.status_date;
                    if(dt){
                        dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+1;
                      var yyyy=dt.getFullYear();
                      var ac=dd+'-'+mm+'-'+yyyy;
                      return ac;
                    }
                    else{
                        return '';
                    }
                } 
            }, 
            {"data":"username"},  
            {
                    "targets": [ -1 ],
                    "data":"po_id", "render": function(data,type,full,meta)
                    {
                        return "<a href='/purchase/order/view/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" //+ 
                        +"<a href='/purchase/template/po/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp"
                        ;
                    },
                    "orderable": false
                }
            ],
            "columnDefs": [
              
                { "orderable": false, "targets": 6 }
            
            ]
          
        });
    }
    function reject(){
        $('#approv').hide();
        $('#rejected').show();
        $('#not_approv').hide();
        $('.chal2').css("background-color","#87CEFA");
        $('.chal1').removeAttr('style');
        $('.chal').removeAttr('style');
        if(dataTable)
            dataTable.destroy();

            dataTable = $('#rej').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/purchase/order/list/api/Rejected",
          "columns": [
                {"data":"po_num"}, 
                {"data":"indent_num"}, 
                // {"data":"po_date"},
                {"data": function(data, type, full, meta){
                var dt=data.po_date;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+1;
                      var yyyy=dt.getFullYear();
                      var ac=dd+'-'+mm+'-'+yyyy;
                      return ac;
                } 
            }, 
                {"data":"master_name"}, 
                {"data":"vendor_name"}, 
                {"data":"py_value"}, 
                {"data":"status"}, 
                {"data": function(data, type, full, meta){
                var dt=data.status_date;
                    if(dt){
                        dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+1;
                      var yyyy=dt.getFullYear();
                      var ac=dd+'-'+mm+'-'+yyyy;
                      return ac;
                    }
                    else{
                        return '';
                    }
                } 
            }, 
                {"data":"username"},  
                {
                    "targets": [ -1 ],
                    "data":"po_id", "render": function(data,type,full,meta)
                    {
                        return "<a href='/purchase/order/update/"+data+"' target='_blank'><button class='btn btn-success btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;"+
                        "<a href='/purchase/order/view/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" //+ 
                        +"<a href='/purchase/template/po/"+data+"' target='_blank'><button class='btn btn-danger btn-xs'> Print </button></a> &nbsp"
                        // +"<a onclick='approval("+data+")'><button class='btn btn-warning btn-xs'> Approve </button></a> &nbsp"
                        ;
                    },
                    "orderable": false
                }
            ],
            "columnDefs": [
              
                { "orderable": false, "targets": 6 }
            
            ]
          
        });
    }
        function approval(ele)
    {
    //   $('#ajax_loader_div').css('display','block');
          $('#modal_div').empty().append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Print</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/purchase/order/approval/'+ele+'">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      '<br><label>Please select One Of Below Option</label>'+
                      '<div class="options">'+
                       ' <label title="item1">'+
                            '<input type="radio" name="app" value="Approved" />Approved'+  
                           '<img />'+
                        '</label><br>'+
                        '<label title="item2">'+
                            '<input type="radio" name="app" value="Rejected" /> Rejected'+
                           
                            '<img />'+
                        '</label> '+  
                    '</div>'+
                    '</div><br><br>'+
                    '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>'+
                      '<a target="_blank"><button type="submit" class="btn btn-success"  onclick="$(\'#infos\').validate();">Submit</button></a>'+
                    '</div>'+
                    '</form>'+
                '</div>'+
              '</div>'+
            '</div>'
      );
          $(document).find('#myModal').modal("show");
          $(document).find('.select2').select2();
    }
    $(document).ready(function() {
        not_approv();
    });
  </script>
@endsection

@section('main_section')
    <section class="content">
        <div id="app">
            @include('sections.flash-message')
            @yield('content')
            
        </div>
        <div id="modal_div"></div>
    <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
            <div class="box-body">
                @section('titlebutton') 
                {{-- <a href="/export/data/purchase/order"><button class="btn btn-sm btn-primary">Export Purchase Order</button></a> --}}
                @endsection 
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
                          <button class="nav-link1 chal"  onclick="not_approv()">Not Approved PO</button>
                        </li>
                        <li class="nav-item">
                          <button class="nav-link1 chal1" onclick="approv()">Approved PO</button>
                        </li>
                        <li class="nav-item">
                          <button class="nav-link1 chal2" onclick="reject()">Rejected PO</button>
                        </li>
                </ul><br><br>
                
                <div id="not_approv" style="display:none">
                    <table id="not_app" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{__('purchase/order.po_num')}}</th>
                                <th>{{__('purchase/order.ind_num')}}</th>
                                <th>{{__('purchase/order.po_date')}}</th>
                                <th>{{__('purchase/order.master')}}</th>
                                <th>{{__('purchase/order.vendor')}}</th>
                                <th>{{__('purchase/order.py_term')}}</th>
                                <th>Status</th>
                                <th>Status Date</th>
                                <th>Status By</th>
                                <th>{{__('waybill.action')}}</th> 
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>               
                    </table>
                </div>

                <div id="approv" style="display:none">
                        <table id="approved" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{__('purchase/order.po_num')}}</th>
                                    <th>{{__('purchase/order.ind_num')}}</th>
                                    <th>{{__('purchase/order.po_date')}}</th>
                                    <th>{{__('purchase/order.master')}}</th>
                                    <th>{{__('purchase/order.vendor')}}</th>
                                    <th>{{__('purchase/order.py_term')}}</th>
                                    <th>Status</th>
                                    <th>Status Date</th>
                                    <th>Status By</th>
                                    <th>{{__('waybill.action')}}</th> 
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>               
                        </table>
                    </div>

                    <div id="rejected" style="display:none">
                            <table id="rej" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{__('purchase/order.po_num')}}</th>
                                        <th>{{__('purchase/order.ind_num')}}</th>
                                        <th>{{__('purchase/order.po_date')}}</th>
                                        <th>{{__('purchase/order.master')}}</th>
                                        <th>{{__('purchase/order.vendor')}}</th>
                                        <th>{{__('purchase/order.py_term')}}</th>
                                        <th>Status</th>
                                        <th>Status Date</th>
                                        <th>Status By</th>
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