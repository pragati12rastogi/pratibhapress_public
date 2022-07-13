
@extends($layout)

@section('title', __('uom.create_title'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="fa ">{{__('uom.mytitle')}}</i></a></li>
  
@endsection
@section('css')

@endsection
@section('js')
<script>
$('#uom_form').on('submit', function(event) {
    $('#uom').each(function(e) { 

        $(this).rules("add",{ 
                required: true,
                messages: { required: "Unit of Measurement is required"}
        });    
    });

});
$("#uom_form").validate();

</script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            </div>
        <!-- Default box -->
            <div class="box-header with-border">
                <div class='box box-default hgt'>  <br>
                    <div class="container-fluid">
                        <form method="post" action="/uom/ins/db" method="POST" id="uom_form">
                                @csrf
                            <div class="row">

                                <div class="col-md-4 {{ $errors->has('uom') ? 'has-error' : ''}}">
                                    <label>{{__('uom.mytitle')}} <sup>*</sup></label><br>
                                    <input type="text" id="uom" class="form-control input-css" name="uom">
                                    {!! $errors->first('uom', '<p class="help-block">:message</p>') !!}
                                 </div><!--col-md-6-->

                                    <div class="row">
                                        <div class="box-footer divshift">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div><!--submit button row-->
                        </form>
                    </div><!--end of container-fluid-->
                </div><!------end of box box-default---->
            </div><!--end of box-header with-border-->
    </section><!--end of section-->
@endsection


