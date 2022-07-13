
@extends($layout)
@section('title', __('consignee_view.consignee_view'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="{{url('consignee/list')}}"><i class="">{{__('consignee_view.consignee')}}</i></a></li>
<li><a href="#"><i class="">{{__('consignee_view.consignee_view')}}</i></a></li>
@endsection

@section('main_section')
    <section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
<!-- Default box -->
    @php
        $data = $consignee[0];
    @endphp
    <div class="box-header with-border">
        <div class='box box-default'><br>
            <div class="container-fluid">
                <div class="row ">
                   
                </div>
                    
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.consignee_name')}}</label></div>
                    <div class="col-md-6 " >{{$data['consignee_name']}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.client_name')}}</label></div>
                    <div class="col-md-6 " >{{$data['partyname']}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.created_by')}}</label></div>
                    <div class="col-md-6 " >{{$data['name']}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.created_date')}}</label></div>
                    <div class="col-md-6 " >{{CustomHelpers::showDate($data['created_time'])}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.gst')}}</label></div>
                    <div class="col-md-6 " >{{$data['gst']}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.pan')}}</label></div>
                    <div class="col-md-6 " >{{$data['pan']}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.address')}}</label></div>
                    <div class="col-md-6 " >{{$data['address']}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.pin')}}</label></div>
                    <div class="col-md-6 " >{{$data['pincode']}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.city')}}</label></div>
                    <div class="col-md-6 " >{{$data['city']}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.state')}}</label></div>
                    <div class="col-md-6 " >{{$data['state']}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " ><label>{{__('consignee_view.country')}}</label></div>
                    <div class="col-md-6 " >{{$data['country']}}</div>
                </div>
               
            </div>
        </div>
    </div>
                
</section>
@endsection
