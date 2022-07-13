@extends($layout)

@section('user', Auth::user()->name)

@section('title', __('taxinvoice.not_dispatch_list'))
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
  $(document).ready(function()  {
      dataTable = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/taxinvoice/notdispatch/api",
          "columns": [
            { "data": "date" }, 
            { "data":"created"}, 
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
           
            { "data": "item_name" },
            { "data": "qty" }, 
            { 
                "data":"challan_number","render": function(data, type, full, meta){
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
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/taxinvoice/view/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href="/taxinvoice/update/'+data+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a>'
                    // "<a href='/templateTax/"+data+"' target='_blank'><button style='margin-bottom: 5px;margin-left: 5px;' class='btn btn-danger btn-xs'> Print </button></a> &nbsp;" 
                    ;
                  },
                  "orderable": false
              }
            ],
            "columnDefs": [
             
            ]
          
        });
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
              <a href="/export/data/taxnotdispatch"><button class="btn btn-sm btn-primary">{{__('taxinvoice.exporttitle')}}</button></a>
              {{-- <a href="/export/data/taxinvoice"><button class="btn btn-sm btn-primary">{{__('taxinvoice.exporttitle')}}</button></a> --}}
              @endsection
                @include('sections.flash-message')
                @yield('content')
            </div>
        <!-- Default box -->
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">{{__('taxinvoice.list')}}</h3>
          </div>
          <div class="box-body">
            <table id="taxinvoice_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Tax Invoice Date</th>
                  <th>Created Date</th>
                  
                  <th>{{__('taxinvoice.mytitle')}}</th>
                  <th>{{__('taxinvoice.party')}}</th>
                  <th>{{__('taxinvoice.consignee')}}</th>
                  <th>Item Name</th>
                  <th>Qty</th>
                  <th>{{__('taxinvoice.delivery')}}</th>
                  <th>{{__('taxinvoice.terms')}}</th>
                  <th>{{__('taxinvoice.amount')}}</th>
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
