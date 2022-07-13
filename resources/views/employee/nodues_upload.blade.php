@extends($layout)

@section('title', 'No Dues Upload')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> No Dues Upload</a></li>
   
@endsection
@section('js')

<script>
  $(".letter_start_date").datepicker({
        endDate:'today',
        format: 'd-m-yyyy',
        orientation: "bottom"
    });
</script>
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
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
       <form action="" method="POST" id="form" enctype="multipart/form-data">
        @csrf

       <div class="box box-header">
           <br>

            <div class="row" >
                <div class="col-md-6 {{ $errors->has('empName') ? ' has-error' : ''}}">
                    <label for="">Employee <sup>*</sup></label>
                    <select name="empName" class="empName input-css select2" required>
                        <option value="">Select Employee</option>
                        @foreach($employee as $emp)
                            <option value="{{$emp->id}}">{{$emp->name}}</option>
                        @endforeach
                    </select>
                    {!! $errors->first('empName', '<p class="help-block">:message</p>') !!} 
                </div>
                <div class="col-md-6 {{ $errors->has('letter_upload_pr') ? ' has-error' : ''}}"">
                     <label for="">No Due Letter Upload <sup>*</sup></label>
                    <input type="file" name="letter_upload_pr" 
                    accept="application/pdf" class="letter_upload_pr" required="">
                    {!! $errors->first('letter_upload_pr', '<p class="help-block">:message</p>') !!}
                </div>
            </div><br><br>
            <div class="row" >
                <div class="col-md-6 {{ $errors->has('letter_start_date') ? ' has-error' : ''}}"">
                     <label for="">No Due Start Date<sup>*</sup></label>
                    <input type="text" autocomplete="off" name="letter_start_date" class="input-css letter_start_date" required="">
                    {!! $errors->first('letter_start_date', '<p class="help-block">:message</p>') !!}
                </div>
            </div><br><br>
 
        <div class="row">
                <div class="col-md-12">
                    
                      <input type="submit" class="btn btn-primary">
                    
                </div>
            </div>
        </form>
      
      </section>
@endsection
