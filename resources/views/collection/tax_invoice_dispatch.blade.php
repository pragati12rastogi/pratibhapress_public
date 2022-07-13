@extends($layout)

@section('user', Auth::user()->name)

@section('title','Create Invoice Receipt Date')
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>

  $(document).ready(function()  {
      dataTable = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/collection/taxinvoice/dispatch/api",
          "createdRow": function( row, data, dataIndex){
              if( data.ti_payment_date_id !=  null){
                  $(row).addClass('bg-maroon-gradient');
              }
          },
          "columns": [
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
            { "data": "tax_date" }, 
            {"data":"dispatch_mode"},
            {"data":"status"},
            { "data": "person" }, 
            { "data": "dispatch_date" }, 
            { "data": "courier_company" }, 
            { "data": "docket_number" }, 
            { "data": "docket_date" },
            { "data": "created_time" }, 
            {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
              { 
                // console.log(data);
                if(data.ti_payment_date_id == null){
                  return '<a onclick="alert_status('+data.tax_id+','+data.payment_term_id+')"><button class="btn btn-foursquare btn-xs"> Create </button></a>&nbsp;';
                }else{
                  return "";
                }
                
              }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 12 },
              // { "orderable": false, "targets": 8 }
            ]
          
        });
    });
    function alert_status(id,pay_t){
      $('#modal_div').empty();
      $('.select2').select2('destroy');

      $('#modal_div').append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Tax Invoice Receipt Date</h4>'+
                  '</div>'+
                  '<form id="infos" method="GET" action="/collection/tax/dispatch/reciept">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                    '<span id="fc_err" style="color:red; display: none;"></span>'+
                      '<input type="hidden" name="id" id="tax_id" value="'+id+'">'+
                      '<input type="hidden" name="pay_term" id="pay_term" value="'+pay_t+'">'+
                      '<div class="row">'+
                          '<div class="col-md-6">'+
                              '<label for="">Tax Invoice Receipt Date :<sup>*</sup></label>'+
                              '<input type="text" autocomplete ="off" class="datepicker3 input-css tir_date" name="tir_date" id="tir_date" required>'+
                              '<label id="tir_date-error" class="error" for="tir_date"></label>'+
                          '</div>'+
                      '</div><br>'+
                    '</div>'+
                    '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>&nbsp;&nbsp;'+
                      '<a target="_blank"><button type="submit" class="btn btn-success">Submit</button></a>'+
                    '</div>'+
                    '</form>'+
                    '</div>'+
                '</div>'+
              '</div>'+
            '</div>'
      );
       $(document).find('#myModal').modal("show");
        
        $('.select2').select2();
        $(".datepicker3").datepicker();
        $('#infos').submit(function(e){     
            // debugger;
            e.preventDefault();
            var $form = $(this);
            // check if the input is valid
            if(! $form.valid()){
              return false; 
            }
            var id = $("#tax_id").val()
            
            var tir_date = $("#tir_date").val();
            var pay_term = $("#pay_term").val();
            $('#ajax_loader_div').css('display','block');
            $.ajax({
              type:'get',
              url:"/collection/tax/dispatch/reciept",
              data:{'id':id,'tir_date':tir_date,'pay_term':pay_term},
              contentType: "application/json",
              dataType: "json",
              success:function(result) {
                  $('#ajax_loader_div').css('display','none');
                  // debugger;
                  if((result.error).length > 0){
                    $("#fc_err").text(result.error).show();
                    setTimeout(function() { 
                        $('#fc_err').fadeOut('fast'); 
                    }, 8000);
                  }else if((result.msg).length > 0){
                      $('#myModal').modal('hide');
                      dataTable.draw();
                      $(".goodmsg").show();
                      $("#mesg").text(result.msg);
                  }
                }

            });
        });
    }
</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class=""> Create Invoice Receipt Date</i></a></li>
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
            <!-- <h3 class="box-title">{{__('taxinvoice.list')}}</h3> -->
          </div>
          <div class="box-body">
            <table id="taxinvoice_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>{{__('taxinvoice.mytitle')}}</th>
                  <th>{{__('taxinvoice.party')}}</th>
                  <th>{{__('taxinvoice.consignee')}}</th>
                  <th>Tax Invoice Date</th>
                  <th>Dispatch Mode</th>
                  <th>Status</th>
                  <th>{{__('tax_invoice.person_name')}}</th>
                  <th>{{__('tax_invoice.tax_invoice_dispatch_date')}}</th>
                  <th>{{__('tax_invoice.courier_company')}}</th>
                  <th>{{__('tax_invoice.docket_number')}}</th>
                  <th>Docket Date</th>
                  <th>Tax Dispatch Timestamp</th>
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
