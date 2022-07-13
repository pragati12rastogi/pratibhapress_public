@extends($layout)

@section('title', 'Pending Client P.O. Entry')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Pending Client P.O. Entry</a></li> 
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
          "ajax": "/report/pending/client/po/list/api",
          "columns": [
            {"data":"io_number"},
            {"data":"ioType"},
            {"data":function(data, type, full, meta){
                var dt=data.created_time;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+ 1;
                      var yyyy=dt.getFullYear();
                      var hh=dt.getHours();
                      var mi=dt.getMinutes();
                      var ss=dt.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                      var ac=dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                      return ac;
            }
           },
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"qty"}

            // {
            //   "targets": [ -1 ],
            //   "data":"id", "render": function(data,type,full,meta)
            //   {
            //     return "<a href='/clientpo' target='_blank'><button class='btn btn-primary btn-xs'> {{__('Create')}} </button></a> &nbsp;" //+ 
            //     //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
            //     ;
            //   }
            //   }
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 1 }
            
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
                    @section('titlebutton')
                    <a href="/export/data/pendingclientpo" ><button class="btn btn-primary "  >Export Pending PO</button></a>
                    @endsection
                    <table id="table" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Internal Order Number</th>
                          <th>Internal Order Type</th>
                          <th>Internal Order Date</th>
                          <th>Reference Name</th>
                          <th>Item Name</th>
                          <th>Item Quantity</th>
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