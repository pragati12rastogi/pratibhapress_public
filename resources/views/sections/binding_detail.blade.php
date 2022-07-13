<?php  //print($labels);die();?>

@extends($layout)

@section('title', __('binding.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('binding.mytitle')}}</i></a></li>
   @endsection
@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="css/party.css">
<style>
.span
{
    color:red;
}
</style>
@endsection

@section('js')
<script src="js/views/element.js"></script>
<script>
    var id;

            $('#box2').empty();
          var io_id = "{{Session::get('io_id')}}";
          $('#ajax_loader_div').css('display','block');

          $.ajax({

              url: "/jobcard/detail/" + io_id,
              type: "GET",
                success: function(result)
                {
                    var arr= result[0];      
                    var id=result[0].item_category_id;
                    var name=result[0].name;
                    var element=result[0].elements;
                    var elem=element.split(',');
                    var count=elem.length;
                    
                    var elem_cat={!! json_encode($element->toArray(), JSON_HEX_TAG) !!};
                    var binding_item_cat={!! json_encode($binding_item->toArray(), JSON_HEX_TAG) !!};
               
                    $('#box2').append(
                        '<div class="box box-default">'+
                            '<div class="container-fluid">'+
                                '<h3 class="box-title">{{__('binding.mytitle')}}'+" "+ name +'</h3>'+
                                '<label id="elem_type[]-error" class="error" for="elem_type[]"></label>'+
                                '{!! $errors->first('elem_type', '<p class="help-block" style="color:#f11717">:message</p>') !!}'+
                                '<div class="row element">'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="box box-default inside_elm" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+
                            '<div class="row inside_label">'+
                            '</div>'+
                        '</div>'+
                        '<div class="box box-default inside_elm1" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+
                            '<div class="row inside_label1">'+
                            '</div>'+
                        '</div>'+
                        '<div class="box box-default inside_elm2" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+
                            '<div class="row inside_label2">'+
                            '</div>'+
                        '</div>'+
                        '<div class="box box-default inside_elm3" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+
                            '<div class="row inside_label3">'+
                            '</div>'+
                        '</div>'+
                        '<div class="box box-default inside_elm4" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+
                            '<div class="row inside_label4">'+
                            '</div>'+
                        '</div>'
                    );
                    if(id>5)
                    {
                        $('.element').append('<input requiredtype="hidden" name="elem_type[]" id="" value="'+element+'" class="elem_type">');
                            var elem_id=id-1;
                            var binding_item1=binding_item_cat[elem_id].form_labels_id;
                            var bind1=binding_item1.split(',');
                            //alert(bind1[0]);
                            
                            var count_bind1=bind1.length;
                            var labels1={!! json_encode($labels->toArray(), JSON_HEX_TAG) !!};
                                    for(var j=0;j<count_bind1;j++){
                                            var binding=bind1[j];
                                            var bind_label=binding-1;

                                            //alert(labels1[bind_label-1].labels);
                                            $('.element').append(
                                            '<div class="col-md-6">'+
                                                '<label>'+labels1[bind_label].labels+'<span class="span">*</span></label>'+
                                                '<div class="col-md-2">'+
                                                    ' <div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="Yes" name="'+element+ "[" +labels1[bind_label].labels+"]"+'">Yes</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-2">'+
                                                    '<div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="No" name="'+element+ "[" +labels1[bind_label].labels+"]"+'">No</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-4">'+
                                                    '<div class="radio">'+
                                                        '<input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark"  name="'+"remark"+element+ "[" +labels1[bind_label].labels+"]"+'">'+
                                                    '</div>'+
                                                '</div>'+
                                        '</div>'
                                    );
                                        }
                                    $('.submit').show();
                    }
                    if(id<6){

                        for(var i=0;i<count;i++)
                        {
                            var element_put=elem[i]-1;
                            var element_put_name=elem_cat[element_put].name;
                            $('.element').append(
                            '<div class="col-md-2"><div class="radio"><label id="elem"><input type="checkbox" name="elem_type[]" id="" value="'+elem[i]+'" class="elem_type">'+element_put_name+'</label></div></div>'
                        );
                        }
                        $('input[type=checkbox][class=elem_type]').change(function()
                        {
                            $('.submit').show();
                            value = $(this).val();
                            x=value-1;
                            //alert(value);
                            y=elem_cat[x].name;
                            z=elem_cat[x].id;
                            var binding_item=binding_item_cat[x].form_labels_id;
                            //alert(binding_item);
                            var bind=binding_item.split(',');
                            var count_bind=bind.length;
                            var labels={!! json_encode($labels->toArray(), JSON_HEX_TAG) !!};

                                    if($(this).prop("checked") == true && value == "1")
                                    {
                                       
                                        for(var j=0;j<count_bind;j++){
                                            var binding=bind[j];
                                            var bind_label=binding-1;
                                            $('.inside_label').append(
                                            '<div class="col-md-6">'+
                                                '<label>'+labels[bind_label].labels+ "{" +y+ "}"+'<span class="span">*</span></label>'+
                                                '<div class="col-md-2">'+
                                                ' <div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="Yes" name="'+z+ "[" +labels[bind_label].labels+"]"+'">Yes</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-2">'+
                                                    '<div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="No" name="'+z+ "[" +labels[bind_label].labels+"]"+'">No</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                
                                                '<div class="col-md-4">'+
                                                    '<div class="radio">'+
                                                        '<input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark"  name="'+"remark"+z+ "[" +labels[bind_label].labels+"]"+'">'+
                                                    '</div>'+
                                                '</div>'+
                                        '</div>'
                                    );
                                        }
                                        $('.inside_elm').slideDown();
                                    }
                                    if($(this).prop("checked") == false && value == "1")
                                    {
                                        $('.inside_elm').empty();
                                        $('.inside_elm').hide();
                                    }
                                    if($(this).prop("checked") == true && value == "2")
                                    {
                                       
                                        for(var j=0;j<count_bind;j++){
                                            var binding=bind[j];
                                            var bind_label=binding-1;
                                            $('.inside_label1').append(
                                            '<div class="col-md-6">'+
                                                '<label>'+labels[bind_label].labels+ "{" +y+ "}"+'<span class="span">*</span></label>'+
                                                '<div class="col-md-2">'+
                                                ' <div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="Yes" name="'+z+ "[" +labels[bind_label].labels+"]"+'">Yes</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-2">'+
                                                    '<div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="No" name="'+z+ "[" +labels[bind_label].labels+"]"+'">No</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-4">'+
                                                        '<input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark"  name="'+"remark"+z+ "[" +labels[bind_label].labels+"]"+'">'+
                                                '</div>'+

                                        '</div>'
                                    );
                                  
                                        }
                                        $('.inside_elm1').slideDown();
                                    }
                                    if($(this).prop("checked") == false && value == "2")
                                    {
                                        $('.inside_elm1').empty();
                                        $('.inside_elm1').hide();
                                    }
                                    if($(this).prop("checked") == true && value == "3")
                                    {
                                       
                                        for(var j=0;j<count_bind;j++){
                                            var binding=bind[j];
                                            var bind_label=binding-1;
                                            $('.inside_label2').append(
                                            '<div class="col-md-6">'+
                                                '<label>'+labels[bind_label].labels+ "{" +y+ "}"+'<span class="span">*</span></label>'+
                                                '<div class="col-md-2">'+
                                                ' <div class="radio">'+
                                                        '<label><input required  autocomplete="off" type="radio"  class="" value="Yes" name="'+z+ "[" +labels[bind_label].labels+"]"+'">Yes</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-2">'+
                                                    '<div class="radio">'+
                                                        '<label><input required  autocomplete="off" type="radio"  class="" value="No" name="'+z+ "[" +labels[bind_label].labels+"]"+'">No</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-4">'+
                                                    '<div class="radio">'+
                                                        '<input   autocomplete="off" type="text" class="form-control input-css" placeholder="Remark"  name="'+"remark"+z+ "[" +labels[bind_label].labels+"]"+'">'+
                                                    '</div>'+
                                                '</div>'+
                                        '</div>'
                                    );
                                        }
                                        $('.inside_elm2').slideDown();
                                    }
                                    if($(this).prop("checked") == false && value == "3")
                                    {
                                        $('.inside_elm2').empty();
                                        $('.inside_elm2').hide();
                                    }
                                    if($(this).prop("checked") == true && value == "4")
                                    {
                                       
                                        for(var j=0;j<count_bind;j++){
                                            var binding=bind[j];
                                            var bind_label=binding-1;
                                            $('.inside_label3').append(
                                            '<div class="col-md-6">'+
                                                '<label>'+labels[bind_label].labels+ "{" +y+ "}"+'<span class="span">*</span></label>'+
                                                '<div class="col-md-2">'+
                                                ' <div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="Yes" name="'+z+ "[" +labels[bind_label].labels+"]"+'">Yes</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-2">'+
                                                    '<div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="No" name="'+z+ "[" +labels[bind_label].labels+"]"+'">No</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-2">'+
                                                    '<div class="radio">'+
                                                        '<input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark"  name="'+"remark"+z+ "[" +labels[bind_label].labels+"]"+'">'+
                                                    '</div>'+
                                                '</div>'+
                                        '</div>'
                                    );
                                   
                                        }
                                        $('.inside_elm3').slideDown();
                                    }
                                    if($(this).prop("checked") == false && value == "4")
                                    {
                                        $('.inside_elm3').empty();
                                        $('.inside_elm3').hide();
                                    }
                                    if($(this).prop("checked") == true && value == "5")
                                    {
                                       
                                        for(var j=0;j<count_bind;j++){
                                            var binding=bind[j];
                                            var bind_label=binding-1;
                                            $('.inside_label4').append(
                                            '<div class="col-md-6">'+
                                                '<label>'+labels[bind_label].labels+ "{" +y+ "}"+'<span class="span">*</span></label>'+
                                                '<div class="col-md-2">'+
                                                ' <div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="Yes" name="'+z+ "[" +labels[bind_label].labels+"]"+'">Yes</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-2">'+
                                                    '<div class="radio">'+
                                                        '<label><input required autocomplete="off" type="radio"  class="" value="No" name="'+z+ "[" +labels[bind_label].labels+"]"+'">No</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="col-md-2">'+
                                                    '<div class="radio">'+
                                                        '<input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark"  name="'+"remark"+z+ "[" +labels[bind_label].labels+"]"+'">'+
                                                    '</div>'+
                                                '</div>'+
                                        '</div>'
                                    );
                                   
                                        }
                                        $('.inside_elm4').slideDown();
                                    }
                                    if($(this).prop("checked") == false && value == "5")
                                    {
                                        $('.inside_elm4').empty();
                                        $('.inside_elm4').hide();
                                    }
                                });
                    }    

                    $('#ajax_loader_div').css('display','none');
                },
                error: function( jqXHR, textStatus, errorThrown) {
                    alert('status:');
                }

          });

</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            </div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
            <form action="/binding/insert" method="post" id="elementForm">
                @csrf
                    <div class="row">
                        <input  type="hidden" name="jc_id" value="{{Session::get('jc_id')}}">
                        <input  type="hidden" name="io_id" value="{{Session::get('io_id')}}">
                    </div>
                    <div class='' id="box2">

                    </div>
                    <div class="row submit" style="display:none">
                        <div class="col-md-12 text-right" >
                                <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
    </section><!--end of section-->
@endsection

