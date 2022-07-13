@extends($layout)

@section('user', Auth::user()->name)

@section('title','Payment Recieved Summary')
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">
<style type="text/css">
  input[type=number]::-webkit-inner-spin-button, 
  input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  margin: 0; 
} .nav1>li>a {
      position: relative;display: block;padding: 10px 34px;background-color:navajowhite;margin-left: 10px;
  }
</style>    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
var dataTable;
  function tableGen(status){
    if(status=="pending"){
    $('.open').css("background-color","#87CEFA");
    $('.closed').removeAttr('style');
  }
  else{
    $('.closed').css("background-color","#87CEFA");
    $('.open').removeAttr('style');
  }
    dataTable = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          ajax: {
            url:"/collection/paymentrecieved/summary/api",
            type:'get',
            data:{'status':status},
            contentType: "application/json",
              dataType: "json"
          },
          "createdRow": function( row, data, dataIndex){
                if( data.status ==  'closed'){
                    $(row).addClass('bg-green-gradient');
                    
                }
            },
          "columns": [
            { "data": "invoice_number" }, 
            { "data": "tax_date" }, 
            { "data": "partyname" }, 
            {"data":"item_name"},
            {"data":"qty"},
            {"data":"rate"},
            { "data": "total_amount" }, 
            { "data": "payment_date" }, 
            { "data": "amt_recieved" }, 
            { "data": "balance_amt" }, 
            {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
              { 
                if(data.status=="closed"){
                  var x="style='display:none'";
                }
                else{
                  var x="style='display:block'";
                }
                
                  return '<a href="/collection/recievedbytax/'+data.id+'"><button class="btn btn-foursquare btn-xs" > Details </button></a>&nbsp;'+
                  '<a onclick="alert_status('+data.id+')"><button class="btn btn-info btn-xs"'+x+'> Close </button></a>&nbsp;';
                
              },"orderable": false
              }
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 11 },
              // { "orderable": false, "targets": 8 }
            ]
          
        });
  }
  $(document).ready(function()  {
      tableGen('pending');
    });
    $(".open").click(function(){
      if(dataTable)
            dataTable.destroy();
           
      tableGen('pending');
    });
    $(".closed").click(function(){
      if(dataTable)
            dataTable.destroy();
    
   
    tableGen('closed');
    });
    function alert_status(id){
      // debugger;
      $('#modal_div').empty();
      $('.select2').select2('destroy');
      $('#modal_div').append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Interview Status</h4>'+
                  '</div>'+
                  '<form id="infos" method="GET" action="/collection/paymentrecievedbytax/status">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      '<input type="hidden" name="id" value="'+id+'">'+
                      '<div class="row">'+
                          '<div class="col-md-12">'+
                              '<label for="">Status :<sup>*</sup></label>'+
                              '<select name="status" id="status" class="select2 input-css" style="width:100%" required>'+
                                '<option value="">Select Status</option>'+
                                '<option value="closed">Close</option>'+
                              '</select>'+
                              '<label id="status-error" class="error" for="status"></label>'+
                          '</div>'+
                      '</div>'+
                      
                    
                    '</div>'+
                    '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>&nbsp;&nbsp;'+
                      '<a target="_blank"><button type="submit" class="btn btn-success"  onclick="$(\'#infos\').validate();">Submit</button></a>'+
                    '</div>'+
                    '</form>'+
                    '</div>'+
                '</div>'+
              '</div>'+
            '</div>'
      );
       $(document).find('#myModal').modal("show");
        $('.select2').select2();
    }
    
</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class=""> Payment Recieved Summary</i></a></li>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
              @section('titlebutton')
              
              @endsection
                @include('sections.flash-message')
                @yield('content')
            </div>
            <div class="alert alert-success alert-block goodmsg" style="display: none;">
              <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong id="mesg"></strong>
            </div>
            <div id="modal_div"></div>
        <!-- Default box -->
        <div class="box">
          <div class="box-header with-border">
            <ul class="nav nav1 nav-pills">
              <li class="nav-item">
                <a class="nav-link open" style="background-color:#87CEFA">Open</a>
              </li>
              <li class="nav-item">
                <a class="nav-link closed">Closed</a>
              </li>
             
            </ul>
          </div>

          <div class="box-body">
            <table id="taxinvoice_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>{{__('taxinvoice.mytitle')}}</th>
                  <th>Tax Invoice Date</th>
                  <th>{{__('taxinvoice.party')}}</th>
                  <th>Item Name</th>
                  <th>Qty</th>
                  <th>Rate</th>
                  <th>Amount</th>
                  <th>Payment Date</th>
                  <th>Amount received</th>
                  <th>Balance amount</th>
                  <th>{{__('taxinvoice.action')}}</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
           
            </table>
          </div>
        </div>
     
        <!-- /.box -->
      </section>
@endsection
