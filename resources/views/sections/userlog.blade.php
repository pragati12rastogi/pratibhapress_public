@extends($layout)

@section('title', __('userlog.userlog_title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>User Log</a></li>
@endsection
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
      dataTable = $('#userlog_table').DataTable({
          "processing": true,
          "serverSide": true,
          "ajax": "/user/log/api",
          "aaSorting": [],
          "responsive": true,
          "columns": [
              { "data": "id" },
              { "data": "name" }, 
              { "data": "action" },
              { "data": "description" },
              {
                  "data":"content_changes", "render": function(data,type,full,meta)
                  {
                    var obj = JSON.parse(data);
                    var desc = "";
                    if(data.length>3)
                    {
                      
                          $.each( obj, function( key, value ) {
                            var d = '<b>'+key+'</b>' + ": " + value ;
                            desc = desc+'<br>'+d;
                          });
                      return desc;
                    }
                    else
                    {
                      return '';
                    }
                    
                  }
              },
              { "data": "data_id" }, 
              { "data": "createdon" }
            ],
            "columnDefs": [
            ]
        });
    });
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

                  <table id="userlog_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>{{__('userlog.userlog_list_id')}}</th>
                      <th>{{__('userlog.userlog_list_name')}}</th>
                      <th>{{__('userlog.userlog_list_action')}}</th>
                      <th>{{__('userlog.userlog_list_update_reason')}}</th>
                      <th>{{__('userlog.userlog_list_changes')}}</th>
                      <th>{{__('userlog.userlog_list_dataid')}}</th>
                      <th>{{__('userlog.userlog_list_date')}}</th>                      
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