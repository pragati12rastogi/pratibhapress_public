
@extends($layout)

@section('title', __('asn.asn grn create'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('asn.asn grn create')}}</i></a></li>
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
$(document).ready(function(){
    $("#asn_party").val([{{$asn_client}}]).trigger('change');
    $("#grn_party").val([{{$grn_client}}]).trigger('change');

});           
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
            <div >Clients added here will use in ASN and GRN form.</div>
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <form method="POST" action="/asn/setting/insert" id="asn_form">
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('asn.mytitle')}}</h2><br><br><br>
                        <div class="container-fluid wdt">
                            @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="">{{__('asn.client')}}<sup>*</sup></label>
                                    <select multiple name="asn_party[]"  class="select2 input-css asn_party" id="asn_party">
                                                <option value="">Select ASN Client</option>
                                                @foreach($party as $key)
                                                <option value="{{$key->id}}">{{$key->partyname}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                    </div>
                    <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('asn.grn')}}</h2><br><br><br>
                        <div class="container-fluid wdt">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="">{{__('asn.client')}}<sup>*</sup></label>
                                        <select multiple name="grn_party[]" class="select2 input-css grn_party" id="grn_party">
                                            <option value="">Select GRN Client</option>
                                            @foreach($party as $key)
                                                <option value="{{$key->id}}">{{$key->partyname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
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


