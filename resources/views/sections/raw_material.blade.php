<?php  //print($paper);die();?>

@extends($layout)

@section('title', __('raw_material.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('raw_material.mytitle')}}</i></a></li>
@endsection
@section('css')
  <link rel="stylesheet" href="/css/party.css">

@endsection

@section('js')
<script src="/js/views/element.js"></script>
<script>
    var id;
    var elem_count={{$elem_count}};
    if(elem_count!=0){
        alert('raw material form has already been created for this Job Card!!!');
    }
            $('#box2').empty();
          var io_id ={{$io_id}};
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
                    var raw=elem[1];
                    var elem_cat={!! json_encode($element->toArray(), JSON_HEX_TAG) !!};
                    $('select').select2('destroy');
                    $('#box2').append(
                        '<div class="box box-default">'+
                            '<div class="container-fluid">'+
                                '<h3 class="box-title">{{__('raw_material.mytitle')}}'+" "+ name +'</h3>'+
                                '<label id="elem_type[]-error" class="error" for="elem_type[]"></label>'+
                                '{!! $errors->first('elem_type', '<p class="help-block" style="color:#f11717">:message</p>') !!}'+
                                '<div class="row">'+
                                    '<div class="col-md-12 element">'+
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
                    if(id>5 && id<15){

                        var texture=
                                    '<div class="row">'+
                                        '<div class="col-md-12">'+
                                            '<label>Paper Type Or Other</label>'+
                                            '<div class="col-md-2">'+
                                                '<div class="radio">'+
                                                    '<label><input type="radio"name="is_paper[]" required onchange="paper_elem(this)" value="Paper" class="is_paper">Paper</label>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-2">'+
                                                '<div class="radio">'+
                                                    '<label><input type="radio"name="is_paper[]" required  onchange="other_elem(this)"  value="Other" class="is_paper">Other</label>'+
                                                '</div>'+
                                            '</div>'+
                                            '<label id="is_paper[]-error" class="error" for="is_paper[]">.</label>'+
                                        '</div>'+
                                       
                                    '</div>'+
                                    '<div class="paper_option" style="display:none">'+
                                        '<div class="row">'+
                                            '<div class="col-md-4">'+
                                                '<input type="hidden" name="elem_type[]" id="" value="'+raw+'" class="elem_type">'+
                                                '<label>{{__('raw_material.paper_size')}}<sup>*</sup></label>'+
                                                '<input type="text" required class="input-css" name="paper_size[]" id="">'+
                                            '</div>'+
                                    
                                            '<div class="col-md-4">'+
                                                '<label>{{__('raw_material.paper_type')}}<sup>*</sup></label>'+
                                                '<select class="select2 input-css"required data-placeholder="" style="width:100%;padding-top: 2px;" name="paper_type[]" id="">'+
                                                    '<option value="">Select paper type</option>'+
                                                        @foreach($paper as $key)
                                                        '<option value="{{$key->id}}">{{$key->name}}</option> '+
                                                        @endforeach
                                                '</select>'+
                                                '<label id="paper_type-error" class="error" for="paper_type[]"></label>'+
                                            '</div>'+
                                    
                                            '<div class="col-md-4">'+
                                                '<label>{{__('raw_material.paperGSM')}}<sup>*</sup></label>'+
                                                '<input type="text" class="input-css" required name="paperGSM[]" id="">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="row">'+
                                            '<div class="col-md-4">'+
                                                '<label>{{__('raw_material.paper_mill')}}<sup>*</sup></label>'+
                                                '<input type="text" class="input-css" required name="paper_mill[]" id="">'+
                                            '</div>'+
                                            '<div class="col-md-4">'+
                                                '<label>{{__('raw_material.sheets')}}<sup>*</sup></label>'+
                                                '<input type="number"  class="input-css" required name="sheets[]" id="">'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+    
                                '<div class="other_option" style="display:none">'+
                                            '<div class="row">'+
                                                '<div class="col-md-4">'+
                                                    '<label>Item Name<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" name="item_name[]" id="item_name_field_id_">'+
                                                    '<label id="item_name[]-error" class="error" for="item_name[]"></label>'+
                                                '</div>'   +

                                                '<div class="col-md-4">'+
                                                    '<label>Item Size<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" name="item_size[]" id="item_size_field_id_">'+
                                                    '<label id="item_size[]-error" class="error" for="paper_type[]"></label>'+
                                                '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>Size Unit<sup>*</sup></label>'+
                                                    '<select class="select2 input-css"required data-placeholder="" style="width:100%;padding-top: 2px;" name="size_unit[]" >'+
                                                            '<option value="">Select Item Size Unit</option>'+
                                                            '<option value="m">Metre</option>'+
                                                                '<option value="mm">Millimeter</option>'+
                                                            ' <option value="cm">Centimeter</option>'+
                                                                '<option value="km">Kilometer</option>'+
                                                                '<option value="in">Inch</option>'+
                                                                '<option value="ft">Foot</option>'+
                                                                '<option value="ton">Ton</option>'+
                                                                '<option value="doz">Dozen</option>'+
                                                                '<option value="kg">Kilogram</option>'+
                                                                '<option value="g">Grams</option>'+
                                                        '</select>'+    
                                                    '<label id="size_unit[]-error" class="error" for="size_unit[]"></label>'+
                                                '</div>'   +
                                            
                                            '</div>'+
                                            '<div class="row">'+
                                                '<div class="col-md-4">'+
                                                    '<label>Item Thickness<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" name="item_thick[]">'+
                                                    '<label id="item_thick[]-error" class="error" for="item_thick[]"></label>'+
                                                 '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>Thickness Unit<sup>*</sup></label>'+
                                                    '<select class="select2 input-css"required data-placeholder="" style="width:100%;padding-top: 2px;" name="thick_unit[]">'+
                                                            '<option value="">Select Item Thickness Unit</option>'+
                                                            '<option value="m">Metre</option>'+
                                                                '<option value="mm">Millimeter</option>'+
                                                            ' <option value="cm">Centimeter</option>'+
                                                                '<option value="km">Kilometer</option>'+
                                                                '<option value="in">Inch</option>'+
                                                                '<option value="ft">Foot</option>'+
                                                                '<option value="ton">Ton</option>'+
                                                                '<option value="doz">Dozen</option>'+
                                                                '<option value="kg">Kilogram</option>'+
                                                                '<option value="g">Grams</option>'+
                                                        '</select>'+    
                                                    '<label id="thick_unit[]-error" class="error" for="thick_unit[]"></label>'+
                                                '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>Other Specification<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" name="specification[]">'+
                                                    '<label id="specification[]-error" class="error" for="specification[]"></label>'+
                                                 '</div>'   +
                                            '</div>'+
                                    '</div>';

                                $('.element').append(texture);
                                $('.select2').select2();
                                $('.submit').show();
                    }
                    if(id<6 || id==15){

                        for(var i=0;i<count;i++)
                        {
                            var element_put=elem[i]-1;
                            //alert(element_put);
                            var element_put_name=elem_cat[element_put].name;
                            $('.element').append(
                            '<div class="col-md-2"><div class="radio"><label id="elem"><input type="checkbox" name="elem_type[]" id="" value="'+elem[i]+'" class="elem_type">'+element_put_name+'</label></div></div>'
                            );
                        }
                        $('input[type=checkbox][class=elem_type]').change(function(){
                            $('.submit').show();
                            value = $(this).val();
                            x=value-1;
                            //alert(value);
                            y=elem_cat[x].name;
                            var raw= '<div class="row">'+
                                        '<div class="col-md-12">'+
                                            '<label>'+ y+" "+'Paper Type Or Other</label>'+
                                            '<div class="col-md-2">'+
                                                '<div class="radio">'+
                                                    '<label><input type="radio"name="is_paper['+x+']" required value="Paper" onchange="paper_elem(this)" class="is_paper">Paper</label>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-2">'+
                                                '<div class="radio">'+
                                                    '<label><input type="radio"name="is_paper['+x+']" required value="Other" onchange="other_elem(this)"  class="is_paper">Other</label>'+
                                                '</div>'+
                                            '</div>'+
                                            '<label id="is_paper[]-error" class="error" for="is_paper['+x+']">.</label>'+
                                        '</div>'+
                                       
                                    '</div>'+
                            
                                    '<div class="paper_option" style="display:none">'+
                                        '<div class="row">'+
                                            '<div class="col-md-4">'+
                                                '<label>'+ y+" "+'{{__('raw_material.paper_size')}}<sup>*</sup></label>'+
                                                '<input type="text" class="input-css" required name="paper_size['+x+']" id="">'+
                                            '</div>'+

                                            '<div class="col-md-4" >'+
                                                    '<label>'+ y+" "+'{{__('raw_material.paper_type')}}<sup>*</sup></label>'+
                                                    '<select class="select2 input-css"required data-placeholder="" style="width:100%;padding-top: 2px;" name="paper_type['+x+']" id="">'+
                                                        '<option value="">Select paper type</option>'+
                                                            @foreach($paper as $key)
                                                            '<option value="{{$key->id}}">{{$key->name}}</option> '+
                                                            @endforeach
                                                    '</select>'+
                                                    '<label id="plate_sets[]-error" class="error" for="paper_type['+x+']"></label>'+
                                            '</div>'+
                                            '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'{{__('raw_material.paperGSM')}}<sup>*</sup></label>'+
                                                    '<input type="text" class="input-css"  required name="paperGSM['+x+']" id="">'+
                                                '</div>'+
                                        '</div>'+
                                        '<div class="row">'+
                                                
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'{{__('raw_material.paper_brand')}}<sup>*</sup></label>'+
                                                    '<input type="text" class="input-css" required  name="paper_brand['+x+']" id="">'+
                                                    '<input type="hidden" class="input-css" name="paper_mill[]" id="">'+
                                                '</div>'+
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'{{__('raw_material.sheets')}}<sup>*</sup></label>'+
                                                    '<input type="number" class="input-css" required name="sheets['+x+']" id="">'+
                                                '</div>'+
                                        '</div>'+        
                                     '</div>'+
                                   
                                    '<div class="other_option" style="display:none">'+
                                            '<div class="row">'+
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Item Name<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" name="item_name['+x+']" id="'+x+'item_name_field_id_">'+
                                                    '<label id="item_name[]-error" class="error" for="paper_type['+x+']"></label>'+
                                                '</div>'   +

                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Item Size<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" name="item_size['+x+']" id="'+x+'item_size_field_id_">'+
                                                    '<label id="item_size[]-error" class="error" for="paper_type['+x+']"></label>'+
                                                '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Size Unit<sup>*</sup></label>'+
                                                    '<select class="select2 input-css"required data-placeholder="" style="width:100%;padding-top: 2px;" name="size_unit['+x+']" >'+
                                                            '<option value="">Select Item Size Unit</option>'+
                                                            '<option value="m">Metre</option>'+
                                                                '<option value="mm">Millimeter</option>'+
                                                            ' <option value="cm">Centimeter</option>'+
                                                                '<option value="km">Kilometer</option>'+
                                                                '<option value="in">Inch</option>'+
                                                                '<option value="ft">Foot</option>'+
                                                                '<option value="ton">Ton</option>'+
                                                                '<option value="doz">Dozen</option>'+
                                                                '<option value="kg">Kilogram</option>'+
                                                                '<option value="g">Grams</option>'+
                                                        '</select>'+    
                                                    '<label id="size_unit[]-error" class="error" for="size_unit['+x+']"></label>'+
                                                '</div>'   +
                                            
                                            '</div>'+
                                            '<div class="row">'+
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Item Thickness<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" name="item_thick['+x+']">'+
                                                    '<label id="item_thick[]-error" class="error" for="item_thick['+x+']"></label>'+
                                                 '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Thickness Unit<sup>*</sup></label>'+
                                                    '<select class="select2 input-css"required data-placeholder="" style="width:100%;padding-top: 2px;" name="thick_unit['+x+']">'+
                                                            '<option value="">Select Item Thickness Unit</option>'+
                                                            '<option value="m">Metre</option>'+
                                                                '<option value="mm">Millimeter</option>'+
                                                            ' <option value="cm">Centimeter</option>'+
                                                                '<option value="km">Kilometer</option>'+
                                                                '<option value="in">Inch</option>'+
                                                                '<option value="ft">Foot</option>'+
                                                                '<option value="ton">Ton</option>'+
                                                                '<option value="doz">Dozen</option>'+
                                                                '<option value="kg">Kilogram</option>'+
                                                                '<option value="g">Grams</option>'+
                                                        '</select>'+    
                                                    '<label id="thick_unit[]-error" class="error" for="thick_unit['+x+']"></label>'+
                                                '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Other Specification<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" name="specification['+x+']">'+
                                                    '<label id="specification[]-error" class="error" for="specification['+x+']"></label>'+
                                                 '</div>'   +
                                            '</div>'+
                                    '</div>';
                                if($(this).prop("checked") == true && value == "1")
                                {
                                    $('.inside_elm').slideDown();
                                    $('.inside_elm').append(raw);
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
                                    $('.inside_elm1').append(raw);
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
                                    $('.inside_elm2').append(raw);
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
                                    $('.inside_elm3').append(raw);
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
                                    $('.inside_elm4').append(raw);
                                    $('select').select2();
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
                    $('#ajax_loader_div').css('display','none');

                    alert('status:');
                }
            });


  function paper_elem(inp){ 
var xs1=$(inp).parent().parent().parent().parent().parent().siblings().eq(0).show();;
var xs=$(inp).parent().parent().parent().parent().parent().siblings().eq(1).hide();

console.log(xs1);

  }
  
  function other_elem(inp){ 
    var xs1=$(inp).parent().parent().parent().parent().parent().siblings().eq(1).show();;
var xs=$(inp).parent().parent().parent().parent().parent().siblings().eq(0).hide();
// console.log(xs);

  }
</script>
<script>

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
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
            <form action="/rawmaterial/insert" id="elementForm"  method="post">
                @csrf
                    <div class="row">
                        <input type="hidden" name="jc_id" value="{{$jc_id}}">
                        <input type="hidden" name="io_id" value="{{$io_id}}">
                    </div>
                    <div class='' id="box2">

                    </div>
                   
                    <div class="row submit" style="display:none">
                        <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
    </section><!--end of section-->
@endsection
{{-- {{CustomHelpers::coolText('hcjsd')}} --}}