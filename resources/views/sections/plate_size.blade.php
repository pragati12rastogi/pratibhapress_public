@extends($layout)

@section('title', 'Plate Size')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="{{url('/master/plate/size/list')}}"><i class=""></i>Plate List</a></li>
    <li><a href="#"><i class=""></i>Create Plate</a></li>
   
@endsection
@section('css')
<style>
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
</style>
@endsection
@section('js')

@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
       <form action="/master/create/plate/size" method="POST" id="form" enctype="multipart/form-data">
        @csrf

       <div class="box box-header">
           <br>
        
        <div class="row" >
                <div class="col-md-6 {{ $errors->has('ps_value') ? ' has-error' : ''}}">
                    <label for="">Plate Size Value <sup>*</sup></label>
                    <input type="text" name="ps_value" id="" class="ps_value input-css" maxlength="50" required=''>
                    {!! $errors->first('ps_value', '<p class="help-block">:message</p>') !!} 
                </div>
     </div><br><br>
     
       </div>
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
