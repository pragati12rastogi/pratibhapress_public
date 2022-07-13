@extends($layout)

@section('title', 'Binding Bills')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Binding Bills</a></li>
   
@endsection
@section('js')
<script>
$(document).ready(function () {
    $('#form').validate({ // initialize the plugin
        rules: {
            io: {
                required: true
            },
            binder:{
                required: true  
            },
            qty:{
                required: true  
            },
            binder_bill:{
                required: true  
            },
            ready_qty:{
                required: true  
            },
            amount:{
                required: true  
            },
            file:{
                required: true  
            },
            process:{
                required: true  
            },
            bill_date:{
                required: true  
            }
            
        }
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
    <form action="/binding/bills/create/db" method="POST" id="form" files="true" enctype="multipart/form-data">
        @csrf
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                <h2 class="box-title" style="font-size: 28px;margin-left:20px">Binding Bills Creation</h2><br><br><br>
                <div class="container-fluid wdt">
                   
                    <div class="row">
                          
                           
                            <div class="col-md-6 {{ $errors->has('io') ? 'has-error' : ''}}">
                                    <label>Internal Order<sup>*</sup></label><br>
                                    <select  class="input-css size select2" id="io" name="io">
                                        <option value="">Select Internal Order</option>
                                            @foreach ($io as $key)
                                            <option value="{{$key->id}}">{{$key->io_number}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('io', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('binder') ? 'has-error' : ''}}">
                                <label>Binder <sup>*</sup></label><br>
                                <select  class="input-css size select2" id="binder" name="binder">
                                        <option value="">Select Binder</option>
                                            @foreach ($binder as $key)
                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                {!! $errors->first('binder', '<p class="help-block">:message</p>') !!}
                        </div>
                           
                    </div><br><br>
                    <div class="row">
                          
                           
                            <div class="col-md-6 {{ $errors->has('binder_bill') ? 'has-error' : ''}}">
                                    <label>Binder Bill No.<sup>*</sup></label><br>
                                    <input type="text" name="binder_bill" id="binder_bill" class="binder_bill input-css">
                                    {!! $errors->first('binder_bill', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('qty') ? 'has-error' : ''}}">
                                    <label>Binder Bill Qty<sup>*</sup></label><br>
                                    <input type="number" step="none" name="qty" id="qty" class="qty input-css">
                                    {!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
                            </div>
                     
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('ready_qty') ? 'has-error' : ''}}">
                                    <label>Binder Bill Ready Qty<sup>*</sup></label><br>
                                    <input type="number" step="none" name="ready_qty" id="ready_qty" class="ready_qty input-css">
                                    {!! $errors->first('ready_qty', '<p class="help-block">:message</p>') !!}
                            </div>
                     
                           
                            <div class="col-md-6 {{ $errors->has('amount') ? 'has-error' : ''}}">
                                    <label>Binder Bill Amount<sup>*</sup></label><br>
                                    <input type="number" step="any" name="amount" id="amount" class="amount input-css">
                                    {!! $errors->first('amount', '<p class="help-block">:message</p>') !!}
                            </div>
                          
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('file') ? 'has-error' : ''}}">
                                    <label>Binder Bill Upload<sup>*</sup></label><br>
                                    <input type="file"  name="file" id="file" class="file input-css">
                                    <p>Allowed Formats: pdf,jpg,png .</p>
                                    {!! $errors->first('file', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('process') ? 'has-error' : ''}}">
                                    <label>Process Name<sup>*</sup></label><br>
                                    <input type="text"  name="process" id="amount" class="amount input-css">
                                    {!! $errors->first('process', '<p class="help-block">:message</p>') !!}
                            </div>
                           
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('bill_date') ? 'has-error' : ''}}">
                                    <label>Binder Bill Date<sup>*</sup></label><br>
                                    <input type="text"  name="bill_date" id="bill_date" class="datepicker bill_date input-css">
                                    
                                    {!! $errors->first('bill_date', '<p class="help-block">:message</p>') !!}
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
