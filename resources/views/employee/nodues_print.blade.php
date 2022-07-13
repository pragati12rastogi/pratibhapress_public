@extends($layout)

@section('title', 'No Dues Print')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> No Dues Print</a></li>
   
@endsection
@section('js')

<script>
    $(".fromdate").datepicker({
        endDate:'today',
        format: 'd-m-yyyy'
    });

// $("#downloadbtn").click(function(){
//     var date = $("#fromdate").val();
//     if(date == ""){
//         return false;
//         $("#fromdate").focus();
//     }
//      $.ajax({
//         type:"GET",
//         url:"/emp/download/template/nodues",
//         data:{'assets':assets},
//         success: function(result){
//             if (result) {
//                         $("#code").empty();
//                         $("#code").append(' <option value="">--Select Code--</option>');
//                         $.each(result, function(key, value) {
//                             $("#code").append('<option value="' + key + '">' + value + '</option>');
//                         });
//                         $('#ajax_loader_div').css('display','none');
//                     }
//         }
//     })
// })


</script>
@endsection
@section('css')
<style>
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
</style>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
       <form action="/emp/download/template/nodues" method="GEt" id="form" enctype="multipart/form-data">
        @csrf

       <div class="box box-header">
           <br>

            <div class="row" >
                <div class="col-md-6 {{ $errors->has('fromdate') ? ' has-error' : ''}}">
                    <label for="">Start from date <sup>*</sup></label>
                    <input autocomplete="off" type="text" class="fromdate input-css" id="fromdate" name="fromdate" required> 
                    {!! $errors->first('fromdate', '<p class="help-block">:message</p>') !!} 
                </div>
            </div><br><br>
       
 
        <div class="row">
                <div class="col-md-12">
                    
                      <input type="submit" id="downloadbtn" class="btn btn-primary downloadbtn" value="Print">
                    
                </div>
            </div>
        </form>
      
      </section>
@endsection
