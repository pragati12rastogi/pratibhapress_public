@extends($layout)

@section('title', 'Add City')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Add City</a></li>
@endsection
<style>
  .list-group-item{
    background: aliceblue;
    padding: 5px!important;
    list-style: none;
    border: none!important;
  }
</style>
@section('js')
<script>
   
  $(document).ready(function(){

    $('#city').on('keypress',function() {
        var text = $('#city').val();
        $("#result").html('');
        var state_id = $("#state").val();
        $("#btnSubmit").attr("disabled", true);
         $("#result").html('');
            $.ajax({
                type: "GET",
                url: '/master/city/search',
                data: { text: text, state_id:state_id },
                success: function(response) {
                    $("#result").html('');
                     // console.log(response);
                      $("#btnSubmit").attr("disabled", false);
                     if (response != '') {
                         $("#result").append('<li class="list-group-item" style="color:grey;"> Added similar city :</li>');
                      }
                      $.each(response,function(key, value){
                         $("#result").append('<li class="list-group-item">'+value.city+'</li>');
                         //var cities = value.city.toLowerCase();
                           //if (cities = text.toLowerCase()) {
                            // $("#msg").append('This city is already added !');
                            //}
                      });  
                },
                error: function() {
                    $("#msg").html('');
                    $("#msg").append('Internal Issue Try Again');
                }
            });
      
    });
    });
</script>
@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
       <form action="/master/city" method="POST">
        @csrf

       <div class="box box-header">
           <br>
            <div class="row" >
                <div class="col-md-6 {{ $errors->has('country') ? 'has-error' : ''}}">
                    <label>Country<sup>*</sup></label>
                    <select name="country" id="country" class="select2 design input-css">
                        <option value="{{$country->id}}" selected="">{{$country->name}}</option>
                    </select>
                    {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-6 {{ $errors->has('state') ? 'has-error' : ''}}">
                    <label>State<sup>*</sup></label>
                    <select name="state" id="state" class="select2 design input-css">
                        <option value="">Select State</option>
                        @foreach ($state as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                    {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
                </div>
                
         </div><br><br>
         <div class="row">
             <div class="col-md-6 {{ $errors->has('city') ? ' has-error' : ''}}">
                <label for="">City <sup>*</sup></label>
                <span id="msg" style="color:red"></span>
                <input type="text" name="city" id="city" class="dept input-css">
                <ul class="list-group" id="result"></ul>
                {!! $errors->first('city', '<p class="help-block">:message</p>') !!} 
            </div>
     </div>
       </div>
       
 
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" id="btnSubmit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
