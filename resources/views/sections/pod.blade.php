
@extends($layout)

@section('title', 'Proof Of Delivery')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class=""> Proof Of Delivery</i></a></li>
@endsection
@section('css')
@endsection

@section('js')
<!-- <script src="/js/views/printing.js"></script> -->
<script>
    $(".is_rec").change(function(){
        
        if($(this).val() == "Delivery Challan"){
            $(".is_dc").show();
            $(".is_docket").hide();
        }else if($(this).val() == "Docket"){
            $(".is_dc").hide();
            $(".is_docket").show();
        }
    })
    $(document).ready(function() {

    // validation for drop downs
    // they must have first option with value="default"
    $.validator.addMethod("notValidIfSelectFirst", function(value, element, arg) {
        return arg !== value;
    }, "This field is required.");

    $('#pod').validate({ // initialize the plugin
        rules: {

            dcn: {
                required: true
            },
            is_rec: {
                required: true
            },
            is_dc: {
                required: true
            },
            is_docket: {
                required: true
            }
            
        }
    });

});
</script>
@endsection

@section('main_section')
<section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
    <!-- Default box -->

    <form method="POST" action="" id="pod" enctype="multipart/form-data">
            @csrf
            <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('dcn') ? ' has-error' : ''}}">
                                    <label>Delivery Challan Number <sup>*</sup></label>
                                    <select  class="form-control select2 input-css dcn" style="width: 100%;"  name="dcn">
                                            <option value="">Select Delivery Challan</option>
                                        @foreach($dcn as $key)
                                            <option value="{{$key['id']}}">{{$key['challan_number']}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('dcn', '<p class="help-block">:message</p>') !!} 
                                </div>
                                <div class="col-md-6 {{ $errors->has('is_rec') ? ' has-error' : ''}}">
                                        <label>POD is recieved on <sup>*</sup></label>
                                        <select class="form-control select2 input-css is_rec" style="width: 100%;"  name="is_rec">
                                            <option value="">Select</option>
                                            <option value="Delivery Challan">Delivery Challan</option>
                                            <option value="Docket">Docket</option>
                                        </select>
                                        {!! $errors->first('is_rec', '<p class="help-block">:message</p>') !!} 
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-6 is_dc {{ $errors->has('upl_dc') ? ' has-error' : ''}}" style="display: none;">
                                    <label>Upload Delivery Challan<sup>*</sup></label>
                                    <input type="file" class="" name="upl_dc" id="upl_dc" required="">
                                    {!! $errors->first('upl_dc', '<p class="help-block">:message</p>') !!}
                                </div>
                          
                                <div class="col-md-6 is_docket {{ $errors->has('upl_docket') ? ' has-error' : ''}}" style="display: none;">
                                    <label> Upload Docket<sup>*</sup></label>
                                    <input type="file" class="" name="upl_docket" id="upl_docket"  required="">
                                    {!! $errors->first('upl_docket', '<p class="help-block">:message</p>') !!}

                                </div>
                               
                            </div><br>
                            
                    </div>
                    <div class="row">
                       <input type="submit" value="Submit" class="btn btn-primary margin">
                   </div>
                </div>
       
    </form>

</section><!--end of section-->



@endsection

