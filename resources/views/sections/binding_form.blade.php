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
  <link rel="stylesheet" href="/css/party.css">
<style>
.span
{
    color:red;
}
</style>
@endsection

@section('js')
<script src="/js/views/binding.js"></script>
<script>
var elem_count={{$elem_count}};
    if(elem_count!=0){
        alert('binding form has already been created for this Job Card!!!');
    }
    $('#box2').empty();
        var io_id = {{$io_id}};
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
                        '</div>'
                    );
                    if(id>5)
                    {
                        $('.element').append('<input type="hidden" name="elem_type[]" id="" value="'+id+'" class="elem_type">');
                            var elem_id=id;
                            $("#" + elem_id).show();
                            $('.submit').show();
                    }
                    if(id<6)
                    {

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
            <form action="/binding/insert" method="post" id="form">
                @csrf
               <div class="box box-header">
                    <div class="row">
                            <div class="col-md-6">
                                    <label for="">{{__('jobcard.remark')}}</label>
                                    <input type="text" name="remark" class="input-css" required>
                                    {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}
                                </div>
                    </div>
               </div>
                    <div class="row">
                        <input  type="hidden" name="jc_id" value="{{$jc_id}}">
                        <input  type="hidden" name="io_id" value="{{$io_id}}">
                        <input  type="hidden" name="item[]"  class="name">
                    </div>
                    @php
                        $elem=$elem['item_category_id'];
                    @endphp
                    <div class='' id="box2">

                    </div>
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
                    <div class="row submit" style="display:none" >
                        <div class="col-md-12 text-right" >
                                <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
    </section><!--end of section-->
@endsection

