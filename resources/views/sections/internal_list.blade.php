@extends($layout)

@section('user', Auth::user()->name)

@section('title', __('internal_order.list'))

@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css"> 
   
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script src="/js/bootbox.min.js"></script>
<script src="/js/bootbox.locales.min.js"></script>
<script>
  var x="{{Auth::user()->user_type}}";

  // alert(x);
  $(document).ready(function()  {
      dataTable = $('#internal_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "ajax": {
            "url": "/internalorder/list",
            "data": function ( d ) {
                d.status = "{{$status}}";
            }
        },
        "aaSorting": [],
        "responsive": true,
          "columns": [
              { "data": "io_number"}, 
              { "data": "date", "orderable": true }, 
              {"data": "job_date"},
              { "data": "reference_name" }, 
              { "data": "name" },
              { "data": "io_type" },
              {"data":"details", "render": function(data,type,full,meta)
                  {
                    var result = '';
                    var str = data;
                    var len =30;
                    while (str.length > 0) {
                      result += str.substring(0, len) + '<br>';
                      str = str.substring(len);
                    }
                    return result;
                  }
              },     
              { "data": "qty" }, 
              {
                  "targets": [ -1 ],
                  data: function(data,type,full,meta)
                  {
                    if(data.st=="pending"){
                          var st="Pending Request For Edit";
                          var cl="btn-success";
                          var clk='';
                          var dis="disabled";
                
                    }
                    else{
                      var st="Request For Edit";
                      var cl="btn-warning";
                      var clk='onclick="cancel_alert_dailog('+data.id+')"';
                      var dis="";
                    }
                    if("{{$status}}"=="open" && x=="superadmin")
                    {
                      return "<a href='/internalorder/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href="/internalorder/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit</button></a> &nbsp;' +
                      '<a href="/internal/stausupdate/close/'+data.id+'" target="_blank"><button onClick="return confirm('+"'Are you sure to close this Internal Order?'"+');" class="btn btn-xs"> Close</button></a>'+
                      "<a href='/template/"+data.id+"' target='_blank'><button style='margin-bottom: 5px;margin-left: 5px;' class='btn btn-danger btn-xs'> Print </button></a> &nbsp;" +
                      
                        '<a href="/addreqiredpermission/internalorder/update/'+data.id+'"><button style="margin-bottom: 5px;margin-left: 5px;" '+dis+' class="btn '+cl+' btn-xs"> '+st+' </button></a> &nbsp;';
                    }
                    else  if("{{$status}}"=="open" && x!="superadmin")
                    {
                      return "<a href='/internalorder/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    // '<a href="/internalorder/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit</button></a> &nbsp' 
                      '<a href="/internal/stausupdate/close/'+data.id+'" target="_blank"><button onClick="return confirm('+"'Are you sure to close this Internal Order?'"+');" class="btn btn-xs"> Close</button></a>'+
                      "<a href='/template/"+data.id+"' target='_blank'><button style='margin-bottom: 5px;margin-left: 5px;' class='btn btn-danger btn-xs'> Print </button></a> &nbsp;" +
                      
                        '<a href="/addreqiredpermission/internalorder/update/'+data.id+'"><button style="margin-bottom: 5px;margin-left: 5px;" '+dis+' class="btn '+cl+' btn-xs"> '+st+' </button></a> &nbsp;';
                     
                    }
                    else if("{{$status}}"=="closed" && x=="superadmin"){
                      return "<a href='/internalorder/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                      '<a href="/internal/stausupdate/open/'+data.id+'" target="_blank"><button onClick="return confirm('+"'Are you sure to Open this Internal Order?'"+');" class="btn btn-success btn-xs"> ReOpen</button></a>'+
                    "<a href='/template/"+data.id+"' target='_blank'><button style='margin-bottom: 5px;margin-left: 5px;' class='btn btn-danger btn-xs'> Print </button></a> &nbsp;" ;
                    }
                    
                    else{
                      return "<a href='/internalorder/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    "<a href='/template/"+data.id+"' target='_blank'><button style='margin-bottom: 5px;margin-left: 5px;' class='btn btn-danger btn-xs'> Print </button></a> &nbsp;" ;
                    }
                    
                  },
                  "orderable": false
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 1 }
            ]
          
        });
    });

    function cancel_alert_dailog(ele)
    {
      $('#ajax_loader_div').css('display','block');

      $.ajax({
        url: "/checkreqiredpermission/internalorder/"+ ele, 
        type: "GET",
        success: function(result) {
          console.log(result);
          var message = result.message;
          $('#ajax_loader_div').css('display','none');
          if(message == 'Generate Request')
          {
            window.location.href = "/addreqiredpermission/internalorder/update/"+ele;
            // bootbox.prompt({
            //   title: "Reason for Edit Internal Order",
            //   inputType: 'text',
            //   placeholder:"Reason to Edit",
            //   callback: function (result) {
            //     if(result== null)
            //     {
            //     }
            //     else
            //     {
            //       if(result.length>0)
            //         var patt = new RegExp('/','g');
            //         window.location.href = "/addreqiredpermission/internalorder/update/"+ele+"/"+result.replace(patt,' - - - ');
            //     }
            //   }
            // });
          }  
  
      }
      });
    }
</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class=""> {{__('order_to_collection.title')}}</i></a></li>
  <li><a href="#"><i class=""> {{__('internal_order.list')}}</i></a></li>
@endsection

@section('main_section')
    <section class="content">
        @section('titlebutton')

        <a href="/import/data/internalorder"><button class="btn btn-sm btn-primary">{{__('internal_order.importtitle')}}</button></a>
        <a href="/export/data/internalorder"><button class="btn btn-sm btn-primary">{{__('internal_order.exporttitle')}}</button></a>
        @endsection
        <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            </div>
        <!-- Default box -->
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">{{__('internal_order.IO')}} [{{ucfirst($status)}} List]
                <a href="{{url('/internal/list')}}/{{$status=='open'?'closed':'open'}}">{{$status=='open'?'Closed':'Open'}} List</a>
              </h3>
          </div>
          <div class="box-body">
            <table id="internal_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>{{__('internal_order.io_num')}}</th>
                  <th>Created Date</th>
                  <th>Job Date</th>
                  <th>{{__('internal_order.ref_name')}}</th>
                  <th>{{__('internal_order.item')}}</th>
                  <th>IO Type</th>
                  <th>Item Description</th>
                  <th>{{__('internal_order.item_qty')}}</th>
                  <th>{{__('internal_order.status')}}</th>
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
