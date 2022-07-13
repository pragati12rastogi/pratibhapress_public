@extends($layout)

@section('user', Auth::user()->name)

@section('title','To be closed IOs')
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
var dataTable;
$(document).ready(function()  {
    dataTable = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
              "url": "/collection/report/closedios/api",
              "datatype": "json",
                  // "data": function (data) {
                  //     data.party = party;
                  //     data.ref = ref;
                  // }
              },
              "createdRow": function( row, data, dataIndex){
                if( data.status ==  'closed'){
                    $(row).addClass('bg-green-gradient');
                    
                }
            },
          "columns": [
           
            // { "data": "io_number" }, 
            { 
                "data":"io_number","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            { "data": "referencename" }, 
            { 
                "data":"invoice_number","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            
            // {"data":"item_name"},
            { 
                "data":"item_name","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            // {"data":"job_date"},
            { 
                "data":"job_date","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            {"data":"io_qty"},
            {"data":"tax_qty"},
            {"data":"total_tax_qty"},
            { "data": "total_amount" }, 
            { "data": "amt_received" },  
            { "data": "amt_left" },
           
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 11 },
              // { "orderable": false, "targets": 8 }
            ]
          
        });

 
     
    });
</script>
@endsection


@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div class="box">
        @section('titlebutton')
            <a href="/export/data/tobeclosedios" ><button class="btn btn-primary">Export To Be Closed IOs</button></a>
            @endsection
          <div class="box-body">
            <table id="taxinvoice_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  
                 
                  <th>Internal Order</th>
                  <th>Reference Name</th>
                  <th>{{__('taxinvoice.mytitle')}}</th>
                  <th>Item Name</th>
                  <th>IO Date</th>
                  <th>IO Qty</th>
                  <th>Tax Qty</th>
                  <th>Total Invoice Qty</th>
                  <th>Tax Invoice Amount</th>
                  <th>Payment Received</th>
                  <th>Payment Left</th>
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
