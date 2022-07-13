@extends($layout)

@section('title', __('layout.dashboard'))

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class="fa "></i> DashBoard</a></li>
@endsection

@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
@endsection

@section('js')
  {{-- <script src="js/adminlte.min.js"></script> --}}
@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
        {{-- @if(in_array(1, Request::get('userAlloweds')['section']))
          <p>Hello</p>
        @endif --}}
        <div class="box">
          <div class="box-header with-border">
          <h3 class="box-title">{{__('dashboard.mytitle')}}</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                      title="Collapse">
                <i class="fa fa-minus"></i></button>
              <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                <i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body">
            <a href="redirect/google">Login in with Google</a>
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