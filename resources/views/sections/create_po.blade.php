
@extends($layout)

@section('title', __('po.title'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('po.mytitle')}}</i></a></li>
  
@endsection
@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="css/party.css">

@endsection

@section('js')
<script src="js/views/party.js"></script>
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
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('po.mytitle')}}</h2><br><br><br>
                    <div class="container-fluid">
                        <form method="post" action="POinsert" method="POST" id="form">
                                @csrf
                            <div class="row">
                                <div class="col-md-4 {{ $errors->has('party_name') ? 'has-error' : ''}}">
                                    <label>{{__('po.party')}} <sup>*</sup></label><br>
                                    <select class="form-control select2"  data-placeholder="" style="width: 100%;" name="party_name">
                                        <option value="">Select Party</option>
                                        @foreach($party as $key)
                                        <option value="{{$key->id}}">{{$key->partyname}}</option>
                                        @endforeach
                                        </select>
                                    {!! $errors->first('party_name', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->

                                <div class="col-md-4 {{ $errors->has('po_number') ? 'has-error' : ''}}">
                                    <label>{{__('po.number')}} <sup>*</sup></label><br>
                                    <input type="text" class="form-control input-css" name="po_number" value="{{ old('po_number') }}">
                                    {!! $errors->first('po_number', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-6-->

                                <div class="col-md-4 {{ $errors->has('po_date') ? 'has-error' : ''}}">
                                        <label>{{__('po.date')}} <sup>*</sup></label><br>
                                        <input type="text" class="form-control input-css datepicker1" name="po_date" value="{{ old('po_date') }}">
                                        {!! $errors->first('po_date', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><!--row-->

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

