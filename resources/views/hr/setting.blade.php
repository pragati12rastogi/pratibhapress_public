@extends($layout)

@section('title', 'Create Leave Authority')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Leave</a></li>
   
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
var hr1="{{$hr1['value']}}";
var hr1=hr1.split(',');
$('.name1').val(hr1).select2();


</script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
       <form action="/hr/setting" method="POST" id="form">
        @csrf
        <div class="box-header with-border">
            <div class='box box-default'> <br>
                <div class="container-fluid">
                    <h3>Level 1</h3>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('name1') ? 'has-error' : ''}}">
                                <label>Name <sup>*</sup></label><br>
                                <select name="name1[]" id="name1" class="name1 select2 input-css" multiple>
                                    <option value="" disabled>Select Name</option>
                                    @foreach ($emp as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('name1', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!-- <div class="col-md-6 {{ $errors->has('level1') ? 'has-error' : ''}}">
                                <label>Level 1 <sup>*</sup></label><br>
                                <select name="level1" id="level1" class="level1 select2 input-css">
                                        <option value="Level1">Level 1</option>
                                    </select>
                                {!! $errors->first('level1', '<p class="help-block">:message</p>') !!}
                        </div> -->
                    </div><br><br>
                    <div class="row">
                <div class="col-md-12">
                     <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
                </div>
            </div> 
                </div>  <br>  <br>
            </div>
        </div>

      
     
        </form>
      
      </section>
@endsection
