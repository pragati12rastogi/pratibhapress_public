
<?php //print($io);die();?>
@extends($layout)

@section('title', __('jobcard.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('jobcard.mytitle')}}</i></a></li>
@endsection
@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <!-- <link rel="stylesheet" href="css/party.css"> -->

@endsection

@section('js')
<script src="/js/views/job_card.js"></script>
 <script>
   $(document).ready(function() {
        $('#internalorder').change(function(e) {
          var io_id = $(e.target).val();
          $('#ajax_loader_div').css('display','block');

          $.ajax({
              url: "/internalorder/" + io_id,
              type: "GET",
              success: function(result) {
                    for (var i = 0; i < result.length; i++) {
                    $('.leftqty').val(result[i].qty);
                    var item=result[i].item_category_id;
                    var market=result[i].marketing_user_id;
                    var other=result[i].other_item_name;
                    if(item==15){
                        $('.other_item').show();
                    }
                    else{
                        $('.other_item').hide();
                    }
                    $(".item").val(item).trigger("change");
                    $(".item1").val(item);
                    $("#other_value").val(other);
                    $(".market").val(market).trigger("change");
              }
              $('#ajax_loader_div').css('display','none');

            }

          });
      });
  });

var message="{{Session::get('message')}}";
if(message=="successfull"){
    document.getElementById("popup_message").click();
}
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

                <div class='box box-default'>
                    <form action="/jobcard/insert" method="post" id="jcform">
                        @csrf
                        <div class="container-fluid">
                            <h3 class="box-title">{{__('jobcard.mytitle')}}</h3>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.internalorder')}}<sup>*</sup></label>
                                    <select class="form-control select2" id="internalorder" data-placeholder="" name="internalorder">
                                        <option value="default">Select internal order</option>
                                       @foreach ($io as $value)
                                    <option value="{{$value->id}}">{{$value->io_number}}</option>
                                       @endforeach
                                    </select>
                                    {!! $errors->first('internalorder', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.qty')}}<sup>*</sup></label>
                                    <input type="number" name="qty" class="input-css leftqty">
                                    {!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
                                </div>
                           
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.creative_name')}}<sup>*</sup></label>
                                    <input type="text" name="creative_name" class="input-css">
                                    {!! $errors->first('creative_name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.open_size')}}<sup>*</sup></label>
                                    <input type="text" name="open_size" class="input-css" placeholder="Please Mention the open size">
                                    {!! $errors->first('open_size', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.close_size')}}<sup>*</sup></label>
                                    <input type="text" name="close_size" class="input-css"placeholder="Please Mention the close size">
                                    {!! $errors->first('close_size', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-4">
                                        <label>{{__('internal_order.dimension')}}<sup>*</sup></label>
                                        <select   value="{{ old('dimension') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="dimension">
                                                <option value="default">Select Dimension</option>
                                                <option value="m">Metre</option>
                                                <option value="mm">Millimeter</option>
                                                <option value="cm">Centimeter</option>
                                                <option value="km">Kilometer</option>
                                                <option value="in">Inch</option>
                                                <option value="ft">Foot</option>
                                                <option value="ton">Ton</option>
                                                <option value="doz">Dozen</option>
                                                <option value="kg">Kilogram</option>
                                                <option value="g">Grams</option>
                                        </select>
                                    {!! $errors->first('dimension', '<p class="help-block">:message</p>') !!}
                                   </div>
                                
                            </div><br>
                            <div class="row">
                            
                                <div class="col-md-4">
                                        <label for="" class="job_sample_label_er">{{__('jobcard.job_sample')}}<sup>*</sup></label>
                                        <div class="col-md-2">
                                            <div class="radio">
                                                <label><input autocomplete="off" type="radio"  class="job_sample" value="1" name="job_sample"> Yes </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="radio">
                                                <label><input autocomplete="off" type="radio"  class="job_sample" value="0" name="job_sample"> No </label>
                                            </div>
                                        </div>
                                       
                                    </div>
                                    {!! $errors->first('job_sample', '<p class="help-block">:message</p>') !!}
                                   <div class="col-md-4">
                                        <label for="">{{__('jobcard.item')}}<sup>*</sup></label>
                                        <select id="" class="form-control input-css item select2" name="item">
                                                <option value="default">Select Internal Order first</option>
                                                @foreach ($item as $key)
                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('item', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <input type="hidden"  class="item1">
                                    <div class="col-md-4 other_item" style="display:none">
                                            <label>Other Item Category</label>
                                              <input type="text"  id="other_value" class="input-css other_value" name="item_desc">
                                        </div>
                            </div><br>
                            <div class="row">
                            <div class="col-md-6">
                                            <label>{{__('internal_order.market')}}<sup>*</sup></label>
                                                <select   value="{{ old('market') }} "   class="select2 form-control input-css market" name="market" style="width:100%" disabled>
                                                        <option value="default">Select Marketing Person</option>
                                                        @foreach($users as $key)
                                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                                        @endforeach
                                                </select>    
                                                {!! $errors->first('market', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        
                            </div><br>
                            <div class="row">
                                <div class="col-md-12">
                                        <label>{{__('jobcard.desc')}}<sup>*</sup></label>
                                        <textarea name="desc" class="input-css form-control desc" id=""></textarea>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="box-footer" style="float:right">
                                    <button type="submit" class="btn btn-primary submit">Submit</button>
                                </div>
                            </div><!--submit button row-->
                        </div><!--end of container-fluid-->
                    </form>


                </div><!------end of box box-default---->
    </section><!--end of section-->
@endsection

