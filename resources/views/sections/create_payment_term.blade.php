
@extends($layout)

@section('title', __('paymentTerm.create_title'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('paymentTerm.mytitle')}}</i></a></li>
@endsection
@section('css')
<style>
        .divshift{
            position: absolute;
        margin-top: 86px;
        }
        @media (max-width: 425px)  
{
.divshift{
position: absolute;
margin-top: 30px;
}

}
@media (max-width: 768px)  
{
.divshift{
position: absolute;
margin-top: 30px;
}
}
    </style>
  
@endsection
@section('js')
<script>

$('#payment_term_form').on('submit', function(event) {
    $('#payment').each(function(e) { 

        $(this).rules("add",{ 
                required: true,
                messages: { required: "Payment Term is required"}
        });    
    });
}); 
$("#payment_term_form").validate();

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
                        <form action="/paymentterm/ins/db" method="POST" id="payment_term_form">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 {{ $errors->has('payment') ? 'has-error' : ''}}">
                                    <label>{{__('paymentTerm.mytitle')}} <sup>*</sup></label><br>
                                    <input type="text" class="form-control input-css" id="payment" name="payment">
                                    {!! $errors->first('payment', '<p class="help-block">:message</p>') !!}
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


