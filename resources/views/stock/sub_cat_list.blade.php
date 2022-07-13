@extends($layout)

@section('title', "Sub Category List")

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>"Sub Category List</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
  .nav1>li>button {
    position: relative;
    display: block;
    padding: 10px 34px;
    background-color: white;
    margin-left: 10px;
}
table{
  width:100%;
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

    function getpaper(type){
      if(type==1){
      
        $('.chal').css("background-color","#87CEFA");
        $('.chal1').removeAttr('style');
        $('.chal2').removeAttr('style');
        $('.chal3').removeAttr('style');
      }
      if(type==2){
       
        $('.chal1').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal2').removeAttr('style');
        $('.chal3').removeAttr('style');
      }
      if(type==3){
      
        $('.chal2').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal1').removeAttr('style');
        $('.chal3').removeAttr('style');
      }
      if(type==4){
      
        $('.chal3').css("background-color","#87CEFA");
        $('.chal').removeAttr('style');
        $('.chal1').removeAttr('style');
        $('.chal2').removeAttr('style');
      }
      
        if(dataTable)
            dataTable.destroy();

        dataTable = $('#paper_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/stock/subcat/list/api",
              "datatype": "json",
              "data":{"type":type}
          },
          
          "columns": [
              {"data":"cat"},
              {"data":"by"},
              {"data":"created"}, 
            
              {
                  "targets": [ -1 ],
                  data: function(data,type,full,meta)
                  {
                    return "<a href='/stock/subcat/update/"+data.id+"'  target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a>";
                  }
              }
            ],
            "columnDefs": [
              
                { "orderable": false, "targets": 3 }
              
            
            ]
          
        });
    }
   


    $(document).ready(function() {
        getpaper(1);
    });


  </script>
@endsection

@section('main_section')
    <section class="content">
        <div id="app">
            @include('sections.flash-message')
            @yield('content')
            {{-- @section('titlebutton')
                <a href="{{url('/createdispatch')}}"><button class="btn btn-primary">{{__('goods_dispatch.title')}}</button></a>
                <a href="" ><button class="btn btn-primary "  >{{__('goods_dispatch.goods_dispatch_import_btn')}}</button></a>
                <a href="" ><button class="btn btn-primary "  >{{__('goods_dispatch.goods_dispatch_export_btn')}}</button></a>
            @endsection --}}
            
        </div>
    <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                @section('titlebutton')
              {{-- <a href="/import/data/stock"><button class="btn btn-sm btn-primary">Import Stock</button></a> --}}
              {{-- <a href="/export/data/taxinvoice"><button class="btn btn-sm btn-primary">{{__('taxinvoice.exporttitle')}}</button></a> --}}
              @endsection
            <div class="box-body">
                
                {{-- <div class="row">
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-primary" onclick="getDeliveryChallan()">{{__('waybill.delivery_challan')}}</button>
                    </div>
                    <div class="col-md-2">
                            <button class="btn btn-sm btn-primary" onclick="getTaxInvoice()">{{__('waybill.tax_invoice')}}</button>
                    </div>
                </div> --}}

                <ul class="nav nav1 nav-pills">
                  <li class="nav-item">
                    <button class="nav-link1 chal"  onclick="getpaper(1)">{{__('stock/stock.paper')}}</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal1" onclick="getpaper(2)">{{__('stock/stock.ink')}}</button>
                  </li>
                  <li class="nav-item">
                    <button class="nav-link1 chal2" onclick="getpaper(3)">{{__('stock/stock.plate')}}</button>
                  </li>
                  <li class="nav-item">
                        <button class="nav-link1 chal3" onclick="getpaper(4)">{{__('stock/stock.misc')}}</button>
                      </li>
                </ul><br><br>
                <div id="paper" >
                    <table id="paper_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                 <th>Sub Category</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                               
                                <th>{{__('waybill.action')}}</th>
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