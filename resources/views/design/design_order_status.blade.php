@extends($layout)

@section('title', 'Design Order Status')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Design Order Status</a></li>
   
@endsection
@section('js')
<script src="/js/Design/design_order_status.js"></script>
<script>
    $('.status').change(function(e){
        $('.sup').show();  
        var value=$(e.target).val();
        var text=$("select[name=status] option:selected").html();
        console.log(text);
        
        if((value=="Cancelled") || (text=="Design Order Closed For Miscellaneuos work")){
            $('#io').rules('remove');
            $('.sup').hide();  
        }
        else{
            $('#io').rules('add',{required: true});
            $('.sup').show();  
        }
    });

    var item_name = {{$id}};
        $('#ajax_loader_div').css('display','block');
            $.ajax({
                url: "/design/details/" + item_name,
                type: "GET",
                success:function(result) {
                   console.log(result);
                   var x=result.io;
                   var io_id=result.design.io;
                   $("#io").empty();
                   $("#io").append('<option disabled selected>Select IO</option>');
                   for(var i=0;i<x.length;i++){
                       $("#io").append('<option value="'+x[i].id+'">'+x[i].io_number+'</option>');
                   }
                   $('#io').val(io_id).select().trigger('change');
                    $('#ajax_loader_div').css('display','none');
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
        <!-- Default box -->
        <form action="/design/order/status/{{$id}}" method="POST" id="form" autocomplete="off">
        @csrf
        <div class="box-header with-border returnable">
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">Design Order Status</h2><br><br><br>
                    <div class="container-fluid wdt">
                     
                                <div class="row">
                                        <div class="col-md-6 {{ $errors->has('design_number') ? 'has-error' : ''}}">
                                            <label>Design Order Number<sup>*</sup></label>
                                            <select name="design_number" id="design_number" disabled class="select2 design_number input-css" style="width:100%">
                                                <option value="">Select Design Number</option>
                                                @foreach ($design as $item)
                                                <option value="{{$item->id}}" {{$id==$item->id ? 'selected=selected':''}}>{{$item->do_number}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('design_number', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('io') ? 'has-error' : ''}}">
                                                <label>Internal Order <sup class="sup">*</sup></label>
                                                <select name="io" id="io" class="select2 io input-css" >
                                                    <option value="" ></option>
                                                </select>
                                                {!! $errors->first('io', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        
                                </div><br><br>
                                <div class="row">
                                        <div class="col-md-6 {{ $errors->has('status') ? 'has-error' : ''}}">
                                                <label>Design Order Status <sup>*</sup></label>
                                                <select name="status" id="status" class="select2 status input-css"style="width:100%">
                                                        <option value="">Select Status</option>
                                                        <option value="Cancelled">Design Order Cancelled</option>
                                                        <option value="Closed">Design Order Closed</option>
                                                        <option value="Closed">Design Order Closed For Miscellaneuos work</option>
                                                </select>
                                                {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div><br><br>
                    </div>
                </div>
        </div>

       
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
