@extends($layout)

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
        <!-- Default box -->
        
        <div class="box">
            <!-- /.box-header -->
                <div class="box-body">
                  <div class="alert alert-danger" role="alert">
                    <strong>Permission Denied!</strong> You are not authorised to access this page.
                  </div>
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection