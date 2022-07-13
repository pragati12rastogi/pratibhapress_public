@extends($layout)

@section('title', __('tax_invoice.title'))

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> {{__('tax_invoice.title')}}</a></li>
@endsection

@section('css')
<style>
    .row{
    margin-top:20px;
  }
  .error{
   color:red;
   font-style: oblique;
  }
</style>
@endsection

@section('js')
    <script src="/js/views/tax_dispatch.js"></script>
    <script>
     $('input[type=radio][name=list_available]').change(function() {
        if (this.value == "Hand"){
            $('.hand').show();
            $('#docket').hide();
         $(".pickbyparty").hide();
       
        }
      
       
        if (this.value == "Courier")
       {
        $('#docket').show();
            $('.hand').hide();
         $(".pickbyparty").hide();
       }
       if(this.value == "Pick By Party"){
        $(".pickbyparty").show();
        $('#docket').hide();
        $('.hand').hide();
       }    
      
    });
    </script>
@endsection

@section('main_section')
<section class="content">
        <!-- Main content -->
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
          @if(in_array(1, Request::get('userAlloweds')['section']))
      {{-- <p>Hello</p> --}}
      @endif
      
      <form method="post" action="/taxdispatch/create" id="form" enctype="multipart/form-data" files="true">
       @csrf 
       <div class="box box-default">
            <div class="box-header with-border">
            
               
                <div class="box-body">
                    <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">{{__('tax_invoice.enrtytitle')}} <sup>*</sup></label>
                                        <select class="form-control tax select2"  data-placeholder="tax" style="width: 100%;" name="num">
                                                <option value="default">Select Tax Invoice Number</option>
                                                @foreach ($tax as $item)
                                        <option value="{{$item['id']}}">{{$item['invoice_number']}}</option>
                                        {!! $errors->first('num', '<p class="help-block">:message</p>') !!}
                                                @endforeach
                                            </select>
                                    </div>
                                  
                            </div>
                            <div class="col-md-6">
                                    <div class="form-group"> 
                                            <label for="">{{__('tax_invoice.tax_invoice_dispatch_date')}} <sup>*</sup></label>
                                            <input autocomplete="off" type="text" class="form-control datepickers input-css" id="datepicker" name="date">
                                            <p style="font-size:12px;color:green">Date Calender Will Auto Generated on selecting Tax Invoice.</p>
                                            {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                                    </div>
                            </div>
                            <div class="form-group">      
                                    <label>{{__('tax_invoice.dispatch_mode')}}<sup>*</sup></label>
                                <div class="col-md-12 list_available_label_er">
                                    <div class="col-sm-4">		
                                        <div class="radio">
                                            <label style="font-style:bold">
                                                <input name="list_available"  autocomplete="off" type="radio" value="Hand"  id="cons" > By Hand </label>
                                               </div>
                                    </div>
                                    <div class="col-sm-4">	
                                            <div class="radio">
                                                <label style="font-style:bold">
                                                    <input name="list_available" autocomplete="off" value="Courier" type="radio"> By Courier </label>
                                                   </div>
                                    </div>
                                    <div class="col-sm-4">	
                                        <div class="radio">
                                            <label style="font-style:bold">
                                                <input name="list_available" autocomplete="off" value="Pick By Party" type="radio"> Pick By Party </label>
                                               </div>
                                </div>
                                    {!! $errors->first('list_available', '<p class="help-block">:message</p>') !!}
                                  
                                </div>
                                
                        </div>
                    </div> 
                </div>                               
            </div>
    </div>
      
      <div id="docket" style="display:none">
            <div class="box box-default">
                    <div class="box-header with-border">
                        <h3>{{__('tax_invoice.docket_detail')}}</h3>
                    <div class="box-body"> 
                    <div class="container-fluid">
                          <div class="row">                     
                          <div class="col-md-3">
                                <label>{{__('tax_invoice.courier_company')}}<sup>*</sup></label>
                                <select  class="form-control select2 input-css" name="com" style="width:100%">
                                    <option value="">Select Company</option>
                                    @foreach ($goods as $item)
                                    <option value="{{$item->id}}">{{$item->courier_name}}</option>
                                    @endforeach
                                </select>

                                {!! $errors->first('com', '<p class="help-block">:message</p>') !!}
                          </div><!--end of col-md-4-->
                                              
                          <div class="col-md-3">
                              <label>{{__('tax_invoice.docket_number')}}<sup>*</sup></label>
                              <input type="text" class="form-control input-css" name="comp">
                              {!! $errors->first('comp', '<p class="help-block">:message</p>') !!}
                          </div><!--end of col-md-4-->
                          <div class="col-md-3">
                              <label>Docket Date<sup>*</sup></label>
                              <input type="text" class="form-control input-css datepicker" name="doc_date" required>
                              {!! $errors->first('comp', '<p class="help-block">:message</p>') !!}
                          </div><!--end of col-md-4-->
                          <div class="col-md-3">
                              <label>Docket Upload<sup>*</sup></label>
                              <input type="file" class="form-control input-css" name="file" required>
                              {!! $errors->first('comp', '<p class="help-block">:message</p>') !!}
                          </div><!--end of col-md-4-->
                          </div><!--end of div row-->
                                           
                          <div class="row">             
                          <!--end of div row--> 
                          </div><!--end of div row--> 
                          </div><!--end of container-fluid-->
                          </div><!--end of box box-body-->
                          </div><!--end of box-header with-border-->
                          </div><!--end of box box-default--> 
                       <div class="form-group">  
                       </div>  
      </div>
          <div  class="box box-default hand" style="display:none">
            <div class="box-header with-border">
            <div class="box-body">
                 <label><h3>{{__('tax_invoice.by_hand_details')}}</h3></label>
                <div class="row">
                    <div class="col-md-6">
                            <label>{{__('tax_invoice.person_name')}}<sup>*</sup></label>
                            <select  class="form-control select2 input-css" name="persname" style="width:100%">
                                <option value="">Select Employee</option>
                                @foreach ($emp as $item)
                            <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('persname', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="col-md-6">
                      <label>Receiving Invoice Upload <sup>*</sup></label>
                      <input type="file" name="hand_invoice" required="">
                      {!! $errors->first('hand_invoice', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                                        
            </div>
            </div><!--end of box box-body-->
          </div><!--end of box-header with-border-->

          <div  class="box box-default pickbyparty" style="display:none">
            <div class="box-header with-border">
            <div class="box-body">
                 <label><h3>Pick By Party</h3></label>
                <div class="row">
                    <div class="col-md-6">
                      <label>Receiving Invoice Upload <sup>*</sup></label>
                      <input type="file" name="pickparty_invoice" required="">
                      {!! $errors->first('pickparty_invoice', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                                        
            </div>
            </div><!--end of box box-body-->
          </div>
<div class="form-group">
       
         <button type="submit" class="btn btn-primary">Submit</button>  
            
    </div>
 
</div>
 
</div><!--end of box box-default-->

</form>
</section>
@endsection
