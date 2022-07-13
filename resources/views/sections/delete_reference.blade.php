@extends($layout)

@section('title', 'Reference')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Delete And Substitute Reference</a></li>
   
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
        <form action="/reference/delete?id={{$id}}" method="POST" id="form">
        @csrf

       <div class="box box-header">
           <br>
           
        <div class="row" >
            <div class="col-md-6 {{ $errors->has('reference') ? ' has-error' : ''}}">
                <label for="">Delete Reference Name <sup>*</sup></label>
                <select name="reference" id="" class="reference input-css select2" required disabled>
                    @foreach ($reference_name as $item)
                    <option value="{{$item->id}}" {{$item->id==$id ? "selected=selected" : ''}}>{{$item->referencename}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="delete_reference" value="{{$id}}">
                 <p class="help-block" style="color:green">Note:-  Reference Name will get Updated in all Parties associated with this Reference Name.</p>
                {!! $errors->first('reference', '<p class="help-block">:message</p>') !!} 
            </div>
            <div class="col-md-6 {{ $errors->has('reference') ? ' has-error' : ''}}">
                    <label for="">Substitue Reference Name <sup>*</sup></label>
                    <select name="sub_reference" id="" class="reference input-css select2" required>
                        <option value="">Select reference name</option>
                        @foreach ($reference_name as $item)
                        <option value="{{$item->id}}">{{$item->referencename}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="delete_reference" value="{{$id}}">
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
