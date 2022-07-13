
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
        $('.carrier').hide();
        
        $('.address').show();
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



</script>
<script>

    var x="{{$dispatch['mode']}}";
if(x==1){
        $('.company').show();
        $('.carrier').hide();
        $('.gst').show();
    }
    if(x==2){
        $('.company').hide();
        $('.carrier').show();
        $('.address').hide();
        $('.gst').hide();
    }
    if(x==3){
        $('.company').show();
        $('.carrier').hide();
        $('.gst').show();
    }
 
    $('#gst_select').select2();
    $('input[type=radio][name=gst_type]').change(function() {
        if (this.value == "0") {
            $('#gst_sel').slideDown();
            $('#gst').slideUp();  
            $('#gst_entry').css('display','none');  
        }
        if (this.value == "1") {
            $('#gst').slideDown();
            $('#gst_entry').css('display','block');
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
            @if( isset(Request::get('userAlloweds')['section']) && in_array(1, Request::get('userAlloweds')['section']))
            
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('goods_dispatch.mytitle')}}</h2><br><br><br>
                    <div class="container-fluid">
                    <form   method="POST" action="/GoodsDispatchupdate/{{$dispatch['id']}}" id="dispatch_form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Update Reason<sup>*</sup></label>
                                    <input  required type="text" autocomplete="off" value="{{$errors->any() ? old('update_reason') : ''}}" class="form-control  input-css update_reason" id="update"  name="update_reason">
                                    {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-md-4 {{ $errors->has('mode') ? 'has-error' : ''}}">
                                    <label>{{__('goods_dispatch.mode')}} <sup>*</sup></label><br>
                                    <select class="form-control select2"  data-placeholder="" style="width: 100%;" name="mode">
                                        <option value="">Select Mode</option>
                                        <option value="1" {{$dispatch['mode']==1 ? 'selected="selected"' : ''}}>Transporter</option>
                                        <option value="2" {{$dispatch['mode']==2 ? 'selected="selected"' : ''}}>Self</option>
                                        <option value="3" {{$dispatch['mode']==3 ? 'selected="selected"' : ''}}>Courier</option>
                                        </select>
                                    {!! $errors->first('mode', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->

                                <div class="col-md-4 carrier {{ $errors->has('carrier') ? 'has-error' : ''}}">
                                    <label class="label_tran">{{__('goods_dispatch.carrier')}} <sup>*</sup></label><br>
                                    <input type="text" class="form-control input-css" name="carrier"  value="{{$dispatch['courier_name']}}">
                                    {!! $errors->first('carrier', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-6-->
                                <div class="col-md-4 company {{ $errors->has('company') ? 'has-error' : ''}}" style="display:none">
                                    <label>{{__('goods_dispatch.company')}} <sup>*</sup></label><br>
                                    <input type="text" required class="form-control input-css" name="company" value="{{$dispatch['courier_name']}}">
                                    {!! $errors->first('company', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-4 {{ $errors->has('number') ? 'has-error' : ''}}">
                                    <label>{{__('goods_dispatch.number')}} <sup>*</sup></label><br>
                                    <input type="number" class="form-control input-css" name="number"  value="{{$dispatch['contact']}}">
                                    {!! $errors->first('number', '<p class="help-block">:message</p>') !!}
                                </div>
 
                            </div><!--row-->
                            <div class="row">
                                

                                {{-- <div class="col-md-4 gst {{ $errors->has('gst') ? 'has-error' : ''}}">
                                    <label>{{__('goods_dispatch.gst')}} <sup>*</sup></label><br>
                                    <input type="text" required class="form-control input-css" name="gst" value="{{$dispatch['gst']}}">
                                    {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                </div> --}}
                                <div class="col-md-4" {{$dispatch['mode']==2 ? 'style=display:none' : 'style=display:block'}}>
                                    <div class="form-group">
                                            <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                        <div class="po_type_label_er">
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" value="1" {{$dispatch['gst']=='NA' ? '' : 'checked=checked'}} class="gst_type" name="gst_type" >Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" value="0" class="gst_type" name="gst_type" {{$dispatch['gst']=='NA' ? 'checked=checked' : ''}}>No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 {{ $errors->has('gst') || $errors->has('gst_sel') ? 'has-error' : ''}}" {{$dispatch['mode']==2 ? 'style=display:none' : 'style=display:block'}}>
                                        <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                        <div id="gst_entry" {{$dispatch['gst']=='NA' ? 'style=display:none' : 'style=display:block'}}>
                                                <input type="text" class="form-control input-css" name="gst" value="{{$dispatch['gst']}}" id="gst" >
                                                {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div id="gst_sel"  {{$dispatch['gst']=='NA' ? 'style=display:block' : 'style=display:none'}}>
                                            <select name="gst_sel" style="width:100%" class="form-control input-css select" id="gst_select">
                                                    <option value="NA" {{old('gst_sel')=='0' ?'selected="selected"':''}}>NA</option>
                                            </select>
                                            {!! $errors->first('gst_sel', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                              </div>
    
                                <div class="col-md-4 address">
                                        <label>{{__('goods_dispatch.address')}} <sup>*</sup></label><br>
                                <input type="text" name="address" required class="input-css form-control" id="" value="{{$dispatch['address']}}">
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
            @endif
    </section><!--end of section-->
@endsection

