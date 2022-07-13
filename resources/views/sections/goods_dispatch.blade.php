
@extends($layout)

@section('title', __('goods_dispatch.title'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('goods_dispatch.mytitle')}}</i></a></li>
   @endsection
@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="/css/party.css">

@endsection

@section('js')
<script src="/js/views/goods_dispatch.js"></script>
<script>
$('select').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
    if(valueSelected==1){
        $('.company').show();
        $('.gst').show();
        $('.address').show();
        $('.carrier').hide();
        
    }
    if(valueSelected==2){
        $('.company').hide();
        $('.carrier').show();
        $('.gst').hide();
        $('.address').hide();
    }
    if(valueSelected==3){
        $('.company').show();
        $('.gst').show();
        $('.address').show();
        $('.carrier').hide();
    }

});
$('#gst_select').select2();
    $('input[type=radio][name=gst_type]').change(function() {
        if (this.value == "0") {
            $('#gst_sel').slideDown();
            $('#gst').slideUp();    
        }
        if (this.value == "1") {
            $('#gst').slideDown();
            $('#gst_sel').slideUp();
    }
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
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('goods_dispatch.mytitle')}}</h2><br><br><br>
                    <div class="container-fluid">
                        <form method="post" action="GoodsDispatch/insert" method="POST" id="dispatch_form">
                                @csrf
                            <div class="row">
                                <div class="col-md-4 {{ $errors->has('mode') ? 'has-error' : ''}}">
                                    <label>{{__('goods_dispatch.mode')}} <sup>*</sup></label><br>
                                    <select class="form-control select2"  data-placeholder="" style="width: 100%;" name="mode">
                                        <option value="">Select Mode</option>
                                        <option value="1">Transporter</option>
                                        <option value="2">Self</option>
                                        <option value="3">Courier</option>
                                        </select>
                                    {!! $errors->first('mode', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->

                                <div class="col-md-4 carrier {{ $errors->has('carrier') ? 'has-error' : ''}}">
                                    <label class="label_tran">{{__('goods_dispatch.carrier')}} <sup>*</sup></label><br>
                                    <input type="text" class="form-control input-css" name="carrier" value="{{ old('carrier') }}">
                                    {!! $errors->first('carrier', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-6-->
                                <div class="col-md-4 company {{ $errors->has('company') ? 'has-error' : ''}}" style="display:none;">
                                    <label>{{__('goods_dispatch.company')}} <sup>*</sup></label><br>
                                    <input type="text" required  class="form-control input-css" name="company" value="{{ old('company') }}">
                                    {!! $errors->first('company', '<p class="help-block">:message</p>') !!}
                                </div>

                                <div class="col-md-4 {{ $errors->has('number') ? 'has-error' : ''}}">
                                    <label>{{__('goods_dispatch.number')}}<sup>*</sup></label><br>
                                    <input type="number" class="form-control input-css" name="number" value="{{ old('number') }}">
                                    {!! $errors->first('number', '<p class="help-block">:message</p>') !!}
                                </div>
                               
                            </div><!--row-->
                            <div class="row">
                                {{-- <div class="col-md-4 gst {{ $errors->has('gst') ? 'has-error' : ''}}">
                                    <label>{{__('goods_dispatch.gst')}} <sup>*</sup></label><br>
                                    <input type="text" required class="form-control input-css" name="gst" value="{{ old('gst') }}">
                                    {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                </div> --}}
                              <div class="gst">
                                <div class="col-md-4">
                                    <div class="form-group">
                                            <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                        <div class="po_type_label_er">
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" value="1" checked class="gst_type" name="gst_type" >Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" value="0" class="gst_type" name="gst_type" >No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 {{ $errors->has('gst') || $errors->has('gst_sel') ? 'has-error' : ''}}">
                                        <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                        <div id="gst_entry">
                                                <input type="text" class="form-control input-css" name="gst" value="{{ old('gst') }}" id="gst">
                                                {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div id="gst_sel" style="display:none">
                                            <select name="gst_sel" style="width:100%" class="form-control input-css select" id="gst_select">
                                                    <option value="NA" {{old('gst_sel')=='0' ?'selected="selected"':''}}>NA</option>
                                            </select>
                                            {!! $errors->first('gst_sel', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                              </div>
                                    <div class="col-md-4 address {{ $errors->has('address') ? 'has-error' : ''}}">
                                        <label>{{__('goods_dispatch.address')}} <sup>*</sup></label><br>
                                        <textarea name="address" required class="input-css form-control" id=""></textarea>
                                        {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
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

