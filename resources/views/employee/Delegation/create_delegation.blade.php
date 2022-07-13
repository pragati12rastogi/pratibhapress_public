@extends($layout)

@section('title', 'Create Delegation')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Create Delegation</a></li>
   
@endsection
@section('js')
<script src="/js/Delegate/delegate.js"></script>
<script>
  $(".datepickers").datepicker({
        startDate:'today',
        format: 'd-m-yyyy'
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
       <form action="" method="POST" id="del_form" enctype="multipart/form-data">
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
                
            </div><br><br>
            <div class="row" >
                <div class="col-md-12 {{ $errors->has('task_detail') ? ' has-error' : ''}}">
                    <label for="">Task Details <sup>*</sup></label>
                    <textarea id="task_detail" name="task_detail" class="input-css task_detail"></textarea>
                    {!! $errors->first('task_detail', '<p class="help-block">:message</p>') !!}
                </div>
                
            </div><br><br>
            <div class="row" >
                <div class="col-md-6 {{ $errors->has('assignby') ? ' has-error' : ''}}">
                    <label for="">Assigned by<sup>*</sup></label>
                    <select name="assignby" id="assignby" class="assignby input-css select2" required>
                        <option value="">Select Employee</option>
                        @foreach($employee as $emp)
                            <option value="{{$emp->id}}">{{$emp->name}}</option>
                        @endforeach
                    </select>
                    {!! $errors->first('assignby', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-6 {{ $errors->has('assignonDate') ? ' has-error' : ''}}">
                    <label for="">Assigned on<sup>*</sup></label>
                    <input type="text" autocomplete="off" id="assignonDate" name="assignonDate" class="assignonDate input-css datepickers">
                    {!! $errors->first('assignonDate', '<p class="help-block">:message</p>') !!}
                </div>
            </div><br><br>
            <div class="row" >
                
                <div class="col-md-6 {{ $errors->has('deadline') ? ' has-error' : ''}}">
                    <label for="">Deadline For The Task</label>
                    <input type="text" autocomplete="off" id="deadline" name="deadline" class="deadline input-css datepickers">
                    {!! $errors->first('deadline', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-6 {{ $errors->has('spc_req') ? ' has-error' : ''}}">
                    <label for="">Any Special Requirements <sup></sup></label>
                    <textarea class="input-css spc_req" id="spc_req" name="spc_req" rows="1"></textarea>
                    {!! $errors->first('spc_req', '<p class="help-block">:message</p>') !!}
                </div>
            </div><br><br>
        <div class="row">
                <div class="col-md-12">
                    
                      <input type="submit" class="btn btn-primary">
                    
                </div>
            </div><br>
        </form>
      
      </section>
@endsection
