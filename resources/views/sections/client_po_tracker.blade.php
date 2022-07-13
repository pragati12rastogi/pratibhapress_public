@extends($layout)

@section('title', 'Client PO Tracker')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Client PO Tracker</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
 
@media (max-width: 768px)  
  {
    
    .content-header>h1 {
      display: inline-block;
     
    }
  }
  @media (max-width: 425px)  
  {
   
    .content-header>h1 {
      display: inline-block;
      
    }
  }
  
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
   
    $("#reference_name").change(function(){
        $('#ajax_loader_div').css('display','block');
        $ref = $(this).val();
        $.ajax({
              url:"/report/fetch/client/po/api/"+$ref,
              type: "GET",
              success: function(result) {
                if (result) {
                    console.log(result);
                        $("#po_num").empty();
                        $("#po_num").append('<option value="0">Select PO</option>');
                        $.each(result, function(value,key) {
                            
                            $("#po_num").append('<option value="' + key.id + '">' + key.po_number + '</option>');
                        });
                        $('#ajax_loader_div').css('display','none');
                    }
              }
        });
            $ref = $("#reference_name").val();
            $po = $("#po_num").val();
            $("#io_table").show();
            $("#io_table").dataTable().fnDestroy()
            table($ref);
      
    });
    function table($ref){
      dataTable = $('#io_table').DataTable({
            "processing": true,
            "serverSide": true,
            "aaSorting":[],
            "responsive": true,
            "ajax": "/report/client/po/ref/tracker/api/"+$ref,
            "columns": [
                {"data":"io_number"},
                {"data":"name"},
                {"data":function(data, type, full, meta){
                    var dt=data.created_time;
                        dt=new Date(dt); 
                        var dd=dt.getDate();
                        var mm=dt.getMonth()+ 1;
                        var yyyy=dt.getFullYear();
                        var hh=dt.getHours();
                        var mi=dt.getMinutes();
                        var ss=dt.getSeconds();
                            var d = "AM";
                            var h = hh;
                            if (h >= 12) {
                            h = hh - 12;
                            d = "PM";
                            }
                            if (h == 0) {h = 12;}
                        var ac=dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                        return ac;
                }
            },
                {"data":"referencename"},
                {"data":function(data, type, full, meta){
                  var po=data.po_number;
                  if(po){
                    return po;
                  }
                  else{
                    return "Verbal";
                  }
                }
                },
                {"data":"item_name"},
                {"data":"qty"},
                {"data":"client_po_qty"},
                {"data":"balance"}

                // {
                //   "targets": [ -1 ],
                //   "data":"id", "render": function(data,type,full,meta)
                //   {
                //     return "<a href='/clientpo' target='_blank'><button class='btn btn-primary btn-xs'> {{__('Create')}} </button></a> &nbsp;" //+ 
                //     //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                //     ;
                //   }
                //   }
                ],
                "columnDefs": [
                  { "orderable": false, "targets": 8 }
                
                ]
            
            });
    }
    var dataTable;
    $(document).ready(function(){
        $("#io_table").hide();
        $('#po_num').change(function() {
            $ref = $("#reference_name").val();
            $po = $("#po_num").val();
            if($po!=0)
            {
              $("#io_table").show();
            $("#io_table").dataTable().fnDestroy()
            dataTable = $('#io_table').DataTable({
            "processing": true,
            "serverSide": true,
            "aaSorting":[],
            "responsive": true,
            "ajax": "/report/client/po/tracker/api/"+$ref+"/"+$po,
            "columns": [
                {"data":"io_number"},
                {"data":"name"},
                {"data":function(data, type, full, meta){
                    var dt=data.created_time;
                        dt=new Date(dt); 
                        var dd=dt.getDate();
                        var mm=dt.getMonth()+ 1;
                        var yyyy=dt.getFullYear();
                        var hh=dt.getHours();
                        var mi=dt.getMinutes();
                        var ss=dt.getSeconds();
                            var d = "AM";
                            var h = hh;
                            if (h >= 12) {
                            h = hh - 12;
                            d = "PM";
                            }
                            if (h == 0) {h = 12;}
                        var ac=dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                        return ac;
                }
            },
                {"data":"referencename"},
                {"data":function(data, type, full, meta){
                  var po=data.po_number;
                  if(po){
                    return po;
                  }
                  else{
                    return "Verbal";
                  }
                }
                },
                {"data":"item_name"},
                {"data":"qty"},
                {"data":"client_po_qty"},
                {"data":"balance"}

                // {
                //   "targets": [ -1 ],
                //   "data":"id", "render": function(data,type,full,meta)
                //   {
                //     return "<a href='/clientpo' target='_blank'><button class='btn btn-primary btn-xs'> {{__('Create')}} </button></a> &nbsp;" //+ 
                //     //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                //     ;
                //   }
                //   }
                ],
                "columnDefs": [
                { "orderable": false, "targets": 8 }
                
                ]
            
            });
        
            }
            else{
              $ref = $("#reference_name").val();
              $("#io_table").show();
            $("#io_table").dataTable().fnDestroy()
            table($ref);
            }
          });   
    })
    // Data Tables
  


  </script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('reference_name') ? 'has-error' : ''}}">
                            <label>Reference Name<sup>*</sup></label>
                            <select name="reference_name" id="reference_name" class="select2">
                                <option value="">Select Reference</option>
                                @foreach($reference as $ref)
                                    <option value="{{$ref['id']}}">{{$ref['referencename']}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('po_num') ? 'has-error' : ''}}">
                            <label>PO Number<sup>*</sup></label>
                            <select name="po_num" id="po_num" class="select2 po_num">
                                <option value="">Select PO</option>
                               
                            </select>
                            {!! $errors->first('po_num', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div><br><br>
                    <table id="io_table" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Internal Order Number</th>
                          <th>Internal Order Type</th>
                          <th>Internal Order Date</th>
                          <th>Reference Name</th>
                          <th>PO Number</th>
                          <th>Item Name</th>
                          <th>IO Quantity</th>
                          <th>PO Qty</th>
                          <th>Balance PO Qty</th>
                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
               
                  </table>
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection