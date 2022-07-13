@extends($layout)
@section('title', __('accounting/voucher.update title'))
{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)
@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/voucher.update title')}}</i></a></li>
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
<script src="/js/accounting/voucherType.js"></script>
<script>
   $(document).ready(function(){
       // add select2 to select element with class 'input-css'
   $('.select').select2({
                containerCssClass:"input-css"
            });
});
    // on voucher type change new form elements are added to page.

    $('.VoucherType').change(function(e){
        var VoucherType = $(e.target).val();
        if(VoucherType!=' ')
        {
            // show loader
            $('#ajax_loader_div').css('display','block');
            // api to get new elements for form
            $.ajax({
                url: "/accounting/vouchertype/avail/" + VoucherType,
                type: "GET",
                success: function(result) 
                {
                    // array to store form element of different section
                    var section=[];
                    $('#VoucherLayout').show();
                    section[1]='<h2 class="box-title" style="font-size: 20px;margin-left:20px">{{__("accounting/voucher.GeneralSetting")}}</h2><br><br><br>';
                    section[2]='<h2 class="box-title" style="font-size: 20px;margin-left:20px">{{__("accounting/voucher.PrintSetting")}}</h2><br><br><br>';
                    section[3]='<h2 class="box-title" style="font-size: 20px;margin-left:20px">{{__("accounting/voucher.AdditionalSetting")}}</h2><br><br><br>';
                    for(i=0;i<result.length;i++)
                    {    
                        // storing data for easy access
                        id = result[i].id;
                        title = result[i].title;
                        name = result[i].name;
                        part = result[i].section;
                        allowed_value = result[i].value;
                        allowed_value_db = result[i].value_db;
                        type = result[i].type;
                        childrenId = result[i].children_id;
                        // variable to store events of element
                        events='';
                        
                        var ele = '<div class="row">'+
                                '<div class="col-md-12">'+
                                '<label for="UsrSetting'+id+'">'+title+'</label>';
                        if(childrenId!="")
                        {   
                            events = 'onchange="getchildren(this.value,\''+childrenId+'\',\'UsrSetting_child'+id+'\')"';                                                             
                        }
                        // generating elements
                        if(type=='email'|| type=='text')
                        {
                            ele = ele + 
                                '<input type="text" class="input-css form-control UsrSettingTxt UsrSetting'+id+'" name="'+name+'" id="UsrSetting'+id+'">';
                            
                        }
                        else if(type=='option')
                        {
                            ele = ele + 
                            '<select '+events +' class="select form-control input-css UsrSettingOpt UsrSetting'+id+'" id="UsrSetting'+id+'" name="'+name+'">'+
                                '<option value="default">Select Value</option>';
                                var values = allowed_value.split(',');   
                                var values_db = allowed_value.split(',');   
                                for (j=0;j<values.length;j++)                                            
                                {
                                    ele = ele + '<option value="'+values_db[j]+'">'+values[j]+'</option>';
                                }
                                ele = ele+  
                            '</select>';
                        }
                        
                        ele=ele + '</div>'+
                            '</div>';
                        // generating container for child elements
                        if(childrenId!="")
                        {   
                            ele = ele + '<div id="UsrSetting_child'+id+'" ></div>';                                                           
                        }
                        // adding data to its section
                        section[part] = section[part] +ele;
                    }
                    section[1]= section[1];
                    section[2]= section[2];
                    section[3]= section[3];
                    // adding new form elements to page.
                    $('#VoucherLayout1').empty().append(section[1]);
                    $('#VoucherLayout2').empty().append(section[2]);
                    $('#VoucherLayout3').empty().append(section[3]);
                    // add select2 to select element with class 'input-css'
                    $('.select').select2({
                        containerCssClass:"input-css"
                    });
                    // hiding the loader
                    $('#ajax_loader_div').css('display','none');
                }
            });
        }
    });
    // getting the children of element 
    function getchildren(value,childrenId,element)
    {
        /*
        * value = value of element requesting for children
        * childrenId = children ids
        * element = element id to store children
        */
        // empty all the current children of 
        $('#'+element).empty();
        // showing the loader
        $('#ajax_loader_div').css('display','block');
        var children_ids = childrenId.split("_");
        // hiding available children for future reference
        for(i=0;i<children_ids.length;i++)
        {
                $(document).find($("#UsrSetting_orig_"+children_ids[i])).hide();
        }
        // api to get children 
        $.ajax({
            url: "/accounting/vouchertype/avail/children/" + value +'/'+childrenId,
            type: "GET",
            success: function(result) {
                for(i=0;i<result.length;i++)
                {  
                    // variable to store event of element
                    events='';
                    // assigning variable for easy access
                    id = result[i].id;
                    // showing available elements
                    if($(document).find($("#UsrSetting_orig_"+id)).length>0)
                    {
                        $(document).find($("#UsrSetting_orig_"+id)).show();
                        continue;
                    }
                    name = result[i].name;
                    part = result[i].section;
                    allowed_value = result[i].value;
                    allowed_value_db = result[i].value_db;
                    attributes = result[i].attributes;
                    type = result[i].type;
                    title=result[i].title;
                    childrenId = result[i].children_id;
                    var ele = '<div class="row">'+
                            '<div class="col-md-12">'+
                            '<label for="UsrSetting'+id+'">'+title+'</label>';
                    // assigning events of element
                    if(childrenId!="")
                    {   
                        events = 'onchange="getchildren(this.value,\''+childrenId+'\',\'UsrSetting_child'+id+'\')"';                                                             
                    }

                    // generating element  
                    if( type=='email'||type=='text')
                    {
                        ele = ele + 
                            '<input type="text" class="input-css form-control UsrSettingTxt UsrSetting'+id+'" name="'+name+'" id="UsrSetting'+id+'">';
                        
                    }   
                    else if(type=='option')
                    {
                        ele = ele + 
                            '<select '+events+' class="select form-control input-css UsrSettingOpt UsrSetting'+id+'" name="'+name+'" id="UsrSetting'+id+'">'+
                                '<option value=" ">Select Value</option>';
                                var values = allowed_value.split(',');   
                                var values_db = allowed_value.split(',');   
                                for (j=0;j<values.length;j++)                                            
                                {
                                    ele = ele + '<option value="'+values_db[j]+'">'+values[j]+'</option>';
                                }
                            ele = ele+  
                        '</select>';
                    }
                    else if(type == 'select-table')
                    {
                        var cols = allowed_value.split(':')[0];
                        var option_val = cols.split(',')[0];
                        var option_name = cols.split(',')[1];
                        ele = ele + '<select '+events+ attributes +' class="select form-control input-css UsrSettingOpt UsrSetting'+id+'" id="UsrSetting'+id+'" name="'+name+'">'+
                                '<option value=" ">Select Value</option>'+
                            '</select>';
                        element_id = id;
                        $('#ajax_loader_div').css('display','block');
                        var ajaxreq2 =  $.ajax({
                            url: "/accounting/voucher/type/get/table/element/" +  element_id,
                            type: "GET",
                            success: function(result) {
                                var ele1 = '';
                                for (j=0;j<result.length;j++)                                            
                                {
                                    ele1 = ele1 + '<option value="'+result[j][option_val]+'">'+result[j][option_name]+'</option>';
                                }
                                // add select2 to select element with class 'input-css'
                                $(document).find('#UsrSetting'+element_id)).append(ele1).select2({
                                    containerCssClass:"input-css"
                                });
                                $('#ajax_loader_div').css('display','none');
                            }
                        });
                        $.when(ajaxreq2 ).done();
                    }
                    ele=ele + '</div>'+
                        '</div>';
                    // creating container for children of element
                    if(childrenId!="")
                    {
                        ele = ele + '<div id="UsrSetting_child'+id+'" ></div>';                                    
                    }
                    $('#'+element).empty();
                    // add new element and adding select2 to select element with class 'input-css'
                    $('#'+element).append(ele).find('.select2').select2({
                        containerCssClass:"input-css"
                    });
                }
                $('#ajax_loader_div').css('display','none');

            }
        });
    }
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
            <form method="POST" action="/accounting/vouchertype/update/{{$id}}" id="voucher_type_form">
                @csrf
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/voucher.update title')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="VoucherName">{{__('accounting/voucher.vouchername')}}</label>
                                <input type="text" name="VoucherName" value="{{$ledger_data['name']['current_value']}}" class="form-control input-css VoucherName" id="VoucherName">
                                <input type="hidden" name="id" value="{{$id}}">
                            </div>
                            <div class="col-md-6">
                                <label for="VoucherAlias">{{__('accounting/voucher.voucheralias')}}</label>
                                <input type="text" name="VoucherAlias" value="{{$ledger_data['alias']['current_value']}}" class="form-control input-css VoucherAlias" id="VoucherAlias">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="VoucherType">{{__('accounting/voucher.vouchertype')}}</label>
                                <select class="select form-control input-css VoucherType" id="VoucherType" name="VoucherType">
                                    <option value=" ">Select Voucher Type</option>    
                                    @foreach ($under as $key)                                            
                                        <option value="{{$key->id}}" {{$ledger_data['parent']['current_value']==$key->id?'selected="selected"':''}}>{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="VoucherAbbr">{{__('accounting/voucher.abbr')}}</label>
                                <input type="text" value="{{$ledger_data['mailing_name']['current_value']}}" class=" form-control input-css VoucherAbbr" id="VoucherAbbr" name="VoucherAbbr">
                                    
                            </div>
                        </div>
                        <br>
                        <hr>
                    </div>
                </div>
            </div>
            <div id="VoucherLayout" >
                    <div class="box-header with-border">
                        <div class="box box-default"><br>
                            <div class="container-fluid wdt">
                                <div class="row">

                                    @php
                                    // array to store section data
                                        $section['1']='';
                                        $section['2']='';
                                        $section['3']='';
                                        for($times = 1;$times<4;$times++)
                                        {
                                            $ledgerSetting = $led_data[$times];
                                            foreach($ledgerSetting as $v=> &$key)
                                            {   
                                                // storing variable for easy access
                                                $result = $key;
                                                $id = $result['id'];
                                                $title = $result['title'];
                                                $name2 = $result['name2'];
                                                $name1 = $result['name1'];
                                                $name = $result['name'];
                                                $current_value_1 = $result['current_value_1'];
                                                $part = $result['section'];
                                                $allowed_value = $result['value'];
                                                $allowed_value_db = $result['value_db'];
                                                $type = $result['type'];
                                                $childrenId = $result['children_id'];
                                                // variable to store event of element
                                                $events='';
                                                $ele = '<div class="row">'.
                                                        '<div class="col-md-12" id="UsrSetting_orig_'.$id.'">'.
                                                        '<label for="UsrSetting'.$id.'">'.$title.'</label>';
                                                // assigning event for element
                                                if($childrenId!="")
                                                {   
                                                $events = 'onchange="getchildren(this.value,\''.$childrenId.'\',\'UsrSetting_child'.$id.'\')"';                                                             
                                                }
                                                // creating element
                                                if($type=='email'|| $type=='text')
                                                {
                                                $ele = $ele .
                                                        '<input type="text" value="'.$current_value_1.'" class="input-css form-control UsrSettingTxt UsrSetting'.$id.'" name="'.$name.'" id="UsrSetting'.$id.'">';
                                                
                                                }
                                                else if($type=='option')
                                                {
                                                    $ele = $ele . 
                                                        '<select '.$events .' class="select form-control input-css UsrSettingOpt UsrSetting'.$id.'" id="UsrSetting'.$id.'" name="'.$name.'">'.
                                                                '<option value="default">Select Value</option>';
                                                                $values = explode('---',$allowed_value);   
                                                                $values_db = explode('---',$allowed_value_db);   
                                                                for ($j=0;$j<count($values);$j++)                                            
                                                                {
                                                                    if($values_db[$j] == $current_value_1)
                                                                        $ele = $ele . '<option value="'.$values_db[$j].'" selected="selected" >'.$values[$j].'</option>';
                                                                    else
                                                                        $ele = $ele . '<option value="'.$values_db[$j].'" >'.$values[$j].'</option>';
                                                                }
                                                                $ele = $ele.  
                                                        '</select>';
                                                }
                                                
                                                $ele=$ele . '</div>'.
                                                '</div>';
                                                // creating container for element children
                                                if($childrenId!="")
                                                {   
                                                $ele = $ele . '<div id="UsrSetting_child'.$id.'" ></div>';                                                           
                                                }
                                                // adding element to respective element
                                                $section[$part] = $section[$part] .$ele;
                                            }
                                        }
                                        // displaying data section wise
                                   echo '<div class="col-md-4" id="VoucherLayout1">'.
                                        '<h2 class="box-title" style="font-size: 20px;margin-left:20px">'.$heading["1"].'</h2><br><br><br>'.
                                        $section['1'].
                                    '</div>'.
                                    '<div class="col-md-4" id="VoucherLayout2">'.
                                        '<h2 class="box-title" style="font-size: 20px;margin-left:20px">'.$heading["2"].'</h2><br><br><br>'.
                                        $section['2'].           
                                    '</div>'.
                                    '<div class="col-md-4" id="VoucherLayout3">'.
                                        '<h2 class="box-title" style="font-size: 20px;margin-left:20px">'.$heading["3"].'</h2><br><br><br>'.
                                        $section['3'].          
                                    '</div>';
                                    @endphp
                                </div>
                            </div>
                    
                            <br>
                            <br>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-success">
                    </div>
                </div>         
                        </form>
    </section><!--end of section-->
@endsection


