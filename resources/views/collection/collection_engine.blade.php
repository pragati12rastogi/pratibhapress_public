@extends($layout)

@section('title', 'Follow Up Sheet')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="/delegation/summary"><i class=""></i>Follow Up Sheet</a></li> 
@endsection
@section('css')
<style>


</style>
 
@endsection
@section('js')

<script src="/js/dataTables.responsive.js"></script>
<script>
  var dataTable;

  // Data Tables
  $(document).ready(function() {
    done();
    $(".select2").css('width','280px');
  });

  function done(){
    if(dataTable){
      dataTable.destroy();
    }
    dataTable = $('#table_done').DataTable({
      "serverSide": true,
          "autoWidth": false,
          "fixedColumns":   {
            "leftColumns": 1
        },
        "pagingType": "full_numbers",
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "scrollY":        true,
        "scrollX":        true,
        "scrollCollapse": true,
      "ajax": "/collection/engine/follow/up/sheet/api",
      "columns": [
        {"data":"referencename"},
        {"data":"partyname"},
        {"data":"contact_person"},
        {"data":"contact"},
        {"data":"email"},
        { 
                "data":"invoice_number","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            {"data":"total_amount","render": function(data, type, full, meta){
                  if(data)
                  return data.toFixed(2);
                  else
                    return "";
                } 
            },
            { 
                data:function(data, type, full, meta){
                  var payment_date=data.payment_date;
                  var z=new Array();
                 
                    payment_date=payment_date.split(',');
                    console.log(payment_date);
                    
                    for(var i=0;i<payment_date.length;i++)
                    {
                      if(payment_date[i]!="-")
                      {
                          var start=new Date(payment_date[i]);
                          var dd=start.getDate();
                          var mm=start.getMonth()+1;
                          var yyyy=start.getFullYear();
                          var days=dd+'-'+mm+'-'+yyyy;
                          z[i]=days;
                      }
                    else{
                      z[i]=payment_date[i];
                    }
                  }
                  z=z.join(',');
                  return z.replace(/,/g,'<br>');
                } 
            },
            {"data":"left_amt","render": function(data, type, full, meta){
                  if(data)
                  return data.toFixed(2);
                  else
                    return "";
                } 
            },
            { 
                data: function(data, type, full, meta){
                  var payment_date=data.payment_date;
                  var z=new Array();
                  var left_amt=data.left_amt;
                  
                    payment_date=payment_date.split(',');
                    console.log(payment_date);
                    
                    for(var i=0;i<payment_date.length;i++)
                    {
                      if(payment_date[i]!="-")
                      {
                        if(left_amt>1)
                        {
                          var start=new Date(payment_date[i]);
                          var end   = new Date();
                          if(start>end){
                            var diff  = new Date(start - end);
                          }
                          else{
                            var diff  = new Date(end - start);
                          }
                          var days = diff/1000/60/60/24;
                          z[i]=Math.round(days);
                        }
                      }
                    else{
                      z[i]=payment_date[i];
                    }
                  }
                  console.log(z);
                  z=z.join(',');
                  return z.replace(/,/g,'<br>');
                } 
            },

        {"data":"id","render": function(data,type,full,meta){
          if(data){
            return "<button id="+data+" onClick='alert_status("+data+")' class='btn btn-primary btn-xs'>Add Status</button> &nbsp;"+
            "<a href='/collection/details/"+data+"' target='_blank'><button id="+data+"  class='btn btn-primary btn-xs' style='background-color:crimson'>Details</button> &nbsp;";
          }else{
            return "";
          }
        }}
        
      ],
      "columnDefs": [
        { "orderable": false, "targets": 7 },
        { "orderable": false, "targets": 8 },
        { "orderable": false, "targets": 9 },
        { "orderable": false, "targets": 10 }
      ]
        
    });
  }
  function alert_status(id){
      $('#modal_div').empty();
      $('.pay_term').select2('destroy');
      $('#modal_div').append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Status</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="" enctype="multipart/form-data">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                    '<span id="fc_err" style="color:red; display: none;"></span>'+
                      '<input type="hidden" name="party_id" id="party_id" value="'+id+'">'+
                      '<div class="row">'+
                          '<div class="col-md-6">'+
                              '<label for="">Payment received status:<sup>*</sup></label>'+
                              '<select class="select2 input-css" name="pr_status" id="pr_status" style="width:100%" required>'+
                              '<option value="">Select</option>'+
                              <?php foreach ($payment_status as $key): ?>
                                '<option value="{{$key->id}}" >{{$key->name}}</option>'+
                              <?php endforeach ?>
                              '</select>'+
                              '<label id="pr_status-error" class="error" for="pr_status"></label>'+
                          '</div>'+
                      '</div><br>'+
                      '<div class="row" id="call_back_div" style="display:none">'+
                          '<div class="col-md-6">'+
                              '<label for="">Date :<sup>*</sup></label>'+
                              '<input type="text" class="input-css cb_date datepicker3" name="cb_date" id="cb_date" >'+
                              '<label id="cb_date-error" class="error" for="cb_date"></label>'+
                          '</div>'+
                          '<div class="col-md-6">'+
                              '<label for="">Time :<sup>*</sup></label>'+
                              '<input type="text" class="timepickers input-css cb_time " name="cb_time" id="cb_time" >'+
                              '<label id="cb_time-error" class="error" for="cb_time"></label>'+
                          '</div>'+
                      '</div>'+
                      '<div class="row" id="dis_nr_dnc_div" style="display:none">'+
                          '<div class="col-md-6">'+
                              '<label for="">Reason :<sup>*</sup></label>'+
                              '<textarea id ="dis_reason" name ="dis_reason" class="input-css dis_reason" ></textarea>'+
                              '<label id="dis_reason-error" class="error" for="dis_reason"></label>'+
                          '</div>'+
                      '</div>'+
                      '<div class="row" id="ringing_div" style="display:none">'+
                          '<div class="col-md-6">'+
                              '<label for="">Upload Screenshot of Phone history:<sup>*</sup></label>'+
                              '<input type="file" class="input-css upload_call_logs " name="upload_call_logs" id="upload_call_logs" >'+
                              '<label id="upload_call_logs-error" class="error" for="upload_call_logs"></label>'+
                          '</div>'+
                      '</div>'+
                      '<div class="row" id="ptp_div" style="display:none">'+
                          '<div class="col-md-6">'+
                              '<label for="">Promise to Pay date:<sup>*</sup></label>'+
                              '<input type="text" class="input-css ptp_date datepicker3" name="ptp_date" id="ptp_date" >'+
                              '<label id="ptp_date-error" class="error" for="ptp_date"></label>'+
                          '</div>'+
                      '</div>'+
                      '<div class="row" id="done_div" style="display:none">'+
                          '<div class="col-md-6">'+
                              '<label for="">Follow up Remark:<sup>*</sup></label>'+
                              '<textarea id ="f_remark" name ="f_remark" class="input-css f_remark" ></textarea>'+
                              '<label id="f_remark-error" class="error" for="f_remark"></label>'+
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
        $('.select2').select2();
        $('.timepickers').wickedpicker();
        $(".datepicker3").datepicker();
        $("#pr_status").change(function(){
            if($("#pr_status option:selected").text() == "Call Back"){
              $("#call_back_div").show();
              $("#dis_nr_dnc_div").hide();
              $("#ringing_div").hide();
              $("#ptp_div").hide();
              $("#done_div").hide();

            }else if($("#pr_status option:selected").text() == "Dispute" || $("#pr_status option:selected").text() == "Not required" || $("#pr_status option:selected").text() == "Did not call"){
              $("#call_back_div").hide();
              $("#dis_nr_dnc_div").show();
              $("#ringing_div").hide();
              $("#ptp_div").hide();
              $("#done_div").hide();

            }else if($("#pr_status option:selected").text() == "Ringing/Not Contactable"){
              $("#call_back_div").hide();
              $("#dis_nr_dnc_div").hide();
              $("#ringing_div").show();
              $("#ptp_div").hide();
              $("#done_div").hide();

            }else if($("#pr_status option:selected").text() == "Promise To Pay"){
              $("#call_back_div").hide();
              $("#dis_nr_dnc_div").hide();
              $("#ringing_div").hide();
              $("#ptp_div").show();
              $("#done_div").hide();

            }else if($("#pr_status option:selected").text() == "Follow up Done"){
              $("#call_back_div").hide();
              $("#dis_nr_dnc_div").hide();
              $("#ringing_div").hide();
              $("#ptp_div").hide();
              $("#done_div").show();
              
            }
        });
        $('#infos').validate({ // initialize the plugin
        rules: {
              pr_status: {
                  required: true
              },
              cb_date: {
                  required: true
              },
              cb_time: {
                  required: true
              },
              dis_reason: {
                  required: true
              },
              upload_call_logs: {
                  required: true
              },
              ptp_date: {
                  required: true
              },
              f_remark: {
                  required: true
              }
            }
        });
        $('#infos').submit(function(e){     
            e.preventDefault();
            var formvalidation=$("#infos").valid();
            var formData = new FormData(this);

            if(formvalidation==true)
            {
              $('#ajax_loader_div').css('display','block');
              $.ajax({
                type:'post',
                url:"/collection/engine/status/update",
                data:formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(result) {
                    $('#ajax_loader_div').css('display','none');
                    if((result.error).length > 0){
                      $("#fc_err").text(result.error).show();
                      setTimeout(function() { 
                          $('#fc_err').fadeOut('fast'); 
                      }, 8000);
                    }else if((result.msg).length > 0){
                        $('#myModal').modal('hide');
                        done();
                        $(".goodmsg").show();
                        $("#mesg").text(result.msg);
                    }
                  }
              });
            }
        });
    }   

        
    
  
</script>

@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            <div class="alert alert-success alert-block goodmsg" style="display: none;">
              <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong id="mesg"></strong>
            </div>
            <div id="modal_div"></div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    
                      @endsection
                    
                    <table id="table_done" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>Reference Name</th>
                            <th>Client Name</th>
                            <th>Contact Person</th>
                            <th>Contact No.</th>
                            <th> Email</th>
                            <th>Bill Nos.</th>
                            <th>Bill Total Amount</th>
                            <th>Payment Date</th>
                            <th>OverDue Amount</th>
                            <th>OverDue by</th>
                            <th>Action</th>
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