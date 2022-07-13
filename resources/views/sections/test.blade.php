@extends($layout)

@section('title', __('layout.dashboard'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> DashBoard</a></li>
    
@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
        if()
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Title</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                      title="Collapse">
                <i class="fa fa-minus"></i></button>
              <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                <i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body">
            <form action='/masters/create/Users' method='post'>
                @csrf
                <input type="text" name="name" placeholder="name"><br>
                <input type="text" name="active" placeholder="active"><br>
                <input type="text" name="login" placeholder="login"><br>
                <input type="text" name="password" placeholder="password"><br>
                <input type="text" name="email" placeholder="email"><br>
                <input type="text" name="phone" placeholder="phone"><br>
                <input type="hidden" name="success_redirect" value="/dashboard">
                <input type="hidden" name="failure_redirect" value="/error">
                <input type='submit'>
            </form>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            Footer
          </div>
          <!-- /.box-footer-->
        </div>
        <!-- /.box -->
      </section>
@endsection
