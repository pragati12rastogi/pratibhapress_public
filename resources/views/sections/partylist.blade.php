@extends($layout)

@section('title', __('view_party.clientlist'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>{{__('view_party.clientlist')}}</a></li>
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
        "ajax": "/client/list/api",
        "responsive": true,
        "aaSorting": [],
        "columns": [
            { "data": "id" },
            { "data": "partyname"}, 
            { "data": "reference_name" },
            {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var pointer=data.gst_pointer;
                      if(pointer==1 || pointer==2)
                            return data.gst;
                      else
                            return "NA";
                    }
                },
             
            { "data": "email" }, 
            { "data": "state" }, 
            { "data": "created" }, 
            {
                "targets": [ -1 ],
                "data":"id", "render": function(data,type,full,meta)
                {
                  return "<a href=/client/view?id="+data+"><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                  '<a href=/client/update?id='+data+'><button class="btn btn-success btn-xs"> Edit </button></a>' ;
                }
            }
          ],
          "columnDefs": [
            { "orderable": false, "targets": 6},
            { "width": "20%", "targets": 1 }
          ]
        
      });
  });

  // datatable.columns.adjust().draw();
  </script>
@endsection

@section('main_section') 
    <section class="content">
    @section('titlebutton')
    <a href="/import/data/client"><button class="btn btn-sm btn-primary">{{__('party_form.importtitle')}}</button></a>
    <a href="/export/data/client"><button class="btn btn-sm btn-primary">{{__('party_form.exporttitle')}}</button></a>
    @endsection
        <!-- Default box -->
        <div class="box">
                <div class="box-header">
                  <h3 class="box-title">{{__('view_party.clientlist')}}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <table id="party_grid" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>ID</th>
                      <th>Client Name</th>
                      <th>Reference Name</th>
                      <th>GST Number</th>
                      <th>Email</th>
                      <th>State</th>
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