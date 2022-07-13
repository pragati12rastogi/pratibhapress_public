
@extends($layout)
@section('title', __('delivery_challan.view'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="fa ">{{__('delivery_challan.view')}}</i></a></li>

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
            <div class='box box-default'><br>
                <div class="container-fluid">
               
                    <div class="row">
                        <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Delivery Challan No:</th>
                                        <td>{{$dc[0]['challan_number']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Delivery Challan Date:</th>
                                        <td>{{$dc[0]['created_time']}}</td>
                                    </tr>
                                    <tr>
                                       <th>Consignee Name:</th> 
                                       <td>{{$party['cname']}}</td>
                                    </tr>
                                    <tr><th>Consignee Address</th>
                                    <td>{{wordwrap($party['caddr'], 80, "\n", true)}},{{$party['ccity']}}, {{$party['cstate']}} - {{$party['cpcode']}}</td></tr>
                                    <tr>
                                        <th>
                                            Party Reference Name:
                                        </th>
                                        <td><p>{{$party['refname']}}</p></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Party Name:
                                        </th>
                                        <td><p>{{$party['pname']}}</p></td>
                                    </tr>
                                    <tr><th>Party Address</th>
                                        <td>{{wordwrap($party['paddr'], 80, "\n", true)}},{{$party['pcity']}}, {{$party['pstate']}} - {{$party['ppcode']}}</td></tr>
                                        <tr>
                                </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>   
    <div class="box-header with-border">
            <div class='box box-default'><br>
                <div class="container-fluid">
              
                    <div class="row">
                        <div class="col-md-12">

                                <table class="table table-bordered table-striped">
                                 <tr>
            <th>Internal Order</th>
            <th>Good Description</th>
            <th>Packing Details</th>
            <th>HSN/SAC</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Per</th>
            <th>GST %</th>
            <th>Amount Taxable Value</th>
        </tr>
        @foreach($dc as $vl)
        <tr>
            <td style="height:80px" class="cen">{{$vl['io_number']}}</td>
            <td style="height:80px" class="cen">{{$vl['good_desc']}}</td>
            <td class="cen">{{$vl['packing_details']}}
                    </td>
            <td class="cen">{{$vl['hsn']}}</td>
            <td class="cen">{{$vl['good_qty']}}</td>
            <td class="cen">{{$vl['challan_rate']}}</td>
            <td class="cen">{{$vl['uom_name']}}</td>
            <td class="cen">{{$vl['gst']}}</td>
            <td class="cen">
                    @php
                                
                    $rate=$vl['challan_rate'];
                    $qty=$vl['good_qty'];
                    $gst=$vl['gst'];
                    $amount=$rate*$qty;
                    $amount_gst=$amount+ (($amount*$gst)/100);
                     @endphp
                    {{round($amount_gst)}}    

            </td>
        </tr>
        @endforeach
                                </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>    
    <div class="box-header with-border">
            <div class='box box-default'><br>
                <div class="container-fluid">
           
                    <div class="row">
                        <div class="col-md-12">

                            <table class="table table-bordered table-striped">
                                <tr style="width:100%;">
                                        <th colspan="3"> Goods Dispatch mode:</th>
                                        <td colspan="5">{{$party['mode']}}</td>
                                    </tr>
                                    <tr style="width:100%;">
                                        <th colspan="3" {{count($goods_dispatch)>0?'rowspan='.count($goods_dispatch):''}}> Carrier's Name :</th>
                                        <td colspan="5">
                                            @foreach ($goods_dispatch as $cn)
                                            {!! $cn['courier_name']."<br>" !!}
                                            @endforeach
                                        </td>
                                    </tr>
                                    @if($party['mode']!="Self")
                                    <tr style="width:100%;">
                                        <th colspan="3">Transporter/Courier Company Address:</th>
                                        <td colspan="5">{{$party['address']}}</td>
                                    </tr>
                                    @endif
                                    @if($party['mode']=="Self")
                                    <tr style="width:100%;">
                                        <th colspan="3"> Vehicle Number:</th>
                                        <td colspan="5">{{$party['vehicle']}}</td>
                                    </tr>
                                    @else
                                    <tr style="width:100%;">
                                        <th colspan="3"> Bilty/Docket Number:</th>
                                        <td colspan="5">{{$dc[0]['bilty_docket']}}</td>
                                    </tr>
                                    <tr style="width:100%;">
                                        <th colspan="3"> Bilty/Docket Date:</th>
                                        <td colspan="5">{{date('d-m-y', strtotime($dc[0]['docket_date']))}}</td>
                                    </tr>
                                    <tr style="width:100%;">
                                        <th colspan="3"> Transporter GSTIN Number:</th>
                                        <td colspan="5">{{$party['dp_gst']}}</td>
                                    </tr>
                                    @endif
                        </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>    
</section>
@endsection
