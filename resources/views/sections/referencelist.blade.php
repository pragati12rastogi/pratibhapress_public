@extends($layout)

@section('title', 'Reference Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Reference Summary</a></li>
@endsection

@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script >
  $(document).ready(function(){

 var datatable = $('#party_grid').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "/reference/list/api",
        "responsive": true,
        "aaSorting": [],
        "columns": [
            { "data": "id" },
            { "data": "referencename"},
            { "data": "created"},
            {
                "targets": [ -1 ],
                "data":"id", "render": function(data,type,full,meta)
                {
                  return '<a href=/reference/update?id='+data+'><button class="btn btn-success btn-xs"> Edit </button></a>&nbsp;'+
                  '<a  href=/reference/delete?id='+data+' ><button class="btn btn-warning btn-xs"> Delete </button></a>' ;
                }
            }
          ],
          "columnDefs": [
            { "orderable": false, "targets": 3},
          ]
        
      });
  });

  // datatable.columns.adjust().draw();
  </script>
@endsection

@section('main_section') 
    <section class="content">
    @section('titlebutton')
    {{-- <a href="/import/data/client"><button class="btn btn-sm btn-primary">{{__('party_form.importtitle')}}</button></a>
    <a href="/export/data/client"><button class="btn btn-sm btn-primary">{{__('party_form.exporttitle')}}</button></a> --}}
    @endsection
        <!-- Default box -->
        <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Reference Summary</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <table id="party_grid" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>ID</th>
                      <th>Reference Name</th>
                      <th>Created Date</th>
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