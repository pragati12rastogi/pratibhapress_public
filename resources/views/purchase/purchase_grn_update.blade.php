@extends($layout)

@section('title', __('purchase/grn.mytitle'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Purchase GRN</a></li> 

@endsection
@section('js')
<script src="/js/purchase/grn.js"></script>
<script>
  var ids=-1;
    var master_cat_id;
@foreach($grn as $key)
 master_cat_id="{{$key->master_cat_id}}";
@endforeach 
  
@foreach($grn_item as $item)
ids++;
console.log(ids);
var sub_cat_id="{{$item->category_id}}";
var item_name_id="{{$item->item_name_id}}";
console.log(sub_cat_id);

if(master_cat_id==1){
 
    $('#paper_cat_'+ids).val(sub_cat_id).select2().trigger("change");
    paper(sub_cat_id,item_name_id,ids);
   
}
	

if(master_cat_id==2){
    $('#ink_cat_'+ids).val(sub_cat_id).select2().trigger("change");
    ink(sub_cat_id,item_name_id,ids);
}
if(master_cat_id==3){
    $('#plate_cat_'+ids).val(sub_cat_id).select2().trigger("change");
    plate(sub_cat_id,item_name_id,ids);
}

if(master_cat_id==4){
    $('#misc_cat_'+ids).val(sub_cat_id).select2().trigger("change");
    misc(sub_cat_id,item_name_id,ids);
}
	

@endforeach
ids=0;

function paper(input,item_name_id,ids){
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/paper/item/" + input,
        type: "GET",
        success:function(result) {
            console.log(result);
            
            $('#paper_item_'+ids).empty();
            for (var i = 0; i < result.length; i++) {
                $('#paper_item_'+ids).append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
               
               $('#paper_item_'+ids).val(item_name_id).select2().trigger("change");
               $('#ajax_loader_div').css('display','none');
        }
    });
}

function ink(input,item_name_id,ids){
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/ink/item/" + input,
        type: "GET",
        success:function(result) {
            console.log(result);
            
            $('#ink_item_'+ids).empty();
            for (var i = 0; i < result.length; i++) {
                $('#ink_item_'+ids).append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
              
               $('#ink_item_'+ids).val(item_name_id).select2().trigger("change");
               $('#ajax_loader_div').css('display','none');
        }
    });
}

function plate(input,item_name_id,ids){
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/plate/item/" + input,
        type: "GET",
        success:function(result) {
            console.log(result);
            
            $('#plate_item_'+ids).empty();
            for (var i = 0; i < result.length; i++) {
                $('#plate_item_'+ids).append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
              
               $('#plate_item_'+ids).val(item_name_id).select2().trigger("change");
               $('#ajax_loader_div').css('display','none');
        }
    });
}

function misc(input,item_name_id,ids){
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/misc/item/" + input,
        type: "GET",
        success:function(result) {
            console.log(result);
            
            $('#misc_item_'+ids).empty();
            for (var i = 0; i < result.length; i++) {
                $('#misc_item_'+ids).append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
              
               $('#misc_item_'+ids).val(item_name_id).select2().trigger("change");
               $('#ajax_loader_div').css('display','none');
        }
    });
}
</script>
<script>
        var id2=10;
    $('.add_paper').click(function () {
       
          $('.new_paper').append(
            '<div class="box-header with-border">'+    
                '<div class="box box-default">  <br>'+
                    '<button type="button" class="close" onclick="$(this).parent().parent().remove();" style="float:right;margin-right:16px;line-height: 0;font-size: 28px;" id="removeconsignee" >X</button>'+
                    '<h2 class="box-title" style="font-size: 28px;margin-left:20px">Paper</h2><br><br><br>'+
                    '<div class="container-fluid wdt">'+
                        '<div class="row">'+
                            '<div class="col-md-6">'+
                                '<label>{{__('purchase/indent.paper')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>'+
                                '<select style="width:100%" name="paper_cat[]" id="paper_cat'+id2+'" onchange="paperitem(this)" class="select2 input-css paper_cat">'+
                                    '<option value="">Select Paper Item Category</option>'+
                                    @foreach ($paper as $key)
                                   '<option value="{{$key->id}}">{{'Paper-' .$key->name}}</option>'+
                                    @endforeach
                                '</select>'+
                            '</div>'+
                            '<div class="col-md-6">'+
                                    '<label>Paper Item Name<sup>*</sup></label>'+
                                    '<select style="width:100%" name="paper_item[]" id="paper_item"  class="select2 input-css paper_item">'+
                                        '<option value="">Select Paper Item Name</option>'+
                                    '</select>'+
                            '</div>'+
                        '</div><br><br>'+
                    '<div class="row">'+
                        '<div class="col-md-6">'+
                            '<label>Quantity<sup>*</sup></label>'+
                            '<input type="number" min="0" step="none" name="paper_item_qty[]" id="paper_item_qty" class="input-css paper_item_qty">'+
                        '</div>'+
                        '<div class="col-md-6">'+
                                '<label>Paper Unit of Quantity<sup>*</sup></label>'+
                                '<select style="width:100%" name="paper_item_unit[]" id="paper_item_unit" class="select2 input-css paper_item_unit">'+
                                   ' <option value="">Select Unit Of Quantity</option>'+
                                    @foreach ($unit_paper as $key)
                        '<option value="{{$key->id}}">{{$key->uom_name}}</option>'+
                            @endforeach
                                '</select>'+
                        '</div>'+
                    '</div><br><br>'+
                    '<div class="row">'+
                        '<div class="col-md-12">'+
                            '<label>Paper Item Description<sup>*</sup></label>'+
                            '<textarea name="paper_item_desc[]" id="paper_item_desc" class="paper_item_desc input-css" style="width: 100%"></textarea>'+
                        '</div>'+
                        
                   ' </div><br><br> '+
                '<div class="row">'+
                        '<div class="col-md-6">'+
                                '<label>Length<sup>*</sup></label>'+
                               ' <input type="number" min="0" step="none" name="paper_length[]"  id="paper_length" class="input-css paper_length">'+
                            '</div>'+
                  '  <div class="col-md-6">'+
                        '<label>Breadth<sup>*</sup></label>'+
                        '<input type="number" min="0" step="none" name="paper_breadth[]"  id="paper_breadth" class="input-css paper_breadth">'+
                    '</div> '+
                '</div> <br><br>'+
                    '<div class="row">'+
                        '<div class="col-md-6">'+
                            '<label>GSM<sup>*</sup></label>'+
                            '<input type="text" name="paper_gsm[]"  id="paper_gsm" class="input-css paper_gsm">'+
                        '</div>'+
                        '<div class="col-md-6 ">'+
                            '<label>Tax<sup>*</sup></label>'+
                            '<select type="text" name="paper_tax[]" id="paper_tax" class="input-css select2 paper_tax" style="width: 100%">'+
                            '<option value="">Select Tax</option>'+
                            @foreach ($tax as $key)
                            '<option value="{{$key->id}}">{{$key->value}}</option>'+
                            @endforeach
                            '</select>'+
                           
                    '</div>'+
                    '</div> <br><br>'+
                    '<div class="row"> '+
                            '<div class="col-md-6">'+
                                '<label>Rate<sup>*</sup></label>'+
                               ' <input type="number" min="0" name="paper_rate[]" id="paper_rate" class="input-css paper_rate">'+
                           ' </div>'+
                            '<div class="col-md-6 {{ $errors->has('paper_amount') ? 'has-error' : ''}}">'+
                                '<label>Amount<sup>*</sup></label>'+
                                '<input type="number" step="any" min="0"  name="paper_amount[]" id="paper_amount" class="input-css paper_amount">'+
                            '</div>'+
                    '</div> <br><br>'+

            '</div> '+
   ' </div>'+
'</div>');
$('select').select2();

id2++;
        });
    </script>
    <script>
            var id2=10;
        $('.add_ink').click(function () {
              $('.new_ink').append(
                        '<div class="row ink" >'+
                                '<div class="box-header with-border">'+
                                        '<div class="box box-default">  <br>'+
                                            '<button type="button" class="close" onclick="$(this).parent().parent().remove();" style="float:right;margin-right:16px;line-height: 0;font-size: 28px;" id="removeconsignee" >X</button>'+
                                                '<h2 class="box-title" style="font-size: 28px;margin-left:20px">Inks & Chemicals</h2><br><br><br>'+
                                                '<div class="container-fluid wdt">'+
                                                    '<div class="row">'+
                                                        '<div class="col-md-6 ">'+
                                                             '<label>Inks & Chemicals Item Category<sup>*</sup></label>'+
                                                             '<select style="width:100%" name="ink_cat[]" id="ink_cat" onchange="inkitem(this)" class="select2 input-css ink_cat">'+
                                                                 '<option value="">Select Inks & Chemicals Item Category</option>'+
                                                                 @foreach ($ink as $key)
                                                             '<option value="{{$key->id}}">{{$key->name}}</option>'+
                                                                 @endforeach
                                                             '</select>'+
                                                            
                                                        '</div>'+
                 
                                                        '<div class="col-md-6">'+
                                                            ' <label>Inks & Chemicals Item Name<sup>*</sup></label>'+
                                                            ' <select style="width:100%" name="ink_item[]" id="ink_item"  class="select2 input-css ink_item">'+
                                                                 '<option value="">Select Inks & Chemicals Item Name</option>  '+
                                                             '</select>'+
                                                        '</div>'+
                                                     '</div><br><br>'+
                                                        '<div class="row">'+
                                                          
                                                            '<div class="col-md-6">'+
                                                                '<label>Quantity<sup>*</sup></label>'+
                                                                '<input type="number" min="0" step="none" name="ink_item_qty[]" id="ink_item_qty" class="input-css ink_item_qty">'+
                                                            '</div>'+
                                                            '<div class="col-md-6">'+
                                                                    '<label>Inks & Chemicals Unit of Quantity<sup>*</sup></label>'+
                                                                    '<select style="width:100%" name="ink_item_unit[]" id="ink_item_unit" class="select2 input-css ink_item_unit">'+
                                                                        '<option value="">Select Unit Of Quantity</option>'+
                                                                        @foreach ($unit_ink as $key)
                                                           ' <option value="{{$key->id}}">{{$key->uom_name}}</option>'+
                                                                @endforeach
                                                                   ' </select>'+
                                                            '</div>'+
                                                        '</div>'+
                    
                                                    '<br><br>'+
                                                    '<div class="row">'+
                                                            '<div class="col-md-12 ">'+
                                                                '<label>Inks & Chemicals Item Description<sup>*</sup></label>'+
                                                                '<textarea name="ink_item_desc[]" id="ink_item_desc" class="ink_item_desc input-css" style="width: 100%"></textarea>'+
                                                            '</div>'+
                                                            
                                                        '</div><br><br> '+
                                                        '<div class="row">'+
                                                            '<div class="col-md-4">'+
                                                                '<label>Tax<sup>*</sup></label>'+
                                                                '<select type="text" name="ink_tax[]" id="ink_tax" class="input-css select2 ink_tax" style="width: 100%">'+
                                                                '<option value="">Select Tax</option>'+
                                                                @foreach ($tax as $key)
                                                                '<option value="{{$key->id}}"</option>'+
                                                                @endforeach
                                                                '</select>'+
                                                             '</div>'+
                                                               
                                                                '<div class="col-md-4">'+
                                                                    '<label>Rate<sup>*</sup></label>'+
                                                                    '<input type="number" min="0" name="ink_rate[]"  id="ink_rate" class="input-css ink_rate">'+
                                                                '</div>'+
                                                                '<div class="col-md-4">'+
                                                                    '<label>Amount<sup>*</sup></label>'+
                                                                    '<input type="number" step="any" min="0"  name="ink_amount[]" id="ink_amount" class="input-css ink_amount">'+
                                                                '</div>'+
                                                        '</div> <br><br>'+
                                                '</div> '+
                                        '</div>'+
                                '</div>'+
                        '</div>'  
               );
    $('select').select2();
    id2++;
            });
    </script>
    <script>
            var id2=10;
        $('.add_plate').click(function () {

              $('.new_plate').append(
                '<div class="row">'+
                                '<div class="box-header with-border">'+
                                        '<div class="box box-default">  <br>'+
                                            '<button type="button" class="close" onclick="$(this).parent().parent().remove();" style="float:right;margin-right:16px;line-height: 0;font-size: 28px;" id="removeconsignee" >X</button>'+
                                                '<h2 class="box-title" style="font-size: 28px;margin-left:20px">Plate</h2><br><br><br>'+
                                                '<div class="container-fluid wdt">'+
                                                   ' <div class="row">'+
                                                        '<div class="col-md-6">'+
                                                            ' <label>Plate Item Category<sup>*</sup></label>'+
                                                             '<select style="width:100%" name="plate_cat[]" id="plate_cat" onchange="plateitem(this)" class="select2 input-css plate_cat">'+
                                                                 '<option value="">Select Plate Item Category</option>'+
                                                                 @foreach ($plate as $key)
                                                             '<option value="{{$key->id}}">{{$key->name}}</option>'+
                                                                 @endforeach
                                                             '</select>'+
                                                        '</div>'+
                 
                                                        '<div class="col-md-6 ">'+
                                                             '<label>Plate Item Name<sup>*</sup></label>'+
                                                             '<select style="width:100%" name="plate_item[]" id="plate_item"  class="select2 input-css plate_item">'+
                                                                 '<option value="">Select Plate Name</option>'+
                                                                 
                                                            ' </select>'+
                                                             
                                                       ' </div>'+
                                                     '</div><br><br>'+
                                                        '<div class="row">'+
                                                          
                                                            '<div class="col-md-6 ">'+
                                                                '<label>Quantity<sup>*</sup></label>'+
                                                                '<input type="number" min="0" step="none" name="plate_item_qty[]"  id="plate_item_qty" class="input-css plate_item_qty">'+
                                                              
                                                            '</div>'+
                                                            '<div class="col-md-6">'+
                                                                    '<label>Plate Unit of Quantity<sup>*</sup></label>'+
                                                                    '<select style="width:100%" name="plate_item_unit[]" id="plate_item_unit" class="select2 input-css plate_item_unit">'+
                                                                        '<option value="">Select Unit Of Quantity</option>'+
                                                                        @foreach ($unit_plate as $key)
                                                            '<option value="{{$key->id}}">{{$key->uom_name}}</option>'+
                                                                @endforeach
                                                                    '</select>'+
                                                            '</div>'+
                                                        '</div>'+
                    
                                                    '<br><br>'+
                                                   ' <div class="row">'+
                                                           ' <div class="col-md-12">'+
                                                               ' <label>Plate Item Description<sup>*</sup></label>'+
                                                                '<textarea name="plate_item_desc[]" id="plate_item_desc" class="plate_item_desc input-css" style="width: 100%"></textarea>'+
                                                           ' </div>'+
                                                            
                                                       ' </div><br><br>'+ 
                                                        '<div class="row">'+
                                                               ' <div class="col-md-12 ">'+
                                                                       ' <label>Job Card<sup>*</sup></label>'+
                                                                        '<select type="text" name="plate_job[]" id="plate_job" class="input-css select2 plate_job" style="width: 100%">'+
                                                                        '<option value="">Select Job Card</option>'+
                                                                        @foreach ($jobcard as $key)
                                                                        '<option value="{{$key->id}}">{{$key->job_number}}</option>'+
                                                                        @endforeach
                                                                        '</select>'+
                                                                   ' </div>'+
                                                                
                                                        '</div><br><br>'+
                                                        '<div class="row">'+
                                                                '<div class="col-md-4 ">'+
                                                                        '<label>Tax<sup>*</sup></label>'+
                                                                        '<select type="text" name="plate_tax[]" id="plate_tax" class="input-css select2 plate_tax" style="width: 100%">'+
                                                                       ' <option value="">Select Tax</option>'+
                                                                        @foreach ($tax as $key)
                                                                      '  <option value="{{$key->id}}">{{$key->value}}</option>'+
                                                                        @endforeach
                                                                       ' </select>'+
                                                                        
                                                                '</div>'+
                                                               
                                                                '<div class="col-md-4">'+
                                                                    '<label>Rate<sup>*</sup></label>'+
                                                                    '<input type="number" min="0" name="plate_rate[]" id="plate_rate" class="input-css plate_rate">'+
                                                                    
                                                                '</div>'+
                                                               ' <div class="col-md-4">'+
                                                                   ' <label>Amount<sup>*</sup></label>'+
                                                                    '<input type="number" step="any" min="0"  name="plate_amount[]" id="plate_amount" class="input-css plate_amount">'+
                                                                '</div>'+
                                                        '</div> <br><br>'+
                    
                                                '</div> '+
                                        '</div>'+
                                '</div>'+
                        '</div>'
               );
    $('select').select2();
    id2++;
            });
    </script>
        <script>
                var id2=10;
            $('.add_misc').click(function () {

                  $('.new_misc').append(
                    '<div class="row">'+
                                '<div class="box-header with-border">'+
                                       ' <div class="box box-default">  <br>'+
                                        '<button type="button" class="close" onclick="$(this).parent().parent().remove();" style="float:right;margin-right:16px;line-height: 0;font-size: 28px;" id="removeconsignee" >X</button>'+
                                               ' <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.misc')}}</h2><br><br><br>'+
                                                '<div class="container-fluid wdt">'+
                                                    '<div class="row">'+
                                                        '<div class="col-md-6">'+
                                                             '<label>{{__('purchase/indent.misc')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>'+
                                                             '<select style="width:100%" name="misc_cat[]" id="misc_cat" onchange="miscitem(this)" class="select2 input-css misc_cat">'+
                                                                 '<option value="">Select Miscellaneous Item Category</option>'+
                                                                 @foreach ($misc as $key)
                                                                '<option value="{{$key->id}}">{{$key->name}}</option>'+
                                                                 @endforeach
                                                                '</select>'+
                                                        '</div>'+
                 
                                                        '<div class="col-md-6">'+
                                                            ' <label>{{__('purchase/indent.misc')}}  {{__('purchase/indent.name')}}<sup>*</sup></label>'+
                                                            ' <select style="width:100%" name="misc_item[]" id="misc_item"  class="select2 input-css misc_item">'+
                                                                 '<option value="">Select Miscellaneous Item Name</option>'+
                                                                 
                                                             '</select>'+
                                                       ' </div>'+
                                                     '</div><br><br>'+
                                                        '<div class="row">'+
                                                          
                                                            '<div class="col-md-6 {{ $errors->has('misc_item_qty') ? 'has-error' : ''}}">'+
                                                                '<label>Quantity<sup>*</sup></label>'+
                                                               ' <input type="number" min="0" step="none" name="misc_item_qty[]"  id="misc_item_qty" class="input-css misc_item_qty">'+
                                                            '</div>'+
                                                            '<div class="col-md-6">'+
                                                                    '<label>{{__('stock/stock.misc')}}  {{__('stock/stock.unit')}}<sup>*</sup></label>'+
                                                                    '<select style="width:100%" name="misc_item_unit[]" id="misc_item_unit" class="select2 input-css misc_item_unit">'+
                                                                       ' <option value="">Select Unit Of Quantity</option>'+
                                                                        @foreach ($unit_misc as $key)
                                                            '<option value="{{$key->id}}">{{$key->uom_name}}</option>'+
                                                                @endforeach
                                                                    '</select>'+
                                                            '</div>'+
                                                        '</div>'+
                    
                                                    '<br><br>'+
                                                    '<div class="row">'+
                                                            '<div class="col-md-12">'+
                                                                '<label>{{__('purchase/grn.misc')}}  {{__('purchase/grn.item_desc')}}<sup>*</sup></label>'+
                                                                '<textarea name="misc_item_desc[]" id="misc_item_desc" class="misc_item_desc input-css" style="width: 100%"></textarea>'+
                                                            '</div>'+
                                                            
                                                        '</div><br><br> '+
                                                        '<div class="row">'+
                                                            '<div class="col-md-4">'+
                                                               ' <label>{{__('purchase/grn.tax')}}<sup>*</sup></label>'+
                                                              '  <select type="text" name="misc_tax[]" id="misc_tax" class="input-css select2 misc_tax" style="width: 100%">'+
                                                               ' <option value="">Select Tax</option>'+
                                                                @foreach ($tax as $key)
                                                               ' <option value="{{$key->id}}">{{$key->value}}</option>'+
                                                                @endforeach
                                                               ' </select>'+
                                                        '</div>'+
                                                               
                                                                '<div class="col-md-4">'+
                                                                    '<label>{{__('purchase/grn.rate')}}<sup>*</sup></label>'+
                                                                   ' <input type="number" min="0" name="misc_rate[]"  id="misc_rate" class="input-css misc_rate">'+
                                                               ' </div>'+
                                                               ' <div class="col-md-4">'+
                                                                    '<label>{{__('purchase/grn.amount')}}<sup>*</sup></label>'+
                                                                    '<input type="number" step="any" min="0"  name="misc_amount[]" id="misc_amount" class="input-css misc_amount">'+
                                                               ' </div>'+
                                                        '</div> <br><br>'+
                    
                                              '  </div> '+
                                      ' </div>'+
                               ' </div>'+
                             
                        '</div>'
                   
                   );
        $('select').select2();
        id2++;
                });
                </script>

                <script>
                    
                    function myFunction(id,grn_id) {
                    var txt;
                    var r = confirm("Are You Sure Want to Delete this File!");
                    if (r == true) {
                        delete_file(id,grn_id);
                    } else {
                        txt = "You pressed Cancel!";
                    }
                    document.getElementById("demo").innerHTML = txt;
                    }
                function delete_file(id,grn_id){
                    // alert(id);
                    // alert(grn_id);
                    $.ajax({
                        url: '/purchase/grn/file/delete/'+id,
                        type: 'GET',
                        success: function(result)
                        {
                            $('#ajax_loader_div').css('display','none');

                        },
                        error: function(xhr, status, error) { 
                            $('#ajax_loader_div').css('display','none');
                            var errorMessage = xhr.status + ': ' + xhr.statusText;
                            console.log(errorMessage);
                        }
                    });
                    $('.file_'+id).remove();
                    $('.delete_file_'+id).attr('value',id);
                }
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
        <form method="POST" action="/purchase/grn/update/{{$id}}" id="form" enctype="multipart/form-data">
                   @csrf
                   @php
                $flag=0;
                $flag1=0;
                $flag2=0;
                $flag3=0;
                @endphp
    
                @if (empty(session('lastformdata')))            
                @php
                  $flag=1;
                  $to=1;
                  $flag1=1;
                  $to1=1;
                  $flag2=1;
                  $to2=1;
                  $flag3=1;
                  $to3=1;
                @endphp
                @else
                @php
                  $to = count(session('lastformdata')['paper_cat']);
                  $to1 = count(session('lastformdata')['ink_cat']);
                  $to2 = count(session('lastformdata')['plate_cat']);
                  $to3 = count(session('lastformdata')['misc_cat']);
                @endphp
                   @endif
                    <div class="box-header with-border">
                        <div class='box box-default'>  <br>
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.mytitle')}}</h2><br><br><br>
                            <div class="container-fluid wdt">
                                    <div class="row">
                                            <div class="col-md-3 ">
                                                    <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                                    <input type="text" name="update_reason" required="" class="input-css" id="update_reason">
                                                    {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                                </div><!--col-md-4-->
                                    </div>
                                    <br><br>
                         
                            </div>
                        </div>
                    </div>
                    @foreach ($grn as $item)
                        
                    @endforeach

                    @foreach ($grn_trans as $value)
                        
                    @endforeach

                    <div class="box-header with-border grn">
                        <div class='box box-default'>  <br>
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.grn')}}</h2><br><br><br>
                                <div class="container-fluid wdt">
                                    <div class="row">
                                        <div class="col-md-6 {{ $errors->has('material_inward') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/grn.material')}}<sup>*</sup></label>
                                            <select name="material_inward" id="material_inward" class="select2 input-css material_inward" style="width: 100%;">
                                                <option value="">Select Material Inward Number</option>
                                                @foreach ($material as $key)
                                            <option value="{{$key->id}}" {{$item->material_inward_id==$key->id ? 'selected="selected"':''}}>{{$key->material_inward_number}}</option>
                                                @endforeach
                                            </select>
                                            </select>
                                            {!! $errors->first('material_inward', '<p class="help-block">:message</p>') !!}

                                        </div>
                                        <div class="col-md-6 {{ $errors->has('received_by') ? 'has-error' : ''}}">
                                                <label>{{__('purchase/grn.rec')}}<sup>*</sup></label>
                                                <input type="text" name="received_by" value="{{$item->received_by}}" id="received_by" class="input-css received_by">
                                                {!! $errors->first('received_by', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div><br><br>
                                    <div class="row">
                                        <div class="col-md-6 {{ $errors->has('invoice') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/grn.invoice')}}<sup>*</sup></label>
                                            <input type="text" name="invoice" value="{{ $item->invoice_number}}" id="invoice_challan" class="input-css invoice_challan">
                                            {!! $errors->first('invoice', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6">
                                                <label>{{__('purchase/grn.po')}}<sup>*</sup></label>
                                                <select name="po_num" id="po_num" class="select2 input-css po_num" style="width: 100%;">
                                                        <option value="">Select PO Number</option>
                                                        @foreach ($po as $key)
                                            <option value="{{$key->id}}" {{$item->po_num_id==$key->id ? 'selected="selected"':''}}>{{$key->po_num}}</option>
                                                @endforeach
                                                    </select>
                                                {!! $errors->first('po_num', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div><br><br>
                                    <div class="row">
                                        <div class="col-md-6 {{ $errors->has('grn_date') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/grn.grn_date')}}<sup>*</sup></label>
                                            <input type="text" autocomplete="off" name="grn_date" value="{{$item->grn_date}}" id="grn_date" class="input-css grn_date datepicker1">
                                            {!! $errors->first('grn_date', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('supp_name') ? 'has-error' : ''}}">
                                                <label>{{__('purchase/grn.supp_name')}}<sup>*</sup></label>
                                                <input type="text" name="supp_name" value="{{$item->supp_name}}" id="supp_name" class="input-css supp_name">
                                                {!! $errors->first('supp_name', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div><br><br> 
                                    <div class="row">
                                        <div class="col-md-12 {{ $errors->has('remark') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/grn.remark')}}<sup>*</sup></label>
                                            <textarea name="remark" id="remark" class="remark input-css" style="width: 100%">{{ $item->remark}}</textarea>
                                            {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        
                                    </div><br><br> 
                                    <div class="row">
                                        <div class="col-md-12 {{ $errors->has('mode') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/grn.mode')}}<sup>*</sup></label>
                                            <div class="col-md-4">
                                                <div class="radio">
                                                    <label><input    autocomplete="off" type="radio" class="mode" {{ $item->mode_of_trans=='By Self' ? 'checked=checked' :'' }} value="By Self" name="mode">By Self</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="radio">
                                                    <label><input autocomplete="off" type="radio" class="mode" {{ $item->mode_of_trans=='By Transporter'? 'checked=checked' :'' }} value="By Transporter" name="mode">By Transporter</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="radio">
                                                    <label><input  autocomplete="off" type="radio" class="mode" {{ $item->mode_of_trans=='By Courier'? 'checked=checked' :'' }} value="By Courier" name="mode">By Courier</label>
                                                </div>
                                            </div>
                                            {!! $errors->first('mode', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    </div><br><br>
                                    <div class="row self" {{ $item->mode_of_trans=='By Self'? 'style=display:block' :'style=display:none' }}>
                                            <h4 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.self')}}</h4><br><br><br>
                                        <div class="col-md-12 {{ $errors->has('self_name') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/grn.name')}}<sup>*</sup></label>
                                            <input type="text" name="self_name" value="{{ $value->name}}" id="self_name" class="input-css self_name">
                                            {!! $errors->first('self_name', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div> 
                                    <div class="row transport" {{ $item->mode_of_trans=='By Transporter'? 'style=display:block' :'style=display:none' }}>
                                        <h4 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.road')}}</h4><br><br><br>
                                       <div class="row">
                                            <div class="col-md-4 {{ $errors->has('tran_name') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/grn.tran')}}<sup>*</sup></label>
                                                    <input type="text" name="tran_name" value="{{$value->name}}" id="tran_name" class="input-css tran_name">
                                                    {!! $errors->first('tran_name', '<p class="help-block">:message</p>') !!}
                                                </div>
                                                <div class="col-md-4 {{ $errors->has('veh_name') ? 'has-error' : ''}}">
                                                        <label>{{__('purchase/grn.veh_name')}}<sup>*</sup></label>
                                                        <input type="text" name="veh_name" value="{{$value->vehicle_name}}"  id="veh_name" class="input-css veh_name">
                                                        {!! $errors->first('veh_name', '<p class="help-block">:message</p>') !!}
                                                </div>
                                                <div class="col-md-4 {{ $errors->has('veh_num') ? 'has-error' : ''}}">
                                                        <label>{{__('purchase/grn.veh_num')}}<sup>*</sup></label>
                                                        <input type="text" name="veh_num" value="{{$value->vehicle_num}}" id="veh_num" class="input-css veh_num">
                                                        {!! $errors->first('veh_num', '<p class="help-block">:message</p>') !!}
                                                </div>
                                       </div><br><br>
                                        <div class="row">
                                                <div class="col-md-4 {{ $errors->has('bilty_num') ? 'has-error' : ''}}">
                                                        <label>{{__('purchase/grn.bilty_num')}}<sup>*</sup></label>
                                                        <input type="text" name="bilty_num" value="{{$value->bilty_num}}" id="bilty_num" class="input-css bilty_num">
                                                        {!! $errors->first('bilty_num', '<p class="help-block">:message</p>') !!}
                                                </div>
                                                <div class="col-md-4 {{ $errors->has('bilty_date') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/grn.bilty_date')}}<sup>*</sup></label>
                                                    <input type="text" name="bilty_date" autocomplete="off" value="{{$value->bilty_date}}" id="bilty_date" class="input-css bilty_date datepicker1">
                                                    {!! $errors->first('bilty_date', '<p class="help-block">:message</p>') !!}
                                                </div>
                                        </div><br><br>   
                                    </div>
                                    <div class="row courier" {{ $item->mode_of_trans =='By Courier'? 'style=display:block' :'style=display:none' }}>
                                            <h4 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.cour')}}</h4><br><br><br>
                                           <div class="row">
                                                <div class="col-md-4 {{ $errors->has('cour_name') ? 'has-error' : ''}}">
                                                        <label>{{__('purchase/grn.cour_name')}}<sup>*</sup></label>
                                                        <input type="text" name="cour_name" value="{{$value->name}}" id="cour_name" class="input-css cour_name">
                                                        {!! $errors->first('cour_name', '<p class="help-block">:message</p>') !!}
                                                    </div>
                                                    <div class="col-md-4 {{ $errors->has('doc_num') ? 'has-error' : ''}}">
                                                            <label>{{__('purchase/grn.doc_num')}}<sup>*</sup></label>
                                                            <input type="text" value="{{$value->docket_num}}" name="doc_num" id="doc_num" class="input-css doc_num">
                                                            {!! $errors->first('doc_num', '<p class="help-block">:message</p>') !!}
                                                    </div>
                                                    <div class="col-md-4 {{ $errors->has('doc_date') ? 'has-error' : ''}}">
                                                            <label>{{__('purchase/grn.doc_date')}}<sup>*</sup></label>
                                                            <input type="text" name="doc_date" autocomplete="off" value="{{$value->docket_date}}" id="doc_date" class="input-css doc_date datepicker1">
                                                            {!! $errors->first('doc_date', '<p class="help-block">:message</p>') !!}
                                                    </div>
                                           </div>
                                    </div><br><br>  
                                    <div class="row">
                                        <div class="col-md-12 {{ $errors->has('supp_challan') ? 'has-error' : ''}}">
                                                <label>{{__('purchase/grn.supp_challan')}}<sup>*</sup></label>
                                        <input type="file" name="supp_challan[]" id="supp_challan" multiple aria-describedby="fileHelp" class="supp_challan">
                                       
                                                {!! $errors->first('supp_challan', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div><br><br> 
                                   <div class="row">
                                      
                                       @php
                                           $i=1;
                                       @endphp
                                       @foreach ($grn_file as $file)
                                      
                                        @php
                                             $file_type=explode('.',$file->supplier_bill_file);
                                        $file_type = $file_type[count($file_type)-1];
                                        @endphp
                                        <div class="col-md-6">
                                            @if($file_type=="")
                                            View File {{$i}} <img height="480" width="720" alt="No File Uploaded">  
                                            
                                            @elseif (file_exists(public_path().'/Purchase/GRN' .'/'. $file->supplier_bill_file ))
                                                <input type="hidden" name="delete_file[]" class="delete_file_{{$file->id}}" >
                                                <div class="file_{{$file->id}}">
                                                        <a href="/Purchase/GRN/{{$file->supplier_bill_file}}" target="_blank"><u>View File {{$i}}</u></a>&nbsp;&nbsp;&nbsp;
                                                        <p onclick='myFunction({{$file->id}},{{$file->grn_id}})'  style="color:red"><u>Delete</u></p>&nbsp;&nbsp;&nbsp;
                                                </div>
                                            @else 
                                            <input type="hidden" name="delete_file[]" class="delete_file_{{$file->id}}" >
                                                <div class="file_{{$file->id}}">
                                                        <a><u>View File {{$i}}</u></a>&nbsp;&nbsp;&nbsp;<img height="480" width="720" alt="This File Does Not Exist">  
                                                        <p onclick='myFunction({{$file->id}},{{$file->grn_id}})'  style="color:red"><u>Delete</u></p>&nbsp;&nbsp;&nbsp;
                                                </div>
                                            @endif
                                            </div><br>
                                            @php
                                                $i++;
                                            @endphp
                                       @endforeach
                                   </div>
                                </div>
                        </div>
                    </div>
                    <div class="box-header with-border">
                            <div class='box box-default'>  <br>
                                <div class="container-fluid wdt">
                                    <div class="row">
                                        <div class="col-md-12 {{ $errors->has('entry_for') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/purchase_req.item')}}<sup>*</sup></label>
                                            @foreach ($master_id as $key)
                                            <div class="col-md-3">
                                                <div class="radio">
                                                    <label><input  disabled  autocomplete="off" type="radio" class="entry_for" {{ $item->master_cat_id==$key->id ? 'checked=checked' :''}} value="{{$key->id}}">{{$key->name}}</label>
                                                </div>
                                            </div> 
                                            @endforeach
                                        <input type="hidden" name="entry_for" value="{{$item->master_cat_id}}">
                                            
                                        </div><!--col-md-6-->
                                        {!! $errors->first('entry_for', '<p class="help-block">:message</p>') !!}
                                    </div><br><br>
                                </div>
                            </div>
                    </div>
                        <div class="row paper" {{ $item->master_cat_id=='1'? 'style=display:block' :'style=display:none' }}>
                           @php
                               $index=0;
                           @endphp
                            @foreach($grn_item as $vl)
                        <input type="hidden" name="old_item_paper[]" value="{{$vl->id}}">
                            <div class="box-header with-border">
                                    <div class='box box-default'>  <br>
                                            <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.paper')}}</h2><br><br><br>
                                            <div class="container-fluid wdt">
                                                <div class="row">
                                                    <div class="col-md-6 {{ $errors->has('paper_cat.'.$index) ? 'has-error' : ''}}">
                                                         <label>{{__('purchase/indent.paper')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>
                                                         <select style="width:100%" name="paper_cat[]" id="paper_cat_{{$index}}" onchange="paperitem(this)" class="select2 input-css paper_cat">
                                                             <option value="">Select Paper Item Category</option>
                                                             @foreach ($paper as $key)
                                                         <option value="{{$key->id}}" {{$vl->category_id==$key->id? 'selected="selected"':''}}>{{'Paper-' .$key->name}}</option>
                                                             @endforeach
                                                         </select>
                                                         {!! $errors->first('paper_cat.'.$index, '<p class="help-block">:message</p>') !!}
                                                    </div>
             
                                                    <div class="col-md-6 {{ $errors->has('paper_item.'.$index) ? 'has-error' : ''}}">
                                                         <label>{{__('purchase/indent.paper')}}  {{__('purchase/indent.name')}}<sup>*</sup></label>
                                                         <select style="width:100%" name="paper_item[]" id="paper_item_{{$index}}"  class="select2 input-css paper_item">
                                                             <option value="">Select Paper Item Name</option>
                                                             
                                                         </select>
                                                         {!! $errors->first('paper_item.'.$index, '<p class="help-block">:message</p>') !!}
                                                    </div>
                                                 </div><br><br>
                                                    <div class="row">
                                                      
                                                        <div class="col-md-6 {{ $errors->has('paper_item_qty.'.$index) ? 'has-error' : ''}}">
                                                            <label>{{__('purchase/grn.qty')}}<sup>*</sup></label>
                                                            <input type="number" min="0" step="none" name="paper_item_qty[]" value="{{ $vl->qty}}" id="paper_item_qty_{{$index}}" class="input-css paper_item_qty">
                                                            {!! $errors->first('paper_item_qty.'.$index, '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                        <div class="col-md-6 {{ $errors->has('paper_item_unit.'.$index) ? 'has-error' : ''}}">
                                                                <label>{{__('stock/stock.paper')}}  {{__('stock/stock.unit')}}<sup>*</sup></label>
                                                                <select style="width:100%" name="paper_item_unit[]" id="paper_item_unit_{{$index}}" class="select2 input-css paper_item_unit">
                                                                    <option value="">Select Unit Of Quantity</option>
                                                                    @foreach ($unit_paper as $key)
                                                        <option value="{{$key->id}}" {{$vl->item_unit_id==$key->id? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                                            @endforeach
                                                                </select>
                                                                {!! $errors->first('paper_item_unit.'.$index, '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                    </div>
                
                                                <br><br>
                                                <div class="row">
                                                        <div class="col-md-12 {{ $errors->has('paper_item_desc.'.$index) ? 'has-error' : ''}}">
                                                            <label>{{__('purchase/grn.paper')}}  {{__('purchase/grn.item_desc')}}<sup>*</sup></label>
                                                            <textarea name="paper_item_desc[]" id="paper_item_desc_{{$index}}"  class="paper_item_desc input-css" style="width: 100%">{{ $vl->item_desc}}</textarea>
                                                            {!! $errors->first('paper_item_desc.'.$index, '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                        
                                                    </div><br><br> 
                                                <div class="row">
                                                        <div class="col-md-6 {{ $errors->has('paper_length.'.$index) ? 'has-error' : ''}}">
                                                                <label>{{__('stock/stock.length')}}<sup>*</sup></label>
                                                                <input type="number" min="0" step="none" name="paper_length[]" value="{{ $vl->length}}" id="paper_length_{{$index}}" class="input-css paper_length">
                                                                {!! $errors->first('paper_length.'.$index, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                    <div class="col-md-6 {{ $errors->has('paper_breadth.'.$index) ? 'has-error' : ''}}">
                                                        <label>{{__('stock/stock.breadth')}}<sup>*</sup></label>
                                                        <input type="number" min="0" step="none" name="paper_breadth[]" value="{{ $vl->breadth}}" id="paper_breadth_{{$index}}" class="input-css paper_breadth">
                                                        {!! $errors->first('paper_breadth.'.$index, '<p class="help-block">:message</p>') !!}
                                                    </div> 
                                                </div> <br><br>
                                                    <div class="row">
                                                        <div class="col-md-6 {{ $errors->has('paper_gsm.'.$index) ? 'has-error' : ''}}">
                                                            <label>{{__('stock/stock.gsm')}}<sup>*</sup></label>
                                                            <input type="text" name="paper_gsm[]" value="{{ $vl->gsm}}" id="paper_gsm_{{$index}}" class="input-css paper_gsm">
                                                            {!! $errors->first('paper_gsm.'.$index, '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                        <div class="col-md-6 {{ $errors->has('paper_tax.'.$index) ? 'has-error' : ''}}">
                                                            <label>{{__('purchase/grn.tax')}}<sup>*</sup></label>
                                                            <select type="text" name="paper_tax[]" id="paper_tax_{{$index}}" class="input-css select2 paper_tax" style="width: 100%">
                                                            <option value="">Select Tax</option>
                                                            @foreach ($tax as $key)
                                                            <option value="{{$key->id}}" {{$vl->tax==$key->id? 'selected="selected"':''}}>{{$key->value}}</option>
                                                            @endforeach
                                                            </select>
                                                            {!! $errors->first('paper_tax.'.$index, '<p class="help-block">:message</p>') !!}
                                                    </div>
                                                    </div> <br><br>
                                                    <div class="row">
                                                           
                                                            <div class="col-md-6 {{ $errors->has('paper_rate.'.$index) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.rate')}}<sup>*</sup></label>
                                                                <input type="number" min="0" name="paper_rate[]" value="{{ $vl->rate}}" id="paper_rate_{{$index}}" class="input-css paper_rate">
                                                                {!! $errors->first('paper_rate.'.$index, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                            <div class="col-md-6 {{ $errors->has('paper_amount.'.$index) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.amount')}}<sup>*</sup></label>
                                                                <input type="number" step="any" min="0"  name="paper_amount[]" value="{{ $vl->amount}}" id="paper_amount_{{$index}}" class="input-css paper_amount">
                                                                {!! $errors->first('paper_amount.'.$index, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                    </div> <br><br>
                
                                            </div> 
                                    </div>
                            </div>
                            @php    
                            $index++;
                            @endphp
                            @endforeach
                            <div class="row new_paper">
                                
                            </div>
                            <div class="row">
                                    <input type="button" style="float:left" class="btn btn-success add_paper" value="Add More">
                            </div>
                        </div>
                        <div class="row ink" {{ $item->master_cat_id=='2'? 'style=display:block' :'style=display:none' }}>
                                @php
                                $index1=0;
                            @endphp
                                @foreach($grn_item as $val)
                                <input type="hidden" name="old_item_ink[]" value="{{$vl->id}}">
                                <div class="box-header with-border">
                                        <div class='box box-default'>  <br>
                                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.ink')}}</h2><br><br><br>
                                                <div class="container-fluid wdt">
                                                    <div class="row">
                                                        <div class="col-md-6 {{ $errors->has('ink_cat.'.$index1) ? 'has-error' : ''}}">
                                                             <label>{{__('purchase/indent.ink')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>
                                                             <select style="width:100%" name="ink_cat[]" id="ink_cat_{{$index1}}" onchange="inkitem(this)" class="select2 input-css ink_cat">
                                                                 <option value="">Select Inks & Chemicals Item Category</option>
                                                                 @foreach ($ink as $key)
                                                             <option value="{{$key->id}}" {{$val->category_id==$key->id? 'selected="selected"':''}}>{{$key->name}}</option>
                                                                 @endforeach
                                                             </select>
                                                             {!! $errors->first('ink_cat.'.$index1, '<p class="help-block">:message</p>') !!}
                                                        </div>
                 
                                                        <div class="col-md-6 {{ $errors->has('ink_item.'.$index1) ? 'has-error' : ''}}">
                                                             <label>{{__('purchase/indent.ink')}}  {{__('purchase/indent.name')}}<sup>*</sup></label>
                                                             <select style="width:100%" name="ink_item[]" id="ink_item_{{$index1}}"  class="select2 input-css ink_item">
                                                                 <option value="">Select Paper Item Name</option>
                                                                 
                                                             </select>
                                                             {!! $errors->first('ink_item.'.$index1, '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                     </div><br><br>
                                                        <div class="row">
                                                          
                                                            <div class="col-md-6 {{ $errors->has('ink_item_qty.'.$index1) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.qty')}}<sup>*</sup></label>
                                                                <input type="number" min="0" step="none" name="ink_item_qty[]" value="{{ $val->qty}}" id="ink_item_qty_{{$index1}}" class="input-css ink_item_qty">
                                                                {!! $errors->first('ink_item_qty.'.$index1, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                            <div class="col-md-6 {{ $errors->has('ink_item_unit.'.$index1) ? 'has-error' : ''}}">
                                                                    <label>{{__('stock/stock.ink')}}  {{__('stock/stock.unit')}}<sup>*</sup></label>
                                                                    <select style="width:100%" name="ink_item_unit[]" id="ink_item_unit_{{$index1}}" class="select2 input-css ink_item_unit">
                                                                        <option value="">Select Unit Of Quantity</option>
                                                                        @foreach ($unit_ink as $key)
                                                            <option value="{{$key->id}}" {{$val->item_unit_id==$key->id? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                                                @endforeach
                                                                    </select>
                                                                    {!! $errors->first('ink_item_unit.'.$index1, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                        </div>
                    
                                                    <br><br>
                                                    <div class="row">
                                                            <div class="col-md-12 {{ $errors->has('ink_item_desc.'.$index1) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.ink')}}  {{__('purchase/grn.item_desc')}}<sup>*</sup></label>
                                                                <textarea name="ink_item_desc[]" id="ink_item_desc_{{$index1}}" class="ink_item_desc input-css" style="width: 100%">{{ $val->item_desc}}</textarea>
                                                                {!! $errors->first('ink_item_desc.'.$index1, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                            
                                                        </div><br><br> 
                                                        <div class="row">
                                                            <div class="col-md-4 {{ $errors->has('ink_tax.'.$index1) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.tax')}}<sup>*</sup></label>
                                                                <select type="text" name="ink_tax[]" id="ink_tax_{{$index1}}" class="input-css select2 ink_tax" style="width: 100%">
                                                                <option value="">Select Tax</option>
                                                                @foreach ($tax as $key)
                                                                <option value="{{$key->id}}" {{$val->tax==$key->id? 'selected="selected"':''}}>{{$key->value}}</option>
                                                                @endforeach
                                                                </select>
                                                                {!! $errors->first('ink_tax.'.$index1, '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                               
                                                                <div class="col-md-4 {{ $errors->has('ink_rate.'.$index1) ? 'has-error' : ''}}">
                                                                    <label>{{__('purchase/grn.rate')}}<sup>*</sup></label>
                                                                    <input type="number" min="0" name="ink_rate[]" value="{{ $val->rate}}" id="ink_rate_{{$index1}}" class="input-css ink_rate">
                                                                    {!! $errors->first('ink_rate.'.$index1, '<p class="help-block">:message</p>') !!}
                                                                </div>
                                                                <div class="col-md-4 {{ $errors->has('ink_amount.'.$index1) ? 'has-error' : ''}}">
                                                                    <label>{{__('purchase/grn.amount')}}<sup>*</sup></label>
                                                                    <input type="number" step="any" min="0"  name="ink_amount[]" value="{{ $val->amount}}" id="ink_amount_{{$index1}}" class="input-css ink_amount">
                                                                    {!! $errors->first('ink_amount.'.$index1, '<p class="help-block">:message</p>') !!}
                                                                </div>
                                                        </div> <br><br>
                    
                                                </div> 
                                        </div>
                                </div>
                                @php    
                                $index1++;
                                @endphp
                                 @endforeach
                                <div class="row new_ink">
                                    
                                </div>
                                <div class="row">
                                        <input type="button" style="float:left" class="btn btn-success add_ink" value="Add More">
                                </div>
                        </div>
                        <div class="row plate" {{ $item->master_cat_id=='3'? 'style=display:block' :'style=display:none' }}>
                                
                                @php
                                $index2=0;
                            @endphp
                                @foreach($grn_item as $valu)
                                <input type="hidden" name="old_item_plate[]" value="{{$vl->id}}">
                                <div class="box-header with-border">
                                        <div class='box box-default'>  <br>
                                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.plate')}}</h2><br><br><br>
                                                <div class="container-fluid wdt">
                                                    <div class="row">
                                                        <div class="col-md-6 {{ $errors->has('plate_cat.'.$index2) ? 'has-error' : ''}}">
                                                             <label>{{__('purchase/indent.plate')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>
                                                             <select style="width:100%" name="plate_cat[]" id="plate_cat_{{$index2}}" onchange="plateitem(this)" class="select2 input-css plate_cat">
                                                                 <option value="">Select Plate Item Category</option>
                                                                 @foreach ($plate as $key)
                                                             <option value="{{$key->id}}" {{$valu->category_id==$key->id? 'selected="selected"':''}}>{{$key->name}}</option>
                                                                 @endforeach
                                                             </select>
                                                             {!! $errors->first('plate_cat.'.$index2, '<p class="help-block">:message</p>') !!}
                                                        </div>
                 
                                                        <div class="col-md-6 {{ $errors->has('plate_item.'.$index2) ? 'has-error' : ''}}">
                                                             <label>{{__('purchase/indent.plate')}}  {{__('purchase/indent.name')}}<sup>*</sup></label>
                                                             <select style="width:100%" name="plate_item[]" id="plate_item_{{$index2}}"  class="select2 input-css plate_item">
                                                                 <option value="">Select Plate Name</option>
                                                                 
                                                             </select>
                                                             {!! $errors->first('plate_item.'.$index2, '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                     </div><br><br>
                                                        <div class="row">
                                                          
                                                            <div class="col-md-6 {{ $errors->has('plate_item_qty.'.$index2) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.qty')}}<sup>*</sup></label>
                                                                <input type="number" min="0" step="none" name="plate_item_qty[]" value="{{ $valu->qty}}" id="plate_item_qty_{{$index2}}" class="input-css plate_item_qty">
                                                                {!! $errors->first('plate_item_qty.'.$index2, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                            <div class="col-md-6 {{ $errors->has('plate_item_unit.'.$index2) ? 'has-error' : ''}}">
                                                                    <label>{{__('stock/stock.plate')}}  {{__('stock/stock.unit')}}<sup>*</sup></label>
                                                                    <select style="width:100%" name="plate_item_unit[]" id="plate_item_unit_{{$index2}}" class="select2 input-css plate_item_unit">
                                                                        <option value="">Select Unit Of Quantity</option>
                                                                        @foreach ($unit_plate as $key)
                                                            <option value="{{$key->id}}" {{$valu->item_unit_id==$key->id ? 'selected=selected':''}}>{{$key->uom_name}}</option>
                                                                @endforeach
                                                                    </select>
                                                                    {!! $errors->first('plate_item_unit.'.$index2, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                        </div>
                    
                                                    <br><br>
                                                    <div class="row">
                                                            <div class="col-md-12 {{ $errors->has('plate_item_desc.'.$index2) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.plate')}}  {{__('purchase/grn.item_desc')}}<sup>*</sup></label>
                                                                <textarea name="plate_item_desc[]" id="plate_item_desc_{{$index2}}" class="plate_item_desc input-css" style="width: 100%">{{ $valu->item_desc}}</textarea>
                                                                {!! $errors->first('plate_item_desc.'.$index2, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                            
                                                        </div><br><br> 
                                                        <div class="row">
                                                                <div class="col-md-12 {{ $errors->has('plate_job.'.$index2) ? 'has-error' : ''}}">
                                                                        <label>{{__('purchase/grn.job')}}<sup>*</sup></label>
                                                                        <select type="text" name="plate_job[]" id="plate_job_{{$index2}}" class="input-css select2 plate_job" style="width: 100%">
                                                                        <option value="">Select Job Card</option>
                                                                        @foreach ($jobcard as $key)
                                                                        <option value="{{$key->id}}" {{$valu->job_card_id==$key->id? 'selected="selected"':''}}>{{$key->job_number}}</option>
                                                                        @endforeach
                                                                        </select>
                                                                        {!! $errors->first('plate_job.'.$index2, '<p class="help-block">:message</p>') !!}
                                                                    </div>
                                                                
                                                        </div><br><br>
                                                        <div class="row">
                                                                <div class="col-md-4 {{ $errors->has('plate_tax.'.$index2) ? 'has-error' : ''}}">
                                                                        <label>{{__('purchase/grn.tax')}}<sup>*</sup></label>
                                                                        <select type="text" name="plate_tax[]" id="plate_tax_{{$index2}}" class="input-css select2 plate_tax" style="width: 100%">
                                                                        <option value="">Select Tax</option>
                                                                        @foreach ($tax as $key)
                                                                        <option value="{{$key->id}}" {{$valu->tax==$key->id? 'selected="selected"':''}}>{{$key->value}}</option>
                                                                        @endforeach
                                                                        </select>
                                                                        {!! $errors->first('plate_tax.'.$index2, '<p class="help-block">:message</p>') !!}
                                                                </div>
                                                               
                                                                <div class="col-md-4 {{ $errors->has('plate_rate.'.$index2) ? 'has-error' : ''}}">
                                                                    <label>{{__('purchase/grn.rate')}}<sup>*</sup></label>
                                                                    <input type="number" min="0" name="plate_rate[]" value="{{ $valu->rate}}" id="plate_rate_{{$index2}}" class="input-css plate_rate">
                                                                    {!! $errors->first('plate_rate.'.$index2, '<p class="help-block">:message</p>') !!}
                                                                </div>
                                                                <div class="col-md-4 {{ $errors->has('plate_amount.'.$index2) ? 'has-error' : ''}}">
                                                                    <label>{{__('purchase/grn.amount')}}<sup>*</sup></label>
                                                                    <input type="number" step="any" min="0"  name="plate_amount[]" value="{{ $valu->amount}}" id="plate_amount_{{$index2}}" class="input-css plate_amount">
                                                                    {!! $errors->first('plate_amount.'.$index2, '<p class="help-block">:message</p>') !!}
                                                                </div>
                                                        </div> <br><br>
                    
                                                </div> 
                                        </div>
                                </div>
                                @php    
                                $index2++;
                                @endphp
                                @endforeach
                                <div class="row new_plate">
                                    
                                </div>
                                <div class="row">
                                        <input type="button" style="float:left" class="btn btn-success add_plate" value="Add More">
                                </div>
                        </div>
                        <div class="row misc" {{ $item->master_cat_id=='4'? 'style=display:block' :'style=display:none'}}>
                                
                                @php
                                $index3=0;
                            @endphp
                                @foreach($grn_item as $valus)
                                <input type="hidden" name="old_item_misc[]" value="{{$vl->id}}">
                                <div class="box-header with-border">
                                        <div class='box box-default'>  <br>
                                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/grn.misc')}}</h2><br><br><br>
                                                <div class="container-fluid wdt">
                                                    <div class="row">
                                                        <div class="col-md-6 {{ $errors->has('misc_cat.'.$index3) ? 'has-error' : ''}}">
                                                             <label>{{__('purchase/indent.misc')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>
                                                             <select style="width:100%" name="misc_cat[]" id="misc_cat_{{$index3}}" onchange="miscitem(this)" class="select2 input-css misc_cat">
                                                                 <option value="">Select Miscellaneous Item Category</option>
                                                                 @foreach ($misc as $key)
                                                             <option value="{{$key->id}}" {{$valus->category_id==$key->id? 'selected="selected"':''}}>{{$key->name}}</option>
                                                                 @endforeach
                                                             </select>
                                                             {!! $errors->first('misc_cat.'.$index3, '<p class="help-block">:message</p>') !!}
                                                        </div>
                 
                                                        <div class="col-md-6 {{ $errors->has('misc_item.'.$index3) ? 'has-error' : ''}}">
                                                             <label>{{__('purchase/indent.misc')}}  {{__('purchase/indent.name')}}<sup>*</sup></label>
                                                             <select style="width:100%" name="misc_item[]" id="misc_item_{{$index3}}"  class="select2 input-css misc_item">
                                                                 <option value="">Select Miscellaneous Item Name</option>
                                                                 
                                                             </select>
                                                             {!! $errors->first('misc_item.'.$index3, '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                     </div><br><br>
                                                        <div class="row">
                                                          
                                                            <div class="col-md-6 {{ $errors->has('misc_item_qty.'.$index3) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.qty')}}<sup>*</sup></label>
                                                                <input type="number" min="0" step="none" name="misc_item_qty[]" value="{{ $valus->qty}}" id="misc_item_qty_{{$index3}}" class="input-css misc_item_qty">
                                                                {!! $errors->first('misc_item_qty.'.$index3, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                            <div class="col-md-6 {{ $errors->has('misc_item_unit.'.$index3) ? 'has-error' : ''}}">
                                                                    <label>{{__('stock/stock.misc')}}  {{__('stock/stock.unit')}}<sup>*</sup></label>
                                                                    <select style="width:100%" name="misc_item_unit[]" id="misc_item_unit_{{$index3}}" class="select2 input-css misc_item_unit">
                                                                        <option value="">Select Unit Of Quantity</option>
                                                                        @foreach ($unit_misc as $key)
                                                            <option value="{{$key->id}}" {{$valus->item_unit_id==$key->id? 'selected="selected"':''}}>{{$key->uom_name}}</option>
                                                                @endforeach
                                                                    </select>
                                                                    {!! $errors->first('misc_item_unit.'.$index3, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                        </div>
                    
                                                    <br><br>
                                                    <div class="row">
                                                            <div class="col-md-12 {{ $errors->has('misc_item_desc.'.$index3) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.misc')}}  {{__('purchase/grn.item_desc')}}<sup>*</sup></label>
                                                                <textarea name="misc_item_desc[]" id="misc_item_desc_{{$index3}}" class="misc_item_desc input-css" style="width: 100%">{{ $valus->item_desc}}</textarea>
                                                                {!! $errors->first('misc_item_desc.'.$index3, '<p class="help-block">:message</p>') !!}
                                                            </div>
                                                            
                                                        </div><br><br> 
                                                        <div class="row">
                                                            <div class="col-md-4 {{ $errors->has('misc_tax.'.$index3) ? 'has-error' : ''}}">
                                                                <label>{{__('purchase/grn.tax')}}<sup>*</sup></label>
                                                                <select type="text" name="misc_tax[]" id="misc_tax_{{$index3}}" class="input-css select2 misc_tax" style="width: 100%">
                                                                <option value="">Select Tax</option>
                                                                @foreach ($tax as $key)
                                                                <option value="{{$key->id}}" {{$valus->tax==$key->id? 'selected="selected"':''}}>{{$key->value}}</option>
                                                                @endforeach
                                                                </select>
                                                                {!! $errors->first('misc_tax.'.$index3, '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                               
                                                                <div class="col-md-4 {{ $errors->has('misc_rate.'.$index3) ? 'has-error' : ''}}">
                                                                    <label>{{__('purchase/grn.rate')}}<sup>*</sup></label>
                                                                    <input type="number" min="0" name="misc_rate[]" value="{{ $valus->rate}}" id="misc_rate_{{$index3}}" class="input-css misc_rate">
                                                                    {!! $errors->first('misc_rate.'.$index3, '<p class="help-block">:message</p>') !!}
                                                                </div>
                                                                <div class="col-md-4 {{ $errors->has('misc_amount.'.$index3) ? 'has-error' : ''}}">
                                                                    <label>{{__('purchase/grn.amount')}}<sup>*</sup></label>
                                                                    <input type="number" step="any" min="0"  name="misc_amount[]" value="{{ $valus->amount}}" id="misc_amount_{{$index3}}" class="input-css misc_amount">
                                                                    {!! $errors->first('misc_amount.'.$index3, '<p class="help-block">:message</p>') !!}
                                                                </div>
                                                        </div> <br><br>
                    
                                                </div> 
                                        </div>
                                </div>
                                @php    
                                $index3++;
                                @endphp
                                @endforeach
                                
                                <div class="row new_misc">
                                    
                                </div>
                                <div class="row">
                                        <input type="button" style="float:left" class="btn btn-success add_misc" value="Add More">
                                </div>
                        </div>
                    <div class="row">
                            <div class="col-md-12">
                                <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
                            </div>
                    </div>  
               </form>
      </section>
@endsection
