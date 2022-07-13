@extends($layout)

@section('title', 'Plates Creation')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Plates Creation</a></li>
   
@endsection
@section('js')
<script>
$(document).ready(function () {
    $('#form').validate({ // initialize the plugin
        rules: {
            job_number: {
                required: true
            },
            reference:{
                required: true  
            },
            element:{
                required: true  
            },
            baking_date:{
                required: true  
            },
            plates:{
                required: true  
            },
            size:{
                required: true  
            },
            employee:{
                required: true  
            },
            
        }
    });
});
</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                   
            </div>
            
        <!-- Default box -->
    <form action="/production/platebyparty/create/{{$id}}" method="POST" id="form">
        @csrf
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                <h2 class="box-title" style="font-size: 28px;margin-left:20px">Plates Creation</h2><br><br><br>
                <div class="container-fluid wdt">
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('job_number') ? 'has-error' : ''}}">
                            <label>JobCard Number <sup>*</sup></label><br>
                            <input type="text" class="form-control input-css" name="sa"  value="{{$job['job_number']}}" disabled>
                            <input type="hidden" class="form-control input-css" name="job_number" id="job_number" value="{{$job['id']}}">
                            {!! $errors->first('job_number', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('reference') ? 'has-error' : ''}}">
                                <label>Reference Name <sup>*</sup></label><br>
                                <input type="text" class="form-control input-css" name="ww"  value="{{$job['referencename']}}" disabled>
                                <input type="hidden" class="form-control input-css" name="reference" id="reference" value="{{$job['reference_name']}}">
                                {!! $errors->first('reference', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('element') ? 'has-error' : ''}}">
                                <label>Element Name <sup>*</sup></label><br>
                                <select class="form-control input-css select2" name="element">
                                    <option value="">Select Element Type</option>
                                    @foreach ($elem as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('element', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('baking_date') ? 'has-error' : ''}}">
                                    <label>Baking Date <sup>*</sup></label><br>
                                    <input type="text" autocomplete="off" class="form-control input-css datepicker1 baking_date" id="baking_date" name="baking_date">
                                    {!! $errors->first('baking_date', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                          
                            <div class="col-md-6 {{ $errors->has('plates') ? 'has-error' : ''}}">
                                    <label>No. Of Plates <sup>*</sup></label><br>
                                    <input type="number" min="0" step="none" class="form-control input-css plates" id="plates" name="plates">
                                    {!! $errors->first('plates', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('size') ? 'has-error' : ''}}">
                                    <label>Plates Size<sup>*</sup></label><br>
                                    <select  class="form-control input-css size select2" id="size" name="size">
                                            @foreach ($plate_size as $key)
                                            <option value="{{$key->value}}">{{$key->value}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('size', '<p class="help-block">:message</p>') !!}
                            </div>
                           
                    </div><br><br>
                    <div class="row">  
                            <div class="col-md-6 {{ $errors->has('employee') ? 'has-error' : ''}}">
                                    <label>Baking Done By <sup>*</sup></label><br>
                                    <select  class="form-control input-css size select2" id="employee" name="employee">
                                            <option value="">Select Employee</option>
                                            @foreach ($employee as $key)
                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('employee', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>

                </div>
            </div>
        </div>
 
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
