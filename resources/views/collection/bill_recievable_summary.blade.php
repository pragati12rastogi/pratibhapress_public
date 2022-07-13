@extends($layout)

@section('user', Auth::user()->name)

@section('title','Bill Recievable Summary')
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">
<style type="text/css">
</style>    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
                
var dataTable;
  function tableGen(party,ref){
    dataTable = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
              "url": "/collection/billrecieve/summary/api",
              "datatype": "json",
                  "data": function (data) {
                      data.party = party;
                      data.ref = ref;
                  }
              },
          "columns": [
            { "data": "invoice_number" }, 
            { "data": "tax_date" }, 
            { "data": "partyname" }, 
            {"data":"item_name"},
            {"data":"qty"},
            {"data":"rate"},
            // { "data": "total_amount" }, 
            { 
                data: function(data, type, full, meta){
                    return data.total_amount.toFixed(2);
                 
                } 
            }, 
            { "data": "payment_date" }, 
            { "data": "amt_recieved" }, 
            // { "data": "balance_amt" }, 
            { 
                data: function(data, type, full, meta){
                    return data.balance_amt.toFixed(2);
                 
                } 
            }, 
            
            {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
              { 
                  if(data.payment_date && data.status!="closed"){
                    var x="style=display:block";
                  }
                  else{
                    var x="style=display:block";
                  }
                  var balance=data.balance_amt.toFixed(2);
                  return '<a onclick="alert_status('+data.id+','+balance+')"><button class="btn btn-foursquare btn-xs" '+x+'> Update payment received </button></a>&nbsp;';
                
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
      tableGen('','');
    });
    function alert_status(id,bal_amt){
      $('#modal_div').empty();
      $('.pay_term').select2('destroy');

      $('#modal_div').append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Tax Invoice Payment Receipt Entry</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                    '<span id="fc_err" style="color:red; display: none;"></span>'+
                      '<input type="hidden" name="tax_id" id="tax_id" value="'+id+'">'+
                      '<div class="row">'+
                          '<div class="col-md-6">'+
                              '<label for="">Payment received on date :<sup>*</sup></label>'+
                              '<input type="text" autocomplete ="off" class="datepicker3 input-css recieve_date" name="recieve_date" id="recieve_date" required>'+
                              '<label id="recieve_date-error" class="error" for="recieve_date"></label>'+
                          '</div>'+
                          '<div class="col-md-6">'+
                              '<label for="">Amount received :<sup>*</sup> Max( '+ bal_amt +' )</label>'+
                              '<input type="number" autocomplete ="off" class="input-css amt_recieve" name="amt_recieve" id="amt_recieve" min="0" placeholder="max:'+bal_amt+'" max ='+ bal_amt +' step="0.01" required>'+
                              '<label id="amt_recieve-error" class="error" for="amt_recieve"></label>'+
                          '</div>'+
                      '</div><br>'+
                      '<div class="row">'+
                          '<div class="col-md-6">'+
                              '<label for=""> Mode of payment :<sup>*</sup></label>'+
                              '<select class="pay_term input-css select2" name="pay_term" id="pay_term" style="width:100%" required>'+
                              '<option value="">Select</option>'+
                              <?php foreach ($mop as $key): ?>
                                '<option value="{{$key->id}}" >{{$key->value}}</option>'+
                              <?php endforeach ?>
                              '</select>'+
                              '<label id="mode_pay-error" class="error" for="mode_pay"></label>'+
                          '</div>'+
                          '<div class="col-md-6">'+
                              '<label for="">Payment Advice Upload :<sup></sup></label>'+
                              '<input type="file" class=" upload_adv" name="upload_adv" id="upload_adv" >'+
                              '<label id="upload_adv-error" class="error" for="upload_adv"></label>'+
                          '</div>'+
                      '</div><br>'+
                      '<div class="row">'+
                          '<div class="col-md-6">'+
                              '<label for="">Deductions (if any) :<sup></sup></label>'+
                              '<input type="number" class="input-css deduct" min="0" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" name="deduct" id="deduct" >'+
                              '<label id="deduct-error" class="error" for="deduct"></label>'+
                          '</div>'+
                      '</div><br>'+
                      '<div class="row">'+
                          '<div class="col-md-6">'+
                              '<label for="">Reason for deduction:<sup></sup></label>'+
                              '<textarea id ="ded_reason" name ="ded_reason" class="input-css ded_reason"></textarea>'+
                              '<label id="ded_reason-error" class="error" for="ded_reason"></label>'+
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
        var currentDate = new Date();
        $(".datepicker3").datepicker({
          format: 'dd-mm-yyyy',
          endDate:currentDate,
        });
        // $("#amt_recieve").change(function(){
        //     if($(this).val() > bal_amt){
        //       $(this).val("");
        //       $("#amt_recieve-error").text('Value is greater than balance').show();
        //     }else{
        //       $("#amt_recieve-error").text('Value is greater than balance').hide();
        //     }
        // });
        $('#infos').submit(function(e){     
            e.preventDefault();
            if($("#deduct").val() != 0){
              $('#ded_reason').attr('required','required');
            }else{
              $('#ded_reason').removeAttr('required');
            }
            var formvalidation=$("#infos").valid();
            var formData = new FormData(this);
            if(formvalidation==true)
            {
              $('#ajax_loader_div').css('display','block');
              $.ajax({
                type:'post',
                url:"/collection/billrecieve/entry/db",
                data:formData,
                cache:false,
                contentType: false,
                processData: false,
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
            }
        });
    }
    $("#reference_name").change(function(){
      
        $('#ajax_loader_div').css('display','block');
        $ref = $(this).val();
       
        $.ajax({
              url:"/report/fetch/client/api/"+$ref,
              type: "GET",
              success: function(result) {
                if (result) {
                   
                        $("#party_name").empty();
                        $("#party_name").append('<option value="0">Select Client</option>');
                        $.each(result, function(value,key) {
                            
                            $("#party_name").append('<option value="' + key.id + '">' + key.partyname + '</option>');
                        });
                        $('#ajax_loader_div').css('display','none');
                    }
              }
        });
        if(dataTable)
          dataTable.destroy();
        var x=$("#reference_name").val();
        tableGen('',x);
    });
    
    function gettax()
    {
      var party = $("#party_name").val();
      if(dataTable)
        dataTable.destroy();
          tableGen(party,''); 
    }
    $("#party_name").change(function(){
     
          if($("#party_name").val()!=0){
            gettax();
          }
          else if($("#party_name").val()==0){
            if(dataTable)
              dataTable.destroy();
            var x=$("#reference_name").val();
            tableGen('',x); 
          }
    });
</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class="">Bill Recievable Summary</i></a></li>
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
            <div class="row">
            <div class="col-md-6 {{ $errors->has('reference_name') ? 'has-error' : ''}}">
                <label>Reference Name<sup>*</sup></label>
                <select name="reference_name" id="reference_name" class="select2">
                    <option value="0">Select Reference</option>
                    @foreach($reference as $ref)
                        <option value="{{$ref['id']}}">{{$ref['referencename']}}</option>
                    @endforeach
                </select>
                {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
            </div>
            <div class="col-md-6 {{ $errors->has('party_name') ? 'has-error' : ''}}">
                <label>Client Name<sup>*</sup></label>
                <select name="party_name" id="party_name" class="select2 party_name">
                    <option value="">Select Client</option>
                    
                </select>
                {!! $errors->first('party_name', '<p class="help-block">:message</p>') !!}
            </div>
          </div><br><br>
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
