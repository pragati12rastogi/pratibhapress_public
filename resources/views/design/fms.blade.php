@extends($layout)

@section('title', 'Design FMS')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Design FMS</a></li> 
@endsection
@section('css')

<link rel="stylesheet" href="/css/responsive.bootstrap.css">    

@endsection
@section('js')
{{-- <script src="/js/dataTables.responsive.js"></script> --}}
<meta name="csrf-token" content="{{ csrf_token() }}" />
  <script>
    $(document).ready(function() {
      $('.yellow').parent().addClass('yellow');
      $('.red').parent().addClass('red');
    // $(document).ready(
    //         function() {
    //             setInterval(function() {
    //                 var randomnumber = Math.floor(Math.random() * 100);
    //                 $('#hsn_table').text(
    //                         'I am getting refreshed every 3 seconds..! Random Number ==> '
    //                                 + randomnumber);
    //             }, 3000);
    //         });
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $('#fms_table').DataTable({
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
            "url": "/design/fms/api",
            "type": "POST",
            "data": {'_token': CSRF_TOKEN},
        },
          "columns": [
            //step1
            {"data":"do_number"},
            {"data":"created"},
            {"data":"referencename"},
            {"data":"do_io"},

            {"data":"item"},
            {"data":"creative"},
            {"data":"no_pages"},
            // {"data":"planned_dw"},
            {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.planned_dw;
                      dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+1;
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
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                      var now=new Date();
                      var ac=dateString+' '+h+':'+mi+':'+ss+' '+d;
                        return ac;
                        
                    }
                },
            {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt = data.actual_dw;
                      var dt1 = data.planned_dw;
                      dt1=new Date(dt1);
                      var jc=data.work_alloted_number;
                      dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth() +1;
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
                        var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                      var now=new Date();
                      var ac=dateString+' '+h+':'+mi+':'+ss+' '+d;
            
                      if ((jc) && (dt1>dt) )
                      { return "<div class='green'>"+ac+"</div>"}
                      else if ((jc) && (dt1<dt) )
                      {return "<div class='red'>"+ac+"</div>"}
                      else if ((!jc) && (dt1>dt) )
                      {return "<div class='red'>-</div>"}
                      else if ((!jc) && (dt1<dt) )
                      {return "<div class='white'>-</div>"}
                      
                      else
                      {return "<div class='red'>-</div>"}  
                      // return dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.work_alloted_number;
                      if(desc){
                        return desc.replace(/,/g,'<br>');;
                      }
                      else{
                        return "-";
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.emp;
                      if(desc){
                        return desc.replace(/,/g,'<br>');;
                      }
                      else{
                        return "-";
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.description;
                      if(desc){
                        return desc.replace(/,/g,'<br>');;
                      }
                      else{
                        return "-";
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.work;
                      if(desc){
                        return desc.replace(/,/g,'<br>');;
                      }
                      else{
                        return 0;
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var dt=new Date(); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth() +1;
                      var yyyy=dt.getFullYear();
                      var hh=19;
                      var mi=00;
                      var ss=00;
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
        
                      return dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                    } 
                },
            {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {

                      var dates = data.actual_dws;
                      if(dates){
                        dates=dates.split(',');
                      }
                      else{
                        dates=0;
                      }
                      var x='';
                     
                      // return dates;
                      var jc=data.work_alloted_number;
                      for(var i=0;i<dates.length;i++){
                        var dt=dates[i];
                        // console.log(dt);
                        
                        if(dt){
                          dt=new Date(dt); 
                          var dd=dt.getDate();
                          var mm=dt.getMonth() +1;
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
                              var dateString = (dd <= 9 ? '0' + dd : dd) + '-' + (mm <= 9 ? '0' + mm : mm) + '-' + yyyy;
                          var now=new Date();
                          var nowdd=now.getDate();
                          var nowmm=now.getMonth()+1;
                          var nowyyyy=now.getFullYear();
                          var nowhh=19;
                          var nowmi=00;
                          var nowss=00;
                          var nowd = "AM";
                          var nowh = hh;
                            if (nowh >= 12) {
                              nowh = nowhh - 12;
                              nowd = "PM";
                            }
                            if (nowh == 0) {nowh = 12;}
                            var nowdateString = (nowdd <= 9 ? '0' + nowdd : nowdd) + '-' + (nowmm <= 9 ? '0' + nowmm : nowmm) + '-' + nowyyyy;
                            var ac=dateString+' '+h+':'+mi+':'+ss+' '+d;
                            var ac1=nowdateString+' '+nowh+':'+nowmi+':'+nowss+' '+nowd;
                            var acc=nowdateString;
                            var cc=dateString;
                          
                         if ((jc) && (ac1<ac) && (acc!=cc))
                            { x= x+"<div class='red'>"+ac+"</div>"}
                        else  if ((jc) && (acc==cc))
                          {return "<div class='green'>"+ac+"</div>"}
                        else if ((jc) && (ac1>ac))
                        {x=x+ "<div class='red'>"+ac+"</div>"}
                        else if ((!jc) && (ac1<ac))
                        {x=x+ "<div class='white'>"+ac+"</div>"}
                        else
                        {x=x+ "<div class='white'>-</div>"}  ;
                      }
                      else{
                        {x=x+ "<div class='red'>-</div>"} 
                      }
                    
                      }
                      return x;
                    }  
                      
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.status;
                      if(desc){
                        return desc.replace(/,/g,'<br>');
                      }
                      else{
                        return "-";
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var desc=data.work_no_pages_done;
                      if(desc){
                        return desc.replace(/,/g,'<br>');
                      }
                      else{
                        return "-";
                      }
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      
                        if(data.do_st && data.do_st==7){
                          return data.a;
                        }
                        else{
                          return "-";
                        }
                      
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var ac=data.a;
                      var std=data.do_status_date;
                      ac1=new Date(ac);
                      std1=new Date(std);
                      var now =new Date();
                     
                        if((std && ac) && std1 > ac1){
                          return "<div class='yellow'>"+std+"</div>";
                        }
                        else if((std && ac) && (std1 < ac1)){
                          return "<div class='red'>"+std+"</div>";
                        }
                        else if((ac && !std) && (ac1<now)){
                          return "<div class='red'>-</div>";
                        }
                        else if((ac && !std) && (ac1>now)){
                          return "<div class='white'>-</div>";
                        }
                        else{
                          return "<div class='white'>-</div>";
                        }

                    
                      
                       
                      
                    }
                },
                {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      
                        return data.do_status;
                      
                    }
                },
            //     {
            //   data:function(data, type, full, meta){
            //       var pages_assign=data.work_no_pages_assign;
            //       var no_pages_done=data.work_no_pages_done;
            //       if(data.work_no_pages_done)
            //         return pages_assign-no_pages_done;
            //       else
            //       return parseInt(pages_assign);
            //     } 
            // },
            ],
            "columnDefs": [
             
              {"scrollY": "500px"}, 
              {"scrollX": false},
             
              
            ]
        });

    });
   // $('.dataTables_scrollHead').css('display','none'); 

   
  </script>
@endsection
<style>

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
          height: 10px;
        }
     
        .hsn_table tbody tr td{
          height: 5px;
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
    padding-right: 120px !important;
}
        .red{
          background-color:red;
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
                    <thead class="fixed-header">
                        <tr style="background-color:#87CEFA;">
                            <td></td>
                            <td colspan="6">Step 1</td>
                            <td colspan="6">Step 2</td>
                            <td colspan="4">Step 3</td>
                           
                            <td colspan="3">Step 4</td>
                        </tr> 
                             <tr>
                                    <td>WHAT</td>
                                    <td colspan="6">Engage Design Order</td>
                                    <td colspan="6">Allotment of Design Work</td>
                                    <td colspan="4">Updation of Work Allotment Status			</td>
                                    <td colspan="3">Design Order Status Update			</td>
                                </tr> 
                             <tr>
                                    <td>WHO</td>
                                    <td colspan="6">Design Coordinator</td>
                                    <td colspan="6">Design Coordinator</td>
                                    <td colspan="4">Designer			</td>
                                    <td colspan="3">Design Coordinator			</td>
                                </tr> 
                                <tr>
                                        <td>HOW</td>
                                        <td colspan="6">Design Order Google Form</td>
                                        <td colspan="6">On Design Work Allotment Google Form</td>
                                        <td colspan="4">On Design Work Allotment Status Google Form			</td>
                                        <td colspan="3">On Design Order Status Google Form			</td>
                                    </tr>
                                    <tr>
                                            <td>WHEN</td>
                                            <td colspan="6">On receipt of creative from client/Information by Sales Team</td>
                                    <td colspan="6">within 1 hours of Design Order</td>
                                    <td colspan="4">Everyday till order closed</td>
                                    <td colspan="3">Within One Hour of Design work over</td>
                                    </tr> 
                        <tr style="background-color:#87CEFA;">
                          
                          <td><b>Design Order Number</b></td>
                          <td><b>Date & Time</b></td>
                          <td><b>Client Name</b></td>
                          <td><b>I.O. Number</b></td>
                          <td><b>Item Name</b></td>
                          <td><b>Creative Name</b></td>
                          <td><b>Nos. of pages</b></td>

                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Work Allotment No.</b></td>
                          <td><b>Work Alloted to</b></td>
                          <td><b>Work Description</b></td>
                          <td><b>Nos of Pages Alloted</b></td>

                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Status</b></td>
                          <td><b>Nos of Pages Done</b></td>


                          <td class="plan"><b>Pln</b></td>
                          <td class="actual"><b>Ac</b></td>
                          <td><b>Order Status</b></td>
                          {{-- <td><b>I.O. Number</b></td> --}}
                    
                        </tr>
                    </thead>
                    <tbody class="fixed-body">
                        
                    </tbody>
               
                  </table>
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection