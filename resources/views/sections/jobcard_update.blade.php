
<?php //print($io);die();?>
@extends($layout)
@section('title', __('updatejobcard.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('updatejobcard.mytitle')}}</i></a></li>
@endsection
@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="/css/party.css">
<style>
.nav1>li>a {
    position: relative;
    display: block;
    padding: 10px 34px;
    background-color: white;
    margin-left: 10px;
}
/* .nav1>li>a:hover {
    background-color:#87CEFA;

} */
</style>
@endsection

@section('js')
<script src="/js/views/job_card.js"></script>
 <script>

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
            <ul class="nav nav1 nav-pills">
                    <li class="nav-item">
                      <a class="nav-link active" style="background-color: #87CEFA"  href="{{url('/jobcardform/update'.'/'.$job['job_id']) }}">Job Card</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link "  href="{{url('/elementform/update'.'/'.$job['job_id'].'/'.$job['io_id']) }}">Element Details</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{url('/rawform/update'.'/'.$job['job_id'].'/'.$job['io_id']) }}">Raw Material Details</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link disabled"  href="{{url('/bindingform/update'.'/'.$job['job_id'].'/'.$job['io_id']) }}">Binding Details</a>
                    </li>
                  </ul>
                  <br>
                <div class='box box-default'>
                <form action="/jobcard/updateDB/{{$job['job_id']}}" method="post" id="jcform">
                        @csrf
                        <div class="container-fluid">
                            <h3 class="box-title">{{__('jobcard.mytitle')}}</h3>
                            <br>
                            <div class="row">
                            {{-- <input type="hidden" name="job_id" value="{{$job['job_id']}}"> --}}
                                <div class="col-md-12">
                                    <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                    <input type="text" name="update_reason" required="" class="form-control input-css " id="update_reason">
                                    {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->
                            </div><br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.internalorder')}}<sup>*</sup></label>
                                    <select class="form-control select2" disabled id="internalorder" data-placeholder="" name="internalorder">
                                        <option value="default">Select internal order</option>
                                       @foreach ($io as $key)
                                       <option value="{{$key->id}}" {{ $job['io_id']==$key->id ? 'selected="selected"' : ''}} >{{$key->io_number}}</option>
                                       @endforeach
                                    </select>
                                    {!! $errors->first('internalorder', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.qty')}}<sup>*</sup></label>
                                <input type="number" name="qty" class="input-css leftqty" value="{{$job['job_qty']}}">
                                <p style="font-size:12px;color:green">Quantity in internal Order is : {{$job['qty']}}</p>
                                    {!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
                                </div>
                           
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.creative_name')}}<sup>*</sup></label>
                                    <input type="text" name="creative_name" class="input-css" value="{{$job['creative_name']}}">
                                    {!! $errors->first('creative_name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.open_size')}}<sup>*</sup></label>
                                    <input type="text" name="open_size" class="input-css" value="{{$job['open_size']}}" placeholder="Please Mention the unit of size">
                                    {!! $errors->first('open_size', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-4">
                                    <label for="">{{__('jobcard.close_size')}}<sup>*</sup></label>
                                    <input type="text" name="close_size" class="input-css" value="{{$job['close_size']}}" placeholder="Please Mention the unit of size">
                                    {!! $errors->first('close_size', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-4">
                                        <label>{{__('internal_order.dimension')}}<sup>*</sup></label>
                                        <select   value="{{ old('dimension') }}"   class="form-control select2"  data-placeholder="" style="width: 100%;" name="dimension">
                                                <option value="default">Select Dimension</option>
                                                <option value="m" {{ $job['dimension']=="m" ? 'selected="selected"' : ''}}>Metre</option>
                                                <option value="mm"{{ $job['dimension']=="mm" ? 'selected="selected"' : ''}}>Millimeter</option>
                                                <option value="cm"{{ $job['dimension']=="cm" ? 'selected="selected"' : ''}}>Centimeter</option>
                                                <option value="km"{{ $job['dimension']=="km" ? 'selected="selected"' : ''}}>Kilometer</option>
                                                <option value="in"{{ $job['dimension']=="in" ? 'selected="selected"' : ''}}>Inch</option>
                                                <option value="ft"{{ $job['dimension']=="ft" ? 'selected="selected"' : ''}}>Foot</option>
                                                <option value="ton"{{ $job['dimension']=="ton" ? 'selected="selected"' : ''}}>Ton</option>
                                                <option value="doz"{{ $job['dimension']=="doz" ? 'selected="selected"' : ''}}>Dozen</option>
                                                <option value="kg"{{ $job['dimension']=="kg" ? 'selected="selected"' : ''}}>Kilogram</option>
                                                <option value="g"{{ $job['dimension']=="g" ? 'selected="selected"' : ''}}>Grams</option>
                                        </select>
                                    {!! $errors->first('dimension', '<p class="help-block">:message</p>') !!}
                                   </div>
                                
                                    
                            </div><br>
                            <div class="row">
                            
                                <div class="col-md-4">
                                        <label for="" class="job_sample_label_er">{{__('jobcard.job_sample')}}<sup>*</sup></label>
                                        <div class="col-md-2">
                                            <div class="radio">
                                                <label><input autocomplete="off" type="radio" {{ $job->job_sample_received==1 ? 'checked="checked"' : ''}} class="job_sample" value="1" name="job_sample"> Yes </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="radio">
                                                <label><input autocomplete="off" type="radio" {{ $job->job_sample_received==0 ? 'checked="checked"' : ''}} class="job_sample" value="0" name="job_sample"> No </label>
                                            </div>
                                        </div>
                                        {!! $errors->first('job_sample', '<p class="help-block">:message</p>') !!}
                                    </div>
                                   <div class="col-md-4">
                                        <label for="">{{__('jobcard.item')}}<sup>*</sup></label>
                                        <select name="item" id="" class="form-control input-css item select2" disabled>
                                                <option value="default">Select Internal Order first</option>
                                                @foreach ($item as $key)
                                                <option value="{{$key->id}}" {{ $job['item_category_id']==$key->id ? 'selected="selected"' : ''}} >{{$key->name}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('item', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    @if($job['item_category_id']==15)
                                    <div class="col-md-4 other_item">
                                            <label>Other Item Category</label>
                                              <input type="text"  id="other_value" class="input-css" disabled value="{{$job['other_item_desc']}}">
                                        </div>
                                    @endif
                                   
                            </div><br>
                            <div class="row">
                            <div class="col-md-6">
                                            <label>{{__('internal_order.market')}}<sup>*</sup></label>
                                                <select   value="{{ old('market') }} "   class="select2 form-control input-css market" name="market" style="width:100%" disabled>
                                                        <option value="default">Select Internal Order</option>
                                                        @foreach($users as $key)
                                                        <option value="{{$key->id}}" {{ $job['marketing_user_id']==$key->id ? 'selected="selected"' : ''}} >{{$key->name}}</option>
                                                        @endforeach
                                                </select>    
                                                {!! $errors->first('market', '<p class="help-block">:message</p>') !!}
                                        </div>
                                  
                            </div><br>
                            <div class="row">
                                <div class="col-md-12">
                                        <label>{{__('jobcard.desc')}}<sup>*</sup></label>
                                <textarea name="desc" class="input-css form-control desc" id="" >{{$job['description']}}</textarea>
                                </div>
                            </div>
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

