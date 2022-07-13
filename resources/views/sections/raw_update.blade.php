<?php  //print($paper);die();?>

@extends($layout)

@section('title', __('raw_material.title1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('raw_material.mytitle')}}</i></a></li>
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
<script src="/js/views/element.js"></script>
<script>
    var id;
            $('#box2').empty();
          var io_id ={{$io_id}};
          var counts={{$count}};
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
                    var elem_data={!! json_encode($element_detail->toArray(), JSON_HEX_TAG) !!};
                    
                    $('select').select2('destroy');
                    $('#box2').append(
                        '<div class="box box-default">'+
                            '<div class="container-fluid">'+
                                '<h3 class="box-title">{{__('raw_material.mytitle')}}'+" "+ name +'</h3>'+
                                '<label id="elem_type[]-error" class="error" for="elem_type[]"></label>'+
                                '{!! $errors->first('elem_type', '<p class="help-block" style="color:#f11717">:message</p>') !!}'+
                                '<div class="row">'+
                                            '<div class="col-md-3">'+
                                                '<label  class="elem_type_label_er">{{__('layout.update_reason')}}<sup>*</sup></label>'+
                                               '<input type="text" name="update_reason" required="" class="form-control input-css " id="update_reason">'+
                                                ' {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}'+
                                            '</div>'+
                                        '</div>'+
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
                        for(var m=0;m<counts;m++){
                                   
                                var paper_size=elem_data[m].paper_size;
                               var paper_type_id=elem_data[m].paper_type_id;
                               var paper_gsm=elem_data[m].paper_gsm;

                               var paper_mill=elem_data[m].paper_mill;
                               var paper_brand=elem_data[m].paper_brand;
                               var no_of_sheets=elem_data[m].no_of_sheets;

                               var is_option=elem_data[m].is_option;
                               var item_name=elem_data[m].item_name;
                               var size=elem_data[m].size;
                               var size_dimension=elem_data[m].size_dimension;
                               var thickness=elem_data[m].thickness;
                               var thickness_dimension=elem_data[m].thickness_dimension;
                               var specification=elem_data[m].specification;
                              console.log(is_option);
                              
                               var val=values-1;
                               var x=values;
                               if(is_option=="Paper"){
                                   var pap="checked=checked";
                                   var style="style=display:block";
                               }
                               else{
                                var pap="";
                                var style="style=display:none";
                               }
                               if(is_option=="Other"){
                                   var oth="checked=checked";
                                   var style1="style=display:block";
                               }
                               else{
                                var oth="";
                                var style1="style=display:none";
                               }
                               
                        var texture=
                                    '<div class="row">'+
                                        '<div class="col-md-12">'+
                                            '<label>Paper Type Or Other</label>'+
                                            '<div class="col-md-2">'+
                                                '<div class="radio">'+
                                                    '<label><input type="radio"name="is_paper['+elem_data[m].element_type_id+']" onchange="paper_elem(this)" '+pap+' value="Paper" class="is_paper">Paper</label>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-2">'+
                                                '<div class="radio">'+
                                                    '<label><input type="radio"name="is_paper['+elem_data[m].element_type_id+']" onchange="other_elem(this)" '+oth+' value="Other" class="is_paper">Other</label>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+ 
                                    '</div>'+
                                    '<div class="paper_option" '+style+'>'+
                                        '<div class="row">'+
                                            '<div class="col-md-4">'+
                                                '<input type="hidden" name="elem_type[]" id="" value="'+raw+'"  class="elem_type">'+
                                                '<label>{{__('raw_material.paper_size')}}<sup>*</sup></label>'+
                                                '<input type="text" required class="input-css" name="paper_size['+elem_data[m].element_type_id+']" value="'+paper_size+'" id="">'+
                                            '</div>'+
                                            '<div class="col-md-4">'+
                                                    '<label>{{__('raw_material.paper_type')}}<sup>*</sup></label>'+
                                                    '<select class="select2 input-css paper_type_id'+elem_data[m].element_type_id+'"required data-placeholder=""  style="padding-top: 2px;" name="paper_type['+elem_data[m].element_type_id+']" id="">'+
                                                        '<option value="">Select paper type</option>'+
                                                            @foreach($paper as $key)
                                                            '<option value="{{$key->id}}">{{$key->name}}</option> '+
                                                            @endforeach
                                                    '</select>'+
                                                    '<label id="paper_type-error" class="error" for="paper_type['+elem_data[m].element_type_id+']"></label>'+
                                            '</div>'+
                                            '<div class="col-md-4">'+
                                                '<label>{{__('raw_material.paperGSM')}}<sup>*</sup></label>'+
                                                '<input type="text" class="input-css" value="'+paper_gsm+'" required name="paperGSM['+elem_data[m].element_type_id+']" id="">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="row">'+
                                            '<div class="col-md-4">'+
                                                '<label>{{__('raw_material.paper_mill')}}<sup>*</sup></label>'+
                                                '<input type="text" class="input-css" value="'+paper_mill+'" required name="paper_mill['+elem_data[m].element_type_id+']" id="">'+
                                            '</div>'+
                                            '<div class="col-md-4">'+
                                                '<label>{{__('raw_material.sheets')}}<sup>*</sup></label>'+
                                                '<input type="number"  class="input-css" value="'+no_of_sheets+'" required name="sheets['+elem_data[m].element_type_id+']" id="">'+
                                                '<input   name="old_id[]" type="hidden" value="'+elem_data[m].id+'" class="old_elem">'+
                                                '<input   name="old_elem[]" type="hidden" value="'+elem_data[m].element_type_id+'" class="old_elem">'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="other_option" '+style1+'>'+
                                            '<div class="row">'+
                                                '<div class="col-md-4">'+
                                                    '<label>Item Name<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" value="'+item_name+'" name="item_name['+elem_data[m].element_type_id+']" id="'+x+'item_name_field_id_">'+
                                                    '<label id="item_name[]-error" class="error" for="paper_type['+elem_data[m].element_type_id+']"></label>'+
                                                '</div>'   +

                                                '<div class="col-md-4">'+
                                                    '<label>Item Size<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;"  value="'+size+'" name="item_size['+elem_data[m].element_type_id+']" id="'+elem_data[m].element_type_id+'item_size_field_id_">'+
                                                    '<label id="item_size[]-error" class="error" for="paper_type['+elem_data[m].element_type_id+']"></label>'+
                                                '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>Size Unit<sup>*</sup></label>'+
                                                    '<select class="select2 input-css size_unit'+elem_data[m].element_type_id+'"required data-placeholder="" style="width:100%;padding-top: 2px;" name="size_unit['+elem_data[m].element_type_id+']" >'+
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
                                                    '<label id="size_unit[]-error" class="error" for="size_unit['+elem_data[m].element_type_id+']"></label>'+
                                                '</div>'   +
                                            
                                            '</div>'+
                                            '<div class="row">'+
                                                '<div class="col-md-4">'+
                                                    '<label>Item Thickness<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" value="'+thickness+'" style="padding-top: 2px;" name="item_thick['+elem_data[m].element_type_id+']">'+
                                                    '<label id="item_thick[]-error" class="error" for="item_thick['+elem_data[m].element_type_id+']"></label>'+
                                                 '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>Thickness Unit<sup>*</sup></label>'+
                                                    '<select class="select2 input-css thick_unit'+elem_data[m].element_type_id+'"required data-placeholder="" style="width:100%;padding-top: 2px;" name="thick_unit['+elem_data[m].element_type_id+']">'+
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
                                                    '<label id="thick_unit[]-error" class="error" for="thick_unit['+elem_data[m].element_type_id+']"></label>'+
                                                '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>Other Specification<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" value="'+specification+'"  style="padding-top: 2px;" name="specification['+elem_data[m].element_type_id+']">'+
                                                    '<label id="specification[]-error" class="error" for="specification['+elem_data[m].element_type_id+']"></label>'+
                                                 '</div>'   +
                                            '</div>'+
                                    '</div>'; 

                                $('.element').append(texture);
                                $(".paper_type_id"+elem_data[m].element_type_id).val(paper_type_id).select2().trigger("change");
                                $(".size_unit"+elem_data[m].element_type_id).val(size_dimension).select2().trigger("change");
                                $(".thick_unit"+elem_data[m].element_type_id).val(thickness_dimension).select2().trigger("change");
                                $('select').select2();
                                $('.submit').show();
                                        }
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
                        for(var m=0;m<counts;m++){
                                   
                                   $('input[type=checkbox][value="'+elem_data[m].element_type_id+'"]').prop('checked',true);
                                   $('input[type=checkbox][value="'+elem_data[m].element_type_id+'"]').prop('disabled',true);
                                   $('.submit').show();
                                var values = elem_data[m].element_type_id;
                              
                               var paper_size=elem_data[m].paper_size;
                               var paper_type_id=elem_data[m].paper_type_id;
                               var paper_gsm=elem_data[m].paper_gsm;

                               var paper_mill=elem_data[m].paper_mill;
                               var paper_brand=elem_data[m].paper_brand;
                               var no_of_sheets=elem_data[m].no_of_sheets;

                               var is_option=elem_data[m].is_option;
                               var item_name=elem_data[m].item_name;
                               var size=elem_data[m].size;
                               var size_dimension=elem_data[m].size_dimension;
                               var thickness=elem_data[m].thickness;
                               var thickness_dimension=elem_data[m].thickness_dimension;
                               var specification=elem_data[m].specification;
                              console.log(is_option);
                              
                               var val=values-1;
                               var x=values;
                               if(is_option=="Paper"){
                                   var pap="checked=checked";
                                   var style="style=display:block";
                               }
                               else{
                                var pap="";
                                var style="style=display:none";
                               }
                               if(is_option=="Other"){
                                   var oth="checked=checked";
                                   var style1="style=display:block";
                               }
                               else{
                                var oth="";
                                var style1="style=display:none";
                               }
                            //alert(value);
                            y=elem_cat[val].name;
                            var raw=
                                    '<div class="row">'+
                                        '<div class="col-md-12">'+
                                            '<label>Paper Type Or Other</label>'+
                                            '<div class="col-md-2">'+
                                                '<div class="radio">'+
                                                    '<label><input type="radio"name="is_paper['+x+']" onchange="paper_elem(this)" '+pap+' value="Paper" class="is_paper">Paper</label>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-md-2">'+
                                                '<div class="radio">'+
                                                    '<label><input type="radio"name="is_paper['+x+']" onchange="other_elem(this)" '+oth+' value="Other" class="is_paper">Other</label>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+ 
                                    '</div>'+
                                    '<div class="paper_option" '+style+'>'+
                                        '<div class="row">'+
                                            '<div class="col-md-4">'+
                                                '<label>'+ y+" "+'{{__('raw_material.paper_size')}}<sup>*</sup></label>'+
                                                '<input type="text" class="input-css" required name="paper_size['+x+']" value="'+paper_size+'" id="">'+
                                            '</div>'+
                                            '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'{{__('raw_material.paper_type')}}<sup>*</sup></label>'+
                                                    '<select class="select2 input-css paper_type_id'+x+'"required data-placeholder=""  style="width:100%;padding-top: 2px;" name="paper_type['+x+']" id="">'+
                                                        '<option value="">Select paper type</option>'+
                                                            @foreach($paper as $key)
                                                            '<option value="{{$key->id}}">{{$key->name}}</option> '+
                                                            @endforeach
                                                    '</select>'+
                                                    '<label id="plate_sets[]-error" class="error" for="paper_type['+x+']"></label>'+
                                            '</div>'+
                                    
                                            '<div class="col-md-4">'+
                                                '<label>'+ y+" "+'{{__('raw_material.paperGSM')}}<sup>*</sup></label>'+
                                                '<input type="text" class="input-css"  required name="paperGSM['+x+']" value="'+paper_gsm+'" id="">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="row">'+
                                            '<div class="col-md-4">'+
                                                '<label>'+ y+" "+'{{__('raw_material.paper_brand')}}<sup>*</sup></label>'+
                                                '<input type="text" class="input-css" required  name="paper_brand['+x+']" value="'+paper_brand+'" id="">'+
                                                '<input type="hidden" class="input-css" name="paper_mill['+x+']" id="">'+
                                            '</div>'+
                                            '<div class="col-md-4">'+
                                                '<label>'+ y+" "+'{{__('raw_material.sheets')}}<sup>*</sup></label>'+
                                                '<input type="number" class="input-css" required name="sheets['+x+']" value="'+no_of_sheets+'" id="">'+
                                                '<input   name="old_id[]" type="hidden" value="'+elem_data[m].id+'" class="old_elem">'+
                                                '<input   name="old_elem[]" type="hidden" value="'+elem_data[m].element_type_id+'" class="old_elem">'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="other_option" '+style1+'>'+
                                            '<div class="row">'+
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Item Name<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;" value="'+item_name+'" name="item_name['+x+']" id="'+x+'item_name_field_id_">'+
                                                    '<label id="item_name[]-error" class="error" for="paper_type['+x+']"></label>'+
                                                '</div>'   +

                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Item Size<sup>*</sup></label>'+
                                                    '<input class="input-css"required data-placeholder="" style="padding-top: 2px;"  value="'+size+'" name="item_size['+x+']" id="'+x+'item_size_field_id_">'+
                                                    '<label id="item_size[]-error" class="error" for="paper_type['+x+']"></label>'+
                                                '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Size Unit<sup>*</sup></label>'+
                                                    '<select class="select2 input-css size_unit'+x+'"required data-placeholder="" style="width:100%;padding-top: 2px;" name="size_unit['+x+']" >'+
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
                                                    '<input class="input-css"required data-placeholder="" value="'+thickness+'" style="padding-top: 2px;" name="item_thick['+x+']">'+
                                                    '<label id="item_thick[]-error" class="error" for="item_thick['+x+']"></label>'+
                                                 '</div>'   +
                                                '<div class="col-md-4">'+
                                                    '<label>'+ y+" "+'Thickness Unit<sup>*</sup></label>'+
                                                    '<select class="select2 input-css thick_unit'+x+'"required data-placeholder="" style="width:100%;padding-top: 2px;" name="thick_unit['+x+']">'+
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
                                                    '<input class="input-css"required data-placeholder="" value="'+specification+'"  style="padding-top: 2px;" name="specification['+x+']">'+
                                                    '<label id="specification[]-error" class="error" for="specification['+x+']"></label>'+
                                                 '</div>'   +
                                            '</div>'+
                                    '</div>';

                                           if(values == "1")
                                           {
                                               $('.inside_elm').slideDown();
                                               $('.inside_elm').append(raw);
                                               $('select').select2();
                                               $(".paper_type_id1").val(paper_type_id).select2().trigger("change");
                                               $(".size_unit1").val(size_dimension).select2().trigger("change");
                                               $(".thick_unit1").val(thickness_dimension).select2().trigger("change");
                                           }
                                           if(values == "2")
                                           {
                                              
                                               $('.inside_elm1').slideDown();
                                               $('.inside_elm1').append(raw);
                                               $(".paper_type_id2").val(paper_type_id).select2().trigger("change");
                                               $(".size_unit2").val(size_dimension).select2().trigger("change");
                                               $(".thick_unit2").val(thickness_dimension).select2().trigger("change");
                                              
                                               $('select').select2();
                                           }
                                          
                                           if(values == "3")
                                           {
                                               $('.inside_elm2').slideDown();
                                               $('.inside_elm2').append(raw);
                                               $(".paper_type_id3").val(paper_type_id).select2().trigger("change");
                                               $(".size_unit3").val(size_dimension).select2().trigger("change");
                                               $(".thick_unit3").val(thickness_dimension).select2().trigger("change");
                                            
                                               $('select').select2();
                                           }
                                           if(values == "4")
                                           {
                                               $('.inside_elm3').slideDown();
                                               $('.inside_elm3').append(raw);
                                               $(".paper_type_id4").val(paper_type_id).select2().trigger("change");
                                               $(".size_unit4").val(size_dimension).select2().trigger("change");
                                               $(".thick_unit4").val(thickness_dimension).select2().trigger("change");
                                               
                                               $('select').select2();
                                           }
                                          
                                           if(values == "5")
                                           {
                                               $('.inside_elm4').slideDown();
                                               $('.inside_elm4').append(raw);
                                               $(".paper_type_id5").val(paper_type_id).select2().trigger("change");
                                               $(".size_unit5").val(size_dimension).select2().trigger("change");
                                               $(".thick_unit5").val(thickness_dimension).select2().trigger("change");
                                             
                                               $('select').select2();
                                           }
                                          
                               }
                               




                        $('input[type=checkbox][class=elem_type]').change(function(){
                            $('.submit').show();
                            value = $(this).val();
                            var z=value-1;
                            var x=value;
                            y=elem_cat[z].name;
                            var raw=
                            '<div class="row">'+
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
                                                    '<input type="hidden" class="input-css" name="paper_mill['+x+']" id="">'+
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
                    alert('status:');
                    $('#ajax_loader_div').css('display','none');

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
    //   function elementform(id){
    //       var x=$(id.target).val();

    //   }
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
            <form action="/rawform/updateDB/{{$jc_id}}/{{$io_id}}" id="elementForm"  method="post">
                @csrf
                    <div class="row">
                            <ul class="nav nav1 nav-pills">
                                    <li class="nav-item">
                                      <a class="nav-link"  href="{{url('/jobcardform/update'.'/'.$jc_id) }}">Job Card</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" href="{{url('/elementform/update'.'/'.$jc_id.'/'.$io_id) }}">Element Details</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link active"style="background-color: #87CEFA"  href="{{url('/rawform/update'.'/'.$jc_id.'/'.$io_id) }}">Raw Material Details</a>
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
                   
                    <div class="row submit" style="display:none">
                        <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
    </section><!--end of section-->
@endsection
{{-- {{CustomHelpers::coolText('hcjsd')}} --}}
