@extends($layout)

@section('title', 'Department')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Update Department</a></li>
   
@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
        <form action="/master/department/edit/{{$id}}" method="POST">
        @csrf

       <div class="box box-header">
           <br>
        
        <div class="row" >
                <div class="col-md-6 ">
                        <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                        <input type="text" name="update_reason" required class="input-css" id="update_reason">
                        {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                    </div><!--col-md-4-->
            <div class="col-md-6 {{ $errors->has('department') ? ' has-error' : ''}}">
                <label for="">Department Name <sup>*</sup></label>
            <input type="text" name="department" value="{{$dept->department}}" id="" class="dept input-css" required>
                {!! $errors->first('department', '<p class="help-block">:message</p>') !!} 
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
