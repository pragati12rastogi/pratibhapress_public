@extends($layout)

@section('user', Auth::user()->name)

@section('title', __('Sale Tracker'))
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
  function gettax()
  {
    if(table)
      table.destroy();
      table = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/report/sale/tracker/list/api",
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
          
          "columns": [
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
            { "data": "item_name" }, 
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
              { "orderable": false, "targets": 3}
            ]
          
        });
  }
 
  function getdatewise()
  {
    var s_date = $("#min-date").val();
    var e_date = $("#max-date").val();
    if(table)
        table.destroy();
          table = $('#taxinvoice_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/report/sale/tracker/date/list/api/"+s_date+"_"+e_date,
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
          
          "columns": [
            { "data": "ch_no" }, 
            { "data": "pname" }, 
            { "data": "cname" }, 
            { "data": "item_name" }, 
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
              { "orderable": false, "targets": 3}
            ]
          
        });
  }

    $(document).ready(function() {
      $('.input-daterange input').each(function() {
        $(this).datepicker('clearDates');
      });
        gettax();
    });

    $('.date-range-filter').change(function() {
      // console.log($("#min-date").val());
      // console.log( $("#max-date").val());

      if($("#max-date").val() == "" || $("#min-date").val() == "" ){
        // $(".total_s").hide();
        gettax();
      }else{
        getdatewise();
      }
      
    });
</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class=""> {{__('Sale Tracker')}}</i></a></li>
@endsection

@section('main_section')
    <section class="content">
      
        <!-- Default box -->
        <div id="modal_div"></div>
        <div class="box">
          
          <div class="box-header with-border">
              <h3 class="box-title">{{__('Sale Tracker')}}</h3>
          </div>
          <div class="box-body">
             @section('titlebutton')
             <a href="/export/data/saletracker" ><button class="btn btn-primary">Export Sale Tracker</button></a> 
             @endsection

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
                    <th>Item Name</th>
                    <th>{{__('taxinvoice.delivery')}}</th>
                    <th>{{__('taxinvoice.io')}}</th>
                    <th>{{__('Amount(Without Tax)')}}</th>
                    <th>{{__('Total Amount')}}</th>
                    <th>Tax Invoice Date</th>
                  </tr>
                  </thead>
                  <tbody>
  
                  </tbody>
             
              </table>
           </div> 
          </div>
        </div>
        <!-- /.box -->
      </section>
@endsection
