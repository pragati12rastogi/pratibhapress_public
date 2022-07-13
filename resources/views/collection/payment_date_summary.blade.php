@extends($layout)

@section('user', Auth::user()->name)

@section('title', 'Payment Date Summary')
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
          "ajax": "/collection/payment/date/summary/api",
          "columns": [
            { "data": "invoice_number" }, 
            { "data": "tax_date" }, 
            { "data": "partyname" }, 
            {"data":"item_name"},
            {"data":"qty"},
            { "data": "rate" }, 
            { "data": "total_amount" }, 
            { "data": "tax_reciept_date" }, 
            { "data": "payment_date" }, 
            // { 
            //     "data":"pay_name","render": function(data, type, full, meta){
            //       if(data != null){
            //         if(full.cp != null){
            //             if(full.col_name == data){
            //                 return full.tax_reciept_date +'('+ data +')';
            //             }else{
            //                 return full.tax_reciept_date +'('+ full.col_name +')';
            //             }
            //         }else{
            //            return full.tax_reciept_date +'('+ data +')'; 
            //         }
                    
            //     }else{
            //       return full.tax_reciept_date;
            //     }
                 
            //     } 
            // }, 
            {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
              {
                // console.log(data);
                var x ="'"+data.payment_date+"'";
                // console.log(x);
                  return '<a onclick="alert_status('+data.id+','+ x +')"><button class="btn btn-foursquare btn-xs"> Edit Payment Date </button></a>&nbsp;'; 
                // if(data.pay_name != null){
                //     if(data.cp != null){
                //         if(data.col_name == data.pay_name){
                //             return '<a onclick="alert_status('+data.id+','+data.payment_term_id+')"><button class="btn btn-foursquare btn-xs"> Status </button></a>&nbsp;';
                //         }else{
                //             return '<a onclick="alert_status('+data.id+','+data.cp+')"><button class="btn btn-foursquare btn-xs"> Status </button></a>&nbsp;';
                //         }
                //     }else{
                //        return '<a onclick="alert_status('+data.id+','+data.payment_term_id+')"><button class="btn btn-foursquare btn-xs"> Status </button></a>&nbsp;'; 
                //     }
                    
                // }else{
                //   return '<a onclick="alert_status('+data.id+','+data.payment_term_id+')"><button class="btn btn-foursquare btn-xs"> Status </button></a>&nbsp;';
                // }
                
              }
            }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 9},
              // { "orderable": false, "targets": 8 }
            ]
          
        });
    });
    function alert_status(id,pay_t){
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
                    '<h4 class="modal-title">Payment Date</h4>'+
                  '</div>'+
                  '<form id="infos" method="GET" action="/collection/tax/dispatch/reciept">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                    '<span id="fc_err" style="color:red; display: none;"></span>'+
                      '<input type="hidden" name="id" id="pay_id" value="'+id+'">'+
                      '<div class="row">'+
                          '<div class="col-md-6">'+
                              '<label for="">Payment Date :<sup>*</sup></label>'+
                              '<input type="text" name ="pay_date" value="'+pay_t+'" id="pay_date" class="input-css datepicker3 pay_date">'+
                              '<label id="pay_date-error" class="error" for="pay_date"></label>'+
                          '</div>'+
                      '</div>'+
                    
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
       // $("#pay_term").val(pay_t).attr("selected", "true");
        $('.select2').select2();
        $(".datepicker3").datepicker({
          format: 'dd-mm-yyyy'
        });
        $('#infos').submit(function(e){     
            // debugger;
            e.preventDefault();
            var $form = $(this);
            // check if the input is valid
            if(! $form.valid()){
              return false; 
            }
            var id = $("#pay_id").val()
            var pay_date = $("#pay_date").val();
            $('#ajax_loader_div').css('display','block');
            $.ajax({
              type:'get',
              url:"/collection/update/payment/date",
              data:{'id':id,'pay_date':pay_date},
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
  <li><a href="#"><i class=""> Payment Date Summary</i></a></li>
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
                  <th>Tax Invoice No</th>
                  <th>Tax Invoice Date</th>
                  <th>Client Name</th>
                  <th> Item Name</th>
                  <th> Qty</th>
                  <th> Rate</th>
                  <th>Amount</th>
                  <th>Invoice Receipt Date</th>
                  <th>Payment Date</th>
                  <th>Action</th>
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
