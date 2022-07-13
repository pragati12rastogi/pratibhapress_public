
@extends($layout)

@section('title', __(''))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('')}}</i></a></li>
 @endsection
@section('css')
<style>
    /* css for adding differentiation between focused and non focused element */
    .input-css:focus, .select2-search__field:focus{
    background-color: #FAEF9F !important;
}
.input-css:focus option{
    background-color: #ffffff !important;
}
</style>
@endsection

@section('js')
    <script src="/js/accounting/Ledger.js"></script>
    <script>
        $(document).ready(function(){
            // add select2 to select element with class 'input-css'
            $('.select').select2({
                containerCssClass:"input-css"
            });
            // add form element on ledger change.
            $('.ledger_under').change(function(e){
                var under = $(e.target).val();
                // empty old form element
                $('#ledgerData').empty();
                if(under!='default')
                {
                    // api to get new form elements
                    $('#ajax_loader_div').css('display','block');
                    $.ajax({
                        url: "/accounting/getledgerform/"+under,
                        type: "GET",
                        success: function(result) {
                            console.log(result);
                            // add new form data and add select2 to select element with class 'input-css'
                            $('#ledgerData').append(result.data).find('.select').select2({
                                containerCssClass:"input-css"
                            });
                            
                            var x= result.child1;
                            for(i=0;i<x.length;i++){
                                getchildren(x[i].a,x[i].b,x[i].c);
                            }
                            
                            var y=result.child2;
                            for(i=0;i<y.length;i++){
                                getModalChildren(y[i].a,y[i].b,y[i].c);
                            }
                            $('#ajax_loader_div').css('display','none');
                        }
                    });
                }
            });
        });
        var nop=0;
        function getchildren(value,childrenId,element)
        {
            /*
            * function to get children of element 
            * value = value of element generating request
            * children = _ seperated id of children element
            * element = id of the container
            */
            $('#'+element).empty();
            if(value!=" ")
            {
                // showing the loader
                $('#ajax_loader_div').css('display','block');
                // api for getting child elements
                $.ajax({
                    url: "/accounting/ledger/avail/children/" + value +'/'+childrenId,
                    type: "GET",
                    success: function(result) {
                        for(i=0;i<result.length;i++)
                        {  
                            // accumulator for creating unique id
                            nop++;
                            // variable to store event of element
                            events='';
                            // storing data for easy access
                            id = result[i].id;
                            name = result[i].name;
                            part = result[i].section;
                            allowed_value = result[i].value;
                            allowed_value_db = result[i].value_db;
                            default_type = result[i].default_value;
                            type = result[i].type;
                            title=result[i].title;
                            attributes = result[i].attributes;
                            childrenId = result[i].children_id;
                            required = result[i].required;
                            change_form = result[i].change_form;
                            // generating element
                            if (type=='modal-label')
                            {   
                                ele=ele+'<h4 class="modal-title">'+title+'</h4>'+
                                    '</div>'+
                                    '<div class="modal-body">';
                                    continue;
                            }
                            // generating element
                            var ele = '<div class="row">'+
                                    '<div class="col-md-12">'+
                                    '<label for="UsrSetting_'+nop+"_"+id+'">'+title+'</label>';
                            if(type=='label')
                            {
                                ele=ele+'</div></div>';
                                continue;
                            }
                            // generating element event
                            if(childrenId!="" && required==1)
                            {   
                                events = 'onchange="getchildren(this.value,\''+childrenId+'\',\'UsrSetting_child_'+nop+"_"+id+'\');validateModalFields(\''+type+'\',this);"';                                                             
                            }
                            // generating element event
                            else if(childrenId!="")
                            {   
                                events = 'onchange="getchildren(this.value,\''+childrenId+'\',\'UsrSetting_child_'+nop+"_"+id+'\')"';                                                             
                            }
                            // generating element event
                            if(change_form!="" && change_form!=null)
                            {   
                                events = 'onchange="getModalChildren(this.value,\''+change_form+'\',\'UsrSetting_child_'+nop+"_"+id+'\')"';                                                             
                            }
                            
                            var bind_value= default_type;
                            if(bind_value != null && bind_value != ''){
                                bind_value= default_type.split(':');
                                var first_parameter= bind_value[0];
                                var input_value='';
                                if(first_parameter == 'get'){
                                    var sec_parameter= bind_value[1];
                                    var third_parameter= bind_value[2];
                                    input_value= bind_value[3].split(',');
                                    
                                    
                                }else if(first_parameter == 'set'){
                                    input_value= bind_value[2];
                                }
                            }else{
                                input_value='';
                            }
                            
                            // generating element
                            if(type=='text'|| type=='email' || type=='number'||type=='date')
                            {
                                // variable for storing class of element
                                var classes = '';
                                // generating element class
                                if(required==1)
                                {
                                    classes = 'UsrSettingTxt';
                                    attributes+=' required="required"';
                                }
                                ele = ele + 
                                    '<input '+events + attributes +' type="'+type+'" value="'+input_value+'" class="input-css form-control '+classes+' UsrSetting_'+nop+"_"+id+'" name="'+name+'" id="UsrSetting_'+nop+"_"+id+'">';
                            }   
                            else if(type=='option')
                            {
                                var classes = '';
                                if(required==1)
                                {
                                    classes = 'UsrSettingOpt';
                                    attributes+='required="required"';
                                }
                                ele = ele + 
                                    '<select '+events+ attributes +' class="select form-control input-css '+classes+' UsrSetting'+id+'" id="UsrSetting_'+nop+"_"+id+'" name="'+name+'">'+
                                        '<option value=" ">Select Value</option>';
                                var values = allowed_value.split(',');
                                var values_db = allowed_value_db.split(',');
                                for (j=0;j<values.length;j++)                                            
                                {   
                                    if(input_value == values_db[j]){
                                        ele = ele + '<option value="'+values_db[j]+'" selected="selected">'+values[j]+'</option>';
                                    }else{
                                        ele = ele + '<option value="'+values_db[j]+'">'+values[j]+'</option>';
                                    }
                                }
                                ele = ele+  
                                '</select>';
                            }
                            else if(type=='select-table')
                            {
                                var cols = allowed_value.split(':')[0];
                                var option_val = cols.split(',')[0];
                                var option_name = cols.split(',')[1];
                                var classes = '';
                                if(required==1)
                                {
                                    classes = 'UsrSettingOpt';
                                    attributes+=' required="required"';
                                }
                                ele = ele + '<select '+events+ attributes +' class="select form-control input-css '+classes+' UsrSetting'+id+'" id="UsrSetting_'+nop+"_"+id+'" name="'+name+'">'+
                                        '<option value=" ">Select Value</option>'+
                                    '</select>';
                                element_id = id;
                                old_nop = nop;
                                $('#ajax_loader_div').css('display','block');
                                console.log("pos","here"+nop);
                                // 
                                var ajaxreq2 =  $.ajax({
                                    url: "/accounting/ledger/get/table/element/" + element_id,
                                    type: "GET",
                                    data: {
                                        ele_id:'UsrSetting_'+old_nop+"_"+element_id
                                    },
                                    success: function(result) {
                                        var pos_id = result.id;
                                        result = result.data;
                                        var ele1 = '';
                                        for (j=0;j<result.length;j++)                                            
                                        {
                                            if(input_value == result[j][option_name]){
                                             ele1 = ele1 + '<option value="'+result[j][option_val]+'" selected="selected">'+result[j][option_name]+'</option>';
                                            }else{
                                                ele1 = ele1 + '<option value="'+result[j][option_val]+'">'+result[j][option_name]+'</option>';
                                            }
                                        }
                                        // add select2 to select element with class 'input-css'
                                        $($(document).find(pos_id)).append(ele1).select2({
                                            containerCssClass:"input-css"
                                        });
                                        $('#ajax_loader_div').css('display','none');

                                    }
                                });
                                $.when(ajaxreq2 ).done(
                                    function(){
                                        console.log('ajaxreq2',ajaxreq2);
                                    }
                                );
                                console.log("pos","there");

                            }
                            ele=ele + '</div>'+
                                '</div>';
                            // generating container for childen
                            if(childrenId!="" || (change_form!="" && change_form!=null))
                            {
                                ele = ele + '<div id="UsrSetting_child_'+nop+"_"+id+'" >';
        
                                ele = ele + '</div>';                                    
                            }
                            // add new element and add select2 to select element with class 'input-css'
                            $('#'+element).append(ele).find('select').select2({
                                containerCssClass:"input-css"
                            });
                        }
                        // hiding the loader
                        $('#ajax_loader_div').css('display','none');
                    }
                });
            } 
            // add select2 to select element with class 'input-css'
            $(document).find($('.select')).select2({
                containerCssClass:"input-css"
            });
            $(document).find('.modal .modal-body').modal('handleUpdate')
        }
        function getNewLine(children,section)
        {
            /*
            * function to add new line in modal element
            * children = children id of the element
            * section = sectionid of the requesting element 
            */
            // display the loader
            $('#ajax_loader_div').css('display','block');
            // api to get newline children of modal
            $.ajax({
                url: "/accounting/ledger/avail/modal/newline/children/" + children +'/'+section,
                type: "GET",
                success: function(result) {
                    var ele = '';
                    console.log(result);
                    for(i=0;i<result.length;i++)
                    {     
                        // accumulator for creating unique id
                        nop++;
                        // variable to store event of element
                        events='';
                        // storing data for easy access
                        id = result[i].id;
                        name = result[i].name;
                        part = result[i].section;
                        allowed_value = result[i].value;
                        allowed_value_db = result[i].value_db;
                        type = result[i].type;
                        title=result[i].title;
                        childrenId = result[i].children_id;
                        required = result[i].required;
                        change_form = result[i].change_form;
                        // generating element
                        if (type=='modal-label')
                        {   
                            ele=ele+'<h4 class="modal-title">'+title+'</h4>'+
                                '</div>'+
                                '<div class="modal-body">';
                                continue;
                        }    
                        // generating element
                        ele = ele+'<div class="row">'+
                            '<div class="col-md-12">'+
                            '<label for="UsrSetting_'+nop+"_"+id+'">'+title+'</label>';
                        if(type=='label')
                        {
                            ele=ele+'</div></div>';
                            continue;
                        }
                        // generating element event
                        if(childrenId!="" && required==1)
                        {   
                            events = 'onchange="getchildren(this.value,\''+childrenId+'\',\'UsrSetting_child_'+nop+"_"+id+'\');validateModalFields(\''+type+'\',this);"';                                                             
                        }
                        // generating element event
                        else if(childrenId!="")
                        {   
                            events = 'onchange="getchildren(this.value,\''+childrenId+'\',\'UsrSetting_child_'+nop+"_"+id+'\')"';                                                             
                        }
                        var bind_value= default_type;
                            if(bind_value != null && bind_value != ''){
                                bind_value= default_type.split(':');
                                var first_parameter= bind_value[0];
                                var input_value='';
                                if(first_parameter == 'get'){
                                    var sec_parameter= bind_value[1];
                                    var third_parameter= bind_value[2];
                                    input_value= bind_value[3].split(',');
                                    
                                    
                                }else if(first_parameter == 'set'){
                                    input_value= bind_value[2];
                                }
                            }else{
                                input_value='';
                            }
                        // generating element
                        if(type=='text'|| type=='email'|| type=='number'||type=='date')
                        {
                            // variable for storing class of element
                            var classes = '';
                            // generating element class
                            if(required==1)
                            {
                                classes = 'UsrSettingTxt';
                                attributes+=' required="required"';
                            }
                            ele = ele + 
                                '<input '+events+ attributes +' type="'+type+'" class="input-css form-control '+classes+' UsrSetting'+id+'" name="'+name+'" id="UsrSetting_'+nop+"_"+id+'">';
                            
                        }   
                        else if(type=='option')
                        {
                            var classes = '';
                            if(required==1)
                            {
                                classes = 'UsrSettingOpt';
                                attributes+=' required="required"';
                            }
                            
                            ele = ele + 
                                '<select '+events+ attributes +' class="select form-control input-css '+classes+' UsrSetting'+id+'" id="UsrSetting_'+nop+"_"+id+'" name="'+name+'">'+
                                    '<option value=" ">Select Value</option>';
                                    var values = allowed_value.split(',');
                                    var values_db = allowed_value_db.split(',');
                                    
                                    for (j=0;j<values.length;j++)                                            
                                    {
                                        if(input_value == values_db[j]){
                                            ele = ele + '<option value="'+values_db[j]+'"selected="selected">'+values[j]+'</option>';
                                        }else{
                                            ele = ele + '<option value="'+values_db[j]+'">'+values[j]+'</option>';
                                        }
                                    }
                                ele = ele+  
                            '</select>';
                        }
                        else if(type=='select-table')
                        {
                            var cols = allowed_value.split(':')[0];
                            var option_val = cols.split(',')[0];
                            var option_name = cols.split(',')[1];
                            var classes = '';
                            if(required==1)
                            {
                                classes = 'UsrSettingOpt';
                                attributes+=' required="required"';
                            }  
                            ele = ele + 
                            '<select '+events+ attributes +' class="select form-control input-css '+classes+' UsrSetting'+id+'" id="UsrSetting_'+nop+"_"+id+'" name="'+name+'">'+
                                    '<option value=" ">Select Value</option>'+
                                '</select>';
                                element_id = id;
                                var ajaxreq2 =  $.ajax({
                                    url: "/accounting/ledger/get/table/element/" +  element_id,
                                    type: "GET",
                                    data: {
                                        ele_id:'UsrSetting_'+nop+"_"+element_id
                                    },
                                    success: function(result) {
                                        $('#ajax_loader_div').css('display','block');
                                        var pos_id = result.id;
                                        result = result.data;
                                        var ele1 = '';
                                        for (j=0;j<result.length;j++)                                            
                                        {
                                            ele1 = ele1 + '<option value="'+result[j][option_val]+'">'+result[j][option_name]+'</option>';
                                        }       
                                        // add select2 to select element with class 'input-css'
                                        $(document).find(pos_id).append(ele1).select2({
                                            containerCssClass:"input-css"
                                        });
                                        $('#ajax_loader_div').css('display','none');
                                        // $('#ajax_loader_div').css('display','none');

                                    }
                                });
                                $.when(ajaxreq2 ).done();
                        }
                        ele=ele + '</div>'+
                            '</div>';
                        // generating container fro children
                        if(childrenId!="" || change_form!="" || change_form!=null)     
                        {
                            ele = ele + '<div id="UsrSetting_child_'+nop+"_"+id+'" ></div>';                                    
                        }
                    }
                    // hiding the loader
                    $('#ajax_loader_div').css('display','none');
                    // add new element to form
                    $('#nextEle'+children).append(ele+'<hr>');
                    // handling the update of modal so that the scroll of modal keeps working.
                    $(document).find('.modal .modal-body').modal('handleUpdate');
                    // add select2 to select element with class 'input-css'
                    $(document).find($('.select')).select2({
                        containerCssClass:"input-css"
                    });
                }
            });  
        }
        function getModalChildren(value,childrenId,element)
        {
            /**
            * function to get modal children alond with modal box.
            * value = value of element generating request
            * childrenID = _ seperated id of children element
            * element = id of element children container
            */
            var origChildrenId = childrenId;
            if(value==1)// yes is selected
            {
                // showing the loader
                $('#ajax_loader_div').css('display','block');
                // checking if modal alreaady created or not.
                if($(document).find($('#'+element+'myModal')).length>0 )
                {    
                    // showing modal container
                    $(document).find($('#'+element)).show();
                    // opening the modal
                    $(document).find('#'+element+'myModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    // add select2 to select element with class 'input-css'
                    $(document).find('#'+element+'myModal').on("shown.bs.modal",function(){
                        $(document).find($('.select')).select2({
                            containerCssClass:"input-css"
                        });
                    });
                    // add select2 to select element with class 'input-css'
                    $(document).find($('.select')).select2({
                        containerCssClass:"input-css"
                    });
                    // hiding the loader
                    $('#ajax_loader_div').css('display','none');
                }
                else
                {
                    // api to get modal data
                    $.ajax({     
                    url: "/accounting/ledger/avail/modal/children/" + value +'/'+childrenId,
                    type: "GET",
                    success: function(result) {
                        // creating modal div and button to open it
                        var ele = '<div class="col-md-2">'+
                            '<button type="button" style="margin-right:1em" class="btn btn-sm bg-navy " onclick="$(\'#'+element+'myModal\').modal(\'show\')">More Details</button>'+
                        '</div>'+ 
                        '<div id="form_status-'+element+'myModal_div" class="col-md-4">'+
                            '<div class="col-md-4">'+
                                '<label>Error:</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<input max="0" type="number" id="form_status-'+element+'myModal" class="form_status form-control">'+ 
                            '</div>' +
                        '</div>'+
                        '<div id="'+element+'myModal" style="overflow-y:scroll;padding: 20px;" class="modal fade" role="dialog">'+
                        '<div class="modal-dialog modal-lg">'+
                            '<div class="modal-content">'+
                                '<div class="modal-header">'+
                                    '<button type="button" class="close" onclick="validateModal(\''+element+'myModal\')">&times;</button>';
                                                
                        for(i=0;i<result.length;i++)
                        {  
                            // accumulator for creating unique id
                            nop++;
                            // variable to store event of element
                            events='';
                            // storing data for easy access
                            id = result[i].id;
                            name = result[i].name;
                            part = result[i].section;
                            allowed_value = result[i].value;
                            attributes = result[i].attributes;
                            allowed_value_db = result[i].value_db;
                            default_type= result[i].default_value;
                            type = result[i].type;
                            title=result[i].title;
                            childrenId = result[i].children_id;
                            required = result[i].required;
                            change_form = result[i].change_form;
                            // generating element
                            if (type=='modal-label')
                            {   
                                ele=ele+'<h4 class="modal-title">'+title+'</h4>'+
                                    '</div>'+
                                    '<div class="modal-body" style="scroll-behavior:smooth;overflow-y: auto;padding: 20px;max-height: 480px;position: relative;">';
                                    continue;
                            }
                            // generating element  
                            ele = ele+'<div class="row">'+
                                '<div class="col-md-12">'+
                                '<label for="UsrSetting_'+nop+"_"+id+'">'+title+'</label>';
                            if(type=='label')
                            {
                                ele=ele+'</div></div>';
                                continue;
                            }
                            // generating element event
                            if(childrenId!="" && required==1)
                            {   
                                events = 'onchange="getchildren(this.value,\''+childrenId+'\',\'UsrSetting_child_'+nop+"_"+id+'\');validateModalFields(\''+type+'\',this)"';
                            }
                            // generating element event
                            else if(childrenId!="" )
                            {
                                events = 'onchange="getchildren(this.value,\''+childrenId+'\',\'UsrSetting_child_'+nop+"_"+id+'\')"';
                            }
                            
                            var bind_value= default_type;
                            if(bind_value != null && bind_value != ''){
                                bind_value = default_type.split(':');
                                var first_parameter= bind_value[0];
                                var input_value='';
                                if(first_parameter == 'get'){
                                    var sec_parameter= bind_value[1];
                                    var third_parameter= bind_value[2];
                                    input_value= bind_value[3].split(',');
                                    
                                }else if(first_parameter == 'set'){
                                    input_value= bind_value[2];
                                }
                            } else{
                                input_value=' ';
                            }             
                           

                            // generating element
                            if( type=='email' || type=='text'||type=='number'||type=='date')
                            {
                                // variable for storing class of element
                                var classes = '';
                                // generating element class
                                if(required==1)
                                {
                                    classes = 'UsrSettingTxt';
                                    attributes+=' required="required"';
                                }
                                ele = ele + 
                                    '<input '+events+ attributes +' type="'+type+'" class="input-css form-control UsrSetting'+id+'" name="'+name+'" id="UsrSetting_'+nop+"_"+id+'">';
                            }   
                            else if(type=='option')
                            {
                                var classes = '';
                                if(required==1)
                                {
                                    classes = 'UsrSettingOpt';
                                    attributes+=' required="required"';
                                }
                                ele = ele + 
                                    '<select '+events+ attributes +' class="select form-control input-css UsrSetting'+id+'" id="UsrSetting_'+nop+"_"+id+'" name="'+name+'">'+
                                        '<option value=" " >Select Value</option>';
                                        var values = allowed_value.split(',');
                                        var values_db = allowed_value_db.split(',');
                                        for (j=0;j<values.length;j++)                                            
                                        {
                                            if(input_value == values_db[j]){
                                                ele = ele + '<option value="'+values_db[j]+'" selected="selected">'+values[j]+'</option>';
                                            }else{
                                                ele = ele + '<option value="'+values_db[j]+'">'+values[j]+'</option>';
                                            }
                                        }
                                    ele = ele+  
                                '</select>';
                            }
                            else if(type=='select-table')
                            {
                                var classes = '';  
                                if(required==1)
                                {
                                    classes = 'UsrSettingTxt';
                                    attributes+=' required="required"';
                                }                          
                                var cols = allowed_value.split(':')[0];
                                var option_val = cols.split(',')[0];
                                var option_name = cols.split(',')[1];
                                ele = ele + 
                                '<select '+events+ attributes +' class="select form-control input-css  UsrSetting'+id+'" id="UsrSetting_'+nop+"_"+id+'" name="'+name+'">'+
                                        '<option value=" ">Select Value</option>'+
                                    '</select>';
                                    element_id = id;
                                    old_nop = nop;
                                var ajaxreq2 =  $.ajax({
                                    url: "/accounting/ledger/get/table/element/" +  element_id,
                                    type: "GET",
                                    data: {
                                        ele_id:'UsrSetting_'+old_nop+"_"+element_id
                                    },
                                    success: function(result) {
                                        $('#ajax_loader_div').css('display','block');
                                        var ele1 = '';
                                        var pos_id = result.id;
                                        result = result.data;

                                        for (j=0;j<result.length;j++)                                            
                                        {
                                            if(input_value == result[j][option_name]){
                                                ele1 = ele1 + '<option value="'+result[j][option_val]+'" selected="selected">'+result[j][option_name]+'</option>';
                                            }else{
                                                ele1 = ele1 + '<option value="'+result[j][option_val]+'">'+result[j][option_name]+'</option>';

                                            }
                                        }
                                        // add select2 to select element with class 'input-css'
                                        $($(document).find(pos_id)).append(ele1).select2({
                                            containerCssClass:"input-css"
                                        });
                                        $('#ajax_loader_div').css('display','none');

                                    }
                                });
                                $.when(ajaxreq2 ).done(
                                );
                            }
                            ele=ele + '</div>'+
                                '</div>';
                            // generating container for childen
                            if(childrenId!="" || change_form!="" || change_form!=null)
                            {
                                ele = ele + '<div id="UsrSetting_child_'+nop+"_"+id+'" ></div>';                                    
                            }
                        }
                        ele  = ele + 
                        // div for storing append data
                        '<hr><div id="nextEle'+origChildrenId+'"></div>'+
                                '</div></>'+
                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-primary" onclick="getNewLine(\''+origChildrenId+'\',2)">Add New Entry</button>'+
                                    '<button type="button" class="btn btn-default" onclick="validateModal(\''+element+'myModal\')">Close</button>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '</div>'
                        ;
                        // add new element 
                        $('#'+element).empty().append(ele);
                        // opening the modal
                        $(document).find('#'+element+'myModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        $(document).find('#'+element+'myModal').on("shown.bs.modal",function(){
                            // add select2 to select element with class 'input-css'
                            $(document).find($('.select')).select2({
                                containerCssClass:"input-css"
                            });
                        });
                        // add select2 to select element with class 'input-css'
                        $(document).find($('.select')).select2({
                            containerCssClass:"input-css"
                        });
                        // hiding the loader
                        $('#ajax_loader_div').css('display','none');
                    }
                    });
                }
            }
            else
            {
                // hiding the children div.
                if($(document).find($('#'+element+'myModal')).length>0 )
                {
                    $(document).find($('#'+element)).hide();
                }
            }
            // add select2 to select element with class 'input-css'
            $('.select').select2({
                containerCssClass:"input-css"
            }); 
            // handling modal update so that scroll of modal work properly
            $(document).find('.modal .modal-body').modal('handleUpdate')
        }
        function validateModalFields(type,ele)
        {
            /*
            * function to validate modal fields
            * type = type attribute of form element
            * ele = element generating request
            */
            var err=0;
            // adding and removing error message against validation 
            if(type=='option')
            {
                if($(ele).val()==" " || $(ele).val()=="default")
                {    
                    $(ele).parent().find('label.error').remove();
                    $(ele).parent().append('<label class="error">This field is required</label>');
                    err++;
                }
                else
                {
                    $(ele).parent().find('label.error').remove();
                }
            }
            else
            {
                if($(ele).val()=="" || $(ele).val()==0)
                {    
                    $(ele).parent().find('label.error').remove();
                    $(ele).parent().append('<label class="error">This field is required</label>');
                    err++;                    
                }
                else
                {
                    $(ele).parent().find('label.error').remove();
                }
            }
            return err;
        } 
        function validateModal(ele_id)
        {
            /*
            * function to validate modal 
            * ele_id = id of the modal to be validated
            */
            console.log(ele_id);
            var err=0;
            // validating each element of modal
            $(document).find('#'+ele_id).find('input[required]:visible').each(function(i,e) {
                err+= validateModalFields($(e).attr('type'),$(e));
            });
            $(document).find('#'+ele_id).find('select[required]:visible').each(function(i,e) {
                err+=validateModalFields('option',$(e));
            });
            // updating input value with number of error.
            if(err!=0)
            {
                $("#form_status-"+ele_id).val(err);
            }
            else    
                $("#form_status-"+ele_id+'_div').hide();
            // hiding the modal
            $('#'+ele_id).modal('hide');
                
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
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/ledger.createLedger')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <form method="POST" action="/accounting/ledger/create" id="ledger_form">
                            @csrf
                         
                          
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="ledger_name">{{__('accounting/ledger.name')}}</label>
                                    <input name="ledger_name" class="form-control UsrSettingTxt input-css ledger_name" id="ledger_name" >
                                </div>
                            </div>
                            @if( $showAlias=='Yes')
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="ledger_alias">{{__('accounting/ledger.alias')}}</label>
                                        <input name="ledger_alias" class="form-control input-css ledger_alias" id="ledger_alias" >
                                    </div>
                                </div>
                            @endif
                            @if( $addNotes=='Yes')
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="ledger_notes">{{__('accounting/ledger.notes')}}</label>
                                        <input name="ledger_notes" class="form-control input-css ledger_notes" id="ledger_notes" >
                                    </div>
                                </div>
                            @endif
                            
                            <br>
                            <br>
                            <hr>
                            <br>
                            <div class="row">             
                                <div class="col-md-6">
                                    <label for="ledger_under">{{__('accounting/ledger.under')}}</label>
                                    <select class="select input-css form-control ledger_under" name="ledger_under" id="ledger_under">
                                        <option value="default">Select Group</option>
                                        @foreach ($group as $key)
                                            <option value="{{$key->id}}">{{$key->name}}</option>                                            
                                        @endforeach
                                    </select>
                                </div>    
                            </div>
                            <br><br><br>
                            {{-- container to store ledger form data --}}
                            <div id="ledgerData">
                            </div>
                            <br>
                            <br>
                            <div class="row">                                
                                <div class="col-md-8 form-group">
                                    <div class="col-md-3">  
                                        <label for="opening_balance">{{__('accounting/ledger.opening balance')}}</label>
                                    </div>
                                    <div class="col-md-3">  
                                        <input type="text" class=" input-css form-control opening_balance" value="0" name="opening_balance" id="opening_balance">                                        
                                    </div>
                                    <div class="col-md-3">                                          
                                        <select class="select balance_type input-css form-control" id="balance_type" name="balance_type"> 
                                            <option value="cr" selected>Credit</option>
                                            <option value="dr">Debit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success">
                            </div>
                        </form>
                       

                    </div>
                </div>
            </div>                 
    </section><!--end of section-->
@endsection