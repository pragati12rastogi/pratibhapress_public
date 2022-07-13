@extends($layout)

@section('title', 'Design Work Allotted')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Design Work Allotted Summary</a></li> 
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
.bg-yellow{
  background-color: #f0e384 !important;
  color: black !important;
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
          "ajax": "/design/work/list/api",
          "createdRow": function( row, data, dataIndex){
                if( data.value == 'Design Working Over'){
                    $(row).addClass('bg-yellow');
                    
                }
            },
          "columns": [
            {"data":"work_alloted_number"},
            {"data":"referencename"},
            {"data":"do_number"},
              {"data":"emp"},
              {"data":"work_date"},
              
              {"data":"do_pages"},
              {"data":"dw_pages"},
              {"data":"description"},
              {"data":"created"},
             
              {"data":"value"},
                   {
                  "targets": [ -1 ],
                  data : function(data,type,full,meta)
                  {
                    if(data.value=="Design Working Over"){
                      return "<a href='/design/work/update/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;";
                    }
                    else if(data.do_status=="Closed"){
                      return "";
                    }
                    else{
                      return "<a href='/design/work/update/"+data.id+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" //+ 
                    +'<a href="/design/work/status/'+data.id+'"><button class="btn btn-danger btn-xs">Status </button></a>' 
                    ;
                    }
                   
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 10 }
            
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
                        <th>Work Allot No.</th>
                    <th>Client Name</th>
                    <th>Design Order No.</th> 
                      <th>Work Allot To</th>
                      <th>Work Allot Date</th>
                      <th>DO No. Pages</th>
                      <th>WA No. Pages</th>
                      <th>Desc</th>
                      <th>Created At</th>
                      <th>Status</th>
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