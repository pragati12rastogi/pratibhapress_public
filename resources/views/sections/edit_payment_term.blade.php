
@extends($layout)

@section('title', __('paymentTerm.update_title'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')
 
@section('breadcrumb')
<li><a href="#"><i class="">{{__('paymentTerm.mytitle')}}</i></a></li>
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
    $('#update_reason').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Update Reason is required"}
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
                <div class='box box-default'>  <br>
                    <div class="container-fluid">
                        <form action="/paymentterm/up/db/{{$data->id}}" method="POST" id="payment_term_form">
                                @csrf
                                <div class="row">
                                    <div class=col-md-8>
                                        <label>Update Reason<sup>*</sup></label>
                                        <input type="text" id="update_reason" autocomplete="off" value="" class="form-control  input-css" name="update_reason">
                                        <input type="hidden" name="_id" value="{{$data->id}}"/>
                                        <br>
                                      </div><!--col-md-3-->
                                    </div>
                             
                            <div class="row">
                                <div class="col-md-4 {{ $errors->has('payment') ? 'has-error' : ''}}">
                                    <label>{{__('paymentTerm.mytitle')}} <sup>*</sup></label><br>
                                <input type="text" value="{{$data->value}}" id="payment" class="form-control input-css" name="payment">
                                    {!! $errors->first('payment', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-6-->
                                <div class="row">
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div><!--submit button row-->
                        </form>
                    </div><!--end of container-fluid-->
                </div><!------end of box box-default---->
            </div><!--end of box-header with-border-->
    </section><!--end of section-->
@endsection


