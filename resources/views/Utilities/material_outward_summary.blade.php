@extends($layout)

@section('title', __('Utilities/material_inward.list1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>{{__('Utilities/material_inward.mytitle2')}}</a></li> 
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
  $('#plate_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/material/outwarding/list/api/",
          "columns": [
            {"data":"material_outward_number"},
            {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return data.date + "<br>" + data.time + "<br>";
                    }
                },
              {"data":"name"}, 
              {"data":"vehicle_no"}, 
              {"data":"gatepass_number"}, 
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var str = data.itemname; 
                      var idss=data.other_item_desc;
                      console.log(data);
                      if(idss)
                        return str+ " : " +idss;
                      else
                         return str;
                    ;
                    }
                },
              {"data":"qty"}, 
              {"data":"mode"}, 
              {"data":"dispatch_to"}, 
              {"data":"driver_name"},  
              {"data":"driver_number"}, 
              {"data":"remark"}, 
                  {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return  "<a href='/material/outwarding/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> Edit </button></a> &nbsp"
                    // '<a href="/purchase/indent/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              
               
              { "orderable": false, "targets": 12 },
          
          ]
          
        });
       

  </script>
@endsection

@section('main_section')
    <section class="content">
        <div id="app">
            @include('sections.flash-message')
            @yield('content')
        </div>
    <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
            <div class="box-body">
              @section('titlebutton')
              {{-- <a href="{{url('/hsn/create')}}"><button class="btn btn-primary">{{__('hsn.hsn_create_btn')}}</button></a>
              <a href="/import/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_import_btn')}}</button></a>
              <a href="/export/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_export_btn')}}</button></a> --}}
              <a href="/export/data/material/outward"><button class="btn btn-sm btn-primary">Export Material Outward</button></a>
              @endsection
            
                <div id="plate">
                    <table id="plate_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                            <th>{{__('Utilities/material_inward.num1')}}</th>
                                    <th>{{__('Utilities/material_inward.date')}} {{__('Utilities/material_inward.time')}}</th>
                                    <th>{{__('Utilities/material_inward.vehicle_type')}}</th>
                                    <th>{{__('Utilities/material_inward.vehicle_no')}}</th>
                                    <th>{{__('Utilities/material_inward.gate')}}</th>
                                    <th>{{__('Utilities/material_inward.material')}}</th>
                                    <th>{{__('Utilities/material_inward.qty')}}</th>
                                    <th>{{__('Utilities/material_inward.trans')}}</th>
                                    <th>{{__('Utilities/material_inward.dispatch')}}</th>
                                    <th>{{__('Utilities/material_inward.driver')}}</th>
                                    <th>{{__('Utilities/material_inward.driver_num')}}</th>
                                    <th>{{__('Utilities/material_inward.remark')}}</th>
                                    <th>{{__('waybill.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>    
                        </tbody>
                    
                    </table>
                </div>
         
                    <!-- /.box-body -->
            </div>
        </div>
        <!-- /.box -->
    </section>
@endsection