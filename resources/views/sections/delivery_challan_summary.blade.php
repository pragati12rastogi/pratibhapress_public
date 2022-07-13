@extends($layout)

@section('user', Auth::user()->name)

@section('title', __('delivery_challan.list'))

@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
<style>
 /* td{
          padding: 0!important ;
     
        } */
</style>
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>

<script src="/js/bootbox.min.js"></script>
<script src="/js/bootbox.locales.min.js"></script>
<script>
   var dataTable;
  $(document).ready(function()  {
    var x="{{Auth::user()->user_type}}";
       dataTable = $('#delivery_challan_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/deliverychallan/api",
          "columns": [
            {"data":"delivery_date"},
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
            { 
                "data":"io","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(",","<br/>");
                  else
                    return "";
                      // return "<div style='background-color:red'>----</div>";
                } 
            }, 
            { 
            "data": "dispatch","render": function(data, type, full, meta){
                if(data==1)
                    return "Transporter";
                else if(data==2)
                    return "Self";
                else if(data==3)
                    return "Courier";
                } 
            }, 
            { 
            "data": function(data, type, full, meta){
                var dt=data.created;
                   
                      return dt;
               }
              
            },
            {
                  "targets": [ -1 ],
                  data: function(data,type,full,meta)
                  {
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
                    var current=new Date();
                      var dd1=current.getDate();
                      var mm1=current.getMonth()+ 1;
                      var yyyy1=current.getFullYear();
                      if(mm1<10){
                        mm1='0'+mm1;
                      }
                      var ac1=dd1+'-'+mm1+'-'+yyyy1;
                      console.log("df"+ac1);
                      var dc=data.delivery_date;
                      var dt=new Date(dc);
                      var dd2=dt.getDate();
                      var mm2=dt.getMonth()+ 1;
                      var yyyy2=dt.getFullYear();
                      if(mm2<10){
                        mm2='0'+mm2;
                      }
                      var ac2=dd2+'-'+mm2+'-'+yyyy2;
                      console.log("dfx"+ac2);
                      if(x=="superadmin"){
                        ls=  '<a href="/deliverychallan/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>&nbsp;';
                      }
                    else if(ac2==ac1 || data.is_update=="allowed"){
                        ls=  '<a href="/deliverychallan/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>&nbsp;';
                      }
                      else{
                        
                        ls='<a class="checkforcancel" onclick="edit_alert_dailog('+data.id+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" '+dis2+' class="btn '+cl2+' btn-xs"> '+st2+'  </button></a> &nbsp;';
                      }
                    if(data.st=="pending" ){
                          var st="Pending Request For Delete";
                          var cl="btn-success";
                          var clk='';
                          var dis="disabled";
                
                    }
                    else{
                      var st="Delete";
                      var cl="btn-warning";
                      var clk='onclick="cancel_alert_dailog('+data.id+')"';
                      var dis="";
                    }
                    return "<a href='/deliverychallan/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                      ls+
                    '<a class="checkforcancel" onclick="cancel_alert_dailog('+data.id+')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" '+dis+' class="btn '+cl+' btn-xs"> '+st+'  </button></a> &nbsp;' +
                    "<a href='/templateDelivery/"+data.id+"' target='_blank'><button style='margin-bottom: 5px;margin-left: 5px;' class='btn btn-danger btn-xs'> Print </button></a> &nbsp;" ;
                  },
                  "orderable": false
              }
            ],
            "columnDefs": [
              
            ]
          
        });
        
    });
    
  //   function cancel_alert_dailog(id)
  //   {
  //     var r = confirm("Are You Sure Want to Delete this Deliver Challan!");
  //     if (r == true) {
  //       $('#ajax_loader_div').css('display','block');
  //       $.ajax({
  //         url: '/deliverychallan/delete/'+id,
  //         type: 'GET',
  //         success: function(result)
  //         {
  //             $('#ajax_loader_div').css('display','none');
  //             dataTable.ajax.reload();
  //            $('#app').append(' <div class="alert alert-success alert-block"><button type="button" class="close" data-dismiss="alert">×</button><strong>Successfully Deleted IO Of Delivery Challan.</strong></div>');
  //         },
  //         error: function(xhr, status, error) { 
  //           $('#app').append(' <div class="alert alert-danger alert-block"><button type="button" class="close" data-dismiss="alert">×</button><strong>Some Error Occurred.</strong></div>');
  //             $('#ajax_loader_div').css('display','none');
  //             var errorMessage = xhr.status + ': ' + xhr.statusText;
  //             console.log(errorMessage);
  //         }
  //     });
  //     } 
  // }
  function cancel_alert_dailog(ele)
    {
      $('#ajax_loader_div').css('display','block');

      $.ajax({
        url: "/checkreqiredpermission/deliverychallan/"+ ele,
        type: "GET",
        success: function(result) {
          console.log(result);
          var message = result.message;
          $('#ajax_loader_div').css('display','none');
          if(message == 'Generate Request')
          {
            bootbox.prompt({
              title: "Reason for Request to Delete Delivery Challan",
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
                    window.location.href = "/addreqiredpermission/deliverychallan/"+ele+"/"+result.replace(patt,' - - - ');
                }
              }
            });
          }  
          else if(message == 'Pending Request'){
            bootbox.alert("Request is still not authorised");
          }
          else if(message == 'Allowed Request'){
            bootbox.alert("This Delivery Challan is already deleted.");
          }
          else if(message == 'Rejected Request' || message == 'Expired Request'){
            var name = result.data['authorised_by'];
            var mes="";
            if(message== 'Expired Request')
               mes="Request to Delete Delivery Challan is expired";
            if(message== 'Rejected Request')
               mes="Request to Delete this Delivery Challan is rejected by "+name;

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
                        title: "Reason for Request to Delete Delivery Challan",
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
                              window.location.href = "/addreqiredpermission/deliverychallan/"+ele+"/"+result.replace(patt,' - - - ');
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
        url: "/checkreqiredpermission/deliverychallanupdate/"+ ele,
        type: "GET",
        success: function(result) {
          console.log(result);
          var message = result.message;
          $('#ajax_loader_div').css('display','none');
          if(message == 'Generate Request')
          {
            bootbox.prompt({
              title: "Reason for Request to Update Delivery Challan",
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
                    window.location.href = "/addreqiredpermission/deliverychallanupdate/"+ele+"/"+result.replace(patt,' - - - ');
                }
              }
            });
          }  
          else if(message == 'Pending Request'){
            bootbox.alert("Request is still not authorised");
          }
          else if(message == 'Allowed Request'){
            bootbox.alert("This Delivery Challan is already Updated.");
          }
          else if(message == 'Rejected Request' || message == 'Expired Request'){
            var name = result.data['authorised_by'];
            var mes="";
            if(message== 'Expired Request')
               mes="Request to Update Delivery Challan is expired";
            if(message== 'Rejected Request')
               mes="Request to Update this Delivery Challan is rejected by "+name;

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
                        title: "Reason for Request to Update Delivery Challan",
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
                              window.location.href = "/addreqiredpermission/deliverychallanupdate/"+ele+"/"+result.replace(patt,' - - - ');
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
</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class=""> {{__('delivery_challan.list')}}</i></a></li>
@endsection

@section('main_section')
    <section class="content">
       
            <div id="app">
                @section('titlebutton')                
                  <a href="/import/data/deliverychallan"><button class="btn btn-sm btn-primary">{{__('delivery_challan.importtitle')}}</button></a>
                  <a href="/export/data/deliverychallan"><button class="btn btn-sm btn-primary">{{__('delivery_challan.exporttitle')}}</button></a>
                @endsection
                @include('sections.flash-message')
                @yield('content')
            </div>
        <!-- Default box -->
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">{{__('delivery_challan.list')}}</h3>
          </div>
          <div class="box-body">
            <table id="delivery_challan_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Delivery Challan Date</th>
                  <th>{{__('delivery_challan.Challan Number')}}</th>
                  <th>{{__('delivery_challan.client_name')}}</th>
                  <th>{{__('delivery_challan.consignee_name')}}</th>
                  <th>{{__('delivery_challan.internal_order_nu')}}</th>
                  <th>{{__('delivery_challan.goods_dispatch_mode')}}</th>
                  <th>Created Date</th>
                  <th>{{__('delivery_challan.action')}}</th>
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
