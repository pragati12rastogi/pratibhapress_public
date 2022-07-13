
@extends($layout)

@section('title', __('vehicle.title'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('vehicle.mytitle')}}</i></a></li>
 
@endsection
@section('css')

@endsection

@section('js')
<script>

$(".select2").on("select2:close", function (e) {
    $(this).valid();
});
$('#hsn_form').on('submit', function(event) {
    $('#vehicle_number').each(function(e) { 

        $(this).rules("add",{ 
                required: true,
                messages: { required: "Vehicle Number is required"}
        });    
    });
    $('#owner').each(function(e) { 

    $(this).rules("add",{ 
        required: true,
        messages: { required: "Owner is required"}
        });    
    });
    $('#vehicle_brand').each(function(e) { 

    $(this).rules("add",{ 
        required: true,
        messages: { required: "Vehicle Brand is required"},
        });    
    });
    $('#vehicle_type').each(function(e) { 

    $(this).rules("add",{ 
        required: true,
        messages: { required: "Vehicle Type is required"},
        });    
    });
});
$("#hsn_form").validate(
    {
        errorPlacement: function(error, element) {
            if(element.attr("name")=='item')
            {
                var v = $("#jqueryerror");
                error.insertAfter($(v));
            }
            else
            error.insertAfter(element);
        },
     
    }
);
     
</script>
@endsection


@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            </div>
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('vehicle.mytitle')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <form method="POST" action="/vehicle/insert" id="hsn_form">
                                @csrf
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('vehicle_number') ? 'has-error' : ''}}">
                                    <label>{{__('vehicle.number')}} <sup>*</sup></label><br>
                                    <input type="text" class="form-control input-css" name="vehicle_number" id="vehicle_number">
                                  
                                    <div id="jqueryerror"></div>
                                    {!! $errors->first('vehicle_number', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->

                                <div class="col-md-6 {{ $errors->has('owner') ? 'has-error' : ''}}">
                                    <label>{{__('vehicle.owner')}} <sup>*</sup></label><br>
                                    <input type="text" class="form-control input-css" name="owner" id="owner">
                                    {!! $errors->first('owner', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-6-->
                            </div><br><br><!--row-->
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('vehicle_brand') ? 'has-error' : ''}}">
                                    <label>Vehicle Brand<sup>*</sup></label><br>
                                    <input type="text" class="form-control input-css" name="vehicle_brand" id="vehicle_brand">
                                    {!! $errors->first('vehicle_brand', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->

                                <div class="col-md-6 {{ $errors->has('vehicle_type') ? 'has-error' : ''}}">
                                    <label>Vehicle Type <sup>*</sup></label><br>
                                    <select class="form-control select2" name="vehicle_type" id="vehicle_type">
                                        <option value="">Select Type</option>
                                        @foreach($v_type as $type)
                                            <option value="{{$type->id}}">{{$type->type}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('vehicle_type', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-6-->
                            </div><br><br>
                                    <div class="row">
                                        <div class="box-footer">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div><br><!--submit button row-->
                        </form>
                    </div><!--end of container-fluid-->
                </div><!------end of box box-default---->
            </div><!--end of box-header with-border-->
    </section><!--end of section-->
@endsection


