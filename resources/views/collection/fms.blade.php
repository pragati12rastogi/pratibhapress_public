@extends($layout)

@section('user', Auth::user()->name)

@section('title','Collection FMS')
@section('css')
<style>
table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting {
    padding-right: 60px !important;
}

.CB{
  background-color:yellow;
 
 
}
.FUD{
  background-color:green;
 
  
}
.DSPT{
  background-color:red;
  
  
}
.NC{
  background-color:aqua;
 

}
.PTP{
  background-color:#CC6600;
 

}
.NR{
  background-color:#A0A0A0;

 
}
.DNC{
  background-color:#E0E0E0;

 
}

.hsn_table {
          width: 100%;
          /* overflow-x: scroll; */
        }
        #fms_table {
          width: 100%;
          /* overflow-x: auto; */
        }

        .dot {
  height: 25px;
  width: 25px;
  /* background-color: #bbb; */
  border-radius: 50%;
  display: inline-block;
}
.col-sm-6 {
    width: 25%;
}

</style>
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script>
     var selected=[];
     var month = $('#month').val();
     if(month!=''){
       var date = new Date(month);
     }
     else{
      var date = new Date();
     }
     var mm=date.getMonth()+1;
     var yy=date.getFullYear();
     var col_length =new Date(yy,mm,0).getDate();
     var col_data = 'd';
    //  console.log(month);
     

   var dataTable;
    function datatablefn() {

      var str = [];
      // str.push({"data":"emp_id"});
      str.push({
                  "targets": [ -1 ],
                  "data":"name", "render": function(data,type,full,meta) {
                    return data;
                  },
                  "class":"party",
                  "orderable": false
              });
      str.push({"data":"contact_person"});
      str.push({"data":"contact"});
      str.push({"data":"email"});
      str.push(        { 
                "data":"invoice_number","render": function(data, type, full, meta){
                  if(data)
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            });
      str.push({"data":"total_amount","render": function(data, type, full, meta){
                  if(data)
                  return data.toFixed(2);
                  else
                    return "";
                } 
            });
      str.push( { 
                data:function(data, type, full, meta){
                  var payment_date=data.payment_date;
                  var z=new Array();
                 
                    payment_date=payment_date.split(',');
                    // console.log(payment_date);
                    
                    for(var i=0;i<payment_date.length;i++)
                    {
                      if(payment_date[i]!="-")
                      {
                          var start=new Date(payment_date[i]);
                          var dd=start.getDate();
                          var mm=start.getMonth()+1;
                          var yyyy=start.getFullYear();
                          var days=dd+'-'+mm+'-'+yyyy;
                          z[i]=days;
                      }
                    else{
                      z[i]=payment_date[i];
                    }
                  }
                  z=z.join(',');
                  return z.replace(/,/g,'<br>');
                } 
            });
      str.push({"data":"left_amt","render": function(data, type, full, meta){
                  if(data)
                  return data.toFixed(2);
                  else
                    return "";
                } 
            });
      str.push({ 
                data: function(data, type, full, meta){
                  var payment_date=data.payment_date;
                  var z=new Array();
                  var left_amt=data.left_amt;
                  
                    payment_date=payment_date.split(',');
                    // console.log(payment_date);
                    
                    for(var i=0;i<payment_date.length;i++)
                    {
                      if(payment_date[i]!="-")
                      {
                        if(left_amt>1)
                        {
                          var start=new Date(payment_date[i]);
                          var end   = new Date();
                          if(start>end){
                            var diff  = new Date(start - end);
                          }
                          else{
                            var diff  = new Date(end - start);
                          }
                          var days = diff/1000/60/60/24;
                          z[i]=Math.round(days);
                        }
                      }
                    else{
                      z[i]=payment_date[i];
                    }
                  }
                  // console.log(z);
                  z=z.join(',');
                  return z.replace(/,/g,'<br>');
                } 
            });
            var yr=$('#month').val();
            var mm=yr.split('-');

            var dd=getDaysInMonth(mm[0], mm[1]);
            mm[1]=mm[1]-1;
            var date = new Date(mm[0], mm[1], 1);  // 2009-11-10
            var month = date.toLocaleString('default', { month: 'short' });
      for(var i = 1 ; i <= dd ; i++)
      {
          var d=0;
        str.push(
      
                  {
                    "data":col_data+i,"render": function(data, type, full, meta)
                    { 
                      d=data;
                      var x='divs';
                      if(data)
                        return "<div class="+data+">"+data+"</div>";
                      else 
                        return "";
                      
                    },
                    "orderable": false 
            }
            );
      }
      // console.log(str);
      
      // 

    if(dataTable){
      dataTable.destroy();
    }
    var month = $('#month').val();
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
       dataTable = $('#admin_table').DataTable({
        
          "ajax": {
            "url": "/collection/fms/api",
            "datatype": "json",
            "type": "post",
                "data": {'_token': CSRF_TOKEN,'month':month},

            },
            "serverSide": true,
          "autoWidth": false,
          "fixedColumns":   {
            "leftColumns": 1
        },
        "pagingType": "full_numbers",
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "scrollY":        "300px",
        "scrollX":        true,
        "scrollCollapse": true,
          "drawCallback": function( settings ) {
            var api = this.api();
                // console.log( api.rows( {page:'current'} ).data() );
              for(var i =1 ; i<=col_length ; i++){
                // console.log(api.rows( {page:'current'} ).data() );
                
               
              }
          
          },
        
            
          "columns": str,
          "columnDefs": [ {
            "targets": '_all',
          "createdCell": function (td, cellData, rowData, row, col) {
            if ( cellData == "CB" ) {
              $(td).css('background-color', 'yellow')
            }
            if ( cellData == "FUD" ) {
              $(td).css('background-color', 'green')
            }
            if ( cellData == "DSPT" ) {
              $(td).css('background-color', 'red')
            }
            if ( cellData == "NC" ) {
              $(td).css('background-color', 'aqua')
            }
            if ( cellData == "PTP" ) {
              $(td).css('background-color', '#CC6600')
            }
            if ( cellData == "NR" ) {
              $(td).css('background-color', '#A0A0A0')
            }
            if ( cellData == "DNC" ) {
              $(td).css('background-color', '#E0E0E0')
            }
            
          }
        } ]
       });
  
     
}

   $(document).ready(function() {
    var yr=$('#month').val();
     
     if(dataTable){
     dataTable.destroy();
   }
 var mm=yr.split('-');

 var dd=getDaysInMonth(mm[0], mm[1]);
 mm[1]=mm[1]-1;
 var date = new Date(mm[0], mm[1], 1);  // 2009-11-10
 var month = date.toLocaleString('default', { month: 'short' });
 $('.tb').empty();
        var ls='<table id="admin_table" class="table  table-bordered table-striped hsn_table fms table-fixed " style="width:100%"><thead>'+
            '<tr>'+
            '<th class="first">Party Name</th>'+
            '<th class="first">Contact Person</th>'+
            '<th class="first">Contact No.</th>'+
            '<th class="first">Email</th>'+
            '<th class="first">Bill No.</th>'+
            '<th class="first">Bill Total Amt.</th>'+
            '<th class="first">Payment Date</th>'+
            '<th class="first">OverDue Amount</th>'+
            '<th class="first">OverDue By</th>';
            for(var i=1;i<=dd;i++){
            if(i<10){
              var j="0"+i;
            }
            else{
              j=i;
            }
              ls=ls+'<th>'+j+" "+month+'</th>';
            }
      
           ls=ls+ '</tr>'+
        '</thead>'+
        '<tbody></tbody></table>';
    $(".tb").append(ls);
    datatablefn();
    $('#admin_table[tr][td][div][class=DNC]').parent().css("background-color","#E0E0E0");
   
    
   // $( "#admin_table" ).parent().css( "overflow-x", "auto" );

  });
  var getDaysInMonth = function(month,year) {
 return new Date(year, month, 0).getDate();
};
     
   $('#month').datepicker({
    autoclose: true,
      format: 'yyyy-mm',
}).datepicker("setDate", new Date());

  $('#month').on('change', function () {

    var yr=$('#month').val();
     
          if(dataTable){
          dataTable.destroy();
        }
        var mm=yr.split('-');

var dd=getDaysInMonth(mm[0], mm[1]);
mm[1]=mm[1]-1;
var date = new Date(mm[0], mm[1], 1);  // 2009-11-10
var month = date.toLocaleString('default', { month: 'short' });
$('.tb').empty();
        var ls='<table id="admin_table" class="table  table-bordered table-striped hsn_table fms table-fixed " style="width:100%"><thead>'+
            '<tr>'+
            '<th class="first">Party Name</th>'+
            '<th class="first">Contact Person</th>'+
            '<th class="first">Contact No.</th>'+
            '<th class="first">Email</th>'+
            '<th class="first">Bill No.</th>'+
            '<th class="first">Bill Total Amt.</th>'+
            '<th class="first">Payment Date</th>'+
            '<th class="first">OverDue Amount</th>'+
            '<th class="first">OverDue By</th>';
            for(var i=1;i<=dd;i++){
            if(i<10){
              var j="0"+i;
            }
            else{
              j=i;
            }
              ls=ls+'<th>'+j+" "+month+'</th>';
            }
   
                 
           ls=ls+ '</tr>'+
        '</thead>'+
        '<tbody></tbody></table>';
    $(".tb").append(ls);
    datatablefn();
          });

    for (i = new Date().getFullYear(); i > 1900; i--) {
      $('#year').append($('<option />').val(i).html(i));
  }
 
  </script>
@endsection

@section('main_section')
<section class="content">
            <div id="app">

        <!-- Default box -->
        <div class="row">
                 
                   
                 <div class="col-md-12">
                 <div class="col-md-2"><span class="dot CB"></span>Call Back</div>
                  <div class="col-md-2"><span class="dot DSPT"></span>Dispute</div>
                  <div class="col-md-3"><span class="dot NC"></span>Ringing/Not Contactable</div>
                  <div class="col-md-2"><span class="dot PTP"></span>Promise To Pay</div>
                  <div class="col-md-2"><span class="dot NR"></span>Not required</div>
                 </div>
                

              </div>
                <div class="row">
                <div class="col-md-12">
                <div class="col-md-2"><span class="dot DNC"></span>Did not call</div>
                    <div class="col-md-2"><span class="dot FUD"></span>Follow up Done</div>
                    <div class="col-md-2"></div>
                    <div class="col-md-2"></div>
                    <div class="col-md-3" >
                            <label>Select Month</label>
                            <input type="text" name="month" id="month" class="input-css" autocomplete="off">
                    </div>
                </div>
                </div>
        <div class="box box-primary">
            <!-- /.box-header -->
            <div class="box-header with-border">
<!--                    <h3 class="box-title">{{__('customer.mytitle')}} </h3>-->
                </div>  
                <div class="box-body">

                </div>
                    <div class="tb">
                      <table id="admin_table" class="table  table-bordered table-striped hsn_table fms table-fixed " style="width:100%">
                          <thead>
                          
                          </thead>
                          <tbody>

                          </tbody>
                
                      </table>
                    </div>
              
                 
           
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection
