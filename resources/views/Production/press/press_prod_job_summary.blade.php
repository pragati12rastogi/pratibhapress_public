@extends($layout)

@section('title', 'Press Production Job Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Press Production Job Summary</a></li> 
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
          "ajax": "/production/press/job/summary/api",
          "columns": [
            {"data":"job_number"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"creative_name"},
            {"data":"element_name"},
            {"data":"open_size"},
            {"data":"close_size"},
            {"data":"e_plate_set"},
            {"data":"e_plate_size" },
            {"data":"impression_per_plate"},
           {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                    return (data.impression_per_plate*data.e_plate_set);
                  }
           },
           
                   {
                  "targets": [ -1 ],
                  data : function(data,type,full,meta)
                  {
                    return "<button id="+data.id+"/"+data.element_type_id+" class='job_det btn btn-warning btn-xs'>Details</button> &nbsp;" //+ 
                    //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 9 }
            
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
               url:"/production/rawmaterial/alldata/"+data,
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
                          <th>JC No.</th>
                          <th>Reference</th>
                          <th>Item</th>
                          <th>Creative</th>
                          <th>Element</th>
                          <th>Open Size</th>
                          <th>Close Size</th>
                          <th>Plate Sets</th>
                          <th>Plate Size</th>
                          <!-- <th>Front Color</th>
                          <th>Back Color</th> -->
                          <th>Imp Per Plate</th>
                          <th>Total Imp</th>
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