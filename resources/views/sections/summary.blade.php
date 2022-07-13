@extends($layout)

@section('title', __('layout.summary'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
  
  $(document).ready(function()  {
    var last_ele = null ;
    var last_tr = null ;
    
     $('.loader').hide()
      dataTable = $('#summary_table').DataTable({
          "processing": true,
          "scrollX":true,
          "serverSide": true,
          "aaSorting": [],
          "responsive": true,
          "ajax": {url: "/summary",
                    timeout:600000,
                    method:'get'
                },
          "columns": [
            { "data": "jc_num" }, 
            { "data": "ts" }, 
            { "data": "date" },//3 
            { "data": "a" }, 
            { "data": "em" }, 
            { "data": "pn" },//6
            { "data": "jqty" }, 
            {
              "targets": [ -1 ],
              "data":"io_id", "render": function(data,type,full,meta)
              {
                return '<button id="'+data+'" class="job_det btn btn-primary btn-xs">Job Details</button> &nbsp;';
              }
            }
          ],
            "columnDefs": [
              { "orderable": false, "targets": 7 }
            ]
        });
        $('#summary_table tbody').on('click', 'button.job_det', function () {
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
               url:"/summary1/jobdet/"+data,
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
            </div>
        <!-- Default box -->
        <div class="box">
          <div class="box-header with-border">
            {{-- <h3 class="box-title">Summary</h3> --}}
          </div>
          <div class="box-body">
            <table id="summary_table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                    
                        <th>Job Card No.</th>
                        <th>Created Date</th>
                        <th>Job Card Date</th> <!-- 3-->
                        <th>Internal Order Number</th>
                        <th>Email</th>
                        <th>Party Name</th><!-- 6-->
                        <th>Job Quantity</th>
                        <th>View Info</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            </div>
        </div>
        <div id="datatab"></div>
        <!-- /.box -->
                
      </section>
@endsection
