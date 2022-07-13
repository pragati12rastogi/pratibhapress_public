<?php  //print($labels);die();?>

@extends($layout)

@section('title', __('binding.title1'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('binding.mytitle')}}</i></a></li>
   
    <li class="active">Blank page</li> 
@endsection
@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="/css/party.css">
<style>
.span
{
    color:red;
}

        .nav1>li>a {
            position: relative;
            display: block;
            padding: 10px 34px;
            background-color: white;
            margin-left: 10px;
        }
        
        </style>
@endsection

@section('js')
<script src="/js/views/binding.js"></script>
<script>
var elem_count="{{$elem_count}}";
    var counts="{{$elem_count}}";
    $('#box2').empty();
        var io_id = "{{$io_id}}";
          $('#ajax_loader_div').css('display','block');

          $.ajax({
              url: "/jobcard/detail/" + io_id,
              type: "GET",
                success: function(result)
                {
                    console.log(result);
                    $('input[name=remark]').val(result[0].remarks);
                    var arr= result[0];      
                    var id=result[0].item_category_id;
                    var name=result[0].name;
                    var element=result[0].elements;
                    var elem=element.split(',');
                    var count=elem.length;
                    var elem_data={!! json_encode($element_detail->toArray(), JSON_HEX_TAG) !!};
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
                                '<div class="row">'+
                                            '<div class="col-md-3">'+
                                                '<label  class="elem_type_label_er">{{__('layout.update_reason')}}<sup>*</sup></label>'+
                                               '<input type="text" name="update_reason" required="" class="form-control input-css " id="update_reason">'+
                                                ' {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}'+
                                            '</div>'+
                                    '</div>'+
                            '</div>'+
                        '</div>'
                    );
                    if(id>5)
                    {
                       
                            // var elem_id=id;
                            // $("#" + elem_id).show();
                            $('.submit').show();
                            for(var m=0;m<counts;m++){
                            $('input[type=checkbox][value="'+elem_data[m].element_type_id+'"]').prop('checked',true);
                                   $('input[type=checkbox][value="'+elem_data[m].element_type_id+'"]').prop('disabled',true);
                            var values = elem_data[m].element_type_id;
                            $("#element_type_id_" + values).show(); 
                                }

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
                        for(var m=0;m<counts;m++)
                        {
                            $('input[type=checkbox][value="'+elem_data[m].element_type_id+'"]').prop('checked',true);
                                   $('input[type=checkbox][value="'+elem_data[m].element_type_id+'"]').prop('disabled',true);
                            var values = elem_data[m].element_type_id;
                          var elem_val=jQuery.parseJSON(elem_data[m].value);
                          
                          if(values == "1")
                            {
                                $("#element_type_id_" + values).show();
                                
                            }
                            
                            if(values == "2")
                            {
                                $("#element_type_id_" + values).show();  
                            }
                            
                            if(values == "3")
                            {
                                $("#element_type_id_" + values).show();  
                                
                            }
                            
                            if(values == "4")
                            {
                                $("#element_type_id_" + values).show();  
                            }
                        
                            if(values == "5")
                            {
                                    $("#element_type_id_" + values).show();  
                            }
                                
                        }
                        $('input[type=checkbox][class=elem_type]').change(function()
                        {
                            $('.submit').show();
                            value = $(this).val();

                                    if($(this).prop("checked") == true && value == "1")
                                    {
                                      $("#" + value).show();      
                                    }
                                    if($(this).prop("checked") == false && value == "1")
                                    {
                                        $("#" + value).hide();
                                    }
                                    if($(this).prop("checked") == true && value == "2")
                                    {
                                        $("#" + value).show();  
                                    }
                                    if($(this).prop("checked") == false && value == "2")
                                    {
                                        $("#" + value).hide();
                                    }
                                    if($(this).prop("checked") == true && value == "3")
                                    {
                                        $("#" + value).show();  
                                      
                                    }
                                    if($(this).prop("checked") == false && value == "3")
                                    { 
                                        $("#" + value).hide();
                                    }
                                    if($(this).prop("checked") == true && value == "4")
                                    {
                                        $("#" + value).show();  
                                    }
                                    if($(this).prop("checked") == false && value == "4")
                                    {
                                        $("#" + value).hide();
                                    }
                                    if($(this).prop("checked") == true && value == "5")
                                    {
                                         $("#" + value).show();  
                                    }
                                    if($(this).prop("checked") == false && value == "5")
                                    {
                                        $("#" + value).hide();
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
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
            <form action="/bindingform/updateDB/{{$jc_id}}/{{$io_id}}" method="post" id="form">
                @csrf
                    <div class="row">
                            <ul class="nav nav1 nav-pills">
                                    <li class="nav-item">
                                      <a class="nav-link"  href="{{url('/jobcardform/update'.'/'.$jc_id) }}">Job Card</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link"   href="{{url('/elementform/update'.'/'.$jc_id.'/'.$io_id) }}">Element Details</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" href="{{url('/rawform/update'.'/'.$jc_id.'/'.$io_id) }}">Raw Material Details</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link active" style="background-color: #87CEFA"  href="{{url('/bindingform/update'.'/'.$jc_id.'/'.$io_id) }}">Binding Details</a>
                                    </li>
                                  </ul>
                                  <br>
                        <input  type="hidden" name="jc_id" value="{{$jc_id}}">
                        <input  type="hidden" name="io_id" value="{{$io_id}}">
                        <input  type="hidden" name="item[]"  class="name">
                    </div>
                    <div class="row box box-header">
                            <div class="col-md-6">
                                    <label for="">{{__('jobcard.remark')}}<sup>*</sup></label>
                                    <input type="text" name="remark"  class="input-css">
                                    {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}
                                </div>
                    </div>
                    <div class='' id="box2">

                    </div>
                    @php
                         $elem=$elems['item_category_id'];
                    @endphp
                    <div class="row text" style="display:none" id="1">
                            @include('binding.text')    
                        </div>
            
                       <div class="row cover" style="display:none" id="2">
                            @include('binding.cover')
                        </div>  
          
                        <div class="row posteen" style="display:none" id="3">
                        @include('binding.posteen')
                        </div>
         
                       <div class="row sepeator" style="display:none" id="4">
                        @include('binding.seperator')
                        </div> 
        
                       <div class="row hard" style="display:none" id="5">
                        @include('binding.hardcase')
                        </div>
      
                      <div class="row form" style="display:none" id="6">
                        @include('binding.form')
                        </div>  
    
                       <div class="row pamphlet" style="display:none" id="7">
                        @include('binding.pamphlet')
                        </div>  
    
                       <div class="row folder" style="display:none" id="8">
                        @include('binding.folder')
                        </div>  
        
                       
                        <div class="row dangler" style="display:none" id="9">
                                @include('binding.dangler')
                        </div>
              
             
                      <div class="row sticker" style="display:none" id="10">
                        @include('binding.sticker')
                         </div>   
              
               
                        <div class="row poster" style="display:none" id="11">
                        @include('binding.poster')
                   </div>
                
                       <div class="row banner" style="display:none" id="12">
                        @include('binding.banner')
                   </div>  
                
                
                        <div class="row tent_card" style="display:none" id="13">
                        @include('binding.tent_card')
                   </div>
                
               
                        <div class="row paper_bags" style="display:none" id="14">
                        @include('binding.paper_bags')
                   </div>
                         <div class="row other" style="display:none"  id="15">
                        @include('binding.other')
                   </div>
                   @foreach($element_detail as $item)
                   <input   name="old_id[]" type="hidden" value="{{$item['id']}}" class="old_elem">
                  <input   name="old_elem[]" type="hidden" value="{{$item['element_type_id']}}" class="old_elem">
                      @php
                          $val=json_decode(json_decode(json_encode($item['value'])));
                          $rem=json_decode(json_decode(json_encode($item['remark'])));
                          foreach ($val as $key => $value){
                              $arr[$key]=$value;           
                          }
                          foreach ($rem as $key => $value){
                              $arr1[$key]=$value;  
                          }
                          $elem=$elems['item_category_id'];
                      @endphp
                        @if ($item['element_type_id']==1)
                        <div class="row text" style="display:none" id="element_type_id_1">
                            @include('binding.text')    
                        </div>
                        @endif

                        @if ($item['element_type_id']==2)
                        <div class="row cover" style="display:none" id="element_type_id_2">
                        @include('binding.cover')
                        </div> 
                        @endif
                        @if ($item['element_type_id']==3)
                        <div class="row posteen" style="display:none" id="element_type_id_3">
                        @include('binding.posteen')
                        </div>
                        @endif
                        @if ($item['element_type_id']==4)
                        <div class="row sepeator" style="display:none" id="element_type_id_4">
                        @include('binding.seperator')
                        </div> 
                        @endif
                        @if ($item['element_type_id']==5)
                        <div class="row hard" style="display:none" id="element_type_id_5">
                        @include('binding.hardcase')
                        </div>
                        @endif

                        @if ($item['element_type_id']==6)
                        <div class="row form" style="display:none" id="element_type_id_6">
                        @include('binding.form')
                        </div>  
                        @endif @if ($item['element_type_id']==7)
                        <div class="row pamphlet" style="display:none" id="element_type_id_7">
                            @include('binding.pamphlet')
                            </div>  
                        @endif
                        @if ($item['element_type_id']==8)
                        <div class="row folder" style="display:none" id="element_type_id_8">
                            @include('binding.folder')
                            </div>  
                        @endif
                        @if ($item['element_type_id']==9)
                        
                        <div class="row dangler" style="display:none" id="element_type_id_9">
                        @include('binding.dangler')
                        </div>

                        @endif
                        @if ($item['element_type_id']==10)

                        <div class="row sticker" style="display:none" id="element_type_id_10">
                        @include('binding.sticker')
                        </div>   

                        @endif
                        @if ($item['element_type_id']==11)
                        <div class="row poster" style="display:none" id="element_type_id_11">
                            @include('binding.poster')
                        </div> 
                        @endif
                        @if ($item['element_type_id']==12)
                        <div class="row banner" style="display:none" id="element_type_id_12">
                            @include('binding.banner')
                        </div>   
                        @endif
                        @if ($item['element_type_id']==13)

                        <div class="row tent_card" style="display:none" id="element_type_id_13">
                        @include('binding.tent_card')
                        </div>
                        @endif
                        @if ($item['element_type_id']==14)
                        <div class="row paper_bags" style="display:none" id="element_type_id_14">
                            @include('binding.paper_bags')
                        </div>  
                        @endif
                        @if ($item['element_type_id']==15)
                        <div class="row other" style="display:none"  id="element_type_id_15">
                            @include('binding.other')
                        </div> 
                        @endif
         @endforeach


         
                    <div class="row submit" >
                        <div class="col-md-12 text-right" >
                                <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
    </section><!--end of section-->
@endsection

