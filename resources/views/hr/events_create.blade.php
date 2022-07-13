@extends($layout)

@section('title', 'Create Events')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Events</a></li>
   
@endsection

​
@section('js')
<script src="/js/hr/leave.js"></script>
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
       <form action="/hr/events/create" method="POST" id="form">
        @csrf
        <div class="box-header with-border">
            <div class='box box-default'> <br>
                <div class="container-fluid">
                  
                    <div class="row">
                            <div class="col-md-4 {{ $errors->has('date') ? 'has-error' : ''}}">
                                    <label>Event Date <sup>*</sup></label><br>
                                    <input type="text" autocomplete="off" name="date" id="date" class="datepickers input-css" required>
                                    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-4 {{ $errors->has('event') ? 'has-error' : ''}}">
                                    <label>Event Name <sup>*</sup></label><br>
                            <input type="text" name="event"  class="input-css event" value="{{old('event')}}" required>
                                    {!! $errors->first('event', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-4 {{ $errors->has('dept') ? 'has-error' : ''}}">
                                    <label>Department <sup></sup></label><br>
                                    <select class="input-css dept select2" style="padding-top:2px" name="dept[]" multiple>
                                <!-- <option value="">{{__('registration.sel_dept')}}</option> -->
                                @foreach($department as $dept)
                                 <option value="{{$dept->id}}">{{$dept->department}}</option>
                                @endforeach
                            </select>
                                    {!! $errors->first('dept', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                 
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
