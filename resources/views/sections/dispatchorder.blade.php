@extends($layout)

@section('title', 'Dispatch Planning Schedule')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>'Dispatch Planning Schedule </a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
  .wickedpicker {
    z-index: 99999;
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

    // Data Tables
    $(document).ready(function() {
      dataTable = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/dispatch/list/api",
          "columns": [
            {"data":"io_number"},
            {"data":"referencename"},
            {"data":"itemss"},
            {"data":"io_qty"},
            {"data":"dispatch_qty"},
            {"data":"remaining_qty"},
            {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                    if(data.balance=="-"){
                      return data.remaining_qty;
                    }
                    else{
                      return (data.remaining_qty - data.balance);
                    }
                    }
              },
          
            {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                    if(data.balance=='-'){
                      var amount=data.remaining_qty;
                    }
                    else{
                      var amount=data.remaining_qty - data.balance;
                    }
                    
                    return '<a onclick="cancel_alert_dailog('+data.id+','+amount+')"><button class="btn btn-primary btn-xs"> Plan </button></a> &nbsp'+
                    '<a href="/dispatch/details/'+data.id+'" target="_blank"><button class="btn btn-success btn-xs"> View </button></a> &nbsp;';
                  }
              }
          
            ],
            "columnDefs": [
              { "orderable": false, "targets": 7 }
            
            ]
          
        });
    });
    function get_qty_by_date(e){
    console.log($('#select_d').val());
      
  }
    function cancel_alert_dailog(id,amount)
    {
     
            var ls='<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Dispatch Planning Schedule</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/dispatch/plan/create/'+id+'">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      '<input type="hidden" name="validate" value="'+(amount)+'">'+
                      '<input type="hidden" name="io" value="'+(id)+'">'+
                      '<br><label>Please Fill Below OPTIONS for Dispatch Planning Schedule</label></br><br>'+
                      
                      '<div class="row">'+
                        '<div class="col-md-6">'+
                            '<label for="">To Be Dispatch Qty : <sup>*</sup></label>'+
                           '<input type="number" step="any" name="qty" id="" min="1" value="'+(amount)+'" placeholder="Please enter less than '+(amount)+'" max="'+(amount)+'" class="pay_approve input-css" required>'+
                        '</div>'+
                        '<div class="col-md-6">'+
                            '<label for="">Dispatch Planned On : <sup>*</sup></label>'+
                           '<input type="text"  name="date" onchange="get_qty_by_date(this)" id="select_d" class="datepickers input-css" required>'+
                        '</div>'+
                      '</div>'+
                      '<div class="row">'+
                        '<div class="col-md-6">'+
                            '<label for="">Priority : <sup>*</sup></label>'+
                            '<input type="text" name="priority" id="" class="input-css old_dc" value="Priority " required placeholder="Enter Priority">'+
                        '</div>'+
                        '<div class="col-md-6">'+
                            '<label for="">Dispatch Time : <sup>*</sup></label>'+
                           '<input type="text"  name="time"  class="timepicker input-css" required>'+
                           '<div></div>'+
                           '</div>'+
                      '</div>'+
                     '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>'+
                      '<a target="_blank"><button type="submit" class="btn btn-success"   onclick="$(\'#infos\').validate();">Submit</button></a>'+
                    '</div>'+
                     '</form>'+
                '</div>'+
              '</div>'+
            '</div>';
            $('#modal_div').empty().append(ls);
          $(document).find('#myModal').modal("show"); 
          var currentDate = new Date();
          $('.timepicker').wickedpicker({
        setTime: null
  });
          $('.datepickers').datepicker({
      autoclose: true,
      format: 'd-m-yyyy',
      startDate:currentDate,
      });
      $(".old_dc").keydown(function(e) {
    var oldvalue=$(this).val();
    var field=this;
    var set="Priority ";
    setTimeout(function () {
        if(field.value.indexOf(set) !== 0) {
            $(field).val(oldvalue);
        } 
    }, 1);
});

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
            @section('titlebutton')
            <!-- <a href="/export/data/pendingtaxdispatch" ><button class="btn btn-primary">Export Tax Dispatch</button></a>  -->
            @endsection
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>IO No.</th>
                          <th>Reference</th>
                          <th>Item</th>
                          <th>IO Qty</th>
                          <th>Dispatch Qty</th>
                          <th>Balance Qty</th>
                          <th>Balance Qty To Be Planned</th>
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