@extends($layout)

@if($list_of=='asn')
        @section('title', __('asn.asn_not_gen_list'))
@elseif($list_of=='grn')
    @section('title', __('asn.grn_not_gen_list'))
@endif
{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>{{strtoupper($list_of)}} Summary</a></li> 
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
    <script>
        var dataTable;

        // Data Tables
      
     function getasnlist(){
        dataTable = $('#asn_table').DataTable({
            "processing": true,
            "serverSide": true,
            "aaSorting":[],
            "responsive": true,
            "ajax": "/asngrnnotgenapi/{{$list_of}}",
            "columns": [
                { "data": "date" },  
                { "data": "partyname" }, 
                { "data": "invoice_number" }, 
               
                  {
                      "targets": [ -1 ],
                      "data":"control", "render": function(data,type,full,meta)
                      {
                         return "<a href='/{{$list_of}}/create/"+data+"'><button class='btn btn-primary btn-xs'> {{__('asn.create')}} </button></a> &nbsp;" //+ 
                        //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                        ;
                      }
                  }
                ],
                "columnDefs": [
                    { "orderable": true, "targets": 0 },
                    { "orderable": true, "targets": 1 },
                    { "orderable": true, "targets": 2 },
              { "orderable": false, "targets": 3 }
            ]
        });
     }

     function getgrnlist(){
        dataTable = $('#grn_table').DataTable({
            "processing": true,
            "serverSide": true,
            "aaSorting":[],
            "responsive": true,
            "ajax": "/asngrnnotgenapi/{{$list_of}}",
            "columns": [
              { "data": "date" },  
                { "data": "partyname" }, 
                { "data": "invoice_number" },  
                  {
                      "targets": [ -1 ],
                      "data":"control", "render": function(data,type,full,meta)
                      {
                         return "<a href='/{{$list_of}}/create/"+data+"'><button class='btn btn-primary btn-xs'> {{__('asn.create')}} </button></a> &nbsp;" //+ 
                        //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                        ;
                      }
                  }
                ],
                "columnDefs": [
                    { "orderable": true, "targets": 0 },
                    { "orderable": true, "targets": 1 },
                    { "orderable": true, "targets": 2 },
              { "orderable": false, "targets": 3 }
            ]
        });
     }
     

var list_of="{{$list_of}}";
if(list_of=='asn'){
    getasnlist();
}
else if(list_of=='grn'){
    getgrnlist();
}

    </script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        @if($list_of=='asn')
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
                        <th>Tax Invoice Date</th>
                        <th>{{__('asn.client')}}</th>
                        <th> {{__('asn.tax')}}</th> 
                        <th> {{__('asn.action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
                <!-- /.box-body -->
        </div>
        @endif

        @if($list_of=='grn')
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                      {{-- <a href="{{url('/hsn/create')}}"><button class="btn btn-primary">{{__('hsn.hsn_create_btn')}}</button></a>
                      <a href="/import/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_import_btn')}}</button></a>
                      <a href="/export/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_export_btn')}}</button></a> --}}
                      @endsection
                    <table id="grn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Tax Invoice Date</th>
                        <th>{{__('asn.client')}}</th>
                        <th> {{__('asn.tax')}}</th>              
                        <th> {{__('asn.action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
                <!-- /.box-body -->
        </div>
        @endif
        <!-- /.box -->
      </section>
@endsection