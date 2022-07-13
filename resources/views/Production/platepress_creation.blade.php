@extends($layout)

@section('title', 'Plate By Press Creation')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Plate By Press Creation </a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
@media (max-width: 768px)  
  {
    .content-header>h1 {
      display: inline-block;
    }
  }
  @media (max-width: 425px)  
  {
   
    .content-header>h1 {
      display: inline-block;
      
    }
  }
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
<script type="text/javascript">
  $(document).ready(function(){
    $(".is_plate").change(function(){
      var x = $(this).val();
      if(x == 1){
        $("#new_show").show();
      }else{
         $("#new_show").hide();
      }
    })
  })
</script>
<script src="/js/Production/platebypress_creation.js"></script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
          <div class="box-body">
          <div class="container-fluid">
            <br>
            <form action="/prod/platebypress/submitted" enctype="multipart/form-data" method="POST" id="plate_creationForm" >
              @csrf
              <div class="row">
                  <div class="col-md-6 {{ $errors->has('ref_name') ? 'has-error' : ''}}">
                      <label>Reference Name <sup>*</sup></label><br>
                      <input autocomplete="off" type="text" class="form-control input-css" name="demo"
                          value="{{ $fulldetails['referencename'] }}" disabled="disabled">
                       <input type="text" class="ref_name" name="ref_name" id="ref_name"
                      value="{{ $fulldetails['reference_name'] }}" style="display:none">
                      {!! $errors->first('ref_name', '<p class="help-block">:message</p>') !!}
                  </div>
                  <!--col-md-4-->
                  <div class="col-md-6 {{ $errors->has('jc_no') ? 'has-error' : ''}}">
                      <label>Job Card Number <sup>*</sup></label><br>
                      <input type="text" class="form-control input-css" name="demo"
                          value="{{ $fulldetails['job_number'] }}" disabled="disabled">
                      <input type="text" class="jc_no" name="jc_no" id="jc_no"
                      value="{{ $fulldetails['id'] }}" style="display:none">
                      {!! $errors->first('jc_no', '<p class="help-block">:message</p>') !!}
                  </div>
              </div><br>
              <div class="row">
                  <div class="col-md-6 {{ $errors->has('element') ? 'has-error' : ''}}">
                      <label>Element<sup>*</sup></label><br>
                      <select name="element" class="form-control input-css select2 element" id="element">
                        <option value="">Select Element</option>
                        @foreach($element as $key=>$value)
                          <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                      </select>
                      {!! $errors->first('element', '<p class="help-block">:message</p>') !!}
                  </div>
                  
                  <div class="col-md-6 {{ $errors->has('is_plate') ? 'has-error' : ''}}">
                      <label>Is Plate Size<sup>*</sup></label><br>
                      <select name="is_plate" class="select2 is_plate" id="is_plate">
                        <option value="">Select</option>
                        <option value="0">Old</option>
                        <option value="1">New</option>
                      </select>
                      {!! $errors->first('is_plate', '<p class="help-block">:message</p>') !!}
                  </div>
              </div><br>
              
              <div class="row" id="new_show" style="display:none">
                  <div class="col-md-6 {{ $errors->has('no_plate') ? 'has-error' : ''}}">
                     <label>Number Of Plates Planned <sup>*</sup></label><br>
                      <input type="number" class="form-control input-css no_plate" value="0" name="no_plate" id="no_plate"
                      onKeyPress="if(this.value.length==10) return false;">
                      {!! $errors->first('no_plate', '<p class="help-block">:message</p>') !!}
                  </div>
                  <div class="col-md-6 {{ $errors->has('pNew_date') ? 'has-error' : ''}}">
                     <label>Plates Date <sup>*</sup></label><br>
                      <input type="text" autocomplete="off" class="form-control input-css datepicker pNew_date" name="pNew_date" id="pNew_date">
                      {!! $errors->first('pNew_date', '<p class="help-block">:message</p>') !!}
                  </div>
                  <div class="col-md-12 {{ $errors->has('machine_name') ? 'has-error' : ''}}" style="margin-top: 20px;display: grid;">
                     <label>Machine Name <sup>*</sup></label>
                       <select name="machine_name" class="select2 form-control input-css machine_name" id="machine_name">
                        <option value="">Select Machine Name</option>
                         @foreach($machine as $mac)
                          <option value="{{ $mac->id}}">{{$mac->name}}</option>
                        @endforeach
                      </select>
                      {!! $errors->first('machine_name', '<p class="help-block">:message</p>') !!}
                  </div>
              </div><br>
               <div class="row">
                  <div class="col-md-6 {{ $errors->has('if_wastage') ? 'has-error' : ''}}">
                      <label>Wastage If Any</label><br>
                      <input type="number" class="input-css form-control if_wastage" value="0" name="if_wastage" id="if_wastage"
                      onKeyPress="if(this.value.length==10) return false;">
                      {!! $errors->first('if_wastage', '<p class="help-block">:message</p>') !!}
                  </div>
                  
                  <div class="col-md-6 {{ $errors->has('reason_wastage') ? 'has-error' : ''}}">
                      <label>Reason For Wastage </label>
                      <textarea class=" input-css" name="reason_wastage" id="reason_wastage"></textarea>
                      
                      {!! $errors->first('reason_wastage', '<p class="help-block">:message</p>') !!}
                  </div>
              </div><br>
               <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div><br>
            </form>
            </div>
            </div>
          </div>
                <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </section>
@endsection