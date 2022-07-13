@extends($layout)

@section('title','Plate Size List')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Plate Size List</a></li>
@endsection

@section('css')
  
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script >
  $(document).ready(function(){

 var datatable = $('#plate_grid').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "/master/plate/size/list/api",
        "responsive": true,
        "aaSorting": [],
        "columns": [
            { "data": "id" },
            { "data": "value"}, 
            { "data": function(data, type, full, meta){
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
               } }, 
            // {
            //     "targets": [ -1 ],
            //     "data":"id", "render": function(data,type,full,meta)
            //     {
            //       return "<a href=/client/view?id="+data+"><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
            //       '<a href=/client/update?id='+data+'><button class="btn btn-success btn-xs"> Edit </button></a>' ;
            //     }
            // }
          ],
          "columnDefs": [
            // { "orderable": false, "targets": 2},
            // { "width": "20%", "targets": 1 }
          ]
        
      });
  });

  // datatable.columns.adjust().draw();
  </script>
@endsection

@section('main_section') 
    <section class="content">
    @section('titlebutton')
    <a href="/master/create/plate/size" ><button class="btn btn-primary "  >Create Plate Size</button></a>
    
     @endsection
        <!-- Default box -->
        <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Plate Size</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <table id="plate_grid" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>ID</th>
                      <th>Value</th>
                      <th>Created Time</th>
                     
                      <!-- <th>Actions</th> -->
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