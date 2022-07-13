@extends($layout)

@section('title', 'Full & final settlement summary')
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Full & final settlement summary</a></li> 
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
   .nav-pills>li {
    
    border: 1px solid #a9a0a0;
}

</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;
   
   $(document).ready(function() {
    if(dataTable){
        dataTable.destroy();
      }
      dataTable = $('#table1').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/employee/fnf/settlement/list/api",
          "columns": [
              {"data":"emp_name"},
              {"data":"doj"},
              {"data":"leaving_date"},
              {"data":"designation"},
              {"data":"total_amount"},
              {"data":"id",
                 "render": function(data,type,full,meta){
                  var str = '';
                     str +="<button id="+data+" class='job_det btn btn-info btn-xs'>Details</button> &nbsp;"
                     return str;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 5 }
            
            ]
          
        });
   });

    
var last_ele = null ;
var last_tr = null ;
$('#table1 tbody').on('click', 'button.job_det', function () {
        var tr = $(this).parents('tr');
        var row = dataTable.row( tr );
        var data=$(this).attr("id");
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
          if(last_ele) {
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
      var year = $('.date').val();
                    data.date = year;
      $.ajax({
               type:'get',
               url:"/employee/fnf/settlement/details/"+data,
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
                  <div class="tab-content"> 
                    <div class="box-header with-border tab-pane fade active in" id="all">
                      <div class="row">
                        <table id="table1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                              <th>Name</th>
                              <th>Joining Date</th>
                              <th>Leaving Date</th>
                              <th>Designation</th>
                              <th>Total F&F Amount</th>
                              <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                       
                        </table>
                      </div>
                    </div>
               
                  </table>
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection