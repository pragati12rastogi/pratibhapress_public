@extends($layout)

@section('title', __('Utilities/material_inward.list'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>{{__('Utilities/material_inward.mytitle')}}</a></li> 
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
    var dataTable;
    var last_ele = null ;
    var last_tr = null ;
   
    function getplate(){
        $('#plate').show();
        $('#inks').hide();
        $('#misc').hide();
        $('#paper').hide();
        $('.chal2').css("background-color","#87CEFA");
        $('.chal3').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#plate_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/material/inwarding/list/api/material",
          "columns": [
            {"data":"material_inward_number"},
            {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return data.date + "<br>" + data.time + "<br>";
                    }
                },
              {"data":"name"}, 
              {"data":"vehicle_no"}, 
              {"data":"company"}, 
              {"data":"item_name"}, 
              {
                  "data" : function(data,type,full,meta)
                  {
                    return data.qty+"  "+data.dimension;
                    
                  }, }, 
              {
                  "data" : function(data,type,full,meta)
                  {
                    if(data)
                    var idsss=data.id;
                    var doc=data.doc_for;
                      return doc.replace(/,/g,'<br>') 
                      + '<br><button id="'+idsss  +'" class="btn btn-warning btn-xs mode">Details</button>&nbsp;';
                    
                  }, },
                  {"data":"driver_name"},  
              {"data":"driver_number"}, 
              {"data":"remark"}, 
                  {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return  "<a href='/material/inwarding/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> Edit </button></a> &nbsp"
                    // '<a href="/purchase/indent/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              
               
              { "orderable": false, "targets": 10 },
          
          ]
          
        });
        $('#plate_table tbody').on('click', 'button.mode', function () {
        var tr = $(this).parents('tr');
        var row = dataTable.row( tr );
        var data=$(this).attr("id");
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
          if(last_ele)
          {
            //  last_ele.child.hide();     
          }
          $(this).parents('li').children('div').remove();
                
          $(this).parents('li').append('<center><div class="card" ><h5> Processing...</h5></div></center>');
              
          row.child('<center><div class="card" ><h5> Processing...</h5></div></center>').show();
          getdata1(data,row,this)

          last_ele=row;
          last_tr=tr;
          tr.addClass('shown');
        }
    } );
    function getdata1(data,ele,button)  {  
      var out;
      $('#ajax_loader_div').css('display','block');

      $.ajax({
               type:'get',
               url:"/summary/doc/"+data,
               timeout:600000,
                   
               success:function(data) {
                $(button).parents('li').children('div').remove();
                $(button).parents('li').children('center').remove();
                
                $(button).parents('li').append(data);
                  ele.child(data).show();
                  $('#ajax_loader_div').css('display','none');

                }

            });

            return out;
    }
    }
    function getmisc(){
        $('#plate').hide();
        $('#misc').show();
        $('.chal3').css("background-color","#87CEFA");
        $('.chal2').removeAttr('style');
        if(dataTable)
            dataTable.destroy();
            dataTable = $('#misc_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/material/inwarding/list/api/returnable",
          "columns": [
            {"data":"material_inward_number"},
            {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return data.date + "<br>" + data.time + "<br>";
                    }
            },
              {"data":"name"}, 
              {"data":"vehicle_no"}, 
              {"data":"item_name"}, 
              {"data":"qty"}, 
                  {"data":"driver_name"},  
              {"data":"driver_number"}, 
              {"data":"remark"}, 
                  {
                  "targets": [ -1 ],
                  "data": function(data,type,full,meta)
                  {
                    var img=data.bill_file;
                      var idss=data.id;
                     
                      
                    return '<a href="/material/inwarding/update/'+idss+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit</button></a> &nbsp' + 
                    '<a href="/file-upload/download1/'+img+'" target="_blank"><button class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-download"></i></button></a> &nbsp' ;
                  }
              }
            ],
            "columnDefs": [
              
               
                { "orderable": false, "targets": 9 },
            
            ]
          
        });
    
    }


    $(document).ready(function() {
        getplate();
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
              <a href="/export/data/material/inward"><button class="btn btn-sm btn-primary">Export Material Inward</button></a>
              @endsection
                <ul class="nav nav1 nav-pills">
                  
                  <li class="nav-item">
                    <button class="nav-link1 chal2" onclick="getplate()">Material In-Warding</button>
                  </li>
                  <li class="nav-item">
                        <button class="nav-link1 chal3" onclick="getmisc()">{{__('Utilities/material_inward.mytitle1')}}</button>
                      </li>
                </ul><br><br>
             
                <div id="plate">
                    <table id="plate_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                            <th>{{__('Utilities/material_inward.num1')}}</th>
                                    <th>{{__('Utilities/material_inward.date')}} {{__('Utilities/material_inward.time')}}</th>
                                    <th>{{__('Utilities/material_inward.vehicle_type')}}</th>
                                    <th>{{__('Utilities/material_inward.vehicle_no')}}</th>
                                    <th>{{__('Utilities/material_inward.company')}}</th>
                                    <th>{{__('Utilities/material_inward.material')}}</th>
                                    <th>{{__('Utilities/material_inward.qty')}}</th>
                                    <th>{{__('Utilities/material_inward.doc')}}</th>
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
                <div id="misc">
                        <table id="misc_table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                <th>{{__('Utilities/material_inward.num2')}}</th>
                                        <th>{{__('Utilities/material_inward.date')}} {{__('Utilities/material_inward.time1')}}</th>
                                        <th>{{__('Utilities/material_inward.vehicle_type')}}</th>
                                        <th>{{__('Utilities/material_inward.vehicle_no')}}</th>
                                        <th>{{__('Utilities/material_inward.material')}}</th>
                                        <th>{{__('Utilities/material_inward.qty')}}</th>
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