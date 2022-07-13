@extends($layout)

@section('title', __('jobdetails.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> DashBoard</a></li>
 
@endsection
@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="css/jobdetails.css">

@endsection

@section('js')
  {{-- <script src="js/adminlte.min.js"></script> --}}
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
 $(document).ready(function () {
 $('#form').validate({ 
   
     rules: {
         date: {
             required: true
         },
         cre: {
             required: true
         },
         qty: {
             required: true
         },
      
         
        
     }
 });
});
</script>
@endsection

@section('main_section')
<section class="content">
  
  <!-- SELECT2 EXAMPLE -->

  <div class="box box-default">
    <div class="box-header with-border">
    <h3 class="box-title">{{__('jobdetails.title')}}</h3>
    <div class='container-fluid'>
        <form method="post" action="{{url('forms')}}" id="form">
          @csrf 
    
    <div class="row">
      <div class="col-md-4">
          <label>{{__('jobdetails.date')}}<sup>*</sup></label>
          <input type="date" id="input-name" class="form-control" name="date">
        </div><!--end of col-md-4-->

        <div class="col-md-4">
            <label>{{__('jobdetails.IO')}} <sup>*</sup></label>
                <select class="form-control select2">
                  <option selected="selected"></option>
                  <option>nan</option>
                  <option>tan</option>
                  <option>Bro</option>
                  </select>
          </div><!--end of col-md-4-->

          <div class="col-md-4">
              <label>{{__('jobdetails.cre name')}}<sup>*</sup></label>
              <input type="text" class="form-control" name="cre">
            </div><!--end of col-md-4-->
        </div><!--end of row-->

        <div class="row">
            <div class="col-md-3">
                <label>{{__('jobdetails.qty')}}<sup>*</sup></label>
                <input type="text" class="form-control" name="qty">
              </div><!--end of col-md-4-->
            <div class="col-md-3">
                <label>{{__('jobdetails.IC')}} <sup>*</sup></label>
                <select class="form-control select2">
                  <option selected="selected"></option>
                  <option>nan</option>
                  <option>tan</option>
                  <option>Bro</option>
                  </select>
              </div><!--end of col-md-4-->
 
              <div class="col-md-3">
                  <label>{{__('jobdetails.OS')}} <sup>*</sup></label>
                  <select class="form-control select2">
                    <option selected="selected"></option>
                    <option>nan</option>
                    <option>tan</option>
                    <option>Bro</option>
                    </select>
                </div><!--end of col-md-4-->

                <div class="col-md-3">
                    <label>{{__('jobdetails.CS')}} <sup>*</sup></label>
                    <select class="form-control select2">
                      <option selected="selected"></option>
                      <option>nan</option>
                      <option>tan</option>
                      <option>Bro</option>
                      
                      </select>
                  </div><!--end of col-md-4-->
          </div><!--end of row-->
          
          <div class="row">
            <div class="col-md-4">
                <label>{{__('jobdetails.paperby')}} <sup>*</sup></label><br>
                <input type="radio" name="abc" value="press"> Press<br>
                <input type="radio" name="abc" value="party"> Party<br>
                   
            </div><!--end of col-md-4-->

            <div class="col-md-4">
                <label>{{__('jobdetails.plateby')}} <sup>*</sup></label><br>
                <input type="radio" name="abc" value="press"> Press<br>
                <input type="radio" name="abc" value="party"> Party<br>
                   
            </div><!--end of col-md-4-->

            <div class="col-md-4">
                
                  <label>{{__('jobdetails.jobsample')}} <sup>*</sup></label><br>
                    <input type="radio" name="abc" value="press"> Press<br>
                    <input type="radio" name="abc" value="party"> Party<br>
                     
               
            </div><!--end of col-md-4-->
            
          </div><!--end of row-->

          <div class='row row'>
            </div><!--end of row-->
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>




</form>
</div><!--end of container-fluid-->
      </div><!--end of box-header with-border-->
    </div><!--end of box box-default-->
  </section><!--end of section-->
@endsection
