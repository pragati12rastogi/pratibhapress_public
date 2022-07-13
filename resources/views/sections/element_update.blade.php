<?php  //print($elem_count);
//print(Session::get('jc_id'));
//die();?>

@extends($layout)

@section('title', __('update_element.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('element.mytitle')}}</i></a></li>
@endsection
@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="/css/party.css">
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
<script>
$(document).ready(function() {

$('#elementForm').validate({
    rules: {
        "elem_type[]": {
            required: true,

        },

    },
    message: {
        required: "Atleast one Element Detail is required"
    },
});

});
</script>
<script>
    var id;
$(document).ready(function(e) {
    var count1;
            var io_id={{$io_id}};
            $('#box2').empty();
            $('#ajax_loader_div').css('display','block');

          $.ajax({
              url: "/jobcard/detail/" + io_id,
              type: "GET",
                success: function(result)
                {
                     var arr= result[0];       
                            var id=arr.item_category_id;
                            var name=arr.name;
                            var element=arr.elements;
                            var elem=element.split(',');
                            var count=elem.length;
                            count1=elem.length;
                            var counts={{$count}};
                            var elem_data={!! json_encode($elem->toArray(), JSON_HEX_TAG) !!};
                            var elem_cat={!! json_encode($element->toArray(), JSON_HEX_TAG) !!};
                           
                            
                            $('#box2').append(
                                '<div class="box box-default">'+
                                    '<div class="container-fluid">'+
                                        '<h3 class="box-title">'+name+" "+ '{{__('element.mytitle')}}</h3>'+
                                        '<div class="row">'+
                                            '<div class="col-md-12 element">'+
                                                '<label  class="elem_type_label_er">{{__('element.element_type')}}</label>'+
                                                '<label id="elem_type[]-error" class="error" for="elem_type[]"></label>'+
                                                '{!! $errors->first('elem_type', '<p class="help-block" style="color:#f11717">:message</p>') !!}'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="row">'+
                                            '<div class="col-md-3">'+
                                                '<label  class="elem_type_label_er">{{__('layout.update_reason')}}<sup>*</sup></label>'+
                                               '<input type="text" name="update_reason" required="" class="form-control input-css " id="update_reason">'+
                                                ' {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="box box-default inside_elm" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+

                                '</div>'+
                                '<div class="box box-default inside_elm1" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+

                                '</div>'+
                                '<div class="box box-default inside_elm2" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+

                                '</div>'+
                                '<div class="box box-default inside_elm3" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+

                                '</div>'+
                                '<div class="box box-default inside_elm4" style="display:none;padding-top: 22px;padding-bottom: 3px;">'+

                               '</div>'
                            );
                            if(id){
                                for(var i=0;i<count;i++){
                                    var element_put=elem[i]-1;
                                    var element_put_name=elem_cat[element_put].name;
                                    if(element_put!==5){
                                    $('.element').append(
                                        '<div class="col-md-2"><div class="radio"><label id="elem"><input type="checkbox" name="elem_type[]" id="" value="'+elem[i]+'"  class="elem_type">'+element_put_name+'</label></div></div>'
                                    );
                                             }
                                }
                                for(var m=0;m<counts;m++){
                                   
                                        $('input[type=checkbox][value="'+elem_data[m].element_type_id+'"]').prop('checked',true);
                                        $('input[type=checkbox][value="'+elem_data[m].element_type_id+'"]').prop('disabled',true);
                                        $('.submit').show();
                                     var values = elem_data[m].element_type_id;
                                   
                                    var plate_size=elem_data[m].plate_size;
                                    var front_color=elem_data[m].front_color;
                                    var back_color=elem_data[m].back_color;
                                   
                                    var val=values-1;
                                    var x=values;
                                    
                                    y=elem_cat[val].name;
                                    var texture='<div class="row">'+
                                                    '<div class="col-md-4">'+
                                                        '<label>'+ y+" "+'{{__('element.plate_size')}}<sup>*</sup></label>'+
                                                        '<select  class="form-control select2 input-css plate_size" style="width:100%;" required name="plate_size['+x+']">'+
                                                                '<option value="">Select Plate Size</option>'+
                                                                @foreach ($plate_size as $key)
                                                                    '<option value="{{$key->value}}">{{$key->value}}</option>'+
                                                                @endforeach
                                                                // '<option value="770*1030">770*1030</option>'+
                                                                // '<option value="664*530">664*530</option>'+
                                                        '</select>'+
                                                        '<label id="plate_size['+x+']-error" class="error" for="plate_size['+x+']"></label>'+
                                                    '</div>'+
                                                    '<div class="col-md-4">'+
                                                            '<label>'+ y+" "+'{{__('element.plate_sets')}}<sup>*</sup></label>'+
                                                            '<input type="text" required class="input-css" value="'+elem_data[m].plate_sets+'" name="plate_sets['+x+']" id="">'+
                                                    '</div>'+
                                                    '<div class="col-md-4">'+
                                                        '<label>'+ y+" "+'{{__('element.impression_plate_sets')}}<sup>*</sup></label>'+
                                                        '<input type="text" class="input-css" required value="'+elem_data[m].impression_per_plate+'"  name="impression_plate_sets['+x+']" id="">'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="row">'+
                                                    '<div class="col-md-4">'+
                                                        '<label>'+ y+" "+'{{__('element.front_color')}}<sup>*</sup></label>'+
                                                            '<select class="form-control front_color select2 input-css" style="width: 100%;"  required name="front_color['+x+']">'+
                                                                '<option value="">Select Front Color</option>'+
                                                                '<option value="0">0</option>'+
                                                                '<option value="1">1</option>'+
                                                                '<option value="2">2</option>'+
                                                                '<option value="3">3</option>'+
                                                                '<option value="4">4</option>'+
                                                                '<option value="5">5</option>'+
                                                                '<option value="6">6</option>'+
                                                                '<option value="7">7</option>'+
                                                                '<option value="8">8</option>'+
                                                        '</select>'+
                                                        '<label id="front_color['+x+']-error" class="error" for="front_color['+x+']"></label>'+
                                                    '</div>'+
                                                    '<div class="col-md-4">'+
                                                        '<label>'+ y+" "+'{{__('element.back_color')}}<sup>*</sup></label>'+
                                                        '<select class="form-control select2 back_color input-css" style="width: 100%;" required name="back_color['+x+']">'+
                                                                '<option value="">Select Back Color</option>'+
                                                                '<option value="0">0</option>'+
                                                                '<option value="1">1</option>'+
                                                                '<option value="2">2</option>'+
                                                                '<option value="3">3</option>'+
                                                                '<option value="4">4</option>'+
                                                                '<option value="5">5</option>'+
                                                                '<option value="6">6</option>'+
                                                                '<option value="7">7</option>'+
                                                                '<option value="8">8</option>'+
                                                        '</select>'+
                                                        '<label id="back_color['+x+']-error" class="error" for="back_color['+x+']"></label>'+
                                                    '</div>'+
                                                    '<div class="col-md-4 no_of_pages" style="display:none">'+
                                                        '<label>'+ y+" "+'{{__('element.no_of_pages')}}<sup>*</sup></label>'+
                                                        '<input type="number" class="input-css" value="'+elem_data[m].no_of_pages+'" name="no_of_pages['+x+']" id="">'+
                                                    '</div>'+
                                                    '<input   name="old_id[]" type="hidden" value="'+elem_data[m].id+'" class="old_elem">'+
                                                    '<input   name="old_elem[]" type="hidden" value="'+elem_data[m].element_type_id+'" class="old_elem">'+
                                                '</div>';

                                                if(values == "1")
                                                {
                                                   
                                                    $('.inside_elm').slideDown();
                                                    $('.inside_elm').append(texture);
                                                    $('select').select2();
                                                    $(".plate_size").val(plate_size).trigger("change");
                                                    $(".front_color").val(front_color).trigger("change");
                                                    $(".back_color").val(back_color).trigger("change");
                                                    $('.no_of_pages').show();
                                                }
                                                if(values == "2")
                                                {
                                                   
                                                    $('.inside_elm1').slideDown();
                                                    $('.inside_elm1').append(texture);
                                                    $(".plate_size").val(plate_size).trigger("change");
                                                     $(".front_color").val(front_color).trigger("change");
                                                    $(".back_color").val(back_color).trigger("change");
                                                    $('select').select2();
                                                }
                                               
                                                if(values == "3")
                                                {
                                                    $('.inside_elm2').slideDown();
                                                    $('.inside_elm2').append(texture);
                                                    $(".plate_size").val(plate_size).trigger("change");
                                                     $(".front_color").val(front_color).trigger("change");
                                                    $(".back_color").val(back_color).trigger("change");
                                                    $('select').select2();
                                                }
                                                if(values == "4")
                                                {
                                                    $('.inside_elm3').slideDown();
                                                    $('.inside_elm3').append(texture);
                                                    $(".plate_size").val(plate_size).trigger("change");
                                                     $(".front_color").val(front_color).trigger("change");
                                                    $(".back_color").val(back_color).trigger("change");
                                                    $('select').select2();
                                                }
                                               
                                                if(values == "5")
                                                {
                                                    $('.inside_elm4').slideDown();
                                                    $('.inside_elm4').append(texture);
                                                    $(".plate_size").val(plate_size).trigger("change");
                                                     $(".front_color").val(front_color).trigger("change");
                                                    $(".back_color").val(back_color).trigger("change");
                                                    $('select').select2();
                                                }
                                               
                                    }
                                    
                                $('input[type=checkbox][class=elem_type]').change(function()
                                {
                                    $('.submit').show();
                                    value = $(this).val();
                                    var z=value-1;
                                    var x=value;
                                    y=elem_cat[z].name;
                                    var texture='<div class="row">'+
                                                    '<div class="col-md-4">'+
                                                        '<label>'+ y+" "+'{{__('element.plate_size')}}<sup>*</sup></label>'+
                                                        '<select  class="form-control select2 input-css " style="width:100%;" required name="plate_size['+x+']">'+
                                                                '<option value="">Select Plate Size</option>'+
                                                                @foreach ($plate_size as $key)
                                                                    '<option value="{{$key->value}}">{{$key->value}}</option>'+
                                                                @endforeach
                                                                // '<option value="770*1030">770*1030</option>'+
                                                                // '<option value="664*530">664*530</option>'+
                                                        '</select>'+
                                                        '<label id="plate_size['+x+']-error" class="error" for="plate_size['+x+']"></label>'+
                                                    '</div>'+
                                                    '<div class="col-md-4">'+
                                                            '<label>'+ y+" "+'{{__('element.plate_sets')}}<sup>*</sup></label>'+
                                                            '<input type="text" required class="input-css" name="plate_sets['+x+']" id="">'+
                                                    '</div>'+
                                                    '<div class="col-md-4">'+
                                                        '<label>'+ y+" "+'{{__('element.impression_plate_sets')}}<sup>*</sup></label>'+
                                                        '<input type="text" class="input-css" required  name="impression_plate_sets['+x+']" id="">'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="row">'+
                                                    '<div class="col-md-4">'+
                                                        '<label>'+ y+" "+'{{__('element.front_color')}}<sup>*</sup></label>'+
                                                            '<select class="form-control  select2 input-css" style="width: 100%;"  required name="front_color['+x+']">'+
                                                                '<option value="">Select Back Color</option>'+
                                                                '<option value="0">0</option>'+
                                                                '<option value="1">1</option>'+
                                                                '<option value="2">2</option>'+
                                                                '<option value="3">3</option>'+
                                                                '<option value="4">4</option>'+
                                                                '<option value="5">5</option>'+
                                                                '<option value="6">6</option>'+
                                                                '<option value="7">7</option>'+
                                                                '<option value="8">8</option>'+
                                                        '</select>'+
                                                        '<label id="front_color['+x+']-error" class="error" for="front_color['+x+']"></label>'+
                                                    '</div>'+
                                                    '<div class="col-md-4">'+
                                                        '<label>'+ y+" "+'{{__('element.back_color')}}<sup>*</sup></label>'+
                                                        '<select class="form-control select2  input-css" style="width: 100%;" required name="back_color['+x+']">'+
                                                                '<option value="">Select Back Color</option>'+
                                                                '<option value="0">0</option>'+
                                                                '<option value="1">1</option>'+
                                                                '<option value="2">2</option>'+
                                                                '<option value="3">3</option>'+
                                                                '<option value="4">4</option>'+
                                                                '<option value="5">5</option>'+
                                                                '<option value="6">6</option>'+
                                                                '<option value="7">7</option>'+
                                                                '<option value="8">8</option>'+
                                                        '</select>'+
                                                        '<label id="back_color['+x+']-error" class="error" for="back_color['+x+']"></label>'+
                                                    '</div>'+
                                                    '<div class="col-md-4 no_of_pages" style="display:none">'+
                                                        '<label>'+ y+" "+'{{__('element.no_of_pages')}}<sup>*</sup></label>'+
                                                        '<input type="number" class="input-css" name="no_of_pages['+x+']" id="">'+
                                                    '</div>'+
                                                    '<input   name="new_elem[]" type="hidden" value="'+value+'">'+
                                                '</div>';

                                                if($(this).prop("checked") == true && value == "1")
                                                {
                                                   
                                                    $('.inside_elm').slideDown();
                                                    $('.inside_elm').append(texture);
                                                    $('select').select2();

                                                    $('.no_of_pages').show();
                                                }
                                                if($(this).prop("checked") == false && value == "1")
                                                {
                                                    $('.inside_elm').empty();
                                                    $('.inside_elm').hide();
                                                }
                                                if($(this).prop("checked") == true && value == "2")
                                                {
                                                   
                                                    $('.inside_elm1').slideDown();
                                                    $('.inside_elm1').append(texture);
                                                    $('select').select2();
                                                }
                                                if($(this).prop("checked") == false && value == "2")
                                                {
                                                    $('.inside_elm1').empty();
                                                    $('.inside_elm1').hide();
                                                }
                                                if($(this).prop("checked") == true && value == "3")
                                                {
                                                    $('.inside_elm2').slideDown();
                                                    $('.inside_elm2').append(texture);
                                                    $('select').select2();
                                                }
                                                if($(this).prop("checked") == false && value == "3")
                                                {
                                                    $('.inside_elm2').empty();
                                                    $('.inside_elm2').hide();
                                                }
                                                if($(this).prop("checked") == true && value == "4")
                                                {
                                                    $('.inside_elm3').slideDown();
                                                    $('.inside_elm3').append(texture);
                                                    $('select').select2();
                                                }
                                                if($(this).prop("checked") == false && value == "4")
                                                {
                                                    $('.inside_elm3').empty();
                                                    $('.inside_elm3').hide();
                                                }
                                                if($(this).prop("checked") == true && value == "5")
                                                {
                                                    $('.inside_elm4').slideDown();
                                                    $('.inside_elm4').append(texture);
                                                    $('select').select2();
                                                }
                                                if($(this).prop("checked") == false && value == "5")
                                                {
                                                    $('.inside_elm4').empty();
                                                    $('.inside_elm4').hide();
                                                }

                                });
                            }
                            else{
                                $('.element').empty();
                            }
                            $('#ajax_loader_div').css('display','none');

                    },
                    fail:function(error){
                        $('#ajax_loader_div').css('display','none');

                        // $('#box2').empty();
                        // $('#box2').html('No job card has been created for this internal order');
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
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="/elementform/updateDB/{{$jc_id}}/{{$io_id}}" method="post" id="elementForm" >
                @csrf
                <div class="row">
                        <ul class="nav nav1 nav-pills">
                                <li class="nav-item">
                                  <a class="nav-link"  href="{{url('/jobcardform/update'.'/'.$jc_id) }}">Job Card</a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link active" style="background-color: #87CEFA"  href="{{url('/elementform/update'.'/'.$jc_id.'/'.$io_id) }}">Element Details</a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link" href="{{url('/rawform/update'.'/'.$jc_id.'/'.$io_id) }}">Raw Material Details</a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link"  href="{{url('/bindingform/update'.'/'.$jc_id.'/'.$io_id) }}">Binding Details</a>
                                </li>
                              </ul>
                              <br>
                <input type="hidden" name="jc_id" value="{{$jc_id}}">
                <input type="hidden" name="io_id" value="{{$io_id}}">
                
                </div>
                    <div class='' id="box2">

                    </div>
                    <input type="hidden" id="counter" value="1">
                    <div class="row submit" style="display:none">
                            <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary submit" >Submit</button>
                            </div>
                    </div>
            </form>
    </section><!--end of section-->
@endsection

