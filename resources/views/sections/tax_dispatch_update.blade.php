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
        var currentDate="{{$tax['created_date']}}";
         $('.datepickers').datepicker('destroy');
                $('.datepickers').datepicker({
                    format: 'dd-mm-yyyy',
                      autoclose: true,
                      startDate:currentDate,
                });
     $('input[type=radio][name=list_available]').change(function() {
        if (this.value == "Hand"){
            $('.hand').show();
            $('.docket_num').val("");
            $('.company').val("");
            $('#docket').hide();
         $('.pickbyparty').hide();
       
        }
      
       
        if (this.value == "Courier")
       {
        $('#docket').show();
        $('.person').val("");
            $('.hand').hide();
         $('.pickbyparty').hide();
       }

       if (this.value == "Pick By Party")
       {
        $('.pickbyparty').show();
        $('#docket').hide();
        $('.person').val("");
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
            
          @if(in_array(1, Request::get('userAlloweds')['section']))
      {{-- <p>Hello</p> --}}
      @endif
      
        <form method="post" action="/taxdispatch/update/{{$tax['id']}}" id="form" enctype="multipart/form-data" files="true">
       @csrf 
       <div class="box box-default">
            <div class="box-header with-border">
            
               
                <div class="box-body">
                        <div class="row">

                                <div class="col-md-12 ">
                                    <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                    <input type="text" name="update_reason" required="" class="form-control input-css " id="update_reason">
                                    {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->
                            </div>
                    <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">{{__('tax_invoice.enrtytitle')}} <sup>*</sup></label>
                                        <select class="tax form-control select2" style="width: 100%;" name="num" disabled>
                                        <option value="">{{$tax['invoice_number']}}</option>    
                                        </select>
                                            {!! $errors->first('num', '<p class="help-block">:message</p>') !!}
                                    </div>
                                  
                            </div>
                            <div class="col-md-6">
                                    <div class="form-group"> 
                                            <label for="">{{__('tax_invoice.tax_invoice_dispatch_date')}} <sup>*</sup></label>
                                    <input type="text" autocomplete="off" class="form-control datepickers input-css" value="{{CustomHelpers::showDate($tax['dispatch_date'],'d-m-Y')}}" id="datepicker" name="date">
                                    {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                    </div>
                            </div>
                            <div class="form-group">      
                                    <label>{{__('tax_invoice.dispatch_mode')}}<sup>*</sup></label>
                                <div class="col-md-12 list_available_label_er">
                                    <div class="col-sm-4">		
                                        <div class="radio">
                                            <label style="font-style:bold">
                                                <input name="list_available" {{ $tax['dispatch_mode']=='Hand' ? 'checked="checked"' : ''}} autocomplete="off" type="radio" value="Hand"  id="cons" > By Hand </label>
                                        
                                                {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}    </div>
                                    </div>
                                    <div class="col-sm-4">	
                                            <div class="radio">
                                                <label style="font-style:bold">
                                                    <input name="list_available" {{ $tax['dispatch_mode']=='Courier' ? 'checked="checked"' : ''}} autocomplete="off" value="Courier" type="radio"> By Courier </label>
                                                     </div>
                                    </div>

                                    <div class="col-sm-4">	
                                        <div class="radio">
                                            <label style="font-style:bold">
                                                <input name="list_available" {{ $tax['dispatch_mode']=='Pick By Party' ? 'checked="checked"' : ''}} autocomplete="off" value="Pick By Party" type="radio"> Pick By Party </label>
                                                 </div>
                                </div>

                                    {!! $errors->first('list_available', '<p class="help-block">:message</p>') !!}
                                </div>
                                
                        </div>
                    </div> 
                </div>                               
            </div>
    </div>
      
      <div id="docket" {{ ($tax['dispatch_mode']=='Courier') ? 'style=display:block' : 'style=display:none'}}>
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
                                        <option value="{{$item->id}}" {{$tax['courier_company']==$item->id ? "selected=selected" : ''}}>{{$item->courier_name}}</option>
                                        @endforeach
                                    </select>
                          </div><!--end of col-md-4-->
                                              
                          <div class="col-md-3">
                              <label>{{__('tax_invoice.docket_number')}}<sup>*</sup></label>
                              <input type="text" class="form-control input-css docket_num" value="{{$tax['docket_number']}}" name="comp">
                          </div><!--end of col-md-4-->
                          <div class="col-md-3">
                              <label>Docket Date<sup>*</sup></label>
                              <input type="text" class="form-control input-css datepickers" name="doc_date"  value="{{$tax['docket_date']}}"required>
                              {!! $errors->first('comp', '<p class="help-block">:message</p>') !!}
                          </div><!--end of col-md-4-->
                          <div class="col-md-3">
                              <label>Docket Upload<sup>*</sup></label>
                              <input type="file" class="form-control input-css" name="file" value="{{$tax['docket_file']}}" >
                              <input type="hidden" class="form-control input-css" name="files" value="{{$tax['docket_file']}}" >
                              <a onclick="$('#usr_img_modal').modal('show')">See File</a>
                              {!! $errors->first('comp', '<p class="help-block">:message</p>') !!}
                          </div><!--end of col-md-4-->
                          </div><!--end of div row-->
                          <div id="usr_upload_img">
                                <div class="container">
                                    <div class="modal fade usr_img_modal" id="usr_img_modal" role="dialog">
                                    <div class="modal-dialog modal-lg">
                                     <!-- Modal content-->
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Docket Upload Details</h4>
                                            <hr>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                                @if($file_type=="")
                                                    <img height="480" width="720" alt="No File Uploaded">  
                                                @elseif ($file_type=="pdf")
                                                    <embed src="/upload/taxdispatch/{{$tax->docket_file}}" height="480" width="720" type="application/pdf">
                                                @else
                                                    <img src="/upload/taxdispatch/{{$tax->docket_file}}" height="480" width="720" alt="No File Uploaded">
                                                @endif
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>               
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
            <div  class="box box-default hand" {{ ($tax['dispatch_mode']=='Hand') ? 'style=display:block' : 'style=display:none'}}>
            <div class="box-header with-border">
            <div class="box-body">
                 <label><h3>{{__('tax_invoice.by_hand_details')}}</h3></label>
                <div class="row">
                    <div class="col-md-6">
                            <label>{{__('tax_invoice.person_name')}}<sup>*</sup></label>
                            <select  class="form-control select2 input-css" name="persname" style="width:100%">
                                    <option value="">Select Employee</option>
                                    @foreach ($emp as $item)
                                <option value="{{$item->id}}" {{$tax['person']==$item->id ? "selected=selected" : ''}}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                    </div>

                    <div class="col-md-6">
                      <label>Receiving Invoice Upload <sup>*</sup></label>
                      @if($tax['byhand_invoice'] != "" || $tax['byhand_invoice'] != null)
                          @if (file_exists(public_path().'/upload/taxdispatch/'.$tax['byhand_invoice'] ))
                              <?php $ext = pathinfo(storage_path().'/upload/taxdispatch/'.$tax['byhand_invoice'], PATHINFO_EXTENSION); ?>
                              @if ($ext == 'pdf')
                                  <a href="{{ asset('/upload/taxdispatch')}}/{{$tax['byhand_invoice']}}" target="_blank">See Invoice</a>
                              @else
                                  <img src="{{ asset('u/upload/taxdispatch')}}/{{$tax['byhand_invoice']}}" height="50" width="100">
                              @endif
                          @endif
                      @endif
                      <input type="file" name="hand_invoice" required="" >
                      <input type="text" name="old_hand_invoice" value="{{$tax['byhand_invoice']}}" hidden>
                      {!! $errors->first('hand_invoice', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                                        
            </div>
</div><!--end of box box-body-->
</div><!--end of box-header with-border-->
<div  class="box box-default pickbyparty" {{($tax['dispatch_mode']=='Pick By Party') ? 'style=display:block' : 'style=display:none'}} >
            <div class="box-header with-border">
            <div class="box-body">
                 <label><h3>Pick By Party</h3></label>
                <div class="row">
                    <div class="col-md-6">
                      <label>Receiving Invoice Upload <sup>*</sup></label>
                      @if($tax['party_invoice'] != "" || $tax['party_invoice'] != null)
                          @if (file_exists(public_path().'/upload/taxdispatch/'.$tax['party_invoice'] ))
                              <?php $ext = pathinfo(storage_path().'/upload/taxdispatch/'.$tax['party_invoice'], PATHINFO_EXTENSION); ?>
                              @if ($ext == 'pdf')
                                  <a href="{{ asset('/upload/taxdispatch')}}/{{$tax['party_invoice']}}" target="_blank">See Invoice</a>
                              @else
                                  <img src="{{ asset('u/upload/taxdispatch')}}/{{$tax['party_invoice']}}" height="50" width="100">
                              @endif
                          @endif
                      @endif
                      <input type="file" name="pickparty_invoice" required="">
                      <input type="text" name="old_party_invoice" value="{{$tax['party_invoice']}}" hidden>
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
