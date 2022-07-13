@extends($layout)

@section('title', 'Design Cancelled Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Design Summary</a></li> 
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

    // Data Tables
    $(document).ready(function() {
      dataTable = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/design/report/cancel/api",
          "columns": [
            {"data":"date"},
            {"data":"do_number"},
              {"data":"referencename"},
              { 
                "data":"io_number","render": function(data, type, full, meta){
                  if(data)
                    
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            }, 
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var str = data.name; 
                      var idss=data.other_item_desc;
                      console.log(data);
                      if(idss)
                        return str+ " : " +idss;
                      else
                         return str;
                    ;
                    }
                },
              {"data":"no_pages"},
              {"data":"creative"},
              {"data":"creative_party"},
              {"data":"status"},
              {"data":"status_date"},
              //      {
              //     "targets": [ -1 ],
              //     "data":"id", "render": function(data,type,full,meta)
              //     {
              //       return "<a href='/design/order/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" //+ 
              //       //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
              //       ;
              //     }
              // }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 9 }
            
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
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                 
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                    <th>Created At</th>
                    <th>Design Order Number</th> 
                      <th>Client</th>
                      <th>Internal Order</th>
                      <th>Item</th>
                      <th>No. Pages</th>
                      <th>Creative Name</th>
                      <th>Creative Received From Client</th>
                      <th>Status</th>
                      <th>Status Date</th>
                      {{-- <th>Action</th> --}}
                     
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