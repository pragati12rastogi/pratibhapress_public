@extends($layout)
 
@section('title', 'Salary')
 
@section('user', Auth::user()->name)
 
@section('breadcrumb')
    <li><a href="/employee/salary/list"><i class=""></i> Employee List</a></li>
    <li><a href="#"><i class=""></i>Salary</a></li>

@endsection
@section('css')
<style>
    .nav1>li>a {
        position: relative;
        display: block;
        padding: 10px 34px;
        background-color: white;
        margin-left: 10px;
    }
    .nav-pills>li.active>a, .nav-pills>li.active>a:hover, .nav-pills>li.active>a:focus {
    border-top-color: #1ca28f !important;
    }
    .nav-pills>li.active>a, .nav-pills>li.active>a:focus, .nav-pills>li.active>a:hover {
    color: #fff;
    background-color: #138167 !important;
    }
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
    #active_sal > li{
        border:1px solid #8a8787;
        margin-right: 20px;
    }
</style>
@endsection

@section('js')
    <script src="/js/Employee/salary.js"></script>
    <script>
    $("#active_sal li a").click(function(){
        var value =$(this).text();
           
        $(".sal_type").val(value);
    })
    function cat_val(){
            var value =$("#active_sal li.active a").text();   
            $(".sal_type").val(value);
    }
    
    $(document).ready(function(){
        $('#active_sal.nav-pills > li > a').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#active_sal a[href="' + activeTab + '"]').tab('show');
        }

        cat_val();

    <?php if($sal_given != 0){ ?>
        $('#salary_a :input').prop('disabled', true);
        $('#salary_b :input').prop('disabled', true);
        $('#salary_c :input').prop('disabled', true);
    <?php } ?>
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
        <form action="/employee/salary/form/{{$id}}" method="POST" id="emp_salary_form" enctype="multipart/form-data">
        @csrf
         
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                <h3 class="box-title" style="font-size: 20px;margin-left: 27px;">{{$emp['name']."(".$emp['employee_number'].")"}}</h3><br><br>
                <div class="container-fluid wdt" style="padding-bottom:20px ">
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('sal_type') ? 'has-error' : ''}}">
                            
                                <ul class="nav nav-pills" id="active_sal">
                                    <li class="active" style="background-color: {{ isset($sal_data['SalaryA'])==1?'#87faca':'' }}">
                                    <a data-toggle="pill" href="#salary_a">Salary A</a></li>
                                    <li style="background-color: {{ isset($sal_data['SalaryB'])==1?'#87faca':'' }}" >
                                    <a data-toggle="pill" href="#salary_b">Salary B</a></li>
                                    <li style="background-color: {{ isset($sal_data['SalaryC'])==1?'#87faca':'' }}" >
                                    <a data-toggle="pill" href="#salary_c">Salary C</a></li>
                                    
                                </ul>
                               <input type="text" name="sal_type" class="sal_type" style="opacity:0">
                            {!! $errors->first('sal_type', '<p class="help-block">:message</p>') !!}
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content"> 
            <div class="box-header with-border tab-pane fade active in" id="salary_a" >
                <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">Salary A</h3><br><br><br>
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($sal_data['SalaryA']) && $sal_given == 0)
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Update Reason <sup>*</sup></label>
                                        <input type="text" name="update_reason_a" class="input-css update_reason" id="update_reason_a">
                                        {!! $errors->first('update_reason_a', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                @endif
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('basic_sal_a') ? 'has-error' : ''}}">
                                        <label for="">Basic Salary <sup>*</sup></label>
                                        <input type="number" name="basic_sal_a" min="0" class="input-css basic_sal_a" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryA']['basic_salary'])==1?$sal_data['SalaryA']['basic_salary']:'' }}">
                                        {!! $errors->first('basic_sal_a', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('da_sal_a') ? 'has-error' : ''}}">
                                        <label for="">Dearness Allowance (DA)<sup>*</sup></label>
                                        <input type="number" name="da_sal_a" min="0" class="da_sal_a input-css" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryA']['dearness_allowance'])==1?$sal_data['SalaryA']['dearness_allowance']:'' }}">
                                        {!! $errors->first('da_sal_a', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('overtime_sal_a') ? 'has-error' : ''}}">
                                        <label for="">Overtime eligible on salary A <sup>*</sup></label>
                                        <input type="text" name="overtime_sal_a" class="input-css joining" id="overtime_sal_a" keypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryA']['overtime'])==1?$sal_data['SalaryA']['overtime']:'' }}">
                                        <!-- <div class="radio">
                                            <label class="radio"><input type="radio" class="overtime_sal_a" 
                                            name="overtime_sal_a" value="1" {{ isset($sal_data['SalaryA']['overtime']) ? (($sal_data['SalaryA']['overtime']==1)?'checked=checked':'') :'' }}>Yes</label>
                                            <label class="radio"><input type="radio" class="overtime_sal_a" 
                                            name="overtime_sal_a" value="0" {{ isset($sal_data['SalaryA']['overtime']) ? (($sal_data['SalaryA']['overtime']==0)?'checked=checked':'') :'' }}>No</label>
                                        </div> -->
                                        {!! $errors->first('overtime_sal_a', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('bonus_sal_a') ? 'has-error' : ''}}">
                                        <label for="">Bonus on <sup>*</sup></label>
                                        <input type="number" name="bonus_sal_a" min="0" class="bonus_sal_a input-css" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryA']['bonus'])==1?$sal_data['SalaryA']['bonus']:'' }}">
                                        {!! $errors->first('bonus_sal_a', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    
                                    <div class="col-md-6 {{ $errors->has('da_cat_sal_a') ? 'has-error' : ''}}">
                                        <label for="">DA Category <sup>*</sup></label>
                                        <select name="da_cat_sal_a" class="select2 input-css da_cat_sal_a">
                                            <option value="">Select DA Category</option>
                                            <option value="skilled" {{ isset($sal_data['SalaryA']['da_category'])? (($sal_data['SalaryA']['da_category']=='skilled')?'selected=selected':''):'' }}>Skilled</option>
                                            <option value="unskilled" {{ isset($sal_data['SalaryA']['da_category'])? (($sal_data['SalaryA']['da_category']=='unskilled')?'selected=selected':''):'' }}>Unskilled</option>
                                            <option value="semi-skilled" {{ isset($sal_data['SalaryA']['da_category'])? (($sal_data['SalaryA']['da_category']=='semi-skilled')?'selected=selected':''):'' }}>Semi-Skilled</option>
                                        </select>
                                        {!! $errors->first('da_cat_sal_a', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div> 
            <div class="box-header with-border tab-pane fade " id="salary_b" >
                <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">Salary B</h3><br><br><br>
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($sal_data['SalaryB']) && $sal_given == 0)
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Update Reason <sup>*</sup></label>
                                        <input type="text" name="update_reason_b" class="input-css update_reason" id="update_reason_b">
                                        {!! $errors->first('update_reason_b', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                @endif
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('overhead_sal_b') ? 'has-error' : ''}}">
                                        <label for="">Overhead Amount <sup>*</sup></label>
                                        <input type="number" name="overhead_sal_b" min="0" class="input-css overhead_sal_b" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryB']['basic_salary'])==1?$sal_data['SalaryB']['basic_salary']:'' }}">
                                        {!! $errors->first('overhead_sal_b', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('overtime_sal_b') ? 'has-error' : ''}}">
                                        <label for="">Overtime eligible on salary B <sup>*</sup></label>
                                        <input type="text" name="overtime_sal_b" class="input-css joining" id="overtime_sal_b" keypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryB']['overtime'])==1?$sal_data['SalaryB']['overtime']:'' }}">
                                        <!-- <div class="radio">
                                            <label class="radio"><input type="radio" class="overtime_sal_b" 
                                            name="overtime_sal_b" value="1" {{ isset($sal_data['SalaryB']['overtime']) ? (($sal_data['SalaryB']['overtime']==1)?'checked=checked':'') :'' }}>Yes</label>
                                            <label class="radio"><input type="radio" class="overtime_sal_b" 
                                            name="overtime_sal_b" value="0" {{ isset($sal_data['SalaryB']['overtime']) ? (($sal_data['SalaryB']['overtime']==0)?'checked=checked':'') :'' }}>No</label>
                                        </div> -->
                                        {!! $errors->first('overtime_sal_b', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="box-header with-border tab-pane fade " id="salary_c" >
                <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">Salary C</h3><br><br><br>
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($sal_data['SalaryC']) && $sal_given == 0)
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Update Reason <sup>*</sup></label>
                                        <input type="text" name="update_reason_c" class="input-css update_reason" id="update_reason_c">
                                        {!! $errors->first('update_reason_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                @endif
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('basic_sal_c') ? 'has-error' : ''}}">
                                        <label for="">Basic Salary <sup>*</sup></label>
                                        <input type="number" name="basic_sal_c" min="0" class="input-css basic_sal_c" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryC']['basic_salary'])==1?$sal_data['SalaryC']['basic_salary']:'' }}">
                                        {!! $errors->first('basic_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('da_sal_c') ? 'has-error' : ''}}">
                                        <label for="">Dearness Allowance (DA)<sup>*</sup></label>
                                        <input type="number" name="da_sal_c" min="0" class="da_sal_c input-css" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryC']['dearness_allowance'])==1?$sal_data['SalaryC']['dearness_allowance']:'' }}">
                                        {!! $errors->first('da_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('hra_sal_c') ? 'has-error' : ''}}">
                                        <label for="">HRA <sup>*</sup></label>
                                        <input type="number" name="hra_sal_c" min="0" class="input-css hra_sal_c" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryC']['hra'])==1?$sal_data['SalaryC']['hra']:'' }}">
                                        {!! $errors->first('hra_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('conveyance_sal_c') ? 'has-error' : ''}}">
                                        <label for="">Conveyance Allowance <sup>*</sup></label>
                                        <input type="number" name="conveyance_sal_c" min="0" class="conveyance_sal_c input-css" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryC']['conveyance'])==1?$sal_data['SalaryC']['conveyance']:'' }}">
                                        {!! $errors->first('conveyance_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('phone_sal_c') ? 'has-error' : ''}}">
                                        <label for="">Telephone Allowance <sup>*</sup></label>
                                        <input type="number" name="phone_sal_c" min="0" class="input-css phone_sal_c" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryC']['telephone'])==1?$sal_data['SalaryC']['telephone']:'' }}">
                                        {!! $errors->first('phone_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('other_sal_c') ? 'has-error' : ''}}">
                                        <label for="">Other Allowance<sup>*</sup></label>
                                        <input type="number" name="other_sal_c" min="0" class="other_sal_c input-css" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryC']['other'])==1?$sal_data['SalaryC']['other']:'' }}">
                                        {!! $errors->first('other_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('bonus_sal_c') ? 'has-error' : ''}}">
                                        <label for="">Bonus on <sup>*</sup></label>
                                        <input type="number" name="bonus_sal_c" min="0" class="input-css bonus_sal_c" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryC']['bonus'])==1?$sal_data['SalaryC']['bonus']:'' }}">
                                        {!! $errors->first('bonus_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                     <div class="col-md-6 {{ $errors->has('pf_applicable_sal_c') ? 'has-error' : ''}}">
                                        <label for="">PF Applicable  <sup>*</sup></label>
                                        <select name="pf_applicable_sal_c" id="pf_applicable_sal_c" class="input-css select2" style="width: 100%">
                                            <option>Select option</option>
                                            <option value="Yes" {{ isset($sal_data['SalaryC']['pf_applicable'])? (($sal_data['SalaryC']['pf_applicable']=='Yes')?'selected=selected':''):'' }}>Yes</option>
                                            <option value="No" {{ isset($sal_data['SalaryC']['pf_applicable'])? (($sal_data['SalaryC']['pf_applicable']=='No')?'selected=selected':''):'' }}>No</option>
                                        </select>
                                        {!! $errors->first('pf_applicable_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('esi_applicable_sal_c') ? 'has-error' : ''}}">
                                        <label for="">ESI Applicable  <sup>*</sup></label>
                                        <select name="esi_applicable_sal_c" id="esi_applicable_sal_c" class="input-css select2" style="width: 100%">
                                            <option>Select option</option>
                                            <option value="Yes" {{ isset($sal_data['SalaryC']['esi_applicable'])? (($sal_data['SalaryC']['esi_applicable']=='Yes')?'selected=selected':''):'' }}>Yes</option>
                                            <option value="No" {{ isset($sal_data['SalaryC']['esi_applicable'])? (($sal_data['SalaryC']['esi_applicable']=='No')?'selected=selected':''):'' }}>No</option>
                                        </select>
                                        {!! $errors->first('esi_applicable_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('wc_sal_c') ? 'has-error' : ''}}">
                                        <label for="">Monthly WC Premium Amount <sup>*</sup></label>
                                        <input type="number" name="wc_sal_c" min="0" class="input-css wc_sal_c" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" value="{{ isset($sal_data['SalaryC']['wc_premium_amount'])==1?$sal_data['SalaryC']['wc_premium_amount']:'' }}">
                                        {!! $errors->first('wc_sal_c', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div> 
        @if($sal_given == 0)
            <div class="row">
                <div class="col-md-12">
                     <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
                </div>
            </div>
        @endif
        </form>
      
      </section>
@endsection
 

