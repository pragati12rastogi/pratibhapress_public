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
  
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;
    var hr;
  function fetchingDataForTable(io_id){
    var last_ele = null ;
    var last_tr = null ;
    
      hr="{{$hr}}";
     hr=hr.split(",");
      dataTable = $('#asn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/binding/bills/report/api/"+io_id,
          "createdRow": function( row, data, dataIndex){
                if( data.status ==  'Closed'){
                    $(row).addClass('bg-green-gradient');
                    
                }
            },
          "columns": [
              { "data": "io_number" },
              { "data": "item_name" },
              { "data": "binder_name" }, 
              
              { "data": "binding_no" },
              { "data": "process" },
             
              { "data": "binding_qty" },
              { "data": "ready_qty" },  
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
              { "data": "status" }, 
              {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                    return "<button id="+data.id+" class='job_det btn btn-warning btn-xs'>View</button> &nbsp;";
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 10 }
            ]
           
          
        });
  }

  $(document).ready(function(ex){
    var last_ele = null ;
    var last_tr = null ;
$("#asn_table").hide();
$('#select_do').on('select2:select', function (e) {
    var select_data = e.params.data;
    if(select_data.id == -1)
      return;
    if($("#asn_table").is(":hidden"))
      $("#asn_table").show();
    if(dataTable)
      dataTable.destroy();
      fetchingDataForTable(select_data.id);
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
});
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
  </script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        <div class="box">
        
            <div id="modal_div"></div>
                <!-- /.box-header -->
                <div class="box-body">
                <div class="row">
                      <div class="col-md-6">
                        <select id="select_do" class="input-css select2"  style="width: 100%;" >
                                <option value="-1">Select Internal Order</option>
                                @foreach ($io as $key)
                                  <option value="{{$key->id}}">{{$key->io_number}}</option>
                                @endforeach
                        </select>
                    </div>
                      </div><br><br>
                    <table id="asn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                     <th>IO Number</th>
                     <th>Item Name</th>
                     <th>Binder</th>
                     <th>Binding Bill No.</th>
                     <th>Process Name</th>
                     <th>Binding Qty</th>
                     <th>Binding Ready Qty</th>
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