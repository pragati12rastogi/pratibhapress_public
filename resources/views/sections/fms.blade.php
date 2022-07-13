@extends($layout)

@section('title', 'Order To Collection FMS')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Order To Collection FMS</a></li> 
@endsection
@section('css')

<link rel="stylesheet" href="/css/responsive.bootstrap.css">    

@endsection
@section('js')
{{-- <script src="/js/dataTables.responsive.js"></script> --}}
<meta name="csrf-token" content="{{ csrf_token() }}" />
  <script>
   var dataTable;
    $(document).ready(function() {
      fms(0);
      $('.yellow').parent().addClass('yellow');
      $('.red').parent().addClass('red');

      var last_ele = null ;
    var last_tr = null ;
      $("#asn_table").hide();

      //Initialize Select2 Elements
      $('.select2').select2();

      // $('#select_do').on('select2:select', function (e) {
      //     var year = $('#select_do').val();
      //     if($("#fms_table").is(":hidden"))
      //       $("#fms_table").show();
      //     if(dataTable)
      //       dataTable.destroy();
      //       fms(0);
      //           $('#ajax_loader_div').css('display','none');
      // });
     

    });

 function fms(is_post){
   var url="";
   var str="";  
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var year = $('#select_do').val();
  if(is_post==1){
     str = $( 'form' ).serialize();
    url="/getdata";
  }
  else{
    str='';
    url="/order/to/collection/fms/api";
  }
 
  dataTable =$('#fms_table').DataTable({
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
         
          // "ajax": "/order/to/collection/fms/api",
          "ajax": {
            "url":'/order/to/collection/fms/api' ,
            "type": "post",
            "data": {'_token': CSRF_TOKEN,'year':year,'search':str},
        },
          "columns": [
            //step1
              {"data":"io_number"},
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.io_date+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.reference_name+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.item_category+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.qty+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.io_type+"</div>";
                    }
              },
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      return "<div class='divs'>"+data.delivery_date+"</div>";
                    }
              },
                //doing
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.actual_job_date;
                      var dt = data.planned_job_date;
                      var jc=data.job_number;
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
                      // return dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.actual_job_date;
                      var dt1 = data.planned_job_date;
                      dt3=new Date(dt1);
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
                      var now=new Date();
                      if ((dt) && (dt1>dt) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt) && (dt1<dt) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (!dt) && (dt3>now) )
                      {return "<div class='white divs'>-</div>"}
                      else if ( (!dt) && (dt3<now) )
                      {return "<div class='red divs'>-</div>"}
                      else
                      {return "<div class='red divs'>-</div>"}  
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.job_number;
                      var io_type=data.io_type;
                      if(io_type=="Scrap Sale" || io_type=="Outsource" || io_type=="Supplies" || io_type=="Services"  ){
                        return "<div class='black divs'></div>"
                      }if(!jc){
                        return "<div class='divs'>-</div>";
                      }
                      else{
                        return "<div class='divs'>"+jc+"</div>";
                      }
                        
                      // return dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                    }
                },
              //step 3
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.pur_actual_time;
                      var dt = data.planned_pr_date;
                      var jc=data.job_number;
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
                     
                        if(jc){
                          if((dt1) && (dt1>dt2))
                            { return "<div class='green divs'>"+ac+"</div>"}
                          else if ((dt1) && (dt1<dt2) )
                            {return "<div class='red divs'>"+ac+"</div>"}
                          else if ((!dt1) && (now<dt2) )
                              {return "<div class='white divs'>"+ac+"</div>"}
                          else if ((!dt1) && (now>dt2) )
                              {return "<div class='red divs'>"+ac+"</div>"}
                          else {return "<div class='white divs'>"+ac+"</div>"}
                        }
                        else{
                          {return "<div class='white divs'>-</div>"}
                        }  
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.pur_actual_time;
                      var dt1 = data.planned_pr_date;
                      dt3=new Date(dt1);
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
                      var now=new Date();
                      if ((dt) && (dt1>dt) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt) && (dt1<dt) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (!dt) && (dt3>now) )
                      {return "<div class='white divs'>-</div>"}
                      else if ( (!dt) && (dt3<now) )
                      {return "<div class='red divs'>-</div>"}
                      else
                      {return "<div class='red divs'>-</div>"}  
                    }
                },
                
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var pr=data.pur_req;
                      if(pr){
                        return "<div class='divs'>"+pr.replace(/,/g,'<br>')+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }
                     
                    }
                },  //dummy data
               
              //step 4
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.actual_delivery_date;
                      var dt = data.planned_delivery_date;
                      var jc=data.delivery_challan_number;
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
                      // return dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.actual_delivery_date;
                      var dt1 = data.planned_delivery_date;
                      dt3=new Date(dt1);
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
                      var now=new Date();
                      if ((dt) && (dt1>dt) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt) && (dt1<dt) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (!dt) && (dt3>now) )
                      {return "<div class='white divs'>-</div>"}
                      else if ( (!dt) && (dt3<now) )
                      {return "<div class='red divs'>-</div>"}
                      else
                      {return "<div class='red divs'>-</div>"}  
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.delivery_challan_number;
                      var io_type=data.io_type;
                      if(io_type=="Services"){
                        return "<div class='black'></div>"
                      }
                      else if(jc){
                        return "<div class='divs'>"+jc.replace(/,/g,'<br>')+"</div>";;
                      }
                      else{
                        return "<div class='divs'></div>";
                      }
                     
                    }
                },
              // {
              //       "targets": [ -1 ],
              //       "data" : function(data,type,full,meta)
              //       {
              //         var desc=data.delivery_challan_good_desc;
              //         if(desc){
              //           return desc.replace(/,/g,'<br>');;
              //         }
              //         else{
              //           return "-";
              //         }
                    
              //       }
              //   },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.delivery_challan_good_qty;
                     
                      
                      if(desc){
                        var str=desc.split(',');
                      var j=0;
                      
                      for(var i=0;i<str.length;i++){
                        var x=str[i].split(':');
                        var y=x[1];
                        j=parseFloat(j)+parseFloat(y);
                      }
                        return "<div class='divs'>"+j+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }
                    }
                },
                { //todo
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.delivery_challan_good_qty;
                      var qty=data.qty;
                      if(desc){
                        var str=desc.split(',');
                      var j=0;
                      
                      for(var i=0;i<str.length;i++){
                        var x=str[i].split(':');
                        var y=x[1];
                        j=parseFloat(j)+parseFloat(y);
                      }
                        return "<div class='divs'>"+(qty-j)+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }

                    }
                },
              //step 5
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.actual_cpo_date;
                      var dt = data.planned_cpo_date;
                      var jc=data.delivery_challan_number;
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
                      // return dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.actual_cpo_date;
                      var dt1 = data.planned_cpo_date;
                      dt3=new Date(dt1);
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
                      var jc=data.is_po_provided;
                      var now=new Date();
                      if ((dt) && (dt1>dt) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt) && (dt1<dt) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (!dt) && (dt3>now) )
                      {return "<div class='white divs'>-</div>"}
                      else if ( (!dt) && (dt3<now) )
                      {return "<div class='red divs'>-</div>"}
                      else
                      {return "<div class='red divs'>-</div>"}  
                    }
                }, 
              { 
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var is_po=data.is_po_provided;
                      var po_num=data.po_number;
                      
                      if((is_po==1)){
                        return "<div class='divs'>"+po_num.replace(/,/g,'<br>')+"</div>";
                      }
                      else if((is_po==0)){
                        return "<div class='divs'>Verbal</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }

                    }
                },
              //invoice  step 6

              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt1 = data.actual_tax_date;
                      var dt = data.planned_tax_date;
                      var jc=data.invoice_number;
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
                      // return dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.actual_tax_date;
                      var dt1 = data.planned_tax_date;
                      dt3=new Date(dt1);
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
                      var now=new Date();
                      if ((dt) && (dt1>dt) )
                      { return "<div class='green divs'>"+ac+"</div>"}
                      else if ((dt) && (dt1<dt) )
                      {return "<div class='yellow divs'>"+ac+"</div>"}
                      else if ( (!dt) && (dt3>now) )
                      {return "<div class='white divs'>-</div>"}
                      else if ( (!dt) && (dt3<now) )
                      {return "<div class='red divs'>-</div>"}
                      else
                      {return "<div class='red divs'>-</div>"}  
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "<div class='divs'>-</div>";
                      
                     
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                      if(io_type=="K Sampling" || io_type=="FOC"){
                        return "<div class='black divs'></div>"
                      }
                      else if(jc){
                        return "<div class='divs'>"+jc.replace(/,/g,'<br>')+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }
                     
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.tax_qty;
                      if(desc){
                        var str=desc.split(',');
                      var j=0;
                      
                      for(var i=0;i<str.length;i++){
                        var x=str[i].split(':');
                        var y=x[1];
                        j=parseFloat(j)+parseFloat(y);
                      }
                        return "<div class='divs'>"+j+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.tax_amount;
                      if(desc){
                        var str=desc.split(',');
                      var j=0;
                      
                      for(var i=0;i<str.length;i++){
                        var x=str[i].split(':');
                        var y=x[1];
                        j=parseFloat(j)+parseFloat(y);
                      }
                        return "<div class='divs'>"+j+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }
                    }
                },
                { //todo
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.tax_qty;
                      var qty=data.qty;
                      if(desc){
                        var str=desc.split(',');
                      var j=0;
                      
                      for(var i=0;i<str.length;i++){
                        var x=str[i].split(':');
                        var y=x[1];
                        j=parseFloat(j)+parseFloat(y);
                      }
                        return "<div class='divs'>"+(qty-j)+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }

                    }
                },
                { //todo
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var sum_qty=data.tax_qty;
                      var desc=data.delivery_challan_good_qty;
                      if(!sum_qty){
                        sum_qty=0;
                        desc=0;
                      }
                      if(!desc){
                        sum_qty=0;
                      }
                      if(desc && sum_qty){
                        var str=desc.split(',');
                        var str1=sum_qty.split(',');
                      var j=0;
                      var j1=0;
                      for(var i=0;i<str.length;i++){
                        var x=str[i].split(':');
                        var y=x[1];
                        j=parseFloat(j)+parseFloat(y);
                      }
                      for(var i=0;i<str1.length;i++){
                        var x1=str1[i].split(':');
                        var y1=x1[1];
                        j1=parseFloat(j1)+parseFloat(y1);
                      }
                        return "<div class='divs'>"+(j-j1)+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }

                    }
                },
              { //todo
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var waybill=data.tax_waybill;
                      var way_dc=data.challan_waybill;
                      if(waybill){
                        var x=waybill;
                        x=x.replace(/ : 1/g,' : Yes');
                        x=x.replace(/ : 2/g,' : Yes');
                        x=x.replace(/ : 3/g,' : Yes');
                        x=x.replace(/ : 0/g,' : No');
                        return "<div class='divs'>"+x.replace(/,/g,'<br>')+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }

                    }
                },
              
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "<div class='divs'>-</div>";
                      
                     
                    }
                },  //dummy data
              //dummy data
              
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "<div class='divs'>-</div>";
                      
                     
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.way_challan_number;
                      var desc1=data.way_tax_number;
                      if(desc1){
                        var str=desc1.split(',');
                      var j;
                      for(var i=0;i<str.length;i++){
                        var x=str[i].split(':');
                        var y=x[1];
                        if(i>0){
                          j=j+","+y;
                        }
                        else{
                          j=y;
                        }
                        
                      }
                        return  "<div class='divs'>"+j.replace(/,/g,'<br>')+"</div>";
                      }
                     
                      else{
                        return "<div class='divs'>-</div>";
                      }
                    }
                },
                //asn grn
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "<div class='divs'>-</div>";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "<div class='divs'>-</div>";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.asn_number;
                      var desc1=data.grn_number;
                      if(desc && desc1){
                        var x=desc + "," +desc1;
                        return  "<div class='divs'>"+x.replace(/,/g,'<br>')+"</div>";
                      }
                      else if(desc && !(desc1)){
                        return "<div class='divs'>"+desc.replace(/,/g,'<br>')+"</div>";
                      }
                      else if(!(desc) && (desc1)){
                        return "<div class='divs'>"+desc1.replace(/,/g,'<br>')+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "<div class='divs'>-</div>";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "<div class='divs'>-</div>";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var doc=data.docket_number;
                      if(doc){
                        return "<div class='divs'>"+doc.replace(/,/g,'<br>')+"</div>";
                      }
                      else{
                        return "<div class='divs'>-</div>";
                      }
                        
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "<div class='divs'>-</div>";
                      
                     
                    }
                },  //dummy data
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.invoice_number;
                      var io_type=data.io_type;
                    
                        return "<div class='divs'>-</div>";
                      
                     
                    }
                },  //dummy data

                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var jc=data.io_status;
                    
                        return "<div class='divs'>"+jc+"</div>";
                      
                     
                    }
                },  //dummy data
            ],
            "columnDefs": [
              {"scrollY": "500px"}, 
              
              {"scrollX": false},
             
              { "orderable": false, "targets": [1,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41] }
            ]
    
          
        });
          // $('.dataTables_length').hide();
          $('#fms_table_filter').hide();
          $('.search').show();
   
 }
 $( "form" ).submit(function( event ) {
 console.log($('.io').val());
 console.log($('.ref').val());
 console.log($('.item').val());
 console.log($('.dc').val());

 var io=$('.io').val();
 var ref=$('.ref').val();
 var item=$('.item').val();
 var dc=$('.dc').val();
  console.log(item);
  
 if(io=="" && ref=="" && item==0 && dc==""){
   alert("Please Fill Any One Input Box For Applying Filter");
    console.log( $( this ).serializeArray() );
  event.preventDefault();
 }
 else{
if(dataTable) 
      dataTable.destroy();
   fms(1);
  console.log( $( this ).serializeArray() );
  event.preventDefault();
 }
  
});
function reset_function(){
  $('.reset').val('');
  $('.item').val('0').select2().trigger("change");
  if(dataTable)
      dataTable.destroy();
  fms(0);
}
  </script>
@endsection
<style>
        .divs{
          width:100px;
        }
        .hsn_table {
          width: 100%;
          /* overflow-x: scroll; */
        }
        #fms_table {
          width: 100%;
          /* overflow-x: auto; */
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
          color:red;
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
                <form  method="post" id="myForm">
                 @csrf 
                 <div class="row search" style="border:1px solid black;display:none"><br>
                  <div class="col-md-2">
                  <input type="search" name="search[internal_order]"  class="input-css reset io"  placeholder="Search:Internal Order">
                  </div>
                  <div class="col-md-2">
                  <input type="search" name="search[reference_name]" class="input-css reset ref"  placeholder="Search:Reference Name">
                  </div>
                  <div class="col-md-2">
                  <select  name="search[item_name]"  class="input-css reset item select2 "  placeholder="Search:Item Name">
                  <option value="0">Search:Item Name</option>
                  @foreach($item as $key)
                    <option value="{{$key->id}}">{{$key->name}}</option>
                  @endforeach
                  </select>
                  </div>
                  <div class="col-md-2">
                  <input type="search" name="search[delivery_challan]" class="input-css reset dc"  placeholder="Search:Delivery Challan">
                  </div>
                  <button type="submit" class="btn-xs btn-primary" style="margin-bottom: 18px;">Apply Filter</button> <button type="button" class="btn-xs btn-primary" style="margin-bottom: 18px;" onclick="reset_function()">Reset</button><br>
                  </div><br>
                 </form>
                    <table id="fms_table" class="table  table-striped hsn_table fms table-fixed">
                    <thead>
                        <tr style="background-color:#87CEFA;">
                            <td></td>
                            <td colspan="6">Step 1</td>
                            <td colspan="3">Step 2</td>
                            <td colspan="3">Step 3</td>
                           
                            <td colspan="5">Step 4</td>
                            <td colspan="3">Step 5</td>
                            <td colspan="8">Step 6</td>
                            <td colspan="1">Step 7</td>
                            <td colspan="3">Step 8</td>
                            <td colspan="3">Step 9</td>
                            <td colspan="3">Step 10</td>
                            {{-- <td colspan="3">Step 11</td>
                            <td colspan="2">Step 12</td>
                            <td colspan="3">Step 13</td> --}}
                            <td colspan="3">Step 14</td>
                        </tr> 
                             <tr>
                                    <td>WHAT</td>
                                    <td colspan="6">Engage Internal Order</td>
                                    <td colspan="3">Creation of Job Card</td>
                                    <td colspan="3">Raise Indent to Store Head for material requirement for the job</td>
                                   
                                    <td colspan="5">Creation of Delivery Challan</td>
                                    <td colspan="3">Creation of Client P.O.</td>
                                    <td colspan="8">Creation of invoice</td>
                                    <td colspan="1" id="yellow">Can dispatch happen witdout Waybill?</td>
                                    <td colspan="3" id="red">Waybill Entry</td>
                                    <td colspan="3" class="blue">ASN/GRN</td>
                                    <td colspan="3" id="green">Invoice Dispatch</td>
                                    {{-- <td colspan="3">Follow up for delivery of material & invoice witd tde client & establish payment date</td>
                                    <td colspan="2">Challan receiving from client to be put back in file</td>
                                    <td colspan="3">Follow up for payment till payment received</td> --}}
                                    <td colspan="3">Closing of Order</td>
                                </tr> 
                             <tr>
                                    <td>WHO</td>
                                    <td colspan="6">CRM: Iffat Rehman</td>
                                    <td colspan="3">GM: Arvind Mishra</td>
                                    <td colspan="3">Sachin Kumar Vaish,Binding Head,Production Head</td>
                                    {{-- <td colspan="3">Binding Head</td>
                                    <td colspan="3">Production Head	</td> --}}
                                   
                                    <td colspan="5">Order Facilitator: Kishan Kumar</td>
                                    <td colspan="3">Order Facilitator: Vikas Chandra</td>
                                    <td colspan="8">Kishan Kumar</td>
                                    <td colspan="1" id="yellow">Automated</td>
                                    <td colspan="3" id="red">Kishan Kumar</td>
                                    <td colspan="3" class="blue">Kishan Kumar</td>
                                    <td colspan="3" id="green">Order Facilitator: VIkas Chandra</td>
                                    {{-- <td colspan="3">CRM: Vaishali Chaurasia</td>
                                    <td colspan="2">Order Facilitator: VIkas Chandra</td>
                                    <td colspan="3">CRM: Vaishali Chaurasia</td> --}}
                                    <td colspan="3">PC</td>
                                </tr> 
                                <tr>
                                        <td>HOW</td>
                                        <td colspan="6">IO Google Form</td>
                                        <td colspan="3">Job Card Google Form</td>
                                        <td colspan="3">PR Google Form</td>
                                        {{-- <td colspan="3">PR Google Form		</td>
                                        <td colspan="3">PR Google Form</td> --}}
                                       
                                        <td colspan="5">Delivery Challan Google Form</td>
                                        <td colspan="3">Client P.O. Google Form</td>
                                        <td colspan="8">Invoice Google Form</td>
                                        <td colspan="1" id="yellow">From Bill Issue Register</td>
                                        <td colspan="3" id="red">Waybill Entry Google Form</td>
                                        <td colspan="3" class="blue">ASN/GRN Entry on Google Form</td>
                                        <td colspan="3" id="green">Invoice Google Form</td>
                                        {{-- <td colspan="3">Telephone/Email</td>
                                        <td colspan="2">Manually by hand</td>
                                        <td colspan="3">Email/telephone</td> --}}
                                        <td colspan="3">On Order closed google form</td>
                                    </tr>
                                    <tr>
                                            <td>WHEN</td>
                                            <td colspan="6">On confirmation of order by Sales Team on whatsapp group</td>
                                            <td colspan="3">Within 2 hours of creation of Internal Order</td>
                                            <td colspan="3">Within 3 hours of receiving Job Card</td>
                                            {{-- <td colspan="3">Within 3 hours of receiving Job Card</td>
                                            <td colspan="3">Within 3 hours of receiving Job Card</td> --}}
                                            
                                       
                                            <td colspan="5">on dispatch of material</td>
                                            <td colspan="3">Before 1 Day of Making Challan</td>
                                            <td colspan="8">Within 30 mins of creation of Delivery Challan</td>
                                            <td id="yellow">Within 5 minutes of creation of invoice</td>
                                            <td colspan="3" id="red">Within 15 minutes of issue of Tax Invoice</td>
                                            <td colspan="3" class="blue">Within 1 day of T.I. Issue</td>
                                            <td colspan="3" id="green">on Dispatch of Material/Within 1 day of creation of Tax Invoice		</td>
                                            {{-- <td colspan="3">Within 2 days of Invoice Dispatch</td>
                                            <td colspan="2">Within 1 day of Delivery Challan Creation</td>
                                            <td colspan="3">Every day till payment is received</td> --}}
                                            <td colspan="3">Within 1 day of Payment received</td>
                                    </tr> 
                        <tr style="background-color:#87CEFA;">
                          <td><b>IO No.</b></td>
                          <td><b>IO Date</b></td>
                          <td><b>Party Name</b></td>
                          <td><b>Item Name</b></td>
                          {{-- <td>Creative Name</td> --}}
                          <td><b>Job Quantity</b></td>
                          <td><b>IO Type</b></td>
                          <td><b>Delivery Date</b></td>
                          <td><b>Pln</b></td>
                          <td><b>Ac</b></td>
                          <td><b>Job Card No.</b></td>
                          <td><b>Pln</b></td>
                          <td><b>Ac</b></td>
                          <td><b>Indent Number</b></td>
                          {{-- <td class="plan"><b>Pln</b></td>
                          <td class="plan"><b>Ac</b></td>
                          <td><b>P.R. Number</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>P.R. Number</b></td> --}}
                         
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Challan No.</b></td>
                          {{-- <td>Challan Description</td> --}}
                          <td><b>Challan quantity</b></td>
                          <td><b>Balance quantity</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Client P.O. Number</b></td>
                          <td><b>Pln</b></td>
                          <td><b>Ac</b></td>
                          <td><b>PO No.</b></td>
                          <td><b>Invoice No.</b></td>
                          <td><b>Invoice quantity</b></td>
                          <td><b>Invoice Amount</b></td>
                          <td><b>Balance Unbilled Order Quantity</b></td>
                          <td><b>Balance Unbilled Dispatched Quantity</b></td>
                          <td><b>Yes/NO?</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Waybill No.</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>ASN/GRN No.</b></td>
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Docket No.</b></td>
                          {{-- <td class="plan">Pln</td>
                          <tdo-sort">Ac</td>
                          <td>Payment date</td>
                          <td class="plan">Pln</td>
                          <td class="actual">Ac</td>
                          <td>Payment Status</td>
                          <td>Payment Amount</td>
                          <td>Payment Balanace</td> --}}
                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Status</b></td> 
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