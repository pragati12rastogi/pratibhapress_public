@extends($layout)

@section('user', Auth::user()->name)

@section('title', __('taxinvoice.list'))
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
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
@endsection
@section('js')
<script src="/js/bootbox.min.js"></script>
<script src="/js/bootbox.locales.min.js"></script>

<script src="/js/dataTables.responsive.js"></script>
<script>
$('#infos').on('submit',function(e) {
        $(document).find('.io').each(function(e) {
            $(this).rules("add", {
                required: true,
                // notValidIfSelectFirst:"default",
                messages: {
                    required: "SAD is required"
                }
            });
        });
    });
  $('#infos').validate();
</script>
<script>
  var dataTable;
   function gettax()
  {
    var x="{{Auth::user()->user_type}}";
        $('#print').hide();
        $('#tax').show();
        $('#cancel').hide();
        $('.chal').css("background-color","#87CEFA");
        $('.chal1').removeAttr('style');
        $('.chal2').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/taxinvoice/api",
          "createdRow": function( row, data, dataIndex){
                if( data.status ==  'Cancelled'){
                    $(row).addClass('bg-yellow-gradient');
                    
                }
            },
          "columns": [
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
            { 
                "data":"challan_number","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            { 
                "data":"io_number","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
           {"data":"terms_of_delivery"},
            { 
                "data":"total_amount","render": function(data, type, full, meta){
                  if(data)
                    return "Rs."+ (data).toFixed(2);
                  else
                    return "";
                } 
            },
            {"data":"total","render": function(data, type, full, meta){
                  if(data)
                    return "Rs."+ (data);
                  else
                    return "";
                } },
            { 
            "data": function(data, type, full, meta){
                var dt=data.created_at;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+ 1;
                      var yyyy=dt.getFullYear();
                      var hh=dt.getHours();
                      var mi=dt.getMinutes();
                      var ss=dt.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                      var ac=dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                      return ac;
               }
              
            },
            {"data":"date"},
            // {'data':'status'},
            {
                  "targets": [ -1 ],
                  data: function(data,type,full,meta)
                  {
                    var dt=data.created_at;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+ 1;
                      var yyyy=dt.getFullYear();
                     
                      var ac=dd+'-'+mm+'-'+yyyy;
                      var current=new Date();
                      var dd1=current.getDate();
                      var mm1=current.getMonth()+ 1;
                      var yyyy1=current.getFullYear();
                      var ac1=dd1+'-'+mm1+'-'+yyyy1;
                        console.log(ac);
                        var ls='';
                        if(data.st2=="pending" && x!="superadmin"){
                          var st2="Pending Request For Edit";
                          var cl2="btn-success";
                          var clk2='';
                          var dis2="disabled";
                
                    }
                    else if(data.st2=="pending" && x=="superadmin"){
                          var st2="Edit";
                          var cl2="btn-success";
                          var clk2='';
                          var dis2="";
                
                    }
                   
                    else{
                      var st2="Request Edit";
                      var cl2="btn-success";
                      var clk2='onclick="delete_alert_dailog('+data.id+')"';
                      var dis2="";
                    }
                     
                      if(x=="superadmin"){
                        ls= '<a href="/taxinvoice/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>';
                      }
                      else if((ac==ac1 || data.is_update=="allowed")){
                        ls= '<a href="/taxinvoice/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>';
                      }
                      else{
                        
                        ls='<a class="checkforcancel" onclick="edit_alert_dailog('+data.id+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" '+dis2+' class="btn '+cl2+' btn-xs"> '+st2+'  </button></a> &nbsp;';
                      }
                      
                    if(data.st=="pending" && x!="superadmin"){
                          var st="Pending Request For Cancel";
                          var cl="btn-success";
                          var clk='';
                          var dis="disabled";
                
                    }
                    else if(data.st=="pending" && x=="superadmin"){
                      var st="Cancel";
                          var cl="btn-success";
                          var clk='';
                          var dis="";
                    }
                    else{
                      var st="Cancel";
                      var cl="btn-warning";
                      var clk='onclick="delete_alert_dailog('+data.id+')"';
                      var dis="";
                    }
                    if(data.st1=="pending" && x!="superadmin"){
                          var st1="Pending Request For Delete";
                          var cl1="btn-warning";
                          var clk1='';
                          var dis1="disabled";
                
                    }
                    else if(data.st1=="pending" && x=="superadmin"){
                          var st1="Delete";
                          var cl1="btn-warning";
                          var clk1='';
                          var dis1="";
                
                    }
                    else{
                      var st1="Delete";
                      var cl1="btn-info";
                      var clk1='onclick="delete_alert_dailog('+data.id+')"';
                      var dis1="";
                    }
                    var inv_type=1;
                    var einv_type=2;
                    
                    if(data.status == 'Cancelled'){
                      return "<a href='/taxinvoice/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href="/taxinvoice/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>'+
                      
                    '<a onclick="print_alert_dailog('+data.id+','+inv_type+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Print </button></a> &nbsp;' ;
                    }else{
                      return "<a href='/taxinvoice/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                      ls+
                    '<a class="checkforcancel" onclick="delete_alert_dailog('+data.id+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" '+dis1+' class="btn '+cl1+' btn-xs"> '+st1+'  </button></a> &nbsp;' +
                    '<a class="checkforcancel" onclick="cancel_alert_dailog('+data.id+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" '+dis+' class="btn '+cl+' btn-xs"> '+st+'  </button></a> &nbsp;' +
                    '<a onclick="print_alert_dailog('+data.id+','+inv_type+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Print </button></a> &nbsp;' ;
                    // '<a onclick="print_alert_dailog('+data.id+','+einv_type+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> E-Print </button></a> &nbsp;' ;
                  
                    }
                  },
                  "orderable": false
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 5 },
              { "orderable": false, "targets": 6 }
            ]
          
        });
  }
  function gettaxprint()
  {
        $('#print').show();
        $('#tax').hide();
        $('#cancel').hide();
        $('.chal1').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal2').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#tax_print').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/taxinvoice/print/api",
          "columns": [
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
            { 
                "data":"challan_number","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(",","<br/>");
                  else
                    return "";
                } 
            },
            { 
                "data":"io_number","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(",","<br/>");
                  else
                    return "";
                } 
            },
            // { "data": "io_number" }, 
            // { "data": "" }, 
            { 
                "data": function(data, type, full, meta){
                  var dataArray = [];
                  var io_po_data=jQuery.parseJSON(data.io_po_number);
                  console.log(io_po_data.length);
                  $.each(io_po_data, function (index, value) {
                    dataArray.push([value["client_po"] ]);
                });
                
                console.log(dataArray.join("<br/>"));
                var join=dataArray.join("<br/>");
                
                return join;
                } 
            },
           {"data":"terms_of_delivery"},
            { 
                "data":"total_amount","render": function(data, type, full, meta){
                  if(data)
                    return "Rs."+ (data).toFixed(2);
                  else
                    return "";
                } 
            },
            { 
            "data": function(data, type, full, meta){
                var dt=data.created_at;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+ 1;
                      var yyyy=dt.getFullYear();
                      var hh=dt.getHours();
                      var mi=dt.getMinutes();
                      var ss=dt.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                      var ac=dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                      return ac;
               }
              
            }, 
            {
                  "targets": [ -1 ],
                  "data": function(data,type,full,meta)
                  {
                    var PoArray = [];
                    var IoArray = [];
                    var io_po=jQuery.parseJSON(data.io_po_number);
                      $.each(io_po, function (index, value) {
                        PoArray.push([value["client_po"]]);
                        IoArray.push([value["io"]]);
                    });
                    var io_j=IoArray.join("$");
                    console.log(io_j);
                    
                    var po_j=PoArray.join('$');
                    var inv_type=1;
                    var einv_type=2;
                    return '<a onclick=print_alert_dailog_print('+data.id+','+data.tx_p+')  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Print </button></a> &nbsp;' ;
                    // '<a href="/taxinvoice/update/'+data+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>'+
                    // '<a onclick="cancel_alert_dailog('+data+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-warning btn-xs"> Cancel </button></a> &nbsp;' +
                    
                  },
                  "orderable": false
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 5 },
              { "orderable": false, "targets": 6 }
            ]
          
        });
  }
  
    function print_alert_dailog(ele,inv)
    {
      $('#ajax_loader_div').css('display','block');
      var msg = '';
      $.ajax({
        url: "/get/taxinvoice_ios/"+ ele,
        type: "GET",
        success: function(result) {
          console.log(result);
          var message = result.data;
          var tax_print=result.data1.length;
          console.log('message',message);
          $('#ajax_loader_div').css('display','none');
        
            for(var i=0;i<message.length;i++)
          {
            if(message[i].is_po_provided==1)
            {
              var opt = message[i].po_number.split(',');
              var count=opt.length;
              var dis='';
              if(count==1)
                  dis="disabled";
              else
                  dis='';
              msg+='<label>Select Client Po for Internal Order '+message[i].io_number+' </label>'+
              '<div class="row"><div class="col-md-6">'+
                '<select name="io['+message[i].io+']" required style="width:100%"  class="io select2 input-css">'+
                '<option value="" '+dis+'>Select at least one client po</option>';
              
              for(var j=0;j<opt.length;j++)
              {
                msg += '<option value="'+opt[j]+'">'+opt[j]+'</option>';
              }
              msg+= '</select></div></div>';
            }
            if(message[i].is_po_provided==0){
              // var opt = message[i].po_number.split(',');
              // var count=opt.length;
              // var dis='';
              // if(count==1)
              //     dis="disabled";
              // else
                  // dis='';
              msg+='<div style="display:none"><label>Select Client Po for Internal Order '+message[i].io_number+' </label>'+
              '<div class="row"><div class="col-md-6">'+
                '<select name="io['+message[i].io+']" required style="width:100%"  class="io select2 input-css">'+
                '<option value="Verbal">Select at least one client po</option>';
              msg+= '</select></div></div></div>';
            }
          }
          
          
          $('#modal_div').empty().append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Print</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/templateTax/'+ele+'">'+
                 
                    '@csrf'+
                    '<div class="modal-body">'+
                      msg+
                     
                      '<br><label>Please select PRINT OPTIONS for Tax Invoice</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="1" checked="checked"> Original for Recipient.</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="2" checked="checked"> Duplicate for Transporter.</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="3" checked="checked"> Triplicate.</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="4" checked="checked"> Extra copy.</label>'+
                    '</div>'+
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
    });  
    }
     function print_alert_dailog(ele,inv)
    {
      $('#ajax_loader_div').css('display','block');
      var msg = '';
      $.ajax({
        url: "/get/taxinvoice_ios/"+ ele,
        type: "GET",
        success: function(result) {
          console.log(result);
          var message = result.data;
          var tax_print=result.data1.length;
          console.log('message',message);
          $('#ajax_loader_div').css('display','none');
        msg='<input type="hidden" name="type" value="new" style="width:100%"  class="input-css">';
            for(var i=0;i<message.length;i++)
          {
            if(message[i].is_po_provided==1)
            {
              var opt = message[i].po_number.split(',');
              var count=opt.length;
              var dis='';
              if(count==1)
                  dis="disabled";
              else
                  dis='';
              msg+='<label>Select Client Po for Internal Order '+message[i].io_number+' </label>'+
              '<div class="row"><div class="col-md-6">'+
                '<select name="io['+message[i].io+'][]" multiple required style="width:100%"  class="io select2 input-css">'+
                '<option value="" '+dis+'>Select at least one client po</option>';
              
              for(var j=0;j<opt.length;j++)
              {
                msg += '<option value="'+opt[j]+'">'+opt[j]+'</option>';
              }
              msg+= '</select></div></div>';
            }
            if(message[i].is_po_provided==0){
              // var opt = message[i].po_number.split(',');
              // var count=opt.length;
              // var dis='';
              // if(count==1)
              //     dis="disabled";
              // else
                  // dis='';
              msg+='<div style="display:none"><label>Select Client Po for Internal Order '+message[i].io_number+' </label>'+
              '<div class="row"><div class="col-md-6">'+
                '<select name="io['+message[i].io+'][]" required style="width:100%"  class="io select2 input-css">'+
                '<option value="Verbal" selected="selected">Verbal</option>';
              msg+= '</select></div></div></div>';
            }
          }
          
          
          $('#modal_div').empty().append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Print</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/templateTax/'+ele+'">'+
                  '<input type="hidden" value="'+inv+'" name="inv">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      msg+
                      '<br><label>Please select PRINT OPTIONS for Tax Invoice</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="1" checked="checked"> Original for Recipient.</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="2" checked="checked"> Duplicate for Transporter.</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="3" checked="checked"> Triplicate.</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="4" checked="checked"> Extra copy.</label>'+
                    '</div>'+
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
    });  
    }
    function print_alert_dailog_print(ele,print)
    {
    
      $('#ajax_loader_div').css('display','block');
      var msg = '';
   
          msg+='<input type="hidden" name="type" value="print" style="width:100%"  class="input-css"><input type="hidden" name="print_id" value="'+print+'" style="width:100%"  class="input-css">';        
   
          $('#modal_div').empty().append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Print</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/templateTax/'+ele+'">'+
                  '<input type="hidden" value="1" name="inv">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      msg+
                      '<br><label>Please select PRINT OPTIONS for Tax Invoice</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="1" checked="checked"> Original for recipient.</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="2" checked="checked"> Duplicate for recipient.</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="3" checked="checked"> Triplicate.</label>'+
                      '<label> <input name="check_box[]" type="checkbox" value="4" checked="checked"> Extra copy.</label>'+
                    '</div>'+
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
          $('#ajax_loader_div').css('display','none');
          $(document).find('.select2').select2();
    }
    function cancel_alert_dailog(ele)
    {
      $('#ajax_loader_div').css('display','block');

      $.ajax({
        url: "/checkreqiredpermission/taxinvoice/"+ ele,
        type: "GET",
        success: function(result) {
          console.log(result);
          var message = result.message;
          $('#ajax_loader_div').css('display','none');
          if(message == 'Generate Request')
          {
            bootbox.prompt({
              title: "Reason for Request to Cancel Tax Invoice",
              inputType: 'text',
              placeholder:"Reason to cancel",
              callback: function (result) {
                if(result== null)
                {
                }
                else
                {
                  if(result.length>0)
                    var patt = new RegExp('/','g');
                    window.location.href = "/addreqiredpermission/taxinvoice/"+ele+"/"+result.replace(patt,' - - - ');
                }
              }
            });
          }  
          else if(message == 'Pending Request'){
            bootbox.alert("Request is still not authorised");
          }
          else if(message == 'Expired Request'){
            bootbox.alert("This Tax Invoice is already cancelled.");
          }
          else if(message == 'Allowed Request'){
            window.location.href = "/taxinvoice/cancel/"+ele;
          }
          else if(message == 'Rejected Request'){
            var name = result.data['authorised_by'];
            bootbox.confirm({
                message: "Request to cancel this Tax Invoice is rejected by "+name,
                buttons: {
                    cancel: {
                        label: 'Ok',
                        className: 'btn-success'
                    },
                    confirm: {
                        label: 'Request Again',
                        className: 'btn-warning'
                    }
                },
                callback: function (result) {
                    if(result){
                      bootbox.prompt({
                        title: "Reason for Request to Cancel Tax Invoice",
                        inputType: 'text',
                        placeholder:"Reason to cancel",
                        callback: function (result) {
                          if(result== null)
                          {
                          }
                          else
                          {
                            if(result.length>0)
                              var patt = new RegExp('/','g');
                              window.location.href = "/addreqiredpermission/taxinvoice/"+ele+"/"+result.replace(patt,' - - - ');
                          }
                        }
                      });
                    } 
                }
            });
          }
      }
      });
    }
    function getcancelList(){
      $('#print').hide();
      $('#tax').hide();
        $('#cancel').show();
        $('.chal2').css("background-color","#87CEFA");
       $('.chal1').removeAttr('style');
       $('.chal').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#taxinvoice_cancel_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/taxinvoice/cancelled/list/api",
          
          "columns": [
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
            { 
                "data":"challan_number","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(",","<br/>");
                  else
                    return "";
                } 
            },
            { 
                "data":"io_number","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(",","<br/>");
                  else
                    return "";
                } 
            },
           {"data":"terms_of_delivery"},
            { 
                "data":"total_amount","render": function(data, type, full, meta){
                  if(data)
                    return "Rs."+ (data).toFixed(2);
                  else
                    return "";
                } 
            },
            { 
            "data": function(data, type, full, meta){
                var dt=data.created_at;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+ 1;
                      var yyyy=dt.getFullYear();
                      var hh=dt.getHours();
                      var mi=dt.getMinutes();
                      var ss=dt.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                      var ac=dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                      return ac;
               }
              
            }, 
            {"data":"date"},
            {'data':'status'},
            {
                  "targets": [ -1 ],
                  data: function(data,type,full,meta)
                  {
                    if(data.st=="pending"){
                          var st="Pending Request For Cancel";
                          var cl="btn-success";
                          var clk='';
                          var dis="disabled";
                
                    }
                    else{
                      var st="Cancel";
                      var cl="btn-warning";
                      var clk='onclick="cancel_alert_dailog('+data.id+')"';
                      var dis="";
                    }
                    var inv=1;
                    return "<a href='/taxinvoice/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a onclick="print_alert_dailog('+data.id+','+inv+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Print </button></a> &nbsp;' ;
                  },
                  "orderable": false
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 5 },
              { "orderable": false, "targets": 6 }
            ]
          
        });
    }
    function delete_alert_dailog(ele)
    {
      $('#ajax_loader_div').css('display','block');

      $.ajax({
        url: "/checkreqiredpermission/taxinvoicedelete/"+ ele,
        type: "GET",
        success: function(result) {
          console.log(result);
          var message = result.message;
          $('#ajax_loader_div').css('display','none');
          if(message == 'Generate Request')
          {
            bootbox.prompt({
              title: "Reason for Request to Delete Tax Invoice",
              inputType: 'text',
              placeholder:"Reason to Delete",
              callback: function (result) {
                if(result== null)
                {
                }
                else
                {
                  if(result.length>0)
                    var patt = new RegExp('/','g');
                    window.location.href = "/addreqiredpermission/taxinvoicedelete/"+ele+"/"+result.replace(patt,' - - - ');
                }
              }
            });
          }  
          else if(message == 'Pending Request'){
            bootbox.alert("Request is still not authorised");
          }
          else if(message == 'Allowed Request'){
            bootbox.alert("This Tax Invoice is already deleted.");
          }
          else if(message == 'Rejected Request' || message == 'Expired Request'){
            var name = result.data['authorised_by'];
            var mes="";
            if(message== 'Expired Request')
               mes="Request to Delete Tax Invoice is expired";
            if(message== 'Rejected Request')
               mes="Request to Delete this Tax Invoice is rejected by "+name;

            bootbox.confirm({
                message: mes,
                buttons: {
                    cancel: {
                        label: 'Ok',
                        className: 'btn-success'
                    },
                    confirm: {
                        label: 'Request Again',
                        className: 'btn-warning'
                    }
                },
                callback: function (result) {
                    if(result){
                      bootbox.prompt({
                        title: "Reason for Request to Delete Tax Invoice",
                        inputType: 'text',
                        placeholder:"Reason to Delete",
                        callback: function (result) {
                          if(result== null)
                          {
                          }
                          else
                          {
                            if(result.length>0)
                              var patt = new RegExp('/','g');
                              window.location.href = "/addreqiredpermission/taxinvoicedelete/"+ele+"/"+result.replace(patt,' - - - ');
                          }
                        }
                      });
                    } 
                }
            });
          }
      }
      });
    }
    function edit_alert_dailog(ele)
    {
      $('#ajax_loader_div').css('display','block');

      $.ajax({
        url: "/checkreqiredpermission/taxinvoiceupdate/"+ ele,
        type: "GET",
        success: function(result) {
          console.log(result);
          var message = result.message;
          $('#ajax_loader_div').css('display','none');
          if(message == 'Generate Request')
          {
            bootbox.prompt({
              title: "Reason for Request to Update Tax Invoice",
              inputType: 'text',
              placeholder:"Reason to Update",
              callback: function (result) {
                if(result== null)
                {
                }
                else
                {
                  if(result.length>0)
                    var patt = new RegExp('/','g');
                    window.location.href = "/addreqiredpermission/taxinvoiceupdate/"+ele+"/"+result.replace(patt,' - - - ');
                }
              }
            });
          }  
          else if(message == 'Pending Request'){
            bootbox.alert("Request is still not authorised");
          }
          else if(message == 'Allowed Request'){
            bootbox.alert("This Tax Invoice is already Updated.");
          }
          else if(message == 'Rejected Request' || message == 'Expired Request'){
            var name = result.data['authorised_by'];
            var mes="";
            if(message== 'Expired Request')
               mes="Request to Update Tax Invoice is expired";
            if(message== 'Rejected Request')
               mes="Request to Update this Tax Invoice is rejected by "+name;

            bootbox.confirm({
                message: mes,
                buttons: {
                    cancel: {
                        label: 'Ok',
                        className: 'btn-success'
                    },
                    confirm: {
                        label: 'Request Again',
                        className: 'btn-warning'
                    }
                },
                callback: function (result) {
                    if(result){
                      bootbox.prompt({
                        title: "Reason for Request to Update Tax Invoice",
                        inputType: 'text',
                        placeholder:"Reason to Update",
                        callback: function (result) {
                          if(result== null)
                          {
                          }
                          else
                          {
                            if(result.length>0)
                              var patt = new RegExp('/','g');
                              window.location.href = "/addreqiredpermission/taxinvoiceupdate/"+ele+"/"+result.replace(patt,' - - - ');
                          }
                        }
                      });
                    } 
                }
            });
          }
      }
      });
    }

    $(document).ready(function() {
        gettax();
    });
</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class=""> {{__('taxinvoice.list')}}</i></a></li>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
              @section('titlebutton')
              <a href="/import/data/taxinvoice"><button class="btn btn-sm btn-primary">{{__('taxinvoice.importtitle')}}</button></a>
              <a href="/export/data/taxinvoice"><button class="btn btn-sm btn-primary">{{__('taxinvoice.exporttitle')}}</button></a>
              @endsection
                @include('sections.flash-message')
                @yield('content')
            </div>
        <!-- Default box -->
        <div id="modal_div"></div>
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">{{__('taxinvoice.list')}}</h3>
          </div>
          <div class="box-body">
              <ul class="nav nav1 nav-pills">
                  <li class="nav-item">
                    <button class="nav-link1 chal"  onclick="gettax()">Tax Invoice</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal1" onclick="gettaxprint()">Printed Tax Invoice </button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal2" onclick="getcancelList()">Cancelled Tax Invoice</button>
                  </li>
                </ul><br><br>
           <div id="tax" style="display:none">
              <table id="taxinvoice_list_table" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{__('taxinvoice.mytitle')}}</th>
                    <th>{{__('taxinvoice.party')}}</th>
                    <th>{{__('taxinvoice.consignee')}}</th>
                    <th>{{__('taxinvoice.delivery')}}</th>
                    <th>{{__('taxinvoice.io')}}</th>
                    <th>{{__('taxinvoice.terms')}}</th>
                    <th>{{__('taxinvoice.amount')}}</th>
                    <th>Invoice Total</th>
                    <th>Created Date</th>
                    <th>Tax Invoice Date</th>
                    {{-- <th>Status</th> --}}
                    <th>{{__('taxinvoice.action')}}</th>
                  </tr>
                  </thead>
                  <tbody>
  
                  </tbody>
             
              </table>
           </div>
           <div id="print" >
              <table id="tax_print" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{__('taxinvoice.mytitle')}}</th>
                    <th>{{__('taxinvoice.party')}}</th>
                    <th>{{__('taxinvoice.consignee')}}</th>
                    <th>{{__('taxinvoice.delivery')}}</th>
                    <th>Internal Order in Delivery Challan</th>
                    <th>Client PO </th>
                    {{-- <th>Client Po </th> --}}
                    <th>{{__('taxinvoice.terms')}}</th>
                    <th>{{__('taxinvoice.amount')}}</th>
                    <th>Tax Invoice Date</th>
                    <th>{{__('taxinvoice.action')}}</th>
                  </tr>
                  </thead>
                  <tbody>
  
                  </tbody>
             
              </table>
           </div>
           <div id="cancel" style="display:none">
            <table id="taxinvoice_cancel_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>{{__('taxinvoice.mytitle')}}</th>
                  <th>{{__('taxinvoice.party')}}</th>
                  <th>{{__('taxinvoice.consignee')}}</th>
                  <th>{{__('taxinvoice.delivery')}}</th>
                  <th>{{__('taxinvoice.io')}}</th>
                  <th>{{__('taxinvoice.terms')}}</th>
                  <th>{{__('taxinvoice.amount')}}</th>
                  <th>Created Date</th>
                  <th>Tax Invoice Date</th>
                  <th>Status</th>
                  <th>{{__('taxinvoice.action')}}</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
           
            </table>
         </div>
          </div>
        </div>
        <!-- /.box -->
      </section>
@endsection
