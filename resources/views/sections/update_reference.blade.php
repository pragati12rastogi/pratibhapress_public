@extends($layout)

@section('title', 'Reference')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Update Reference</a></li>
   
@endsection
@section('js')
   <script>
   $(document).ready(function() {
    
    // validation for drop downs
    // they must have first option with value="default"
    $.validator.addMethod("notValidIfSelectFirst", function(value, element, arg) {
        return arg !== value;
    }, "This field is required.");
    
    $('#form').validate({ // initialize the plugin
        rules: {
   
            reference: {
                required: true
            }
          
        }
    });

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
        <form action="/reference/update?id={{$reference_name->id}}" method="POST" id="form">
        @csrf

       <div class="box box-header">
           <br>
           
        <div class="row" >
                <div class="col-md-6 ">
                        <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                        <input type="text" name="update_reason" required class="input-css" id="update_reason">
                        {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                    </div><!--col-md-4-->
            <div class="col-md-6 {{ $errors->has('reference') ? ' has-error' : ''}}">
                <label for="">Reference Name <sup>*</sup></label>
            <input type="text" name="reference" value="{{$reference_name->referencename}}" id="" class="reference input-css" required>
            <p class="help-block" style="color:green">Note:-  Reference Name will get Updated in all Parties associated with this Reference Name.</p>
                {!! $errors->first('reference', '<p class="help-block">:message</p>') !!} 
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
