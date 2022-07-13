@extends($layout)

@section('title', __('view_party.title') )

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class=""></i> {{__('view_party.title')}}</a></li>

@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                    <!-- <img class="profile-user-img img-responsive img-circle" src="/images/avatar.png" alt="User profile picture"> -->
        
                    <h3 class="profile-username text-center">{{$party->partyname}}</h3>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                        <b>{{__('view_party.reference_name')}}</b> <a class="pull-right">{{$party->referencename}}</a>
                        </li>
                        <li class="list-group-item">
                        <b>{{__('view_party.contact')}}</b> <a class="pull-right">{{ $party->contact }}</a>
                        </li>
                        <li class="list-group-item">
                        <b>{{__('view_party.alt_contact')}}</b> <a class="pull-right">{{ $party->alt_contact }}</a>
                        </li>
                        <li class="list-group-item">
                        <b>{{__('view_party.email')}}</b> <a class="pull-right">{{ $party->email }}</a>
                        </li>
                    </ul>
        
                    <!-- <a href="client/update?id='{{$party->id}}'" class="btn btn-primary btn-block"><b>{{__('view_party.edit')}}</b></a> -->
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
            <!-- /.col-md-3 -->

            <div class="col-md-8">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{__('view_party.mrd')}}</h3>
                    </div>
                    <div class="box-body">
            
                        <div class="row">
                                <div class="col-md-3"><b>{{__('view_party.contact_person')}}:</b></div> <div class="col-md-8">{{ $party->contact_person }}</div>
                        </div>
                        {{-- end of row --}}
                        
                        <div class="row">
                                <div class="col-md-3"><b>{{__('view_party.payment_term')}}:</b></div> 
                                <div class="col-md-8">{{ $party->payment_term }}</div>
                        </div>
                        {{-- end of row --}}
            
                        <div class="row">
                            <div class="col-md-3"><b>{{__('view_party.pan')}}:</b></div> 
                            <div class="col-md-8">{{ $party->pan }}</div>
                        </div>
                        {{-- end of row --}}
            
                        <div class="row">
                            <div class="col-md-3"><b>{{__('view_party.gst')}}:</b></div> 
                            <div class="col-md-8">{{ $party->gst }}</div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>{{__('view_party.createdby')}}:</b></div> 
                            <div class="col-md-8">{{ $party->username }}</div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>{{__('view_party.createddate')}}:</b></div> 
                            <div class="col-md-8">{{ $party->created_time }}</div>
                            
                        </div>
                        {{-- end of row --}}
            
                    </div>
                    <!-- /.box-body -->
                    </div>
                </div>
            </div>

            <!-- About Me Box -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">

                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-2">
                                        <strong><i class="fa fa-map-marker margin-r-5"></i> {{__('view_party.addr')}}</strong>
                                </div>
                                <div class="col-md-10">
                                        <p class="text-muted">
                                                {{ $party->address }}
                                            </p>
                                
                                </div>
                            </div>
                            
                
                           
                            <hr>
                <div class="row">
                    <div class="col-md-2">
                            <strong><i class="fa fa-location-arrow margin-r-5"></i> {{__('view_party.city')}}</strong>
                    </div>
                    <div class="col-md-10">
                            <p class="text-muted">{{ $party->city }}</p>
                    </div>
                </div>
                           
                
                            
                            <hr>
                            <div class="row">
                                    <div class="col-md-2">
                                            <strong><i class="fa fa-location-arrow margin-r-5"></i> {{__('view_party.state')}}</strong>
                                    </div>
                                    <div class="col-md-10">
                                            <p class="text-muted">{{ $party->state }}</p>
                                    </div>
                                </div>
                                <hr>
                 <div class="row">
                    <div class="col-md-2">
                            <strong><i class="fa fa-location-arrow margin-r-5"></i> {{__('view_party.pincode')}}</strong>
                    </div>
                    <div class="col-md-10">
                            <p class="text-muted">{{ $party->pincode }}</p>
                    </div>
                </div>
                            
                
                            
                            <hr>
                            <div class="row">
                                    <div class="col-md-2">
                                            <strong><i class="fa fa-plane margin-r-5"></i> {{__('view_party.country')}}</strong>
                                    </div>
                                    <div class="col-md-10">
                                            <p class="text-muted">{{ $party->country }}</p>
                                    </div>
                                </div>
                           
                
                           
        
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>
            <!-- /row -->

        </div>
        <!-- /.row -->


    </section>
@endsection