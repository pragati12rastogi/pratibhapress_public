
@extends($layout)

@section('title', __('hsn.title'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('hsn.mytitle')}}</i></a></li>
 
@endsection
@section('css')

@endsection

@section('js')
<script>

$(".select2").on("select2:close", function (e) {
    $(this).valid();
});
$('#hsn_form').on('submit', function(event) {
    $('#gst').each(function(e) { 

        $(this).rules("add",{ 
                required: true,
                messages: { required: "GST Rate is required"}
        });    
    });
    $('#hsn').each(function(e) { 

    $(this).rules("add",{ 
        required: true,
        messages: { required: "HSN/SAC is required"}
        });    
    });
    $('#item_desc').each(function(e) { 
        $(this).rules("add",{ 
            required: true,
            messages: { required: "HSN/SAC is required"}
        });    
    });
    $('#item').each(function(e) { 

    $(this).rules("add",{ 
        required: true,
        messages: { required: "Item Name is required"},
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
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('hsn.mytitle')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <form method="POST" action="/hsn/insert" id="hsn_form">
                                @csrf
                            <div class="row">
                                <div class="col-md-4 {{ $errors->has('item') ? 'has-error' : ''}}">
                                    <label>{{__('hsn.item')}} <sup>*</sup></label><br>
                                    <input type="text" name="item" id="item" class="input-css">
                                    <div id="jqueryerror"></div>
                                    {!! $errors->first('item', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->

                                <div class="col-md-4 {{ $errors->has('hsn') ? 'has-error' : ''}}">
                                    <label>{{__('hsn.hsn')}} <sup>*</sup></label><br>
                                    <input type="text" class="input-css" name="hsn" id="hsn">
                                    {!! $errors->first('hsn', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-6-->

                                <div class="col-md-4 {{ $errors->has('gst') ? 'has-error' : ''}}">
                                        <label>{{__('hsn.gst')}} <sup>*</sup></label><br>

                                        <select class="form-control select2"  data-placeholder=""  id="gst" name="gst" style="width: 100%;">
                                            <option value="">Select Front Color</option>
                                            <option value="0"  >0</option>
                                            <option value="5"  >5</option>
                                            <option value="12" >12</option>
                                            <option value="18" >18</option>
                                            <option value="28" >28</option>
                                        </select>
                                       {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><!--row-->
                            <br>
                            <div class="row">
                                <div class="col-md-12 {{ $errors->has('hsn') ? 'has-error' : ''}}">
                                    <label>{{__('hsn.item desc')}} <sup>*</sup></label><br>
                                    <input type="text" class="input-css" name="item_desc" id="item_desc">
                                    {!! $errors->first('hsn', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
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


