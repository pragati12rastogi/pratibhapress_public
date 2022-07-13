@extends($layout)

@section('title', __('purchase/grn.title1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Purchase GRN Summary</a></li> 
@endsection
@section('css')

<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
        var last_ele = null ;
    var last_tr = null ;
    
     $('.loader').hide()
      dataTable = $('#asn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/purchase/grn/list/api",
          "columns": [
            { "data": "id" }, 
              // { "data": "grn_number" }, 
              { "data": "material_inward_number" }, 
              { "data": "received_by" },
              { "data": "invoice_number" },
              { "data": "po_num" },  
              { "data": "grn_date" }, 
              { "data": "supp_name" }, 
              { "data": "remark" },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var strs = data.mode_of_trans; 
                      var idsss=data.id;
                      return strs +
                      '<br><button id="'+idsss  +'" class="btn btn-warning btn-xs mode">Details</button>&nbsp;'
                    ;
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var str = data.name; 
                      var idss=data.id;
                      console.log(data);
                      
                      return str +
                      '<br><button id="'+idss+'"  class="btn btn-success btn-xs item">Details</button> &nbsp;'
                    ;
                    }
                },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var img=data.supplier_bill_file;
                      var ids=data.id;
                      console.log(ids);
                      
                            return '<a href="/purchase/grn/update/'+ids+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit</button></a> &nbsp' ;
                            // '<a href="/file-upload/download/'+img+'" target="_blank"><button class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-download"></i></button></a> &nbsp' ;
                    }
                },
            ],
            "columnDefs": [
              { "orderable": false, "targets": 10 }
            ]
           
          
        });
        $('#asn_table tbody').on('click', 'button.mode', function () {
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
    $('#asn_table tbody').on('click', 'button.item', function () {
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
          getdata2(data,row,this)

          last_ele=row;
          last_tr=tr;
          tr.addClass('shown');
        }
    } );
    });

    function getdata1(data,ele,button)  {  
      var out;
      $('#ajax_loader_div').css('display','block');

      $.ajax({
               type:'get',
               url:"/summary/mode/"+data,
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
    function getdata2(data,ele,button)  {  
      var out;
      $('#ajax_loader_div').css('display','block');

      $.ajax({
               type:'get',
               url:"/summary/item/"+data,
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
                    <th>{{__('purchase/grn.number')}}</th>
                      <th>{{__('purchase/grn.material')}}</th>
                      <th>{{__('purchase/grn.rec')}}</th>
                      <th>{{__('purchase/grn.invoice')}}</th>
                      <th> {{__('purchase/grn.po')}}</th>
                      <th>{{__('purchase/grn.grn_date')}}</th>

                      <th>{{__('purchase/grn.supp_name')}}</th>
                      <th>{{__('purchase/grn.remark')}}</th>
                      <th>{{__('purchase/grn.mode')}}</th>
                      <th> {{__('purchase/grn.detail')}}</th>
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