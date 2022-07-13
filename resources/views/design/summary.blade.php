@extends($layout)

@section('title', 'Design Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Design Summary</a></li> 
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
  table{
    font-size: 13px;
  }
  table thead tr th{
    font-size: 12px;
  }
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;
    $(document).ready(function() {
      var last_ele = null ;
    var last_tr = null ;
      dataTable = $('#table').DataTable({
        "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/design/summary/api",
          "columns": [
            {"data":"do_status"},
            {"data":"do_number"},
              {"data":"created"},
              { 
                "data":"referencename","render": function(data, type, full, meta){
                  if(data)
                    
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            {"data":"do_io"}, 
            {"data":"item"}, 
            {"data":"creative"}, 
            {"data":"no_pages"}, 
            { 
                "data":"work_no_pages_done","render": function(data, type, full, meta){
                  if(data)
                  return data;
                  else
                    return 0;
                } 
            },
            {
              data:function(data, type, full, meta){
                  var pages_assign=data.no_pages  ;
                  var no_pages_done=data.work_no_pages_done;
                  if(data.work_no_pages_done)
                       return parseInt(pages_assign)-parseInt(no_pages_done);
                  else
                  return parseInt(pages_assign);
                } 
            },
            
            {"data":"creative_party"},
            { 
                "data":"emp","render": function(data, type, full, meta){
                  if(data)
                    
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            // { 
            //     "data":"work_alloted_number","render": function(data, type, full, meta){
            //       if(data)
                    
            //       return data.replace(/,/g,'<br>');
            //       else
            //         return "";
            //     } 
            // },
            // { 
            //     "data":"work_no_pages","render": function(data, type, full, meta){
            //       if(data)
                    
            //       return data.replace(/,/g,'<br>');
            //       else
            //         return "";
            //     } 
            // },
            // {"data":"creative"},
            //   { 
            //     "data":"status","render": function(data, type, full, meta){
            //       if(data)
                    
            //       return data.replace(/,/g,'<br>');
            //       else
            //         return "";
            //     } 
            // },
             
              // {"data":"status_date"},
              // {"data":"creative_party"},
              // {"data":"status"},
              // {"data":"status_date"},
                   {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<button id="+data+" class='job_det btn btn-warning btn-xs'>Details</button> &nbsp;" //+ 
                    //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 12 }
            
            ]
          
        });
        $('#table tbody').on('click', 'button.job_det', function () {
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
    });

    function getdata1(data,ele,button)  {  
      var out;
      $('#ajax_loader_div').css('display','block');

      $.ajax({
               type:'get',
               url:"/design/summary/alldata/"+data,
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
                 
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                    <th>Order Status</th> 
                      <th>Order No</th>
                      <th>Date</th>
                      <th>Client Name</th>
                      <th>IO No</th>
                      <th>Item Name</th>
                      <th>Creative Name</th>
                      <th>Pages</th>
                      <th>Pages Done</th>
                      <th>Pages Left</th>
                      <th>Creative Received</th>
                      <th>Designer</th>
                      {{-- <th>Status</th> --}}
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