@extends($layout)

@section('title', __('Employee/document.title'))

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="/employee/profile/list"><i class=""></i> Employee List</a></li>
    <style>
        .nav1>li>a {
            position: relative;
            display: block;
            padding: 10px 34px;
            background-color: white;
            margin-left: 10px;
        }
        .img_hover{
            cursor: pointer;
        }
        /* .nav1>li>a:hover {
            background-color:#87CEFA;
        
        } */
        </style>
@endsection
@section('js')
<script src="/js/Employee/pfesi.js"></script>
<script>

</script>
@endsection
@section('main_section')
    <section class="content">
        <div id="app">
            @include('sections.flash-message')
            @yield('content')
        </div>
        <!-- Default box -->
     <form action="/employee/document/update/{{$id}}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('layouts.employee_tab')
              
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/document.mytitle')}}</h3><br><br><br>
                <div class="container-fluid wdt">
                    <div class="row">
                        <div class="col-md-3 ">
                            <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                            <input type="text" name="update_reason" required="" class="input-css" id="update_reason">
                            {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                        </div><!--col-md-4-->
                    </div>
                    <br><br>   
                </div>
            </div>
        </div>
        <div class='box box-default'>  <br>
            <div class="container-fluid wdt"> 
                 <div class="row">           
                 
                        <div class="col-md-6">
                            @if(isset($document['aadhar']['document_file']))
                                @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['aadhar']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['aadhar']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_ad">See your Aadhar Card</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['aadhar']['document_file']}}" height="50" width="100" data-toggle="modal" data-target="#myModal_ad" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.aadhar')}} </label>
                            <input type="file" name="file[aadhar]" value="@if(isset($document['aadhar']['document_file'])){{$document['aadhar']['document_file']}} @endif" id="aadhar" class="input-css">
                            {!! $errors->first('file.aadhar', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div id="myModal_ad" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Adhaar Detail</h4>
                                    </div>
                                    <div class="modal-body" style="overflow:auto;height:450px;">
                                        @if(isset($document['aadhar']['document_file']))
                                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['aadhar']['document_file']))
                                                <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['aadhar']['document_file'], PATHINFO_EXTENSION); ?>
                                                @if ($ext == 'pdf')
                                                    <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['aadhar']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                                @else
                                                    <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['aadhar']['document_file']}}"  width="100%">
                                                @endif
                                            @endif
                                        @endif
                                       
                                    </div>
                                    <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 {{ $errors->has('pan') ? 'has-error' : ''}}">
                            @if(isset($document['pan']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['pan']['document_file']))
                                    <?php 
                                        
                                        $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['pan']['document_file'], PATHINFO_EXTENSION);
                                        
                                    ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_pan">See your PAN Document</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['pan']['document_file']}}" height="50" width="100" data-toggle="modal" data-target="#myModal_pan" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.pan')}} </label>
                            <input type="file" name="file[pan]" value="@if(isset($document['pan']['document_file'])){{$document['pan']['document_file']}} @endif" id="pan" class="pan input-css">
                            {!! $errors->first('file.pan', '<p class="help-block">:message</p>') !!}
                        </div>
                </div><br><br> 
                <div id="myModal_pan" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
        
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">PAN Card</h4>
                            </div>
                            <div class="modal-body" style="overflow:auto;height:450px;">
                                @if(isset($document['pan']['document_file']))
                                    @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['pan']['document_file']))
                                            <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['pan']['document_file'], PATHINFO_EXTENSION); ?>
                                            @if ($ext == 'pdf')
                                            <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['pan']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                            @else
                                                <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['pan']['document_file']}}" width="100%">
                                        @endif
                                    @endif
                                @endif
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="col-md-6 {{ $errors->has('bank_file') ? 'has-error' : ''}}">
                            @if(isset($document['bank']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['bank']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['bank']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_bank">See your Bank Document</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['bank']['document_file']}}" height="50" width="100" data-toggle="modal" data-target="#myModal_bank" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.bank')}} </label>
                            <input type="file" name="file[bank]" value="@if(isset($document['bank']['document_file'])){{$document['bank']['document_file']}} @endif" id="bank" class="bank input-css">
                            {!! $errors->first('file.bank', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div id="myModal_bank" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Bank Documents</h4>
                                    </div>
                                    <div class="modal-body" style="overflow:auto;height:450px;">
                                        @if(isset($document['bank']['document_file']))
                                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['bank']['document_file']))
                                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['bank']['document_file'], PATHINFO_EXTENSION); ?>
                                                    @if ($ext == 'pdf')
                                                    <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['bank']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                                    @else
                                                        <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['bank']['document_file']}}" width="100%">
                                                @endif
                                            @endif
                                        @endif
                                       
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 {{ $errors->has('salary_slip') ? 'has-error' : ''}}">
                            @if(isset($document['salary_slip']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['salary_slip']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['salary_slip']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_sal">See your Salary slip</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['salary_slip']['document_file']}}" height="50" width="100" data-toggle="modal" data-target="#myModal_sal" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.salary_slip')}} </label>
                            <input type="file" name="file[salary_slip]" value="@if(isset($document['salary_slip']['document_file'])){{$document['salary_slip']['document_file']}} @endif" id="salary_slip" class="salary_slip input-css">
                            {!! $errors->first('file.salary_slip', '<p class="help-block">:message</p>') !!}
                        </div>
                </div><br><br>
                <div id="myModal_sal" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Salary slip</h4>
                                    </div>
                                    <div class="modal-body" style="overflow:auto;height:450px;">
                                    @if(isset($document['salary_slip']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['salary_slip']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['salary_slip']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                    <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['salary_slip']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['salary_slip']['document_file']}}" width="100%">
                                   @endif
                                @endif
                                @endif
                                        
                                       
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                <div class="row">
                        <div class="col-md-6 {{ $errors->has('photo') ? 'has-error' : ''}}">
                             @if(isset($document['photo']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['photo']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['photo']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_ph">See your Photo</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['photo']['document_file']}}" height="50" width="100" data-toggle="modal" data-target="#myModal_ph" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.photo')}} </label>
                            <input type="file" name="file[photo]" value="@if(isset($document['photo']['document_file'])){{$document['photo']['document_file']}} @endif" id="photo" class="photo input-css">
                            {!! $errors->first('file.photo', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div id="myModal_ph" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Photo</h4>
                                    </div>
                                    <div class="modal-body" style="overflow:auto;height:450px;">
                                    @if(isset($document['photo']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['photo']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['photo']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                    <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['photo']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['photo']['document_file']}}" width="100%">
                                   @endif
                                @endif
                                @endif
                                    
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 {{ $errors->has('high_school') ? 'has-error' : ''}}">
                            @if(isset($document['high_school']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['high_school']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['high_school']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_hi">See your High school document</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['high_school']['document_file']}}" height="50" width="100" data-toggle="modal" data-target="#myModal_hi" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.hsc')}} </label>
                            <input type="file" name="file[high_school]" value="@if(isset($document['high_school']['document_file'])){{$document['high_school']['document_file']}} @endif" id="high_school" class="high_school input-css">
                            {!! $errors->first('file.high_school', '<p class="help-block">:message</p>') !!}
                        </div>
                </div><br><br>
                <div id="myModal_hi" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">High School Document</h4>
                            </div>
                            <div class="modal-body" style="overflow:auto;height:450px;">
                                @if(isset($document['high_school']['document_file']))
                                    @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['high_school']['document_file']))
                                            <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['high_school']['document_file'], PATHINFO_EXTENSION); ?>
                                            @if ($ext == 'pdf')
                                            <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['high_school']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                            @else
                                                <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['high_school']['document_file']}}" width="100%">
                                        @endif
                                    @endif
                                @endif
                        
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="col-md-6 {{ $errors->has('intermediate') ? 'has-error' : ''}}">
                            @if(isset($document['intermediate']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['intermediate']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['intermediate']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_in">See your Intermediate Certificate</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['intermediate']['document_file']}}" height="50" width="100" data-toggle="modal" data-target="#myModal_in" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.ic')}} </label>
                            <input type="file" name="file[intermediate]" value="@if(isset($document['intermediate']['document_file'])){{$document['intermediate']['document_file']}} @endif" id="intermediate" class="intermediate input-css">
                            {!! $errors->first('file.intermediate', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div id="myModal_in" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Intermediate Document</h4>
                            </div>
                            <div class="modal-body" style="overflow:auto;height:450px;">
                            @if(isset($document['intermediate']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['intermediate']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['intermediate']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                    <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['intermediate']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['intermediate']['document_file']}}" width="100%">
                                   @endif
                                @endif
                                @endif
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                        <div class="col-md-6 {{ $errors->has('gc_file') ? 'has-error' : ''}}">
                            @if(isset($document['graduation']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['graduation']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['graduation']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_gr">See your Graduation Certificate</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['graduation']['document_file']}}" height="50" width="100" data-toggle="modal" data-target="#myModal_gr" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.gc')}} </label>
                            <input type="file" name="file[graduation]" value="@if(isset($document['graduation']['document_file'])){{$document['graduation']['document_file']}} @endif" id="graduation" class="graduation input-css">
                            {!! $errors->first('file.graduation', '<p class="help-block">:message</p>') !!}
                        </div>
                </div><br><br>
                <div id="myModal_gr" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Graduation Document</h4>
                            </div>
                            <div class="modal-body" style="overflow:auto;height:450px;">
                            @if(isset($document['graduation']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['graduation']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['graduation']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                    <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['graduation']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['graduation']['document_file']}}"width="100%">
                                   @endif
                                @endif
                                @endif
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="col-md-6 {{ $errors->has('higher_degree') ? 'has-error' : ''}}">
                            @if(isset($document['higher_degree']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['higher_degree']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['higher_degree']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_deg">See your Higher Degree</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['higher_degree']['document_file']}}" height="50" width="100"data-toggle="modal" data-target="#myModal_deg" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.hd')}} </label>
                            <input type="file" name="file[higher_degree]" value="@if(isset($document['higher_degree']['document_file'])){{$document['higher_degree']['document_file']}} @endif" id="higher_degree" class="higher_degree input-css">
                            {!! $errors->first('file.higher_degree', '<p class="help-block">:message</p>') !!}
                        </div>
                    <div id="myModal_deg" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Higher Degree Document</h4>
                            </div>
                            <div class="modal-body" style="overflow:auto;height:450px;">
                            @if(isset($document['higher_degree']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['higher_degree']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['higher_degree']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                    <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['higher_degree']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['higher_degree']['document_file']}}" width="100%">
                                   @endif
                                @endif
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                        <div class="col-md-6 {{ $errors->has('other') ? 'has-error' : ''}}">
                             @if(isset($document['other']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['other']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['other']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="#" data-toggle="modal" data-target="#myModal_ot">See your Other Document</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['other']['document_file']}}" height="50" width="100"data-toggle="modal" data-target="#myModal_ot" class="img_hover">
                                   @endif
                                @endif
                                @endif
                            <label for="">{{__('Employee/document.other')}} </label>
                            <input type="file" name="file[other]" value="@if(isset($document['other']['document_file'])){{$document['other']['document_file']}} @endif" id="other" class="other input-css">
                            {!! $errors->first('file.other', '<p class="help-block">:message</p>') !!}
                        </div>
                </div><br><br>
                <div id="myModal_ot" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Other Document</h4>
                            </div>
                            <div class="modal-body" style="overflow:auto;height:450px;">
                            @if(isset($document['other']['document_file']))
                            @if (file_exists(public_path().'/upload/employee/'.$id.'/document/' .$document['other']['document_file']))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/document/'.$document['other']['document_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                    <embed src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['other']['document_file']}}" type="application/pdf" frameborder="0" width="100%" height="400px">
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/document/{{$document['other']['document_file']}}" width="100%">
                                   @endif
                                @endif
                                @endif
                           
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
        <div class="row">
            <div class="col-md-12">
                <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
            </div>
        </div><br><br>
    </form>
      
    </section>
@endsection
