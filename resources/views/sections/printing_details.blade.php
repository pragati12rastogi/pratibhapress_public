
@extends($layout)

@section('title', __('binding.print_title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class=""> {{__('binding.print')}}</i></a></li>
@endsection
@section('css')
@endsection

@section('js')
<script src="/js/views/printing.js"></script>
<script>
var elem_count={{$elem_count}};
    if(elem_count!=0){
        alert('printing form has already been created for this Job Card!!!');
    }
</script>
@endsection

@section('main_section')
<section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
    @if ($errors->any())
    <div class="alert alert-warning">
        <ul>
            @foreach ($errors->all() as $error)
                <li style="list-style-type: square;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <!-- Default box -->
        @if(in_array(1, Request::get('userAlloweds')['section']))
        <p></p>
        @endif

    <form method="POST" action="/printingInsert" id="form">
            @csrf
            <div class="box">
                    <div class="box-header with-border">
                        <h2>Printing Details</h2>
                        <div class="row">
                                <div class="col-md-6">
                                    <label>Plate Size<sup></sup></label>
                                    <select  class="form-control select2 input-css plate_size" style="width: 100%;"  name="plate_size">
                                            <option value="default">Select Plate Size</option>
                                            <option value="7701030">770*1030</option>
                                            <option value="664530">664*530</option>
                                    </select>
                                    <label id="plate_sets-error" class="error" for="plate_size"></label>
                                </div>
                                <div class="col-md-6">
                                        <label>Plate Sets<sup></sup></label>
                                        <input type="text"  class="plate_sets input-css" name="plate_sets" id="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Impression Per Plate<sup></sup></label>
                                    <input type="text" class="input-css"   name="impression_plate_sets" id="">
                                </div>
                          
                                <div class="col-md-6">
                                    <label> Front Color<sup></sup></label>
                                        <select class="form-control  select2 input-css" style="width: 100%;" name="front_color">
                                            <option value="default">Select Back Color</option>
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                    </select>
                                    <label id="front_color-error" class="error" for="front_color"></label>
                                </div>
                               
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                            <label>Back Color<sup></sup></label>
                                            <select class="form-control select2  input-css" style="width: 100%;" name="back_color">
                                                    <option value="default">Select Back Color</option>
                                                    <option value="0">0</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                            </select>
                                            <label id="back_color-error" class="error" for="back_color"></label>
                                        </div>
                                       
                            </div>
                    </div>
                </div>
       <div class="row">
            <input  type="hidden" name="jc_id" value="{{$jc_id}}">
            <input  type="hidden" name="io_id" value="{{$io_id}}">
           <input type="submit" value="Submit" class="btn btn-primary">
       </div>
    </form>

</section><!--end of section-->



@endsection

