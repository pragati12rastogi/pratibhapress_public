@extends($layout)

@section('user', Auth::user()->name)

@section('title','Payment Recieved Summary')
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">
<style type="text/css">
  input[type=number]::-webkit-inner-spin-button, 
  input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  margin: 0; 
}
</style>    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
var dataTable;
  function tableGen(){
    dataTable = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/collection/paymentrecievedbytax/api/{{$tax_id}}",
          "columns": [
            { "data": "invoice_number" }, 
            { "data": "pr_date" }, 
            { "data": "pr_amount" }, 
            { "data": "value" }, 
            {"data":"advice_upload","render": function(data, type, full, meta){
                  if(data)
                    return '<a href="/upload/Recievable/'+data+'">Check Advice</a>&nbsp;';
                  else
                    return "";
                } },
            {"data":"deduction"},
            {"data":"reason_for_deduction"}
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 11 },
              // { "orderable": false, "targets": 8 }
            ]
          
        });
  }
  $(document).ready(function()  {
      tableGen();
    });
   
    
    
</script>
@endsection
@section('breadcrumb')
  <li><a href="/collection/paymentrecieved/summary"><i class=""> </i></a></li>
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
            
          </div>
          <div class="box-body">
            <table id="taxinvoice_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>{{__('taxinvoice.mytitle')}}</th>
                  <th>Payment received on date</th>
                  <th>Amount received</th>
                  <th>Mode of payment</th>
                  <th>Payment advice upload</th>
                  <th>Deductions</th>
                  <th>Reason for deduction</th>
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
