@extends($layout)

@section('user', Auth::user()->name)

@section('title','Business Tracker')
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
<style>
    .content{
     padding: 30px;
   }
   .nav1>li>button {
     position: relative;
     display: block;
     padding: 10px 34px;
     background-color: white;
     margin-left: 10px;
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
@endsection
@section('js')
<script src="/js/bootbox.min.js"></script>
<script src="/js/bootbox.locales.min.js"></script>

<script src="/js/dataTables.responsive.js"></script>

<script>
var table; 
$("#reference_name").change(function(){
        $('#ajax_loader_div').css('display','block');
        $ref = $(this).val();
       geturl();
        $.ajax({
              url:"/report/fetch/client/api/"+$ref,
              type: "GET",
              success: function(result) {
                if (result) {
                   
                        $("#party_name").empty();
                        $("#party_name").append('<option value="0">Select Client</option>');
                        $.each(result, function(value,key) {
                            
                            $("#party_name").append('<option value="' + key.id + '">' + key.partyname + '</option>');
                        });
                        $('#ajax_loader_div').css('display','none');
                    }
              }
        });
        if(table)
          table.destroy();
          $("#tax").show();
        table_tracker1($ref); 
    });

    function geturl(){
      var startDate = $('#min-date').val();
        var endDate = $('#max-date').val();
        var ref=$("#reference_name").val();
        var party=$("#party_name").val();
        if(ref==0){
          ref='';
        }
        $("#anchor").attr("href", '/export/data/businesstracker?from='+startDate+'&to='+endDate+'&ref='+ref+'&party='+party);
   
    }
  function gettax(party)
  {
    var party = $("#party_name").val();
    if(table)
      table.destroy();
        table_tracker(party); 
  }
    function table_tracker(party){
        
      table = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/report/businesstracker/api/"+party,
            "datatype": "json",
                "data": function (data,json) {
                    var startDate = $('#min-date').val();
                    var endDate = $('#max-date').val();
                    data.startDate = startDate;
                    data.endDate = endDate;
                    
                    
                }
            },
            "createdRow": function( row, data, dataIndex){
                if( data.sum_without_tax){
                  $("#withoutTax").text("");
                  $("#withoutTax").text(data.sum_without_tax.toFixed(2));    
                }
                if( data.sum_with_tax){
                  $("#withTax").text("");
                  $("#withTax").text(data.sum_with_tax.toFixed(2));    
                }
            },
          
            "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api();
            var decTotal = api
    .column( 6, { page: 'current'} )
    .data()
    .reduce( function (a, b) {
        return parseFloat(a) + parseFloat(b);
    }, 0 );
              console.log(decTotal);
              if(decTotal==0){
                $("#withoutTax").text("");
                $("#withoutTax").text("0");
                $("#withTax").text("");
                $("#withTax").text("0");
              }
              
            },
          "columns": [
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
            { 
                "data":"challan_number","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            { 
                "data":"io_number","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            { 
                "data":"item_name","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
           // {"data":"terms_of_delivery"},
            { 
                "data":"amount","render": function(data, type, full, meta){
                  if(data)
                    return "Rs."+ (data).toFixed(2);
                  else
                    return "";
                } 
            },
              { 
                "data":"total_amount","render": function(data, type, full, meta){
                  if(data)
                    return "Rs."+ (data).toFixed(2);
                  else
                    return "";
                } 
            },
            { 
            "data": function(data, type, full, meta){
                var dt=data.created_at;
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
                      var ac=dd+'-'+mm+'-'+yyyy;
                      return ac;
               }
              
            } 
         
            ],
            "columnDefs": [
            //   { "orderable": false, "targets": 3}
            ]
          
        });
    }
    function table_tracker1(party){
        
        table = $('#taxinvoice_list_table').DataTable({
            "processing": true,
            "serverSide": true,
            "aaSorting":[],
            "responsive": true,
            "ajax": {
              "url": "/report/businesstracker/ref/api/"+party,
              "datatype": "json",
                  "data": function ( data ) {
                //Make your callback here.
                var startDate = $('#min-date').val();
                      var endDate = $('#max-date').val();
                      data.startDate = startDate;
                      data.endDate = endDate;
            }       
              },
              "createdRow": function( row, data, dataIndex){
                if( data.sum_without_tax){
                  $("#withoutTax").text("");
                  $("#withoutTax").text(data.sum_without_tax.toFixed(2));    
                }
              
                if( data.sum_with_tax){
                  $("#withTax").text("");
                  $("#withTax").text(data.sum_with_tax.toFixed(2));    
                }
              
            },
            "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api();
            var decTotal = api
    .column( 6, { page: 'current'} )
    .data()
    .reduce( function (a, b) {
        return parseFloat(a) + parseFloat(b);
    }, 0 );
              console.log(decTotal);
              if(decTotal==0){
                $("#withoutTax").text("");
                $("#withoutTax").text("0");
                $("#withTax").text("");
                $("#withTax").text("0");
              }
              
            },
            "columns": [
              { "data": "ch_no" }, 
              { "data": "pname" }, 
              { "data": "cname" }, 
              { 
                  "data":"challan_number","render": function(data, type, full, meta){
                    if(data)
                    return data.replace(/,/g,'<br>');
                    else
                      return "";
                  } 
              },
              { 
                  "data":"io_number","render": function(data, type, full, meta){
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else
                      return "";
                  } 
              },
              { 
                  "data":"item_name","render": function(data, type, full, meta){
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else
                      return "";
                  } 
              },
             // {"data":"terms_of_delivery"},
              { 
                  "data":"amount","render": function(data, type, full, meta){
                    if(data)
                      return "Rs."+ (data).toFixed(2);
                    else
                      return "";
                  } 
              },
                { 
                  "data":"total_amount","render": function(data, type, full, meta){
                    if(data)
                      return "Rs."+ (data).toFixed(2);
                    else
                      return "";
                  } 
              },
              { 
              "data": function(data, type, full, meta){
                  var dt=data.created_at;
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
                        var ac=dd+'-'+mm+'-'+yyyy;
                        return ac;
                 }
                
              } 
           
              ],
              "render": function (data, type, row) {
                    console.log(data[0]);
                    
                },
              "columnDefs": [
              //   { "orderable": false, "targets": 3}
              ]
            
          });
          
      }
    $(document).ready(function() {
        $("#tax").hide();
        $('.input-daterange input').each(function() {
            $(this).datepicker('clearDates');
        });

     
       
    });
    $("#party_name").change(function(){
          if($("#party_name").val()!=0){
            $("#tax").show();
            geturl();
            gettax();
          }
          else if($("#party_name").val()==0){
            if(table)
              table.destroy();
          $("#tax").show();
          var x=$("#reference_name").val();
        table_tracker1(x); 
          }
        
        });
    $('.date-range-filter').change(function() {
        table.draw();
        geturl();
    });
</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class=""> Business Tracker</i></a></li>
@endsection

@section('main_section')
    <section class="content">
    @section('titlebutton')
<a href="/export/data/businesstracker" id="anchor"><button class="btn btn-sm btn-primary">Export Business Tracker</button></a>
@endsection
        <!-- Default box -->
        <div id="modal_div"></div>
        <div class="box">
          
          <div class="box-header with-border">
              <h3 class="box-title">Business Tracker</h3>
          </div>
          <div class="box-body">
          <div class="row">
            <div class="col-md-6 {{ $errors->has('reference_name') ? 'has-error' : ''}}">
                <label>Reference Name<sup>*</sup></label>
                <select name="reference_name" id="reference_name" class="select2">
                    <option value="0">Select Reference</option>
                    @foreach($reference as $ref)
                        <option value="{{$ref['id']}}">{{$ref['referencename']}}</option>
                    @endforeach
                </select>
                {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
            </div>
            <div class="col-md-6 {{ $errors->has('party_name') ? 'has-error' : ''}}">
                <label>Client Name<sup>*</sup></label>
                <select name="party_name" id="party_name" class="select2 party_name">
                    <option value="">Select Client</option>
                    
                </select>
                {!! $errors->first('party_name', '<p class="help-block">:message</p>') !!}
            </div>
        </div><br><br>

           <div id="tax" >
              <div class="col-md-4 pull-right margin">
                  <div class="input-group input-daterange">
                    <input autocomplete="off" type="text" id="min-date" class="form-control date-range-filter" data-date-format="yyyy-mm-dd" placeholder="From:">
                    <div class="input-group-addon">to</div>
                    <input autocomplete="off" type="text" id="max-date" class="form-control date-range-filter" data-date-format="yyyy-mm-dd" placeholder="To:">
                  </div>
                  <div class="col-md-4 total_s" style="display:contents">
                    <span class="margin" ><label class="inline">WithoutTax: </label><i id="withoutTax"></i></span>
                    <span class="margin" ><label class="inline">WithTax: </label><i id="withTax"></i></span>
                  </div>
              </div>
              
              <table id="taxinvoice_list_table" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{__('taxinvoice.mytitle')}}</th>
                    <th>{{__('taxinvoice.party')}}</th>
                    <th>{{__('taxinvoice.consignee')}}</th>
                    <th>{{__('taxinvoice.delivery')}}</th>
                    <th>{{__('taxinvoice.io')}}</th>
                    <th>Item Name</th>
                    <th>{{__('Amount(Without Tax)')}}</th>
                    <th>{{__('Total Amount')}}</th>
                    <th>Tax Invoice Date</th>
                  </tr>
                  </thead>
                  <tbody>
  
                  </tbody>
                  <tfoot align="right">
                    <tr>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot>
              </table>
           </div> 
          </div>
        </div>
        <!-- /.box -->
      </section>
@endsection
