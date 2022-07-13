
@extends($layout)

@section('title', __('asn.title1'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('asn.mytitle1')}}</i></a></li>
<style>
.input-css {
    border: none;
    border-bottom: 2px solid #D1C4E9;
    width: 100%;
}

.input-css:focus {
    outline: none;
    border: none;
    border-bottom: 2px solid #673AB7;
}
</style>
 
@endsection
@section('css')
<style>
p{
    font-size:21px;
}
</style>
@endsection

@section('js')
<script>
  $('.datepicker1').datepicker({
                            autoclose: true,
                        }).datepicker("setDate", new Date());
</script>
<script>

$('#party').change(function(e) {
          var party = $(e.target).val();
          tax(party);
        });    
        @if($type=='')
            tax({{old('party')}});
        @endif
        function tax(party){
            if(party!='')
            {
                $('#ajax_loader_div').css('display','block');

                $.ajax({
              url: "/party/grn/" + party,
              type: "GET",
              success: function(result) {
                  
                @if($type=='')
                  $('.tax_asn').empty();
                @endif
                 if(result!=''){
                    $(".tax_asn").empty();
                    var len=result.length-1;
                    
                    for (var i = 0; i < result.length; i++) {
                        $('.datepicker1').datepicker({
                            autoclose: true,
                        }).datepicker("setDate", new Date());
                    $('.tax_asn').append(
                        '<div class="row">'+
                        '<div class="col-md-12">'+
                            '<div class="col-md-3">'+
                                    '<p>'+result[i].invoice_number+'</p>'+
                                '</div>'+
                                '<div class="col-md-5">'+
                                        '<input type="hidden" value="'+result[i].id+'"  name="tax[]">'+
                                        '<input type="text" class="asn input-css" placeholder="Enter GRN Number" name="grn_num['+result[i].id+']">'+
                                       
                                '</div>'+
                                '<div class="col-md-4">'+
                                    '<input type="text" class="input-css datepicker1" id="'+i+'" placeholder="Enter GRN Date" name="grn_date['+result[i].id+']">'+
                                '</div>'+
                            '</div>'+
                            '</div>'+
                        '</div><br>'    
                    );
                    $("#" + len).datepicker({
                            autoclose: true,
                        }).datepicker("setDate", new Date());
                  }
                 }
                 else{
                    @if($type=='')
                     $(".tax_asn").append('<option value="">No Tax Invoice</option>')
                    @endif
                }
                $('#ajax_loader_div').css('display','none');

            }
                  
        });
        }}
$(".select2").on("select2:close", function (e) {
    $(this).valid();
});
$('#asn_form').on('submit', function(event) {
    $('.party').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Client is required"}
        });    
    });
});
$("#asn_form").validate(
    {
        errorPlacement: function(error, element) {
            if(element.attr("name")=='item')
            {
                var v = $("#jqueryerror");
                error.insertAfter($(v));
            }
            else
            error.insertAfter(element);
        },
     
    }
);

</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                   
            </div>
            @if($errors->any())
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
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('asn.mytitle1')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                         @if($type!='' && $invoice!= NULL)
                            <form method="POST" action="/grn/insert/{{$type}}/{{$invoice_id}}" id="asn_form">
                        @else
                            <form method="POST" action="/grn/insert" id="asn_form">
                        @endif
                                @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('asn.client')}}<sup>*</sup></label>
                                        <select value="{{ old('party') }}" name="party"  class="select2 input-css party" id="party">
                                                    <option value="">Select Client</option>
                                                    @foreach($party as $key)
                                                    <option value="{{$key->id}}" {{old('party')==$key->id  || $type==$key->id ? 'selected="selected"' : ''}}>{{$key->partyname}}</option>
                                                    @endforeach
                                            </select>
                                           
                                            <label id="party-error" class="error" for="party"></label>
                                            {!! $errors->first('party', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    {!! $errors->first('grn_num', '<p class="help-block">:message</p>') !!}
                                    <br><br>
                                    <div class="row tax_asn">
                                        @if($type!='' && $invoice!= NULL)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                        <p>{{$invoice}}</p>
                                                    </div>
                                                    <div class="col-md-5">
                                                            <input type="hidden" value="{{$invoice_id}}"  name="tax[]">
                                                            <input type="text" class="asn input-css" placeholder="Enter GRN Number" name="grn_num[{{$invoice_id}}]">
                                                     
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" style="" class="form-control input-css datepicker" placeholder="Enter GRN Date" name="grn_date[{{$invoice_id}}]">
                                                    </div>
                                                </div>
                                                </div>
                                            </div><br>  
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="box-footer">
                                            <button type="submit" class="btn btn-primary" style="float:right">Submit</button>
                                        </div>
                                    </div><!--submit button row-->
                        </form>
                    </div><!--end of container-fluid-->
                </div><!------end of box box-default---->
            </div><!--end of box-header with-border-->
    </section><!--end of section-->
@endsection


