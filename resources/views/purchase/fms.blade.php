@extends($layout)

@section('title', 'Purchase FMS')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Purchase FMS</a></li> 
@endsection
@section('css')

<link rel="stylesheet" href="/css/responsive.bootstrap.css">    

@endsection
@section('js')
{{-- <script src="/js/dataTables.responsive.js"></script> --}}
<meta name="csrf-token" content="{{ csrf_token() }}" />
 <script>
  var dataTable;

// Data Tables
$(document).ready(function() {
    var last_ele = null ;
  var last_tr = null ;
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
 $('.loader').hide()
  dataTable = $('#fms_table').DataTable({
    "serverSide": true,
          "autoWidth": false,
          "fixedColumns":   {
            "leftColumns": 1
        },
        "pagingType": "full_numbers",
    "responsive": true,
    "processing": true,
    "serverSide": true,
    "scrollY":        "300px",
        "scrollX":        true,
        "scrollCollapse": true,
      "ajax": {
            "url":"/purchase/fms/api" ,
            "type": "post",
            "data": {'_token': CSRF_TOKEN},
        },
      "columns": [
        {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.indent_no.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.indent_date.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.requested_by.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.item_req_for.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.io_number.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.job_number.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.referencename.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.required_date.replace(/,/g,'<br>')+"</div>";
                    }
              },
              //step 2
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.pr_actual_date;
                      var dt = data.pr_planned_date;
                      var jc=data.pr_number;
                      dt2=new Date(dt); 
                      
                      var dd=dt2.getDate();
                      var mm=dt2.getMonth()+1;
                      var yyyy=dt2.getFullYear();
                      var hh=dt2.getHours();
                      var mi=dt2.getMinutes();
                      var ss=dt2.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                      var now=new Date();
                      var ac=dateString+' '+h+':'+mi+':'+ss+' '+d;
                     if(dt=="-"){
                       ac="-";
                     }
                      if ((dt1) && (dt>dt1) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt1) && (dt<dt1) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (!dt1) && (dt2>now) )
                      {return "<div class='white divs'>"+ac+"</div>"}
                      else if ( (!dt1) && (dt2<now) )
                      {return "<div class='red divs'>"+ac+"</div>"}
                      else
                      {return "<div class='red divs'>-</div>"} 
                      
                      // return "<div class='divs'>"+data.po_planned_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.pr_actual_date;
                      var dt = data.pr_planned_date;
                      var pr=data.po_number;
                
                      var pp = data.pr_planned_date;
                      var ac = data.pr_actual_date;
                    
                    
                      var now=new Date();
                      var dd=now.getDate();
                      var mm=now.getMonth()+1;
                      var yyyy=now.getFullYear();
                      var hh=now.getHours();
                      var mi=now.getMinutes();
                      var ss=now.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                        now=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        
                    if ((dt1!="-") && (dt>dt1) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt1!="-") && (dt<dt1) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1>now) )
                      {return "<div class='white divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1<now) )
                      {return "<div class='red divs'>"+ac+"</div>"}
                      else
                      {return "<div class='red divs'>"+ac+"</div>"}
                      // return "<div class='divs'>"+data.po_actual_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.pr_number.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.pr_item_desc.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.pr_item_qty.replace(/,/g,'<br>')+"</div>";
                    }
              },
              //step 3
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.po_actual_date;
                      var dt = data.po_planned_date;
                      var pr=data.po_number;
                
                      var pp = data.po_planned_date;
                      var ac = data.po_actual_date;
                    
                      var now=new Date();
                      var dd=now.getDate();
                      var mm=now.getMonth()+1;
                      var yyyy=now.getFullYear();
                      var hh=now.getHours();
                      var mi=now.getMinutes();
                      var ss=now.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                        now=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        
                    if ((dt1!="-") && (dt>dt1) )
                      { return "<div class='green divs'>"+pp+"</div>"}
                      else if ((dt1!="-") && (dt<dt1) )
                      {return "<div class='yellow divs'>"+pp+"</div>"}
                      else if ( (dt1=="-") && (dt1>now) )
                      {return "<div class='white divs'>"+pp+"</div>"}
                      else if ( (dt1=="-") && (dt1<now) )
                      {return "<div class='red divs'>"+pp+"</div>"}
                      else
                      {return "<div class='red divs'>"+pp+"</div>"}
                      
                      // return "<div class='divs'>"+data.po_planned_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.po_actual_date;
                      var dt = data.po_planned_date;
                      var pr=data.po_number;
                
                      var pp = data.po_planned_date;
                      var ac = data.po_actual_date;
                    
                      var now=new Date();
                      var dd=now.getDate();
                      var mm=now.getMonth()+1;
                      var yyyy=now.getFullYear();
                      var hh=now.getHours();
                      var mi=now.getMinutes();
                      var ss=now.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                        now=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        
                    if ((dt1!="-") && (dt>dt1) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt1!="-") && (dt<dt1) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1>now) )
                      {return "<div class='white divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1<now) )
                      {return "<div class='red divs'>"+ac+"</div>"}
                      else
                      {return "<div class='red divs'>"+ac+"</div>"}
                      // return "<div class='divs'>"+data.po_actual_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.po_number.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.vendor.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>-</div>";
                    }
              },
              //step 4
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.po_app_approved_date;
                      var dt1 = data.po_app_planned_date;
                      var pr=data.po_number;
                      var pp = data.po_app_planned_date;
                      var ac = data.po_app_approved_date;
                    
                      var now=new Date();
                      var dd=now.getDate();
                      var mm=now.getMonth()+1;
                      var yyyy=now.getFullYear();
                      var hh=now.getHours();
                      var mi=now.getMinutes();
                      var ss=now.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                        now=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        
                    if ((dt1!="-") && (dt>dt1) )
                      { return "<div class='green divs'>"+pp+"</div>"}
                      else if ((dt1!="-") && (dt<dt1) )
                      {return "<div class='yellow divs'>"+pp+"</div>"}
                      else if ( (dt1=="-") && (dt1>now) )
                      {return "<div class='white divs'>"+pp+"</div>"}
                      else if ( (dt1=="-") && (dt1<now) )
                      {return "<div class='red divs'>"+pp+"</div>"}
                      else
                      {return "<div class='red divs'>"+pp+"</div>"}
                      // return "<div class='divs'>"+data.po_app_planned_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.po_app_approved_date;
                      var dt1 = data.po_app_planned_date;
                      var pr=data.po_number;
                      var pp = data.po_app_planned_date;
                      var ac = data.po_app_approved_date;
                    
                      var now=new Date();
                      var dd=now.getDate();
                      var mm=now.getMonth()+1;
                      var yyyy=now.getFullYear();
                      var hh=now.getHours();
                      var mi=now.getMinutes();
                      var ss=now.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                        now=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        
                    if ((dt1!="-") && (dt>dt1) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt1!="-") && (dt<dt1) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1>now) )
                      {return "<div class='white divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1<now) )
                      {return "<div class='red divs'>"+ac+"</div>"}
                      else
                      {return "<div class='red divs'>"+ac+"</div>"}
                      // return "<div class='divs'>"+data.po_app_approved_date+"</div>";
                    }
              },
              //step 5

              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      
                      return "<div class='divs'>"+data.mat_planned_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                     
                      return "<div class='divs'>"+data.mat_planned_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.material_inward_number.replace(/,/g,'<br>')+"</div>";
                    }
              },
              //step 6
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.grn_actual_date;
                      var dt1 = data.grn_planned_date;
                      var pr=data.grn_number;
                      var pp = data.grn_planned_date;
                      var ac = data.grn_actual_date;
                    
                      var now=new Date();
                      var dd=now.getDate();
                      var mm=now.getMonth()+1;
                      var yyyy=now.getFullYear();
                      var hh=now.getHours();
                      var mi=now.getMinutes();
                      var ss=now.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                        now=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        
                    if ((dt1!="-") && (dt>dt1) )
                      { return "<div class='green divs'>"+pp+"</div>"}
                      else if ((dt1!="-") && (dt<dt1) )
                      {return "<div class='yellow divs'>"+pp+"</div>"}
                      else if ( (dt1=="-") && (dt1>now) )
                      {return "<div class='white divs'>"+pp+"</div>"}
                      else if ( (dt1=="-") && (dt1<now) )
                      {return "<div class='red divs'>"+pp+"</div>"}
                      else
                      {return "<div class='red divs'>"+pp+"</div>"}
                      // return "<div class='divs'>"+data.grn_planned_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.grn_actual_date;
                      var dt1 = data.grn_planned_date;
                      var pr=data.grn_number;
                      var pp = data.grn_planned_date;
                      var ac = data.grn_actual_date;
                    
                      var now=new Date();
                      var dd=now.getDate();
                      var mm=now.getMonth()+1;
                      var yyyy=now.getFullYear();
                      var hh=now.getHours();
                      var mi=now.getMinutes();
                      var ss=now.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                        now=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        
                    if ((dt1!="-") && (dt>dt1) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt1!="-") && (dt<dt1) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1>now) )
                      {return "<div class='white divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1<now) )
                      {return "<div class='red divs'>"+ac+"</div>"}
                      else
                      {return "<div class='red divs'>"+ac+"</div>"}
                      // return "<div class='divs'>"+data.grn_actual_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.grn_number.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.grn_bill_number.replace(/,/g,'<br>')+"</div>";
                    }
              },
              //step 7
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var po=data.po_status;
                      if(po!="-"){
                        var x=po.split(",");
                        var z=new Array();
                        for(var i=0;i<x.length;i++){
                          if(x[i]=="Not Approved"){
                            z[i]="-";
                          }
                          else if(x[i]=="Approved"){
                            z[i]="Yes";
                          }
                          else if(x[i]=="Rejected"){
                            z[i]="No";
                          }
                          else{
                            z[i]="-";
                          }

                        }
                        z=z.join(',');
                        return "<div class='divs'>"+z.replace(/,/g,'<br>')+"</div>";
                      }
                      else{
                         return "<div class='divs'>"+po.replace(/,/g,'<br>')+"</div>";
                      }
                     
                    }
              },
              //step 8

              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.prr_actual_date;
                      var dt = data.prr_planned_date;
                      var pr=data.po_number;
                
                      var pp = data.prr_planned_date;
                      var ac = data.prr_actual_date;
                    
                      var now=new Date();
                      var dd=now.getDate();
                      var mm=now.getMonth()+1;
                      var yyyy=now.getFullYear();
                      var hh=now.getHours();
                      var mi=now.getMinutes();
                      var ss=now.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                        now=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        
                    if ((dt1!="-") && (dt>dt1) )
                      { return "<div class='green divs'>"+pp+"</div>"}
                      else if ((dt1!="-") && (dt<dt1) )
                      {return "<div class='yellow divs'>"+pp+"</div>"}
                      else if ( (dt1=="-") && (dt1>now) )
                      {return "<div class='white divs'>"+pp+"</div>"}
                      else if ( (dt1=="-") && (dt1<now) )
                      {return "<div class='red divs'>"+pp+"</div>"}
                      else
                      {return "<div class='red divs'>"+pp+"</div>"}
                      
                      // return "<div class='divs'>"+data.po_planned_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.prr_actual_date;
                      var dt = data.prr_planned_date;
                      var pr=data.po_number;
                
                      var pp = data.prr_planned_date;
                      var ac = data.prr_actual_date;
                    
                      var now=new Date();
                      var dd=now.getDate();
                      var mm=now.getMonth()+1;
                      var yyyy=now.getFullYear();
                      var hh=now.getHours();
                      var mi=now.getMinutes();
                      var ss=now.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                        now=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        
                    if ((dt1!="-") && (dt>dt1) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt1!="-") && (dt<dt1) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1>now) )
                      {return "<div class='white divs'>"+ac+"</div>"}
                      else if ( (dt1=="-") && (dt1<now) )
                      {return "<div class='red divs'>"+ac+"</div>"}
                      else
                      {return "<div class='red divs'>"+ac+"</div>"}
                      // return "<div class='divs'>"+data.po_actual_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.prr_number.replace(/,/g,'<br>')+"</div>";
                    }
              },
              //step 9
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var ref=data.referencename;
                      if(ref!="-"){
                        var x="Yes";
                      }
                      else{
                        var x="No";
                      }
                      return "<div class='divs'>"+x.replace(/,/g,'<br>')+"</div>";
                    }
              },
              //step 10
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.supply_to.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.supply_date.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.supply_qty.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.supply_io.replace(/,/g,'<br>')+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.supply_challan.replace(/,/g,'<br>')+"</div>";
                    }
              },
        ],
        "columnDefs": [
          { "orderable": false, "targets": 10 }
        ]
       
      
    });
  });
 </script>
@endsection
<style>
        .divs{
          width: 100%;
          text-align: center;
    height: 100%;
        }
        .hsn_table {
          width: 100%;
          overflow-x: scroll;
        }
        #fms_table {
          width: 100%;
          overflow-x: auto;
        }

        .hsn_table thead tr  td {
          border: 1px solid black;
          text-align: center;
          border-style: solid;
          /* padding: 0!important ; */
        }
        td{
          padding: 0!important ;
          vertical-align: middle !important; 
          line-height: 1 !important;
        }
        .hsn_table thead tr{
          height: 20px;
        }
     
        .hsn_table tbody tr td{
          height: 20px;
        }
        /* .hsn>tbody tr td{
          line-height: 0.8;
        } */
        /* .hsn_table tr:nth-child(even){background-color: #f2f2f2;} */
        
        
        .hsn_table th {
          text-align: top;
          border: 1px solid black;
          
        }
        .hsn_table td {
          text-align: center;
          border: 1px solid black;
          
        }
        .yellow{
          background-color: yellow;
            /* height: 76px;
            width: 176px;
            padding-top: 25px; */
        }
        table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting {
    padding-right: 122px !important;
  }
        .red{
         background:red;
            /* height: 10px;
            width: 176px; */
            opacity: .9;
            text-align: center;
            /* padding-top: 25px; */
        }
        .green{
          background-color: yellowgreen;
            /* height: 76px;
            width: 176px;
            padding-top: 25px; */
        }
        .white{
            background-color: white;
            /* height: 76px;
            width: 176px;
            padding-top: 25px; */
        }
        .black{
            background-color: gray;
            /* height: 76px;
            width: 176px;
            padding-top: 25px; */
        }
        .blue{
            background-color: #1E90FF;
        }
        #yellow{
            background-color: #FFD700;
            
        }
        #red{
            background-color: red;
           
        }
        #green{
            background-color: yellowgreen;
           
        }
        #blue{
            background-color: #1E90FF;
        }
        .fms{
            border: 1px solid black; !important;
            font-size:14px;
        }
        .plan{
            background-color: grey;
        }
        .actual{
            background-color: white; 
        }
        .no-sort{
          width:200px;
        }
        /* thead.fixed-header
        {
          display: table;
          width: 100%;
          overflow: auto;
        } */
       /* tbody.fixed-body
       {
         display: block;
         width: 100%;
         height: 400px;
         overflow: auto;
       } */
       /* tbody.fixed-body tr{
        display: table;
          width: 100%;
          table-layout: fixed;
          position: relative;
       }
       tbody.fixed-body tr td {
         
          width: 100%;
         
      } */
        </style>
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->

        <div id="show" ></div>
        <div class="box" style="overflow-x:auto;overflow-y:auto;">
                <!-- /.box-header -->
                <div class="box-body">
              
                    <table id="fms_table" class="table  table-striped hsn_table fms table-fixed">
                    <thead>
                        <tr style="background-color:#87CEFA;">
                            <td colspan="1"></td>
                            
                            <td colspan="7">Step 1</td>
                            <td colspan="5">Step 2</td>
                            <td colspan="5">Step 3</td>
                            <td colspan="2">Step 4</td>
                            <!-- <td colspan="2">Step 5</td>
                            <td colspan="1">Step 6</td>
                            <td colspan="2">Step 7</td> -->
                            <td colspan="3">Step 8</td>
                            <td colspan="4">Step 9</td>
                            <td colspan="1">Step 10</td>
                            <td colspan="3">Step 11</td>
                            <td colspan="1">Step 12</td>
                            <td colspan="5">Step 13</td>
                        
                        </tr> 
                             <tr>
                                    <td colspan="1">WHAT</td>
                                    <td colspan="7">Raise Indent</td>
                                    <td colspan="5">Raise PR</td>
                                    <td colspan="5">Issue PO</td>
                                    <!-- <td colspan="2">Take approval on PO from MD</td>
                                    <td colspan="2">Follow up with Vendor for confirmation of dispatch</td>
                                    <td colspan="1">Can Purchase happen without waybill?</td> -->
                                    <td colspan="2" id="yellow">Entry of Purchase Way Bill</td>
                                    <td colspan="3" id="red">Entry of material at the gate</td>
                                    <td colspan="4" class="blue">Receive material</td>
                                    <td colspan="1" id="green">Is material received found to be ok?</td>
                                    <td colspan="3">Return of goods Entry		</td>
                                    <td colspan="1">Is material not purchased for direct supply?</td>
                                    <td colspan="5">Entry of Direct Dispatch Details				</td> 
                                  
                                </tr> 
                             <tr>
                                    <td>WHO</td>
                                    <td colspan="7">Production Manager/Dispatch Manager (Sachin Vaish)/Binding Supervisor</td>
                                    <td colspan="5">Store Manager</td>
                                    <td colspan="5">Purchase Manager: Arvind Mishra</td>
                                    <td colspan="2">EA to MD</td>
                                    <!-- <td colspan="2">by PC	</td>
                                    <td colspan="1">PC1</td>
                                    <td colspan="2">Satyendra Singh</td> -->
                                    <td colspan="3">Security Guard</td>
                                    <td colspan="4" id="yellow">Store Head</td>
                                    <td colspan="1" id="red">Purchasing Authority</td>
                                    <td colspan="3" class="blue">Purchase Manager:Arvind Mishra		</td>
                                    <td colspan="1" id="green">PC1</td>
                                    <td colspan="5">PC1				</td>
                                  
                                </tr> 
                                <tr>
                                        <td>HOW</td>
                                        <td colspan="7">Indent Google Form</td>
                                        <td colspan="5">PR Google Form</td>
                                        <td colspan="5">PO Google Form</td>
                                        <td colspan="2">PO Google Form for Approval</td>
                                        <!-- <td colspan="2">on telephone/mail</td>
                                        <td colspan="1">Telephone/ mail/ person/watsapp</td>
                                        <td colspan="2">Waybill Entry Google Form</td> -->
                                        <td colspan="3">Material Inwarding Form</td>
                                        <td colspan="4" id="yellow">Goods Receipt Note Google Form</td>
                                        <td colspan="1" id="red">In person</td>
                                        <td colspan="3" class="blue">Purchase Return Request Google Form		</td>
                                        <td colspan="1" id="green">Autometic as Per PR. Entry</td>
                                        <td colspan="5">Asking Dispatch manager on telephone/in person				</td>
                                     
                                    </tr>
                                    <tr>
                                            <td>WHEN</td>
                                            <td colspan="7">Whenever required or within 1 day of receiving Job Card</td>
                                            <td colspan="5">Within 2 hours of receiving Indent</td>
                                            <td colspan="5">within 1 day of Indent Receipt</td>
                                            <td colspan="2">within 1 day of issuance of PO</td>
                                            <!-- <td colspan="2">1 day before expected delivery date</td>
                                            <td colspan="1">on day of dispatch</td>
                                            <td colspan="2">On Expected Delivery Date</td> -->
                                            <td colspan="3">On entry of material at gate</td>
                                            <td colspan="4" id="yellow">within 1 hour of receipt of material at gate</td>
                                            <td colspan="1" id="red">within 1 day of material receipt</td>
                                            <td colspan="3" class="blue">within 1 day of material receipt		</td>
                                            <td colspan="1" id="green">On dispatch date	</td>
                                            <td colspan="5">On dispatch date				</td>
                                        
                                    </tr> 
                        <tr style="background-color:#87CEFA;">
                          <td><b>Indent No.</b></td>
                          <td><b>Indent Date</b></td>
                          <td><b>Requested by</b></td>
                          <td><b>Indent Type</b></td>
                          <td><b>IO No.</b></td>
                          <td><b>Job Name</b></td>
                          <td><b>"To be shipped to(If Direct Supply)</b></td>
                          <td><b>Item Required by date</b></td>
                          <td><b>Pln</b></td>
                          <td><b>Ac</b></td>
                          <td><b>PR No.</b></td>
                          <td><b>Item Description</b></td>
                          <td><b>Qty Required</b></td>
                         
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>PO No.</b></td>
                          <td><b>PO Supplier</b></td> 
                          <td><b>Expected Delivery Date</b></td> 
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                         <!-- //step 5 -->
                         
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Gate Entry No.</b></td>
                          
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>GRN No.</b></td>
                          <td><b>Bill No.</b></td>

                          <td><b>Yes/No?</b></td>

                         <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>PRR No.</b></td>
                          <td><b>Yes/No?</b></td>

                          <td><b>Supplied to</b></td>
                          <td><b>Supply Date</b></td>
                          <td><b>Supply Qty</b></td>
                          <td><b>Supply Internal Order</b></td>
                          <td><b>Supply Challan No.</b></td>

                         

                          <!-- <td><b>Remarks</b></td>  -->
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