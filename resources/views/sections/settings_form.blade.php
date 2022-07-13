@extends($layout)

@section('title', __('settings_form.title'))
{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class=""></i> Settings</a></li>
@endsection
@section('css')
<link rel="stylesheet" href="/css/consignee.css">
@endsection

@section('js')
<script>
  $('.datepickers').datepicker({
      autoclose: true,
      format: 'yyyy-mm'
  });
 
 
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
    <form id="form" action='/setting/addform' method='post'>
        @csrf
      
    <div class="box-header with-border">
            <div class='box box-default'>  <br>
               
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">Order To Collection</h2><br><br><br>
                    <div class="row">
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('settings_form.ION')}} <sup>*</sup></label>
                                        <input type="text" value="{{$settings['internal_order_prefix']}}"
                                            class="form-control input-css" name="internal_order_prefix"
                                            id="internal_order_prefix" placeholder="Internal Order No">
                                    </div>
                                </div>
                                <!--end of col-md-6-->
        
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('settings_form.TIN')}}  <sup>*</sup></label>
                                        <input type="text" class="form-control input-css"
                                            value="{{$settings['Tax_Invoice_Prefix']}}" name="Tax_Invoice_Prefix"
                                            id="Tax_Invoice_Prefix" placeholder="Tax Invoice No">
                                    </div>
                                </div>
                                {{-- col-md-4 end --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('settings_form.DCN')}}  <sup>*</sup></label>
                                        <input type="text" class="form-control input-css"
                                            value="{{$settings['Delivery_Challan_Prefix']}}" name="Delivery_Challan_Prefix"
                                            id="Delivery_Challan_Prefix" placeholder="Delivery Challan No">
        
                                    </div>
                                </div>
                    </div>
                    <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{__('settings_form.JCN')}} <sup>*</sup></label>
                                    <input type="text" value="{{$settings['Job_Card_Prefix']}}"
                                        class="form-control input-css" name="Job_Card_Prefix"
                                        id="po_number_prefix" placeholder="Job Card No">
                                </div>            
                            </div>
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{__('settings_form.ip')}} <sup>*</sup></label>
                                    <input type="text" value="{{$settings['login_allowed_ip']}}"
                                        class="form-control input-css" name="login_allowed_ip"
                                        id="po_number_prefix" placeholder="Login allowed ip">
                                    <sup class="text-muted">Add ip address seperated by comma.</sup>
                                </div>            
                            </div>
                           
                        </div><br><br>
                   
            </div>
    </div>
    <div class="box-header with-border">
            <div class='box box-default'>  <br>
               
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">Purchase Module</h2><br><br><br>
                    <div class="row">
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('settings_form.pr')}} <sup>*</sup></label>
                                        <input type="text" value="{{$settings['purchase_requisition_prefix']}}"
                                            class="form-control input-css" name="purchase_requisition_prefix"
                                            id="purchase_requisition_prefix" placeholder="Purchase Requisition">
                                    </div>
                                </div>
                                <!--end of col-md-6-->
        
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('settings_form.pi')}}  <sup>*</sup></label>
                                        <input type="text" class="form-control input-css"
                                            value="{{$settings['purchase_indent_Prefix']}}" name="purchase_indent_Prefix"
                                            id="purchase_indent_Prefix" placeholder="Purchase Indent ">
                                    </div>
                                </div>
                                {{-- col-md-4 end --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('settings_form.po')}}  <sup>*</sup></label>
                                        <input type="text" class="form-control input-css"
                                            value="{{$settings['purchase_order_Prefix']}}" name="purchase_order_Prefix"
                                            id="purchase_order_Prefix" placeholder="Purchase Order ">
        
                                    </div>
                                </div>
                    </div>
                    <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{__('settings_form.prr')}} <sup>*</sup></label>
                                    <input type="text" value="{{$settings['purchase_return_request_Prefix']}}"
                                        class="form-control input-css" name="purchase_return_request_Prefix"
                                        id="purchase_return_request_Prefix" placeholder="Purchase Return Request">
                                </div>            
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{__('settings_form.pgrn')}} <sup>*</sup></label>
                                    <input type="text" value="{{$settings['purchase_grn_Prefix']}}"
                                        class="form-control input-css" name="po_number_prefix"
                                        id="po_number_prefix" placeholder="Purchase GRN">
                                </div>            
                            </div>
                           
                        </div>
                   
            </div>
    </div>
    <div class="form-group"">
            <input type="submit" name="sub" class="btn btn-primary " value="Submit">
        </div>
</form>
   
</section>
@endsection