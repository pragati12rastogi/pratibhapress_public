@extends($layout)

@section('user', Auth::user()->name)

@section('title', __('tax_invoice.list'))
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
<style>
.nav1>li>a {
      position: relative;display: block;padding: 10px 34px;background-color:navajowhite;margin-left: 10px;
  }
</style>
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
  var dataTable;
  $(document).ready(function()  {
    var last_ele = null ;
    var last_tr = null ; 
    job('Hand');
    });

    function job(mode){
      if(mode=="Hand"){
        $('.hand').css("background-color","#87CEFA");
        $('.courier').removeAttr('style');
        $('.party').removeAttr('style');
      }
      else if(mode=="Courier"){
        $('.courier').css("background-color","#87CEFA");
        $('.hand').removeAttr('style');
        $('.party').removeAttr('style');
      }
      else{
        $('.party').css("background-color","#87CEFA");
        $('.hand').removeAttr('style');
        $('.courier').removeAttr('style');
      }
      dataTable = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/taxinvoicedispatch/api/"+mode,
          "columns": [
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
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
                    var cour=data.dispatch_mode;
                    if(data.docket_file){
                      var d="display:block";
                    }
                    else{
                      data.docket_file=1;
                      var d="display:none";
                    }
                    if(data.byhand_invoice){
                      var d1="display:block";
                    }
                    else{
                      data.byhand_invoice=1;
                      var d1="display:none";
                    }
                    if(data.party_invoice){
                      var d2="display:block";
                    }
                    else{
                      data.party_invoice=1;
                      var d2="display:none";
                    }
                    if(cour=="Courier"){
                      return "<a href='/taxinvoicedispatch/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href="/taxinvoicedispatch/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>'+
                    '<a onclick="file(\''+data.docket_file+'\')"><button class="btn btn-primary btn-xs" style="background-color:crimson; '+d+'" > See File </button></a>'+
                    '<a onclick="alert_status('+data.id+')"><button class="btn btn-foursquare btn-xs"> Status </button></a>&nbsp;'
                    ;
                    }
                    if(cour=="Hand"){
                      return "<a href='/taxinvoicedispatch/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href="/taxinvoicedispatch/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>'+
                    '<a onclick="file(\''+data.byhand_invoice+'\')"><button class="btn btn-primary btn-xs" style="background-color:crimson; '+d1+'" > See File </button></a>'
                    ;
                    }
                    if(cour=="Pick By Party"){
                      return "<a href='/taxinvoicedispatch/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href="/taxinvoicedispatch/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>'+
                    '<a onclick="file(\''+data.party_invoice+'\')"><button class="btn btn-primary btn-xs" style="background-color:crimson; '+d2+'" > See File </button></a>'
                    ;
                    }
                    else{
                      return "<a href='/taxinvoicedispatch/view/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href="/taxinvoicedispatch/update/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>';
                    }
                    
                  },
                  "orderable": false
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 7 },
              { "orderable": false, "targets": 8 }
            ]
          
        });
    }
    function file(file){
      $('#modal_div1').empty();
           var ls= '<div id="myModal1" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">File Uploaded</h4>'+
                  '</div>'+
                
                    '@csrf'+
                    '<div class="modal-body">'+
                    '<div class="modal-body">'+
                        '<center>';
                            if(file!=1){
                              var x=file.split('.');
                            }
                            else{
                              x="";
                            }
                            console.log(x);
                            
                            if(x=="")
                                ls=ls +'<img height="480" width="720" alt="No File Uploaded">  ';
                            else if (x[1]=="pdf")
                                ls=ls+'<embed src="/upload/taxdispatch/'+file+'" height="480" width="720" type="application/pdf">';
                            else
                            ls=ls+'<img src="/upload/taxdispatch/'+file+'" height="480" width="720" alt="No File Uploaded">';
                           
                        ls=ls+'</center>'+
                   ' </div>'+
                    
                    '</div>'+
                   
                   
                    '</div>'+
                '</div>'+
              '</div>'+
            '</div>';
            $('#modal_div1').append(ls);
       $(document).find('#myModal1').modal("show");
    }
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
                  '<form id="infos" method="GET" action="/tax/dispatch/status">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      '<input type="hidden" name="id" value="'+id+'">'+
                      '<div class="row">'+
                          '<div class="col-md-12">'+
                              '<label for="">Status :<sup>*</sup></label>'+
                              '<select name="status" id="status" class="select2 input-css" style="width:100%" required>'+
                                '<option value="">Select Status</option>'+
                                '<option value="Delivered">Delivered</option>'+
                                '<option value="In Transit">In Transit</option>'+
                                '<option value="Not delivered">Not delivered</option>'+
                                '<option value="Invoice returned">Invoice returned</option>'+
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


    $(".hand").click(function(){
      if(dataTable)
            dataTable.destroy();
      job('Hand');
    });
    $(".courier").click(function(){
      if(dataTable)
            dataTable.destroy();
      job('Courier');
    });
    $(".party").click(function(){
      if(dataTable)
            dataTable.destroy();
      job('Party');
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
              <a href="/import/data/taxdispatch"><button class="btn btn-sm btn-primary">{{__('tax_invoice.importtitle')}}</button></a>
              <a href="/export/data/taxdispatch"><button class="btn btn-sm btn-primary">{{__('tax_invoice.exporttitle')}}</button></a>
              @endsection
                @include('sections.flash-message')
                @yield('content')
            </div>
            <div id="modal_div"></div>
            <div id="modal_div1"></div>
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <div class="box-header with-border">
                        <ul class="nav nav1 nav-pills">
                          <li class="nav-item">
                            <a class="nav-link hand" style="background-color:#87CEFA">By Hand</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link courier" style="background-color:#87CEFA">By Courier</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link party" style="background-color:#87CEFA">Pick By Party</a>
                          </li>
                        </ul>
                      </div>
              </div>
          <div class="box-header with-border">
            <h3 class="box-title">{{__('taxinvoice.list')}}</h3>
          </div>
          <div class="box-body">
            <table id="taxinvoice_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>{{__('taxinvoice.mytitle')}}</th>
                  <th>{{__('taxinvoice.party')}}</th>
                  <th>{{__('taxinvoice.consignee')}}</th>
                  <th>Dispatch Mode</th>
                  <th>Status</th>
                  <th>{{__('tax_invoice.person_name')}}</th>
                  <th>{{__('tax_invoice.tax_invoice_dispatch_date')}}</th>
                  <th>{{__('tax_invoice.courier_company')}}</th>
                  <th>{{__('tax_invoice.docket_number')}}</th>
                  <th>Docket Date</th>
                  <th>Created Date</th>
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
