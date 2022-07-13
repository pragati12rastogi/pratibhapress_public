
@extends($layout)

@section('title', __('accounting/group.create title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/group.create title')}}</i></a></li>
@endsection
@section('css')
<style>
    /* css for adding differentiation between focused and non focused element */
    .input-css:focus, .select2-search__field:focus{
        background-color: #FAEF9F !important;
    }
</style>
@endsection

@section('js')
<script src="/js/accounting/creategroup.js"></script>
<script>
$(document).ready(function(){
    
    // add select2 to select element with class 'input-css'
    $('.select').select2({
        containerCssClass:"input-css"
    });  
    // on group parent change 'nature_div' and 'gross_profit_div' show/hide and 
    $('.group_under').change(function(e) {
        var group_under = $(e.target).val();
        if(group_under==1)
        $('#nature_div').show();
        else
        {
            $('#nature_div').hide();
            $('#gross_profit_div').hide();
            $(nature_of_group).val(null).trigger('change');
        }                
    });  
    // on 'nature_of_group' change 'gross_profit_div' show/hide
    $('.nature_of_group').change(function(e) {
            
        var group_nature = $(e.target).val();
        if(group_nature==2 || group_nature==3)
            $('#gross_profit_div').show();
        else
        $('#gross_profit_div').hide();
        
    }); 
    // incomplete function (code to add new modal form in group create page.)
    $('#').change(function(){
        $.ajax({
            url:'',
            success:function(Result){
                if(Result==1)
                {
                    $('#GSTFields').append(
                        '<div class="col-md-8">'+
                            '<label for="">{{__('accounting/group.')}}</label>'+
                            '<select name="" class="input-css form-control select2" id="">'+
                                '<option value="1">Yes</option>'+
                                '<option value="0" selected="selected">No</option>'+
                            '</select>'+
                        '</div>';
                    );
                    
                }
            }

        });
    });
});
</script>

@endsection
@section('main_section')
    <section class="content">
            <div class="row">
                

                    <div style="text-align:center" class="col-md-12">
                        <h3>
                            <label for="company_name">{{AccountingCustomHelper::getCompanyName()}}</label>
                        </h3>    
                    </div>
                </div>
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                   
            </div>
            @if($errors->any())
        {{-- display all  the errors if they are present. --}}
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
            <div class="box-header with-border">
                <div class='box box-default'>  
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                    <br>
                    <div class="container-fluid wdt">
                        <form method="POST" action="/accounting/group/create" id="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="group_name">{{__('accounting/group.groupname')}}</label>
                                    <input type="text" name="group_name" class="input-css form-control group_name" id="group_name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                        <label for="group_alias_name">{{__('accounting/group.groupaliasname')}}</label>
                                        <input type="text" name="group_alias_name" class="input-css form-control group_alias_name" id="group_alias_name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="group_under">{{__('accounting/group.groupunder')}}</label>
                                    <select type="text" name="group_under" class="input-css select select2 form-control group_under" id="group_under">
                                        <option value="default">Select Group</option>
                                        @foreach ($group as $key)
                                            <option value="{{$key->id}}">{{$key->name}}&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp{{$key->alias}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="nature_div" class="row" style="display:none">
                                <div class="col-md-6">
                                    <label for="group_nature">{{__('accounting/group.groupnature')}}</label>
                                    <select class="select select2 nature_of_group input-css form-control" id="nature_of_group" name="nature_of_group">
                                        <option value="default">Select Nature of Group</option>
                                        @foreach ($nature as $key)                                            
                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="gross_profit_div" class="row" style="display:none">
                                <div class="col-md-6">
                                    <label for="gross_profit">{{__('accounting/group.groupprofit')}}</label>
                                    <div class="col-md-2"><label><input checked type="radio" class=" group_gross_profit " id="group_gross_profit" name="group_gross_profit">
                                    No</label></div>
                                    <div class="col-md-2"><label><input type="radio" class="gross_profit " id="gross_profit" name="gross_profit">
                                    Yes</label></div>
                                </div>
                            </div>
                            <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="group_behave">{{__('accounting/group.groupbehavelikesubledger')}}</label>
                                        <div class="col-md-4"><label><input type="radio" checked name="group_behave" class=" group_behave" value="0">{{__('accounting/group.no')}}</label></div>
                                        <div class="col-md-4"><label><input type="radio" name="group_behave" class=" group_behave" value="1">{{__('accounting/group.yes')}}</label></div>
                                        <div class="group_behave_label_er">
                                            <label id="group_behave-error" class="error" for="group_behave"></label>
                                            {!! $errors->first('group_behave', '<p class="help-block">:message</p>') !!}
                                        </div>
                
                                    </div>
                                </div>
                                <div class="row"> 
                                    <div class="col-md-6">
                                        <label for="group_nett">{{__('accounting/group.groupnettdebit/creditbalance')}}</label>
                                    <div class="col-md-4">    <label><input type="radio" checked name="group_nett" class=" group_nett" value="0">{{__('accounting/group.no')}}</label></div>
                                    <div class="col-md-4">    <label><input type="radio" name="group_nett" class=" group_nett" value="1">{{__('accounting/group.yes')}}</label></div>
                                    <div class="group_nett_label_er">
                                        <label id="group_nett-error" class="error" for="group_nett"></label>
                                        {!! $errors->first('group_nett', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                
                                </div>
                                <div class="row"> 
                                    <div class="col-md-6">
                                        <label for="group_calc">{{__('accounting/group.groupuseforcalculation')}}</label>
                                        <div class="col-md-4"><label><input type="radio" checked name="group_calc" class=" group_calc" value="0">{{__('accounting/group.no')}}</label></div>
                                        <div class="col-md-4"><label><input type="radio" name="group_calc" class=" group_calc" value="1">{{__('accounting/group.yes')}}</label></div>
                                    </div>
                                    <div class="group_calc_label_er">
                                        <label id="group_calc-error" class="error" for="group_calc"></label>
                                        {!! $errors->first('group_calc', '<p class="help-block">:message</p>') !!}
                                    </div>
                
                                </div>
                                 <div class="row"> 
                                    <div class="col-md-7">
                                        <label for="group_method">{{__('accounting/group.groupmethodtoallocate')}}</label>
                                        <div class="col-md-4"><label><input type="radio" checked name="group_method" class=" group_method" value="0">{{__('accounting/group.na')}}</label></div>
                                        <div class="col-md-4"><label><input type="radio" name="group_method" class=" group_method" value="1">{{__('accounting/group.appbyqty')}}</label></div>
                                        <div class="col-md-4"><label><input type="radio" name="group_method" class=" group_method" value="2">{{__('accounting/group.appbyval')}}</label></div>
                                        <div class="group_method_label_er">
                                            <label id="group_method-error" class="error" for="group_method"></label>
                                            {!! $errors->first('group_method', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="GSTFields">

                                </div>
                                <hr>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-success" ></button>
                                </div>
                                
                        </form>
                    </div>
                </div>
            </div>                 
    </section><!--end of section-->
@endsection


