@extends($layout)

@section('title', 'Update Recruitment Info')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Recruitment</a></li>
   
@endsection

​
@section('js')
<script src="/js/hr/recruitment.js"></script>
<script>
 var currentDate = new Date();
$('.datepickers').datepicker({
    format: 'dd-mm-yyyy',
        autoclose: true,
        startDate:currentDate,
});
</script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
        <form files="true" enctype="multipart/form-data" action="/hr/recruitment/data/update/{{$id}}" method="POST" id="form" >
        @csrf
        <div class="box-header with-border">
            <div class='box box-default'> <br>
                <div class="container-fluid">
                        <div class="row">
                                <div class="col-md-12 {{ $errors->has('update_reason') ? 'has-error' : ''}}">
                                        <label>Update Reason<sup>*</sup></label>
                                        <input   type="text" autocomplete="off" required value="{{$errors->any() ? old('update_reason') : ''}}" class="input-css update_reason" id="update"  name="update_reason">
                                        {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-3-->
                        </div><br>
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('name') ? 'has-error' : ''}}">
                                <label>Name <sup>*</sup></label><br>
                        <input type="text" name="name" id="name" class="name input-css" value="{{$hr->name}}">
                                {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('email') ? 'has-error' : ''}}">
                                    <label>Email Address <sup>*</sup></label><br>
                                    <input type="text" name="email" id="email" class="email input-css" value="{{$hr->email}}">
                                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('contact') ? 'has-error' : ''}}">
                                    <label>Contact number <sup>*</sup></label><br>
                            <input type="number" name="contact" id="contact" class="input-css contact" value="{{$hr->contact}}">
                                    {!! $errors->first('contact', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('reference') ? 'has-error' : ''}}">
                                    <label>Reference From <sup>*</sup></label><br>
                                    <input type="text" name="reference" id="reference" class="reference input-css" value="{{$hr->reference_from}}">
                                    {!! $errors->first('reference', '<p class="help-block">:message</p>') !!}
                            </div>
                        
                            <div class="col-md-6 {{ $errors->has('interview_date') ? 'has-error' : ''}}">
                                    <label>Interview Date <sup>*</sup></label><br>
                                    <input type="text" autocomplete="off" name="interview_date" id="interview_date" class="interview_date input-css datepicker1" value="{{$hr->interview_date}}">
                                    {!! $errors->first('interview_date', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('position') ? 'has-error' : ''}}">
                                    <label>Position Interviewed For<sup>*</sup></label><br>
                                    <input type="text" name="position" id="position" class="position input-css" value="{{$hr->position_for}}">
                                    {!! $errors->first('position', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('resume') ? 'has-error' : ''}}">
                                    <label>Resume<sup></sup></label><br>
                                    <input  type="file" name="resume" id="resume" class="resume input-css" >
                                    <p>Allowed Formats: pdf,jpg,png .</p>
                                    {!! $errors->first('resume', '<p class="help-block">:message</p>') !!}
                                    @if(isset($hr->resume))
                                        @if (file_exists(public_path().'/upload/recruitment_resume/'.$hr->resume))
                                            <a href="/upload/recruitment_resume/{{$hr->resume}}" target="_blank"><u>View File</u></a>
                                        @endif
                                    @endif
                                        
                                    <input type="hidden" name="resume_old" value="{{$hr->resume}}">
                            </div>
                    </div><br>
                     <div class="row">
                        <div class="col-md-12 {{ $errors->has('address') ? 'has-error' : ''}}">
                                <label>Address <sup>*</sup></label><br>
                                <textarea name="address" id="address" class="input-css address" required="">{{$hr->address}}</textarea>
                                {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-12 { $errors->has('remark') ? 'has-error' : ''}}" >
                                <label>Remark <sup>*</sup></label><br>
                                <textarea name="remark" id="remark" class="input-css reason" cols="30" rows="5">{{$hr->remark}}</textarea>
                        </div>
                    </div>
                </div>  <br>  <br>
            </div>
        </div>
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
