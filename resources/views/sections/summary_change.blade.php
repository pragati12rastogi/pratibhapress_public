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
            { "data": "a" }, 
            { "data": "b" }, 
            { "data": "reference_name" },//3 
            {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var str = data.item_name; 
                      var idss=data.other_item_name;
                      console.log(data);
                      if(idss)
                        return str+ " : " +idss;
                      else
                         return str;
                    ;
                    }
                },
                { "data": "io_type" },
                {"data":"qty"},
                {"data":"rate_per_qty"},
            { "data": "jc_num" }, 
            { 
                "data":"dc_num","render": function(data, type, full, meta){
                  if(data)
                    
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            }, 
            { 
                "data":"ti_num","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            }, 
            {
              "targets": [ -1 ],
              "data":"io_id", "render": function(data,type,full,meta)
              {
                return '<button id="'+data+'" class="job_det btn btn-primary btn-xs">IO Details</button> &nbsp;';
              }
            }
          ],
            "columnDefs": [
              
              { "orderable": false, "targets": 7 },
              { "orderable": false, "targets": 8 },
              { "orderable": false, "targets": 9 },
              { "orderable": false, "targets": 10 }
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

    @if(in_array(1, Request::get('userAlloweds')['section']))
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
            <table id="summary_table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Internal Order Number</th>
                        <th>IO Created Date</th>
                        <th>Client Name</th><!-- 6-->
                        <th>Item Name</th><!-- 6-->
                        <th>IO Type</th><!-- 6-->
                        <th>Quantity</th><!-- 6-->
                        <th>Rate</th><!-- 6-->
                        <th>Job Card No.</th>
                        <th>Delivery Challan No.</th>
                        <th>Tax Invoice No.</th>
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
      @else
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
            You do not have permission to access this section.

          </div>
        </div>
        <div id="datatab"></div>
        <!-- /.box -->
                
      </section>
      @endif
@endsection
