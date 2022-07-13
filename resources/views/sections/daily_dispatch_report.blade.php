@extends($layout)

@section('title', 'Daily Dispatch Planned Report')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Daily Dispatch Planned Report</a></li> 
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
   input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
</style>

@endsection
@section('js')
<script src="/js/Production/platebypress_creation.js"></script>

<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
      $("#date_f").val($('#selectDate').val());
      fulltable();
      $('#plate tbody').on('click', 'button.job_det', function () {
          debugger;
        var tr = $(this).parents('tr');
        var row = dataTable.row( tr );
        var data=$(this).attr("id");
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
          if(last_ele)
          {
            //  last_ele.child.hide();     
          }
          $(this).parents('li').children('div').remove();
                
          $(this).parents('li').append('<center><div class="card" ><h5> Processing...</h5></div></center>');
              
          row.child('<center><div class="card" ><h5> Processing...</h5></div></center>').show();
          getdata1(data,row,this)

          last_ele=row;
          last_tr=tr;
          tr.addClass('shown');
        }
    } );
    });
    $('#selectDate').change(function(){
      $("#date_f").val($('#selectDate').val());
      dataTable.draw();
    })
    function fulltable(){
      if(dataTable){
        dataTable.destroy();
      }
      
      dataTable = $('#plate').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/dispatch/daily/planned/report/list/api",
            "datatype": "json",
                "data": function (data) {
                    var selectDate = $('#selectDate').val();
                    data.selectDate = selectDate;
                }
          },
          "createdRow": function( row, data, dataIndex){
                var qty=data.day_dc_qty;
                if(qty){
                  var ratio=qty.split(',');
                var x=0;
                console.log(ratio);
                for(var i=0;i<ratio.length;i++){
                  var z=ratio[i].split(':');
                  x=parseInt(x)+parseInt(z[1]);
                }
                
                if( data.qty <=  x){
                    $(row).addClass('bg-green-gradient');
                    
                }
                }
            },
          "columns": [
            {"data":"io_number"},
            {"data":"referencename"},
            {"data":"itemss"},
            {"data":"io_qty"},
            {"data":"dispatch_qty"},
            {"data":"remaining_qty"},
            {"data":"qty"},
            {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                    var qty=data.day_dc_qty;
                if(qty){
                  var ratio=qty.split(',');
                var x='';
                console.log(ratio);
                for(var i=0;i<ratio.length;i++){
                  var z=ratio[i].split(':');
                  if(x==''){
                    x=z[0];
                  }
                  else{
                    x=x+'<br>'+z[0];
                  }
                  
                }
                  return x;
              
                }
                else{
                  return "";
                }
                  }
              },
              {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                    var qty=data.day_dc_qty;
                if(qty){
                  var ratio=qty.split(',');
                var x='';
                console.log(ratio);
                for(var i=0;i<ratio.length;i++){
                  var z=ratio[i].split(':');
                  if(x==''){
                    x=z[1];
                  }
                  else{
                    x=x+'<br>'+z[1];
                  }
                  
                }
                  return x;
              
                }
                else{
                  return "";
                }
                  }
              },
              {
                  "targets": [ -1 ],
                  data:function(data,type,full,meta)
                  {
                    var mat=data.mat;
                    var ret=data.ret;
                    var x='';
                    var y='';
                if(mat){
                  var ratio=mat.split(',');
                console.log(ratio);
                for(var i=0;i<ratio.length;i++){
                  var z=ratio[i].split(':');
                  if(x==''){
                    x=z[1];
                  }
                  else{
                    x=x+'<br>'+z[1];
                  }
                  
                }
                 
                }
                if(ret){
                  var ratio=ret.split(',');
                console.log(ratio);
                for(var i=0;i<ratio.length;i++){
                  var z=ratio[i].split(':');
                  if(y==''){
                    y=z[1];
                  }
                  else{
                    y=y+'<br>'+z[1];
                  }
                  
                }
                 
                }
                  return x+'<br>'+y;
                  }
              },
            {"data":"date"},
            {"data":"priority"},
            {"data":"time"},
           
            ],
            "columnDefs": [
               { "orderable": false, "targets": 1 },
               { "orderable": false, "targets": 7 },
               { "orderable": false, "targets": 8 }
            
            ]
          
        });
    }
    // function getdata1(data,ele,button)  {  
    //   var out;
    //   $('#ajax_loader_div').css('display','block');

    //   $.ajax({
    //            type:'get',
    //            url:"/dispatch/data/"+data,
    //            timeout:600000,
                   
    //            success:function(data) {
    //             $(button).parents('li').children('div').remove();
    //             $(button).parents('li').children('center').remove();
                
    //             $(button).parents('li').append(data);
    //               ele.child(data).show();
    //               $('#ajax_loader_div').css('display','none');

    //             }

    //         });

    //         return out;
    // }
   
</script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        <div class="box">
        <div class="row">
                        <div class="col-md-6 {{ $errors->has('selectDate') ? 'has-error' : ''}}">
                            <label>Date<sup>*</sup></label>
                           <input autocomplete="off" type="text" id="selectDate" class="form-control date-range-filter datepicker" data-date-format="yyyy-mm-dd" placeholder="Date:">
                            {!! $errors->first('selectDate', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div>
                <!-- /.box-header -->
                <div class="box-body">
                  

                    <table id="plate" class="table table-bordered table-striped" style=" width:100% ">
                      <thead>
                        <tr>
                        <th>IO No.</th>
                          <th>Reference</th>
                          <th>Item</th>
                          <th>IO Qty</th>
                          <th>Dispatch Qty</th>
                          <th>Balance Qty</th>
                          <th>To Be Dispatch</th>
                          <th>Delivery Challan</th>
                          <th>Delivery Challan Qty</th>
                          <th>Material Outward No.</th>
                          <th>Dispatch Planned On</th>
                          <th>Priority No.</th>
                          <th>Delivery Time</th> 
                          <!-- <th>Action</th>                      -->
                        
                        </tr>
                      </thead>
                      <tbody class="table">

                      </tbody>
               
                  </table>
                
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      
        </div>
      </section>
@endsection