
@extends($layout)

@section('title', __('hsn.title'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('hsn.mytitle')}}</i></a></li>
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
    $('#update_reason').each(function(e) { 
        $(this).rules("add",{ 
            required: true,
            messages: { required: "Update Reason is required"}
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
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('hsn.mytitle')}}</h2><br><br><br>
                    <div class="container-fluid">
                    <form action="/hsn/update/{{$data->id}}" method="POST" id="hsn_form">
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
                                <div class="col-md-4 {{ $errors->has('item') ? 'has-error' : ''}}">
                                    <label>{{__('hsn.item')}} <sup>*</sup></label><br>
                                <input type="text" name="item" id="item" class="input-css" value="{{$data->item_id}}">
                                    <div id="jqueryerror"></div>
                                    {!! $errors->first('item', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->

                             
                                <div class="col-md-4 {{ $errors->has('hsn') ? 'has-error' : ''}}">
                                    <label>{{__('hsn.hsn')}} <sup>*</sup></label><br>
                                <input type="text" value="{{$data->hsn}}" id="hsn" class="form-control input-css" name="hsn">
                                    {!! $errors->first('hsn', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-6-->

                                <div class="col-md-4 {{ $errors->has('gst') ? 'has-error' : ''}}">
                                        <label>{{__('hsn.gst')}} <sup>*</sup></label><br>
                                        <select class="form-control select2"  data-placeholder=""  id="gst" name="gst" style="width: 100%;">
                                            <option value="">Select Front Color</option>
                                            <option value="0" {{  $data->gst_rate==0 ? 'selected="selected"' : ''}}>0</option>
                                            <option value="5" {{  $data->gst_rate==5 ? 'selected="selected"' : ''}}>5</option>
                                            <option value="12" {{ $data->gst_rate==12 ? 'selected="selected"' : ''}}>12</option>
                                            <option value="18" {{ $data->gst_rate==18 ? 'selected="selected"' : ''}}>18</option>
                                            <option value="28" {{ $data->gst_rate==28 ? 'selected="selected"' : ''}}>28</option>
                                        </select>
                                       
                                        {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><!--row-->
                            <div class="row">
                                <div class="col-md-12 {{ $errors->has('hsn') ? 'has-error' : ''}}">
                                    <label>{{__('hsn.item desc')}} <sup>*</sup></label><br>
                                    <input type="text" value="{{$data->item_description}}" class="form-control input-css" name="item_desc" id="item_desc">
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


