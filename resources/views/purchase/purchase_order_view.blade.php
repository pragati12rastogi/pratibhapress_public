
@extends($layout)
@section('title', __('purchase/order.pr_order_det_title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('purchase/order.pr_order_det_title')}}</i></a></li>
@endsection

@section('css')
{{-- <link rel="stylesheet" href="/css/bootstrap.min.css"> --}}
    <style>
        .help-block{
        color:red;
        }
        table.th{
            text-indent: 50px;
        }
    </style>
@endsection
@section('main_section')
    <section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
<!-- Default box --> 
    <div class="box-header with-border">
        <div class='box box-default'>
            <div class="container-fluid col-12 row"><br>
                <div class="row">
                    <div class="col-md-3 " style="text-indent:30px"><label>{{__('purchase/order.po_num')}}</label></div>
                    <div class="col-md-3 " style="text-indent:30px">{{$order['po_num']}}</div>
                    <div class="col-md-3 " style="text-indent:30px"><label>{{__('purchase/order.ind_num')}}</label></div>
                    <div class="col-md-3 " style="text-indent:30px">{{$order['indent_num']}}</div>
                </div>
                <div class="row">
                    <div class="col-md-3 " style="text-indent:30px"><label>{{__('purchase/order.po_date')}}</label></div>
                    <div class="col-md-3 " style="text-indent:30px">{{$order['po_date']}}</div>
                    <div class="col-md-3 " style="text-indent:30px"><label>{{__('purchase/order.master')}}</label></div>
                    <div class="col-md-3 " style="text-indent:30px">{{$order['master_name']}}</div>
                </div>
                <div class="row">
                    <div class="col-md-3 " style="text-indent:30px"><label>{{__('purchase/order.vendor')}}</label></div>
                    <div class="col-md-3 " style="text-indent:30px">{{$order['vendor_name']}}</div>
                    <div class="col-md-3 " style="text-indent:30px"><label>{{__('purchase/order.py_term')}}</label></div>
                    <div class="col-md-3 " style="text-indent:30px">{{$order['py_value']}}</div>
                </div>
                <br>
            </div> 
        </div>
    </div>
 

    
        <div class="box-header with-border">
            <div class='box box-default'><br>
                <div class="row">
                    <div class="col-md-2" style="text-indent:30px"><b>{{__('purchase/order.sub_cat')}}</b></div>
                    <div class="col-md-2"style="text-indent:30px"><b>{{__('purchase/order.item_name')}}</b></div>
                    <div class="col-md-1"style="text-indent:30px"><b>{{__('purchase/order.qty')}}</b></div>
                    <div class="col-md-2"style="text-indent:30px"><b>{{__('purchase/order.uom')}}</b></div>
                    <div class="col-md-1"style="text-indent:30px"><b>{{__('purchase/order.tax')}}</b></div>
                    <div class="col-md-2"style="text-indent:30px"><b>{{__('purchase/order.delivery')}}</b></div>
                    <?php if(!empty($order_detail[0]['item_rate'])){ ?>
                    <div class="col-md-2"style="text-indent:30px"><b>{{__('purchase/order.item_rate')}}</b></div>
                    <?php } ?>
                </div>
                @foreach($order_detail as $details) 
                <br>
                <div class="row">
                    <div class="col-md-2"style="text-indent:30px">{{$details['sub_cat']}}</div>
                    <div class="col-md-2"style="text-indent:30px">{{$details['item_name']}}</div>
                    <div class="col-md-1"style="text-indent:30px">{{$details['item_qty']}}</div>
                    <div class="col-md-2"style="text-indent:30px">{{$details['uom_name']}}</div>
                    <div class="col-md-1"style="text-indent:30px">{{$details['tax_value']}}</div>
                    <div class="col-md-2"style="text-indent:30px">{{$details['delivery_date']}}</div>
                    <?php if(!empty($order_detail[0]['item_rate'])){ ?>
                        <div class="col-md-2"style="text-indent:30px">{{$details['item_rate']}}</div>
                    <?php } ?>
                    
                <!-- <div class="col-sm-1" style="text-indent:30px"></div> -->
                </div>
                @endforeach
                <br>
            </div>
        </div>
  

</section>
@endsection
