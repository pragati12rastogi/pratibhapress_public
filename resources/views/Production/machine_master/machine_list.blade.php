@extends($layout)

@section('title','Machine List')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Machine List</a></li>
@endsection

@section('css')
  
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script >
var datatable;
  $(document).ready(function(){

  datatable = $('#plate_grid').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "/master/machine/list/api",
        "responsive": true,
        "aaSorting": [],
        "columns": [
            { "data": "id" },
            { "data": "name"}, 
            { "data": "category"}, 
            { "data": "brand"}, 
            { "data": "size"}, 
            { "data": "purchase_date"}, 
            { "data": "bill_no"}, 
            { data: function(data, type, full, meta){
                var dt=data.created_at;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+ 1;
                      var yyyy=dt.getFullYear();
                      var hh=dt.getHours();
                      var mi=dt.getMinutes();
                      var ss=dt.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                      var ac=dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                      return ac;
               }
             }, 
             {
              data:function(data,type,full,meta){
                  console.log(data);
                  var x="";
                    if(data.photo != "" && data.photo != null){
                      x= "<a href='/upload/machine/"+data.photo+"' target='_blank'><button class='btn btn-primary btn-xs'> Photo </button></a> &nbsp;";
                    }else{
                       x='';
                    }
                    if(data.bill_upload != "" && data.bill_upload != null){
                      x+= '<a href="/upload/machine/'+data.bill_upload+'" target="_blank"><button class="btn btn-success btn-xs"> Bill </button></a>';
                    }else{
                       x+="";
                    }
                  return x+"<a href='/master/machine/update/"+data.id+"' target='_blank'><button class='btn btn-danger btn-xs'> Edit </button></a> &nbsp;"+
                  "<a onclick=delete_alert_dailog("+data.id+")><button class='btn btn-warning btn-xs'> Delete </button></a> &nbsp;" ;
                }
             }
          ],
          "columnDefs": [
            { "orderable": false, "targets": 8},
            // { "width": "20%", "targets": 1 }
          ]
        
      });
  
  });
  function delete_alert_dailog(id)
    {
      var r = confirm("Are You Sure Want to Delete this Machine!");
      if (r == true) {
        $('#ajax_loader_div').css('display','block');
        $.ajax({
          url: '/master/machine/delete/'+id,
          type: 'GET',
          success: function(result)
          {
            console.log(result);
            
              $('#ajax_loader_div').css('display','none');
              if(result==1){
                datatable.ajax.reload();
             $('#app').append(' <div class="alert alert-success alert-block"><button type="button" class="close" data-dismiss="alert">×</button><strong>Successfully Deleted Machine.</strong></div>');
              }
              else{
                $('#app').append(' <div class="alert alert-danger alert-block"><button type="button" class="close" data-dismiss="alert">×</button><strong>Some Error Occurred.</strong></div>');
             
              }
            
          }
      });
      } 
  }
  // datatable.columns.adjust().draw();
  </script>
@endsection

@section('main_section') 
    <section class="content">
   
     <div id="app">
     @section('titlebutton')
    <a href="/master/machine/create" ><button class="btn btn-primary "  >Create Machine</button></a>
    
     @endsection
                @include('sections.flash-message')
                @yield('content')
            </div>
        <!-- Default box -->
        <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Machine</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <table id="plate_grid" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>ID</th>
                      <th>Machine Name</th>
                      <th>Machine Category</th>
                      <th>Machine Brand</th>
                      <th>Machine Size</th>
                      <th>Machine Purchase Date</th>
                      <th>Machine Bill No</th>
                      <th>Created Time</th>
                     
                      <th>Actions</th>
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