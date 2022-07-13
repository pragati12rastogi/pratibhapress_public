@extends($layout)

@section('title', 'Press Production Summary')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Press Production Summary</a></li> 
@endsection
@section('css')
<style>

  #plate {
          width: 100%;
          overflow-x: auto;
        }

</style>

@endsection
@section('js')
<script src="/js/Production/platebypress_creation.js"></script>

<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
      var last_ele = null ;
    var last_tr = null ;
      dataTable = $('#plate').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax":  "/production/press/summary/api",
          "columns": [
            {"data":"planneddate"},
            {"data":"job_number"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"creative_name"},
            {"data":"element_name"},
            {"data":"total_plates"},
            {"data":"planned_plates"},
            {"data":"actual"},
            {"data":"wastage"},
            {
                  "targets": [ -1 ],
                  data : function(data,type,full,meta)
                  {
                    return "<button id="+data.prod+" class='job_det btn btn-warning btn-xs'>Details</button> &nbsp;" //+ 
                    //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                    ;
                  }
              }
           
            ],
            "columnDefs": [
               { "orderable": false, "targets": 10 }
            
            ]
          
        });
        $('#plate tbody').on('click', 'button.job_det', function () {
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
               url:"/production/planning/alldata/"+data,
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
                <div class="hsn" style="overflow-x:auto;overflow-y:auto;">
                    

                    <table id="plate" class="table table-bordered table-striped" style="width:100%">
                      <thead>
                        <tr>
                        <th>Date</th>
                          <th>JC No.</th>
                          <th>Reference</th>
                          <th>Item</th>
                          <th>Creative</th>
                          <th>Element</th>
                          <th>Total Imp</th>
                          <th>Planned</th>
                          <th>Actual</th>
                          <th>Total Wastage</th>
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
       
        </div>
      </section>
@endsection