@extends($layout)

@section('title', __('Employee/pfesi.title'))

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="/employee/profile/list"><i class=""></i> Employee List</a></li>
    <style>
        .nav1>li>a {
            position: relative;
            display: block;
            padding: 10px 34px;
            background-color: white;
            margin-left: 10px;
        }
        /* .nav1>li>a:hover {
            background-color:#87CEFA;
        
        } */
        </style>
@endsection
@section('js')
<script src="/js/Employee/pfesi.js"></script>
<script>
    var pf_date=null;
        var pf_leaving=null;
        $(document).ready(function(){
            $('#pf_date').datepicker()
                .on('changeDate', function(ev){
                    pf_date=new Date(ev.date.getFullYear(),ev.date.getMonth(),ev.date.getDate(),0,0,0);
                    if(pf_leaving!=null&&pf_leaving!='undefined'){
                        if(pf_date <= pf_leaving){
                               $('#pf_leaving-error').hide();
                        }else{
                            $('#pf_leaving-error').html('Please select equal to pf date or more');
                            $('#pf_leaving').val('');
                        }
                    }
                });
            $("#pf_leaving").datepicker()
                .on("changeDate", function(ev){
                    pf_leaving=new Date(ev.date.getFullYear(),ev.date.getMonth(),ev.date.getDate(),0,0,0);
                    if(pf_date!=null&&pf_date!='undefined'){
                        if(pf_date <= pf_leaving){
                            $('#pf_leaving-error').hide();
                        }else{
                            $('#pf_leaving-error').html('Please select equal to pf date or more');
                            $('#pf_leaving').val('');
                        }
                    }
                });
        });

         var esi_date=null;
        var esi_leaving=null;
        $(document).ready(function(){
            $('#esi_date').datepicker()
                .on('changeDate', function(ev){
                    esi_date=new Date(ev.date.getFullYear(),ev.date.getMonth(),ev.date.getDate(),0,0,0);
                    if(esi_leaving!=null&&esi_leaving!='undefined'){
                        if(esi_date <= esi_leaving){
                               $('#esi_leaving-error').hide();
                        }else{
                            $('#esi_leaving-error').html('Please select equal to esi date or more');
                            $('#esi_leaving').val('');
                        }
                    }
                });
            $("#esi_leaving").datepicker()
                .on("changeDate", function(ev){
                    esi_leaving=new Date(ev.date.getFullYear(),ev.date.getMonth(),ev.date.getDate(),0,0,0);
                    if(esi_date!=null&&esi_date!='undefined'){
                        if(esi_date <= esi_leaving){
                            $('#esi_leaving-error').hide();
                        }else{
                            $('#esi_leaving-error').html('Please select equal to esi date or more');
                            $('#esi_leaving').val('');
                        }
                    }
                });
        });
// jQuery('#pf_leaving').on('change', function(){
//     debugger;
//     var pf_date = new Date($('#pf_date').val());
//     var pf_leaving = new Date($('#pf_leaving').val());
//     if((pf_date == pf_leaving) || (pf_date < pf_leaving)){
//         $('#pf_leaving-error').hide();
//     }else{
        
//     }
// });

// jQuery('#esi_leaving').on('change', function(){
//     debugger;
//     var esi_date = new Date($('#esi_date').val()).getTime();
//     var esi_leaving = new Date($('#esi_leaving').val()).getTime();
//     if((esi_date == esi_leaving) || (esi_date < esi_leaving)){
//         $('#esi_leaving-error').hide();
//     }else{
//         $('#esi_leaving-error').html('Please select equal to esi date or more');
//         $('#esi_leaving').val('');
//     }
// });
$("#pf").change(function(){
    var pf =$(this).prop("checked");
    if(pf == true){
        $(".pfcheck").show();
    }else{
        $(".pfcheck").hide();
    }
})
$("#esi").change(function(){
    var esi = $(this).prop("checked");
    if(esi == true){
        $(".esicheck").show();
    }else{
        $(".esicheck").hide();
    }
})
</script>
@endsection
@section('main_section')
<section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
        <!-- Default box -->
    <form action="/employee/pfesi/update/{{$id}}" method="POST" id="form">
        @csrf
         @include('layouts.employee_tab')
     <br>
              
    <div class="box-header with-border">
        <div class='box box-default'>  <br>
            <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/pfesi.mytitle')}}</h3><br><br><br>
            <div class="container-fluid wdt">
                @if(isset($pfesi))
                    <div class="row">
                        <div class="col-md-3">
                            <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                            <input type="text" name="update_reason" required="" class="input-css update_reason" id="update_reason">
                            {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div> <br><br>
                @endif
                
            </div>
        </div>
    </div>
    <div class="box-header with-border pfesi" >
        <div class='box box-default'>  <br>
            <div class="container-fluid wdt">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                            <div class="checkbox">
                                <label style="font-weight: bold;"><input autocomplete="off" type="checkbox" class="pf" name="pf" id="pf" value="pf"  {{isset($pfesi)==1 ? ($pfesi->pf=='pf'? 'checked=checked' :''):''}} > PF</label>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="checkbox">
                                <label style="font-weight: bold;"><input autocomplete="off" type="checkbox" class="esi" name="esi" id="esi" value="esi"  {{isset($pfesi)==1? ($pfesi->esi=='esi'? 'checked=checked' :''):''}}> ESI</label>
                            </div>
                            </div>
                        </div><hr>
                    </div>
                    <div class="col-md-6 pfcheck" {{isset($pfesi)==1 ? ($pfesi->pf=='pf'? 'style=display:block' :'style=display:none'):'style=display:none'}}>
                         <div class="row">
                            <div class="col-md-12 {{ $errors->has('pf_date') ? 'has-error' : ''}}">
                                <div class="row">
                                <label for="">{{__('Employee/pfesi.pf_date')}} <sup>*</sup></label>
                                <input autocomplete="off" type="text" name="pf_date" id="pf_date" class="pf_date datepicker1 input-css"  <?php 
                                if(isset($pfesi)){
                                    if(($pfesi->enroll_date_pf)!="1970-01-01"){
                                        $x = CustomHelpers::showDate($pfesi->enroll_date_pf,"d-m-Y");
                                        echo ("value=".$x);
                                    }else{

                                    }
                                }
                                ?> >
                                <label id="pf_date-error" class="error" for="pf_date"></label>
                                {!! $errors->first('pf_date', '<p class="help-block">:message</p>') !!}
                                </div><br>
                            </div>
                            <div class="col-md-12 {{ $errors->has('pf_leaving') ? 'has-error' : ''}}">
                                <div class="row">
                                <label for="">{{__('Employee/pfesi.pf_leaving')}} </label>
                                <input autocomplete="off" type="text" name="pf_leaving" id="pf_leaving" class="pf_leaving datepicker1 input-css" <?php 
                                if(isset($pfesi)){
                                    if(($pfesi->leave_date_pf)=="1970-01-01" ||($pfesi->leave_date_pf)==""){
                                        
                                    }else{
                                        $x = CustomHelpers::showDate($pfesi->leave_date_pf,"d-m-Y");
                                        echo ("value=".$x);
                                    }
                                }
                                ?> > 
                                <label id="pf_leaving-error" class="error" for="pf_leaving"></label>
                                {!! $errors->first('pf_leaving', '<p class="help-block">:message</p>') !!}
                                </div><br>
                            </div>
                            <div class="col-md-12 {{ $errors->has('pf_no') ? 'has-error' : ''}}">
                                <div class="row">
                                <label for="">{{__('Employee/pfesi.pf_no')}} <sup>*</sup></label>
                                <input  type="text" name="pf_no" id="pf_no" class="pf_no  input-css" value="{{isset($pfesi)==1? ($pfesi-> pf_no):''}}">
                                <!-- <label id="pf_no-error" class="error" for="pf_no"></label> -->
                                {!! $errors->first('pf_no', '<p class="help-block">:message</p>') !!}
                                </div><br>
                            </div>
                             <div class="col-md-12 {{ $errors->has('pf_withdrawal') ? 'has-error' : ''}}">
                                <div class="row">
                                <label for="">{{__('Employee/pfesi.pf_withdrawal')}} </label>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label><input autocomplete="off" type="radio" class="pf_withdrawal" value="full" name="pf_withdrawal">Full</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label><input autocomplete="off" type="radio" class="pf_withdrawal" value="partly" name="pf_withdrawal">Partly </label>
                                        </div>
                                    </div>
                                    <label id="pf_withdrawal-error" class="error" for="pf_withdrawal"></label>
                                    {!! $errors->first('pf_withdrawal', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div>
                        <div class="col-md-12 {{ $errors->has('withdrawal_date') ? 'has-error' : ''}}">
                            <div class="row">
                            <label for="">{{__('Employee/pfesi.wd_date')}} </label>
                            <input autocomplete="off" type="text" name="withdrawal_date" id="withdrawal_date" class="datepicker1 input-css">
                            <label id="withdrawal_date-error" class="error" for="withdrawal_date"></label>
                            {!! $errors->first('withdrawal_date', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-6 esicheck" {{isset($pfesi)==1 ? ($pfesi->esi=='esi'? 'style=display:block' :'style=display:none'):'style=display:none'}}>
                        <div class="row">
                            <div class="col-md-12 {{ $errors->has('esi_date') ? 'has-error' : ''}}">
                                <div class="row">
                                <label for="">{{__('Employee/pfesi.esi_date')}} <sup>*</sup></label>
                                <input type="text" autocomplete="off" name="esi_date" id="esi_date" class="esi_date datepicker1 input-css" <?php 
                                if(isset($pfesi)){
                                    if(($pfesi->enroll_date_esi)!="1970-01-01"){
                                        $x = CustomHelpers::showDate($pfesi->enroll_date_esi,"d-m-Y");
                                        echo ("value=".$x);
                                    }else{

                                    }
                                }
                                ?>>
                                <label id="esi_date-error" class="error" for="esi_date"></label>
                                {!! $errors->first('esi_date', '<p class="help-block">:message</p>') !!}
                                </div><br>
                            </div>
                            
                            <div class="col-md-12 {{ $errors->has('esi_leaving') ? 'has-error' : ''}}">
                                <div class="row">
                                <label for="">{{__('Employee/pfesi.esi_leaving')}} </label>
                                <input autocomplete="off" type="text" name="esi_leaving" id="esi_leaving" class="esi_leaving datepicker1 input-css" <?php
                                if(isset($pfesi)){ 
                                    if(($pfesi->leave_date_esi)!="1970-01-01"){
                                        $x = CustomHelpers::showDate($pfesi->leave_date_esi,"d-m-Y");
                                        echo ($x);
                                    }else{

                                    }
                                }
                                ?> >
                                <label id="esi_leaving-error" class="error" for="esi_leaving"></label>
                                {!! $errors->first('esi_leaving', '<p class="help-block">:message</p>') !!}
                                </div><br>
                            </div>
                            <div class="col-md-12 {{ $errors->has('esi_no') ? 'has-error' : ''}}">
                                <div class="row">
                                <label for="">{{__('Employee/pfesi.esi_no')}} <sup>*</sup></label>
                                <input type="text" name="esi_no" id="esi_no" class="esi_no input-css" value="{{isset($pfesi)==1?($pfesi->esi_no):''}}">
                                <!-- <label id="esi_no-error" class="error" for="esi_no"></label> -->
                                {!! $errors->first('esi_no', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                           
                        </div>
                    </div>
                    
                </div><br><br>

                            
            </div>
        </div>

       
    </div>
     <div class="row">
        <div class="col-md-12">
            <input type="submit" style="float:right;" class="btn btn-primary" value="Submit">
        </div>
    </div>
@if(isset($pfesi_withdrawal))
    @if($pfesi_withdrawal != "[]")
    <div class="box-header with-border pfesi" >
        <div class='box box-default'>  <br>
            <div class="container-fluid wdt">
                <div class="row">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Withdrawal Type</th>
                                <th>Withdrawal Date</th>
                            </tr>
                        </thead>
                        <tbody>                             
                            @foreach($pfesi_withdrawal as $item)
                            <tr>
                                <td>{{$item->name}}</td>
                                <td>{{$item->withdrawal_type}}</td>
                                <td>{{$item->withdrawal_date}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
              
               </div>
            </div>
        </div>
    </div>
      @endif
@endif
    <br><br>
    </form>
      
</section>
@endsection
