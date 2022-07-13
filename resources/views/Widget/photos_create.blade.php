@extends($layout)

@section('title', 'Photos')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Photos</a></li>
   
@endsection
@section('js')
<script>
$(document).ready(function() {
    $.validator.addMethod("notValidIfSelectFirst", function(value, element, arg) {
        return arg !== value;
    }, "This field is required.");
    
    $('#form').validate({ // initialize the plugin
        
        rules: {

            album:{
                required: true
            },
            
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
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="/dashboard/photos/insert" method="post" id="form" enctype="multipart/form-data">
        @csrf 
        <div class="box box-default">
                <div class="box-header with-border">
                        
                    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
            <label>Album<sup>*</sup></label>
                <select name="album" id="" class="select2 input-css" required>
                <option value="">Select Album</option>
                @foreach($album as $key)
                    <option value="{{$key->id}}">{{$key->name}}</option>
                @endforeach
                </select>
                <label id="album-error" class="error" for="album"></label>
            </div>
            <div class="col-md-6">
                    <label>Select Multiple Photos<sup>*</sup></label>
                    <input type="file" name="file[]" multiple id="supp_challan" required  class="input-css">
            </div>
        </div><br><br> 
        </div>
        </div>
        </div>  
        <div class="row">
       
                            <div class="col-md-6">
                                <input type="submit" style="float:left" class="btn btn-primary" value="Submit">
                            </div>
                    </div> 
       
       
        </form>
      
      </section>
@endsection
