@extends($layout)

@section('title', __('paymentTerm.mytitle'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

     <li><a href="#"><i class=""></i>{{__('paymentTerm.payment_term_list_Payment_term')}}</a></li> 
    
@endsection
@section('css')
<style>
   .content{
    padding: 32px;
  }
  
@media (max-width: 991px){
    .content-header>h1 {
      display: inline-block;
    }
  }
  @media (max-width: 768px)  
  {
    
    .content-header>h1 {
      display: inline-block;
      padding: 8px;
    }
  }
  @media (max-width: 425px)  
  {
    .btnshift1{
    position: absolute;
    margin-left: 139px;
    top: 113px;
    padding: 5px;
    display: inline-block;
}
    .content-header>h1 {
      display: inline-block;
      padding: 8px;
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
      dataTable = $('#paymentterm_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/paymentterm/data",
          "columns": [
              { "data": "id" }, 
              { "data": "value" }, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/paymentterm/update/"+data+"'><button class='btn btn-primary btn-xs'> {{__('paymentTerm.payment_term_list_Edit')}} </button></a> &nbsp;" ;//+ 
                    /*'<a href="/paymentterm/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("paymentTerm.payment_term_list_Delete")}} </button></a>' ;
                  */                }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 2 }
            ]
          
        });
    });


  </script>
@endsection
@section('main_section')
    <section class="content">
      <div id="app">
        @include('sections.flash-message')
        @yield('content')

        <!-- Default box -->
        @section('titlebutton')
          <a href="/paymentterm/create"><button class="btn btn-primary">{{__('paymentTerm.payment_term_create_btn')}}</button></a>
          <a href="/import/data/paymentterm" ><button class="btn btn-primary "  >{{__('paymentTerm.payment_term_import_btn')}}</button></a>
          <a href="/export/data/paymentterm" ><button class="btn btn-primary "  >{{__('paymentTerm.payment_term_export_btn')}}</button></a>
    
        @endsection
        <div class="box">
          <!-- /.box-header -->
                <div class="box-body">
                  <table id="paymentterm_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>{{__("paymentTerm.payment_term_list_Id")}}</th>
                      <th>{{__("paymentTerm.payment_term_list_Payemnt_Term")}}</th>
                      <th>{{__("paymentTerm.payment_term_list_Action")}}</th>                      
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
