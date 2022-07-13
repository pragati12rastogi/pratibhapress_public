@extends($layout)

@section('title', __('gatepass.list2'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Returnable Gatepass Summary</a></li> 
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
      dataTable = $('#gatepass_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/gatepass/returnable/api",
          "columns": [
              { "data": "gatepass_number" }, 
              {
                  "targets": [ -1 ],
                  "data":"challan_type", "render": function(data,type,full,meta)
                  {
                    if(data=="internal_dc")
                      return "Internal Delivery Challan";
                    else if(data=="delivery_challan")
                      return "Delivery Challan";
                    else
                      return "";
                  }
              },
              { "data": "created" }, 
              {
                  "targets": [ -1 ],
                  "data" : function(data,type,full,meta)
                  {
                    if(data.challan_type=="internal_dc")
                      return data.idc_number;
                    else if(data.challan_type=="delivery_challan")
                      return data.challan_number;
                    else
                      return "";
                  }
              },
              { "data": "remark" },  
              { "data": "return_date" }, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/gatepass/returnable/update/"+data+"'><button class='btn btn-primary btn-xs'> {{__('gatepass.edit')}} </button></a> &nbsp;"+ 
                    '<a href="/rgatepass/template/'+data+'"><button class="btn btn-success btn-xs"> {{__("gatepass.print")}} </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 4 },
              { "orderable": false, "targets": 5 },
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
                      {{-- <a href="{{url('/hsn/create')}}"><button class="btn btn-primary">{{__('hsn.hsn_create_btn')}}</button></a>
                      <a href="/import/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_import_btn')}}</button></a>
                      <a href="/export/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_export_btn')}}</button></a> --}}

                      <a href="/export/data/returnable/gatepass"><button class="btn btn-sm btn-primary">Export Returnable Gatepass</button></a>
    
                      @endsection
                    <table id="gatepass_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>{{__('gatepass.num')}}</th>
                      <th>Challan Type</th>
                      <th>{{__('gatepass.time')}}</th>
                      <th>{{__('gatepass.challan')}}</th>
                      <th> {{__('gatepass.remark')}}</th>
                      <th>{{__('gatepass.return')}}</th>
                      <th>{{__('hsn.hsn_list_Action')}}</th>
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