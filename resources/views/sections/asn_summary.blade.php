@extends($layout)

@section('title', __('asn.asn_list'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>ASN Summary</a></li> 
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
      dataTable = $('#asn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/asn/list/api",
          "columns": [
              { "data": "asn_date" }, 
              { "data": "partyname" }, 
              { "data": "name" },
              { "data": "invoice_number" },  
              { "data": "asn_number" }, 
              { "data": function(data, type, full, meta){
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
                      var ac=dd+'-'+dt.getMonth()+1+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                      return ac;
               } } 
            //   {
            //       "targets": [ -1 ],
            //       "data":"id", "render": function(data,type,full,meta)
            //       {
            //         // return "<a href='/hsn/list/edit/"+data+"'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" //+ 
            //         //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
            //         ;
            //       }
            //   }
            ],
            "columnDefs": [
              
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
    
                      @endsection
                    <table id="asn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Asn Date</th>
                      <th>{{__('asn.client')}}</th>
                      <th>{{__('asn.state')}}</th>
                      <th> {{__('asn.tax')}}</th>
                      <th>{{__('asn.asn')}}</th>
                      <th>Created Date</th>
                      {{-- <th>{{__('hsn.hsn_list_Action')}}</th> --}}
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