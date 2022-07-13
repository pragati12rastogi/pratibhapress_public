@extends($layout)

@section('title', 'Design Work Allotment')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Design Work Allotment</a></li>
   
@endsection
@section('js')
<script src="/js/Design/design_work.js"></script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                   
                    @yield('content')
            </div>
        <!-- Default box -->
       <form action="/design/work/create" method="POST" id="form" autocomplete="off">
        @csrf
        <div class="box-header with-border returnable">
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">Create Design Work Allotment</h2><br><br><br>
                    <div class="container-fluid wdt">
                     
                                <div class="row">
                                        <div class="col-md-6 {{ $errors->has('design') ? 'has-error' : ''}}">
                                            <label>Design Order Number<sup>*</sup></label>
                                            <select name="design" id="design" class="select2 design input-css">
                                                <option value="">Select Design Order</option>
                                                @foreach ($design as $item)
                                                <option value="{{$item->id}}" {{old('design')==$item->id ? 'selected=selected':''}}>{{$item->do_number}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('design', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('work_to_emp') ? 'has-error' : ''}}">
                                                <label>Work Allotment to</label>
                                                <select name="work_to_emp" id="work_to_emp" class="select2 work_to_emp input-css">
                                                        <option value="">Select Work Allotment to</option>
                                                        @foreach ($emp as $item)
                                                        <option value="{{$item->id}}" {{old('work_to_emp')==$item->id ? 'selected=selected':''}}>{{$item->name}}</option>
                                                        @endforeach
                                                </select>
                                                {!! $errors->first('work_to_emp', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div><br><br>
                                <div class="row">
                                        <!--<div class="col-md-6 {{ $errors->has('work_date') ? 'has-error' : ''}}">
                                            <label>Work Allotment Date<sup>*</sup></label>
                                            <input type="text" name="work_date" id="work_date" class="datepicker1 work_date input-css">
                                         
                                            {!! $errors->first('work_date', '<p class="help-block">:message</p>') !!}
                                        </div>-->
                                        <div class="col-md-6 {{ $errors->has('no_pages') ? 'has-error' : ''}}">
                                            <label>Number Pages/Degree/Creative <sup>*</sup></label>
                                           <input type="number" name="no_pages" value="{{old('no_pages')}}" id="no_pages" class="no_pages input-css">
                                           <input type="hidden" name="left" class="left_page">
                                            {!! $errors->first('no_pages', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div><br><br>
                                   
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->has('desc') ? 'has-error' : ''}}">
                                        <label>Work Description <sup>*</sup></label> 
                                        <textarea name="desc" id="desc" class="desc input-css">{{old('desc')}}</textarea>
                                        {!! $errors->first('desc', '<p class="help-block">:message</p>') !!}
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
