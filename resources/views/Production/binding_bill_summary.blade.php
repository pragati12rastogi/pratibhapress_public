@extends($layout)

@section('title', 'Binding Bill Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Binding Bill Summary</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
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
  .nav1>li>a {
      position: relative;display: block;padding: 10px 34px;background-color:navajowhite;margin-left: 10px;
  }
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
  
   var dataTable;
    // Data Tables
    $(document).ready(function() {
    var hr;
    job('Opened');
    });
function job(status){
  var last_ele = null ;
    var last_tr = null ; 
  if(status=="Opened"){
    $('.open').css("background-color","#87CEFA");
    $('.closed').removeAttr('style');
  }
  else{
    $('.closed').css("background-color","#87CEFA");
    $('.open').removeAttr('style');
  }
  hr="{{$hr}}";
     hr=hr.split(",");
      dataTable = $('#asn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/binding/bills/list/api/"+status,
          "createdRow": function( row, data, dataIndex){
                if( data.bind_status ==  'Closed'){
                    $(row).addClass('bg-green-gradient');
                    
                }
            },
          "columns": [
              { "data": "io_number" }, 
              { "data": "binder_name" }, 
              { "data": "binding_no" },
              { "data": "binding_qty" },
              { "data": "ready_qty" },  
              { "data": "bill_date" },  
              { "data": "amount" }, 
            
              {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                      var amt=data.amount;
                      var amount=amount+amt;
                    return data.amount-data.amount_approved;
                  }
              } ,
              { "data": "bind_status" }, 
              {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                    var amt=data.amount;
                    var amount=amount+amt;
                    var bl=data.amount-data.amount_approved;
                   if(data.bind_status=="Closed" || bl>=amt ){
                     var st="style=display:none";
                   }
                   else{
                     st="";
                   }
                    // return "111";
                    var x="no";
                    var i=0;
                
                      var id=data.id;
                     if(id!=null){
                      if(data.amount_approved==null){
                        var amt=0;
                      }
                      else{
                        var amt=data.amount_approved;
                      }
                      var auth="{{Auth::id()}}";
                      for (var index = 0; index < hr.length; index++) {
                        var ind=hr[index];
                          if(auth==ind){
                            i=hr[index];
                            var level=1;
                            x= "yes" ;
                          }
                      }
                      
                      if(x=="yes"){
                        var text="1";
                        if(data.bind_status ==  'Closed'){
                        
                          return "<button id="+data.id+" class='job_det btn btn-warning btn-xs'>Details</button> &nbsp;";
                        }
                        else{
                          return '<a onclick="cancel_alert_dailog('+id+','+data.amount+','+amt+','+i+','+text+')"><button class="btn btn-primary btn-xs"> Approve </button></a> &nbsp;'
                        + "<button id="+data.id+" class='job_det btn btn-warning btn-xs'>Details</button> &nbsp;"
                        + "<a href='/binding/bills/update/"+data.id+"'><button class='btn btn-success btn-xs'>Edit</button></a> &nbsp;"
                        + "<a href='/binding/bills/status/"+data.id+"' "+st+"><button class='btn btn-danger btn-xs'>Close</button></a> &nbsp;";
                     
                        }
                         }
                      else{
                        return "You Are Not Eligible To Approve"+
                        "<button id="+data.id+" class='job_det btn btn-warning btn-xs'>Details</button> &nbsp;"
                        + "<a href='/binding/bills/update/"+data.id+"'><button class='btn btn-success btn-xs'>Edit</button></a> &nbsp;"
                        ;
                      }
                     }
                     else{
                       return '';
                     }
                     
                    
                    
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 8 },
              { "orderable": false, "targets": 9 }
            ]
           
          
        });
        $('#asn_table tbody').on('click', 'button.job_det', function () {
        var tr = $(this).parents('tr');
        var row = dataTable.row( tr );
        var data=$(this).attr("id");
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
          if(last_ele)
          {
            //  last_ele.child.hide();     
          }
          $(this).parents('li').children('div').remove();
                
          $(this).parents('li').append('<center><div class="card" ><h5> Processing...</h5></div></center>');
              
          row.child('<center><div class="card" ><h5> Processing...</h5></div></center>').show();
          getdata1(data,row,this)

          last_ele=row;
          last_tr=tr;
          tr.addClass('shown');
        }
    } );
}
function jobs(status){
  var last_ele = null ;
    var last_tr = null ; 
  if(status=="Opened"){
    $('.open').css("background-color","#87CEFA");
    $('.closed').removeAttr('style');
  }
  else{
    $('.closed').css("background-color","#87CEFA");
    $('.open').removeAttr('style');
  }
  hr="{{$hr}}";
     hr=hr.split(",");
      dataTable = $('#asn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/binding/bills/list/api/"+status,
          "createdRow": function( row, data, dataIndex){
                if( data.bind_status ==  'Closed'){
                    $(row).addClass('bg-green-gradient');
                    
                }
            },
          "columns": [
              { "data": "io_number" }, 
              { "data": "binder_name" }, 
              { "data": "binding_no" },
              { "data": "binding_qty" },
              { "data": "ready_qty" },  
              { "data": "bill_date" },  
              { "data": "amount" }, 
            
              {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                      var amt=data.amount;
                      var amount=amount+amt;
                    return data.amount-data.amount_approved;
                  }
              } ,
              { "data": "bind_status" }, 
              {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                    var amt=data.amount;
                    var amount=amount+amt;
                    var bl=data.amount-data.amount_approved;
                   if(data.bind_status=="Closed" || bl>=amt ){
                     var st="style=display:none";
                   }
                   else{
                     st="";
                   }
                    // return "111";
                    var x="no";
                    var i=0;
                
                      var id=data.id;
                     if(id!=null){
                      if(data.amount_approved==null){
                        var amt=0;
                      }
                      else{
                        var amt=data.amount_approved;
                      }
                      var auth="{{Auth::id()}}";
                      for (var index = 0; index < hr.length; index++) {
                        var ind=hr[index];
                          if(auth==ind){
                            i=hr[index];
                            var level=1;
                            x= "yes" ;
                          }
                      }
                      
                      if(x=="yes"){
                        var text="1";
                        if(data.bind_status ==  'Closed'){
                        
                          return "<button id="+data.id+" class='job_det btn btn-warning btn-xs'>Details</button> &nbsp;";
                        }
                        else{
                          return '<a onclick="cancel_alert_dailog('+id+','+data.amount+','+amt+','+i+','+text+')"><button class="btn btn-primary btn-xs"> Approve </button></a> &nbsp;'
                        + "<button id="+data.id+" class='job_dets btn btn-warning btn-xs'>Details</button> &nbsp;"
                        + "<a href='/binding/bills/update/"+data.id+"'><button class='btn btn-success btn-xs'>Edit</button></a> &nbsp;"
                        + "<a href='/binding/bills/status/"+data.id+"' "+st+"><button class='btn btn-danger btn-xs'>Close</button></a> &nbsp;";
                     
                        }
                         }
                      else{
                        return "You Are Not Eligible To Approve"+
                        "<button id="+data.id+" class='job_det btn btn-warning btn-xs'>Details</button> &nbsp;"
                        + "<a href='/binding/bills/update/"+data.id+"'><button class='btn btn-success btn-xs'>Edit</button></a> &nbsp;"
                        ;
                      }
                     }
                     else{
                       return '';
                     }
                     
                    
                    
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 8 },
              { "orderable": false, "targets": 9 }
            ]
           
          
        });
        $('#asn_table tbody').on('click', 'button.job_dets', function () {
        var tr = $(this).parents('tr');
        var row = dataTable.row( tr );
        var data=$(this).attr("id");
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
          if(last_ele)
          {
            //  last_ele.child.hide();     
          }
          $(this).parents('li').children('div').remove();
                
          $(this).parents('li').append('<center><div class="card" ><h5> Processing...</h5></div></center>');
              
          row.child('<center><div class="card" ><h5> Processing...</h5></div></center>').show();
          getdata1(data,row,this)

          last_ele=row;
          last_tr=tr;
          tr.addClass('shown');
        }
    } );
}

 //------------------   
    function cancel_alert_dailog(id,amount,amount_app,app_id,text,bill='')
    {
     
            var ls='<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Approval</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/binding/bills/approve/'+id+'">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      '<input type="hidden" name="app_id" value="'+app_id+'">'+
                      '<input type="hidden" name="text" value="'+text+'">'+
                      '<input type="hidden" name="approve" value="'+bill+'">'+
                      '<input type="hidden" name="validate" value="'+(amount-amount_app)+'">'+
                      '<br><label>Please Fill Below OPTIONS for Payment Approval</label></br><br>'+
                      
                      '<div class="row">'+
                        '<div class="col-md-6">'+
                            '<label for="">Payment Approve : <sup>*</sup></label>';
                          
  ls=ls+ '<input type="number" step="any" name="pay_approve" id="" min="1" value="'+(amount-amount_app)+'" placeholder="Please enter less than '+(amount-amount_app)+'" max="'+(amount-amount_app)+'" class="pay_approve input-css" required>';

                        
                       ls=ls+'</div>'+
                        '<div class="col-md-6">'+
                            '<label for="">Payment Already Approved By Level 1 : <sup>*</sup></label>'+
                            '<input type="number" step="any" name="pay_appove_already" id="" value="'+amount_app+'" disabled class="pay_appove_already input-css" required>'+
                        '</div>'+
                      '</div>'+
                          '</div>';
                          if(amount-amount_app==0){
                              ls=ls+'<center><p style="color:red">No Amount Left For Approval.Now Approval Cannot Be done.</p></center><br><br>'
                            }
                            else{
                    ls=ls+ '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>'+
                      '<a target="_blank"><button type="submit" class="btn btn-success"  onclick="$(\'#infos\').validate();">Submit</button></a>'+
                    '</div>';
                            }
                   ls=ls+ '</form>'+
                '</div>'+
              '</div>'+
            '</div>';
            $('#modal_div').empty().append(ls);
          $(document).find('#myModal').modal("show"); 
  }
  function getdata1(data,ele,button)  {  
      var out;
      $('#ajax_loader_div').css('display','block');

      $.ajax({
               type:'get',
               url:"/binding/bills/approve/data/"+data,
               timeout:600000,
                   
               success:function(data) {
                $(button).parents('li').children('div').remove();
                $(button).parents('li').children('center').remove();
                
                $(button).parents('li').append(data);
                  ele.child(data).show();
                  $('#ajax_loader_div').css('display','none');

                }

            });

            return out;
    }
    $(".open").click(function(){
      if(dataTable)
            dataTable.destroy();
            var last_ele = null ;
            var last_tr = null ; 
      job('Opened');
    });
    $(".closed").click(function(){
      if(dataTable)
            dataTable.destroy();
    
    var last_ele = null ;
    var last_tr = null ; 
      jobs('Closed');
    });
  </script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <div class="box-header with-border">
                        <ul class="nav nav1 nav-pills">
                          <li class="nav-item">
                            <a class="nav-link open" style="background-color:#87CEFA">Open Binding bills</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link closed">Closed Binding bills</a>
                          </li>
                         
                        </ul>
                      </div>
              </div>
            <div id="modal_div"></div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- @section('titlebutton')
                      {{-- <a href="{{url('/hsn/create')}}"><button class="btn btn-primary">{{__('hsn.hsn_create_btn')}}</button></a>
                      <a href="/import/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_import_btn')}}</button></a>
                      <a href="/export/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_export_btn')}}</button></a> --}}
    
                      @endsection -->
                    <table id="asn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                     <th>IO Number</th>
                     <th>Binder</th>
                     <th>Binding Bill No.</th>
                     <th>Binding Qty</th>
                     <th>Binding Ready Qty</th>
                     <th>Bill Date</th>
                     <th>Amount</th>
                     <th>Balance Amt</th>
                     <th>Status</th>
                      <th>{{__('hsn.hsn_list_Action')}}</th>
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