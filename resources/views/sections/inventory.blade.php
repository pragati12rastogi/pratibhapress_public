@extends($layout)

@section('title', __('inventory.title'))

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Inventory</a></li>
@endsection

@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="css/inventory.css"> 
@endsection

@section('js')
  {{-- <script src="js/adminlte.min.js"></script> --}}
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
       <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
  <script>
      $(document).ready(function () {
      $('#form').validate({ 
        
          rules: {
              name: {
                  required: true
              },
              master: {
                  required: true
              },
              size1: {
                  required: true,
                  
              },
              size2: {
                  required: true,
                  
              },
              sp: {
                  required: true
              },
              isp: {
                  required: true
              },
              qty1: {
                  required: true
              },
              qty: {
                  required: true
              },
              sku: {
                  required: true
              },
              
             
          }
      });
  });
  </script>
  
     
@endsection

@section('main_section')
<section class="content">
  
    <!-- SELECT2 EXAMPLE -->

    <div class="box box-default">
      <div class="box-header with-border">
      <h3 class="box-title">{{__('inventory.mytitle')}}</h3>

        <div class='container-fluid'>
<form method="post" action="{{url('forms')}}" id="form">
    @csrf
    <div class='row row'>
            
            
        <div class='col-md-6'>
            <label>{{__('inventory.Master')}}<sup>*</sup></label>
            <input type="text" class="form-control" name="master">
          </div><!--end of col-md-6-->  
         
          <div class='col-md-6'>
               <label>{{__('inventory.Item Cat')}} <sup>*</sup></label>
                  <select class="form-control select2">
                    <option selected="selected"></option>
                    <option>nan</option>
                    <option>tan</option>
                    <option>Bro</option>
                    </select>
                  </div><!--end of col-md-6-->
        </div><!--end of row-->
      
        <div class='row row'>
            <div class='col-md-4'>
                <label>{{__('inventory.name')}}<sup>*</sup></label>
                <input type="text" class="form-control" name="name">
              </div><!--end of col-md-4-->  
             
              <div class='col-md-4'>
                  <label>{{__('inventory.size')}}<sup>*</sup></label>
                   <div class="row">
                  <div class="col-md-3 abc">
                      <input type="number" class="form-control" placeholder="eg. 20*30" name="size1">
                    </div> 
                    <div class="col-xs-12 col-md-2 align-center">
                        To
                      </div>
                    <div class="col-md-3 abc">
                        <input type="number" class="form-control" placeholder="eg. 20*30" name="size2">
                      </div> 
                  </div>   
                  
                  
                    </div><!--end of col-md-4-->
                
                  
                 <div class='col-md-4'>
                     <label>{{__('inventory.specific paper')}}<sup>*</sup></label>
                        <input type="text" class="form-control" name="sp"> 
                      </div><!--end of col-md-4--> 
                 </div><!--end of row-->
      
               <div class='row row'>
                  <div class='col-md-4'>
                    <label>{{__('inventory.ISP')}}<sup>*</sup></label>
                    <input type="text" class="form-control" name="isp">
                   </div><!--end of col-md-4-->

                <div class='col-md-4'>
                    <label>{{__('inventory.QTY1')}}<sup>*</sup></label>
                    <input type="text" class="form-control" name="qty1">
                  </div><!--end of col-md-4-->  
                 
                  <div class='col-md-4'>
                      <label>{{__('inventory.unit qty')}}<sup>*</sup></label>
                      <input type="text" class="form-control" name="qty">
                    </div><!--end of col-md-4-->
                </div><!--end of row-->
                  
                <div class='row row'>
                    <div class='col-md-6'>
                        <label>{{__('inventory.SKU')}}<sup>*</sup></label>
                        <input type="text" class="form-control" name="sku">
                      </div><!--end of col-md-4-->  
                      
                      <div class='col-md-6'>
                          <label>{{__('inventory.item location')}} <sup>*</sup></label>
                  <select class="form-control select2">
                    <option selected="selected"></option>
                    <option>nan</option>
                    <option>tan</option>
                    <option>Bro</option>
                    </select>
                        </div><!--end of col-md-4-->
                        
                   
                  

                    <div class='row row'>
                        </div><!--end of row-->
                          <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                          </div>
    
    
   
    
  </form>
</div>
      </div>
    </div>
  </section>
    
 
@endsection