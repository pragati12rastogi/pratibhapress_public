@extends($layout)

@section('title', __('admin.title'))

{{-- TODO: fetch from auth --}}


@section('breadcrumb')
    <li><a href="#"><i class=""></i>Admin Summary</a></li>
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
      dataTable = $('#admin_table').DataTable({
          "processing": true,
          "serverSide": true,
          "ajax": "/admindata",
          "aaSorting": [],
          "responsive": true,
          "columns": [
              { "data": "id" },
              { "data": "name" }, 
              { "data": "email" },
              { "data": "department" },
              { "data": "user_type" },
              { "data": "created_at" },
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    
                      return "<a href='/admin/view/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href="/user/update/'+data+'" target="_blank"><button class="btn btn-success btn-xs"> Edit</button></a> &nbsp;' + 
                    '<a href="/admin/permission/'+data+'" target="_blank"><button class="btn btn-warning btn-xs"> Rights</button></a> &nbsp;' +
                    '<a href="/widget/permission/'+data+'" target="_blank"><button class="btn btn-danger btn-xs"> Widgets</button></a> &nbsp;' ;
                  },
                  "orderable": false
              }
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

                  <table id="admin_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>{{__('admin.id')}}</th>
                      <th>{{__('admin.name')}}</th>
                      <th>{{__('admin.email')}}</th>
                      <th>Department</th>
                      <th>{{__('admin.usertype')}}</th>
                      <th>{{__('admin.createdat')}}</th>
                      <th>{{__('admin.action')}}</th>                      
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