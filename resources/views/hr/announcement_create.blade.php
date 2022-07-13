@extends($layout)

@section('title', 'Create Announcement')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Announcement</a></li>
   
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
       <form action="/hr/announcements/create" method="POST" id="form" enctype="multipart/form-data">
        @csrf
        <div class="box-header with-border">
            <div class='box box-default'> <br>
                <div class="container-fluid">
                  
                    <div class="row">
                            <div class="col-md-4 {{ $errors->has('date') ? 'has-error' : ''}}">
                                    <label>Expiry Date <sup>*</sup></label><br>
                                    <input type="text"  autocomplete="off" name="date" id="date" class="datepickers input-css" required>
                                    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-4 {{ $errors->has('event') ? 'has-error' : ''}}">
                                    <label>Announcement  <sup>*</sup></label><br>
                            <input type="text" name="event"  class="input-css event" value="{{old('event')}}" required>
                                    {!! $errors->first('event', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-4 {{ $errors->has('dept') ? 'has-error' : ''}}">
                                    <label>Department <sup></sup></label><br>
                                    <select class="input-css dept select2" style="padding-top:2px" name="dept[]" multiple>
                                @foreach($department as $dept)
                                 <option value="{{$dept->id}}">{{$dept->department}}</option>
                                @endforeach
                            </select>
                                    {!! $errors->first('dept', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
            <div class="col-md-12">
                    <label>Pic (If Any)<sup></sup></label>
                    <input type="file" name="file"  id="supp_challan"   class="input-css">
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
