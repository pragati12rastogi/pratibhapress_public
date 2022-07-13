@extends($layout)

@section('title', __('goods_dispatch.list'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Disaptch Profile Summary</a></li> 
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
      dataTable = $('#hsn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/goodsdispatch/list/api",
          "columns": [
              {"data":"name"},
              {"data":"courier_name", "render": function(data,type,full,meta)
                  {
                    if(data)
                        return data;
                    else return "-";    
                  }
                }, 
           
              {"data":"contact", "render": function(data,type,full,meta)
                  {
                    if(data)
                        return data;
                    else return "-";     
                  }
                },  
              {"data":"gst", "render": function(data,type,full,meta)
                  {
                    if(data)
                        return data;
                    else return "-";    
                  }
                }, 
                {"data":"address", "render": function(data,type,full,meta)
                  {
                    if(data)
                        return data;
                    else return "-";    
                    
                  }
                }, 
                   {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/dispatch/edit/"+data+"'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" //+ 
                    //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 5 },
              { "orderable": true, "targets": 1 },
            
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
                      <a href="{{url('/createdispatch')}}"><button class="btn btn-primary">{{__('goods_dispatch.title')}}</button></a>
                      <a href="/import/data/goodsinvoicedispatch" ><button class="btn btn-primary "  >{{__('goods_dispatch.goods_dispatch_import_btn')}}</button></a>
                      <a href="/export/data/goodsinvoicedispatch" ><button class="btn btn-primary "  >{{__('goods_dispatch.goods_dispatch_export_btn')}}</button></a>
          
                      @endsection
                    <table id="hsn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                       
                      <th>{{__('goods_dispatch.mode')}}</th>
                      <th>{{__('goods_dispatch.carrier')}}</th>
                      <th>{{__('goods_dispatch.number')}}</th>
                      <th>{{__('goods_dispatch.gst')}}</th>
                      <th>{{__('goods_dispatch.address')}}</th>
                      <th>{{__('goods_dispatch.status')}}</th>
                     
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