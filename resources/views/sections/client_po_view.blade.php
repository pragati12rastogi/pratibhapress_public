
@extends($layout)
@section('title', __('client_po.view'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('client_po.view')}}</i></a></li>

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
    @php
        $d = $data[0]
    @endphp
    <div class="box-header with-border">
        <div class='box box-default'><br>
            <div class="container-fluid">
                <div class="row ">
                    <div class="col-md-8"><h3 class="box-title" style="font-size: 28px;">{{__('client_po.mytitle')}}</h3></div>
                </div>
                    
                <div class="row">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>{{__('client_po.Ref Name')}}</th>
                            <td>{{$d->reference_name}}</td>
                      
                            <th>{{__('client_po.Internal Order')}}</th>
                            <td>{{$d->io_number}}</td>
                        </tr>
                        <tr>
                            <th>{{__('client_po.Is client providing a PO?')}}</th>
                            <td>{{$d->is_po_provided == 1?'Yes':'Verbal'}}</td>
                     
                        @if($d->is_po_provided==1)
                       
                            <!-- <th>{{__('client_po.po_num')}}</th>
                            <td>{{$d->po_number}}</td> -->
                        </tr>
                        {{-- @foreach ($data as $d)
                        --}}
                        <!-- <tr>
                                <th>{{__('client_po.po_num')}}</th>
                                <td>{{$d->po_number}}</td>
                                <th>{{__('client_po.po_date')}}</th>
                                <td>{{CustomHelpers::showDate($d->po_date)}}</td>
                            </tr> -->
                            <tr>
                                <th>{{__('client_po.hsn')}}</th>
                                <td>{{$hsn->name.' - '.$hsn->hsn.' - '.$hsn->gst_rate}}</td>
                                <th>{{__('client_po.Item Description')}}</th>
                                <td>{{$d->item_desc}}</td>
                            </tr>
                            <tr>
                                <th>{{__('client_po.Delivery Date')}}</th>
                                <td>{{CustomHelpers::showDate($d->delivery_date)}}</td>
                                <th>{{__('client_po.qty')}}</th>
                                <td>{{$d->qty}}</td>
                            </tr>
                            <tr>
                                <th>{{__('client_po.unit_m')}}</th>
                                <td>{{$d->uom_name}}</td>
                                <th>{{__('client_po.per_unit_price')}}</th>
                                <td>{{$d->per_unit_price}}</td>
                            </tr>
                            <tr>
                                <th>{{__('client_po.discount')}}</th>
                                <td>{{$d->discount}}</td>
                                <th>{{__('client_po.Consignee list Available?')}}</th>
                                <td>{{$d->is_consignee==1?'Yes':'No'}}</td>
                            </tr>
                          
                            {{-- @endforeach --}}
                        @endif
                        <tr>
                                <th>
                                    <h3>Consignee Details</h3>
                                </th>
                            </tr>
                                    <tr>
                                        <th>{{__('client_po.Part Name')}}</th>
                                        <th>Is Consignee</th>
                                        {{-- <th>{{__('client_po.pay_term')}}</th> --}}
                                        <th>{{__('client_po.consignee_name')}}</th>
                                        <th>{{__('client_po.qty')}}</th>
                                    </tr>
                                    
                                    @foreach($data1 as $item)
                                        <tr>
                                            <td>{{$item['partyname']}}</td>
                                            <td>@if ($item['is_consignee']==1)
                                                {{"Yes"}}
                                                @else
                                                    {{"No"}}
                                                @endif</td>
                                                @if($item->is_consignee==1)
                                                        <td>
                                                                @php
                                                                    $con=explode(',',$item['consignee_name']);
                                                                    foreach ($con as $key) {
                                                                        echo $key."<br>";
                                                                    } 
                                                                @endphp  
                                                        </td> 
                                                        <td>
                                                                @php
                                                                $qty=explode(',',$item['cpoc_qty']);
                                                                    foreach ($qty as $key) {
                                                                    echo $key."<br>";
                                                                } 
                                                            @endphp 
                                                        </td> 
                                            
                                                @endif
                                                @if($item->is_consignee==0)
                                                        <td>
                                                            {{"---"}}
                                                        </td> 
                                                        <td>
                                                                {{"---"}}
                                                        </td> 
                                            
                                                @endif
                                    @endforeach      
                    </table>
                </div>
                <br>
                <div class="row">
                <h3>PO Details</h3>
                <table class="table table-bordered table-striped">
                       <th>PO Number</th>
                       <th>PO Qty</th>
                       <th>PO File</th>
                       
                                    @foreach($po as $item)
                                        <tr>
                                            <td>{{$item['po_number']}}</td>
                                            <td>{{$item['po_qty']}}</td>
                                            <td>
                                            @if($item['po_upload'])
                                                    @php 
                                                    $file_types=explode('.',$item['po_upload']);
                                                    $file_types = $file_types[count($file_types)-1];
                                                    @endphp

                                                    <a href="/upload/clientpo/{{$item->po_upload}}" target="_blank"><u>View File</u></a>
                                            @else
                                                    <p style="color:green">No File uploaded</p>
                                            @endif
                                            </td>
                                         </tr> 
                                             
                                               
                                    @endforeach      
                    </table>
                </div>
                <!-- <div class="row">
                    <div class="col-md-6">
                            <h3>Uploaded File</h3>
                            @if($file_type=="")
                                <p>No File Uploaded</p>
                            @elseif ($file_type=="pdf")
                                <embed src="/upload/clientpo/{{$d->po_file_name}}" height="480" width="720" type="application/pdf">
                            @else
                                <img src="/upload/clientpo/{{$d->po_file_name}}" height="480" width="720" alt="No File Uploaded">
                            @endif
                       
                    </div>
                </div> -->

                
</section>
@endsection
