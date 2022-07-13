@extends($layout)

@section('title', __('waybill.list'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>{{__('waybill.list')}}</a></li> 
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
          "ajax": "/waybill/api",
          "columns": [
              {"data":"waybill_for"},
              // {"data":"partyname"}, 
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var party=data.partyname;
                      if(party)
                            return party;
                      else if(data.dc_partyname)
                            return data.dc_partyname;
                      else
                      return data.tx_partyname;
                    }
                },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      // var pointer=data.gst_pointer;
                      // if(pointer==1)
                            return data.gst_number;
                      // else
                      //       return "NA";
                    }
                },
             
              {"data":"invoice_number", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":"challan_number", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
              {"data":"waybill_date"}, 
              {"data":"waybill_number"}, 
              {"data":"amount", "render": function(data,type,full,meta)
                  {
                    
                      return parseFloat(data).toFixed(2);   
                   
                  }, }, 
              {"data":"date"},
              {"data":"created_time"}, 
              /*{
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/dispatch/edit/"+data+"'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" //+ 
                    //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                    ;
                  }
              }*/
            ],
            "columnDefs": [
              
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
                    {{-- @section('titlebutton')
                      <a href="{{url('/createdispatch')}}"><button class="btn btn-primary">{{__('goods_dispatch.title')}}</button></a>
                      <a href="" ><button class="btn btn-primary "  >{{__('goods_dispatch.goods_dispatch_import_btn')}}</button></a>
                      <a href="" ><button class="btn btn-primary "  >{{__('goods_dispatch.goods_dispatch_export_btn')}}</button></a>
          
                      @endsection --}}
                    <table id="hsn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                       
                      <th>{{__('waybill.waybill')}}</th>
                      <th>{{__('waybill.party1')}}</th>
                      <th>{{__('waybill.party')}}</th>
                      <th>{{__('waybill.number')}}</th>
                      <th>{{__('waybill.number1')}}</th>
                      <th>{{__('waybill.waybill_date')}}</th>
                      <th>{{__('waybill.waybill_number')}}</th>
                      <th>{{__('waybill.amount')}}</th>
                      <th>{{__('waybill.challan/billdate')}}</th>
                      <th>Created Date</th>
                      {{-- <th>{{__('waybill.action')}}</th>
                      --}}
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