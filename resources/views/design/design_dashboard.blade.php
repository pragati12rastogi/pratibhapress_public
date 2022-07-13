@extends($layout)

@section('title', 'Design Dashboard')


@section('breadcrumb')
    <li><a href="#"><i class=""></i> Design Dashboard</a></li>
 <style>

 .col{
        /* margin-top: 10px;
        margin-bottom: 10px; */
        border: 2px solid black;
 }
 .select2-container{
        margin-top: 10px;
    margin-bottom: 1px;
  
 }
 .select2-container--default .select2-selection--single{
        background-color:lemonchiffon;
 }
 table thead th{
        border: 2px solid black;
        background-color: royalblue;
        height: 30px;
        text-align: center;
        color: white;
      
 }
 table tbody tr td{
        border: 1px dotted black;
        background-color: lightblue;
        height: 30px;
        text-align: center;
      
 }
 .table tr td{
        border: 1px dotted black;
        background-color: lightblue;
        height: 30px;
        width: 50%;
        text-align: left;
        font-weight: 700;
        text-indent: 4%;

      
 }
 .table tr th{
        border: 1px dotted black;
        background-color: royalblue;
        height: 30px;
        width: 50%;
        text-align: left;
        color: white;
        text-indent: 4%;
      
 }
 table{
        width: 100%;
       
 }
 .table{
        width: 100%;
        border: 2px solid black;
 }
 .label{
        border: 2px solid black;
        text-align:center;
        display: inline-block;
        font-weight: 700;
        color: black;
        font-size: 14px;
        margin-bottom: 0px;
        background-color: chartreuse;
 }
 .label1{
         background-color:red;
 }
 a{
         color:green;
         font-weight: 700;
 }
 a:hover{
         color:green;
         font-weight: 700;
 }
 </style>  
@endsection
@section('js')
{{-- <script src="/js/Design/design_order.js"></script> --}}
<script>
      var emp_name;
   $('.designer').change(function(e){ 
       emp_name = $(this).find("option:selected").val();
        if(emp_name!=0){
        $('#ajax_loader_div').css('display','block');
            $.ajax({
                url: "/design/dashboard/order/" + emp_name,
                type: "GET",
                success:function(result) {
                        console.log(result);
                        var designopen=result.design;
                        // var designpending=result.design_open;
                        var dt =new Date();
                        console.log(dt);
                        var dd=dt.getDate();
                        var mm=("0" + (dt.getMonth() + 1)).slice(-2);
                        var yyyy=dt.getFullYear();
                        var ac=dd+'-'+mm+'-'+yyyy;
                     
                        $('.do').empty();
                        $('table[class=work_allot] tbody').empty();
                        
                        $('.do').append('<option value="">Select Work Allot Number</option>');
                        for(var i=0;i<designopen.length;i++){
                                $('.do').append("<option value="+designopen[i].id+">"+designopen[i].work_alloted_number+"</option>")   ;
                                
                                
                                var ls= '<tr>'+
                                       
                                        '<td>'+designopen[i].work_alloted_number+'</td>'+
                                        '<td>'+designopen[i].value+'</td>'+
                                        '<td>'+designopen[i].description+'</td>'+
                                        '<td>'+designopen[i].no_pages+'</td>'+
                                        '<td>'+designopen[i].date+'</td>';
                                        if(designopen[i].st == ac){
                                                ls=ls+ '<td><a href="/design/work/status/'+designopen[i].work_id+'" target="_blank"><u style="color:green">Update Status</u></a></td></tr>'; 
                                        }
                                        else{
                                                ls=ls+ '<td><a href="/design/work/status/'+designopen[i].work_id+'" target="_blank"><u style="color:red">Update Status</u></a></td></tr>';
                                        }
                                       
                                        $('table[class=work_allot] tbody').append(ls);                    
                               
                        }
            
                
                    $('#ajax_loader_div').css('display','none');
                }
        });
   }
    });

    $('.do').change(function(e){
       var doss = $(this).find("option:selected").val();
               if(doss!=0){
        $('#ajax_loader_div').css('display','block');
            $.ajax({
                url: "/design/dashboard/api/" + doss + "/" + emp_name,
                type: "GET",
                success:function(result) {
                   var alldesign=result.design_details   ;
                   var design=result.design_other;
                   var designall=result.design_status;
           
                   console.log(alldesign);
                   
                   $('table[class=other_allotment] tbody').empty();
                   for (var i = 0; i < design.length; i++) {
                           var name="";
                           var work_alloted_number="";
                           var values="";
                           var no_pages='';
                           var work_no_pages_done=0;
                           var created='';
                           if(design[i].name!=null){
                                   name=design[i].name;
                           }
                           if(design[i].work_alloted_number!=null){
                                   work_alloted_number=design[i].work_alloted_number;
                           }
                           if(design[i].value!=null){
                                   values=design[i].value;
                           }
                           if(design[i].no_pages!=null){
                                   no_pages=design[i].no_pages;
                           }
                           if(design[i].work_no_pages_done!=null){
                                   work_no_pages_done=design[i].work_no_pages_done;
                           }
                           if(design[i].created!=null){
                                   created=design[i].created;
                           }
                           if(design[i].work_no_pages_done==null && design[i].no_pages==null){
                                        work_no_pages_done='';
                           }
                        $('table[class=other_allotment] tbody').append(
                                '<tr>'+
                                        '<td>'+name+'</td>'+
                                        '<td>'+work_alloted_number+'</td>'+
                                        '<td>'+values+'</td>'+
                                        '<td>'+no_pages+'</td>'+
                                        '<td>'+(no_pages-work_no_pages_done)+'</td>'+
                                        '<td>'+created+'</td>'+
                                      
                                '</tr>'                     
                                );
                   }
                  
                
                        $('table[class=all_details] tbody').empty();
           
                   for (var i = 0; i < designall.length; i++) {
                        var value="Work Not Yet Started";
                        var remark="-";
                        var created="-";
                        var pages="-";
                           if(designall[i].remark!=null){
                                 remark=designall[i].remark;
                           }
                           
                           if(designall[i].value!=null){
                                 value=designall[i].value;
                           }
                           if(designall[i].created!=null){
                                created=designall[i].created;
                           }
                           if(designall[i].pages!=null){
                                pages=designall[i].pages;
                           }
                          
                        $('table[class=all_details] tbody').append(
                                '<tr>'+
                                        '<td>'+designall[i].work_alloted_number+'</td>'+
                                        '<td>'+created+'</td>'+
                                        '<td>'+remark+'</td>'+
                                        '<td>'+pages+'</td>'+
                                        '<td>'+value+'</td>'+
                                '</tr>'                     
                                );
                   }
                


                  $('table[class=design_all] tbody').empty();
                  for (var i = 0; i < alldesign.length; i++) {
                               if(alldesign[i].io_number==null){
                           var io_number="-";
                   }
                   else{
                           var io_number=designall[i].io_number;
                   }

                   if(alldesign[i].item=="Other"){
                           if(alldesign[i].other_item_desc==null){
                                   var desc='';
                           }
                           else{
                                  var desc=" : "+alldesign[i].other_item_desc; 
                           }
                           var item=alldesign[i].item + desc;
                   }
                   else{
                        var item=alldesign[i].item;   
                   }
                        $('table[class=design_all] tbody').append(
                                '<tr>'+
                                '<td>'+alldesign[i].do_number+'</td>'+
                                '<td>'+alldesign[i].referencename+'</td>'+
                                '<td>'+io_number+'</td>'+
                                '<td>'+item+'</td>'+
                                '<td>'+alldesign[i].no_pages+'</td>'+
                                '<td>'+alldesign[i].creative_party+'</td>'+
                                '<td>'+alldesign[i].creative+'</td>'+
                                '<td>'+alldesign[i].created+'</td>'+
                        
                        '</tr>'                   
                                );
                   }
                
                
                    $('#ajax_loader_div').css('display','none');
                }
        });
   }
    })

    
</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                   
                    @yield('content')
            </div>
        <!-- Default box -->
      
        <div class="box-header with-border returnable">
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">Design Dashboard</h2><br><br><br>
                    <div class="container-fluid wdt">
                     
                       <div class="row">
                                <div class="col-md-6">       
                                        <div class="col-md-6 col" style="background-color:lightskyblue;">
                                                <label for="" style="margin-top:10px;margin-bottom:10px;color: white;">Designer</label>
                                        </div>
                                        <div class="col-md-6 col" style="background-color:#ffffff;">
                                                <select name="designer" id="designer" class="designer input-css select2" style="margin-top:10px;margin-bottom:10px;background-color:lemonchiffon">
                                                        <option value="0" style="font-weight: 700;">Select Designer</option>
                                                                @foreach ($emp as $item)
                                                        <option value="{{$item->id}}" {{old('work_to_emp')==$item->id ? 'selected=selected':''}}>{{$item->name}}</option>
                                                                @endforeach
                                                </select>
                                                
                                        </div>
                                        
                                                
                                                
                                </div>
                                {{-- <div class="col-md-6">
                                      
                                        <div class="col-md-6 col" style="background-color:green;">
                                                <label for="" style="margin-top:10px;margin-bottom:10px;color: white;">Working On Order</label>
                                        </div>
                                        <div class="col-md-6 col" style="background-color:#ffffff;">
                                                <select class="do input-css select2" style="margin-top:10px;margin-bottom:10px;background-color:lemonchiffon">
                                                       <option>Select</option>
                                                </select>
                                                
                                        </div>
                                        
                                                
                                                
                                
                                   <br>
                                                                
                                </div> --}}
                        </div>
                        <br><br>   

                        <div class="row">
                                <div class="col-md-12">
                                <table class="work_allot">
                                                <label for="" class="label" style="background-color:green">Work Allotment Details</label>
                                                <thead>    
                                                        <tr>   
                                                               
                                                                <th>WA No.</th>
                                                                <th>Status</th> 
                                                                <th>Description</th>        
                                                                <th>No. of Pages</th>

                                                                <th>WA Date</th> 
                                                                    <th>Update Status</th>  
                                                        </tr>
                                                        	
                                                        
                                                </thead>

                                                <tbody>
                                                        <tr>
                                                          <td></td>
                                                          <td></td>
                                                          <td></td>
                                                          <td></td>
                                                          <td></td>
                                                         <td></td>
                                                         <td></td>
                                                        </tr>
                                                        
                                                                  
                                                                 
                                                </tbody>
                                                
                                        </table>
                                </div>
                        </div>
                        <br><br>
                        <div class="row">
                                        <div class="col-md-12">
                                        
                                                <div class="col-md-4 col" style="background-color:darksalmon;">
                                                        <label for="" style="margin-top:10px;margin-bottom:10px;color: white;">Working On Order</label>
                                                </div>
                                                <div class="col-md-8 col" style="background-color:#ffffff;">
                                                        <select class="do input-css select2" style="margin-top:10px;margin-bottom:10px;background-color:lemonchiffon">
                                                                <option>Select</option>
                                                        </select>
                                                </div><br>
                                                                        
                                        </div>
                                       
                                </div><br><br>
                                 <div class="row">
                                        <div class="col-md-12">
                                                        <table class="design_all">
                                                                <label for="" class="label" style="background-color:#dd4b39">Design Order Details:     
                                                                </label>
                                                                 <thead>
                                                                <th>Design Order</th>
                                                                <th>Client</th>
                                                                <th>Internal Order</th>
                                                                <th>Item Name</th>
                                                                <th>No. Of Pages</th>
                                                                <th>Creative</th>
                                                                <th>Creative Received</th>
                                                                <th>Date</th>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td> 
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>      
                                                                </tr>     
                                                                </tbody>
                                                        </table>
                                                    </div>
                        </div>
                        <br><br>
                        <div class="row">
                                        <div class="col-md-12">
                                                        <table class="other_allotment">
                                                                <label for="" class="label" style="background-color:#999999">Working Order Alloted to Any Other:     
                                                                </label>
                                                                 <thead>
                                                                <th>Name</th>
                                                                <th>Work Number</th>
                                                                <th>Status</th>
                                                                <th>Page</th>
                                                                <th>Remaining Pages</th>
                                                                <th>Date</th>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                </tr>     
                                                                </tbody>
                                                        </table>
                                                    </div>
                        </div>
                        <br><br>
                       
                        <div class="row">
                                <div class="col-md-12" >
                                                <table class="all_details">
                                                                <label for="" class="label" style="background-color:goldenrod;">Work Allotment Status Detail</label>
                                                                <thead>
                                                                        <th>Number</th>
                                                                        <th>Date </th>
                                                                        <th>Work Description </th>
                                                                        <th>Pages</th>
                                                                        <th>Status </th>
                                
                                                                      
                                                                        
                                                                </thead>
                                                                <tbody>
                                                                        <tr>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                
                                                                               
                                                                               
                                                                        </tr>
                                                                      
                                                                </tbody>
                                                        </table> 
                                </div>
                        </div>
                        <br><br><br>
                    </div>
                </div>
        </div>
       
<style type="text/css">
 
 table thead th {
    border: 1px solid #999999 !important;
    background-color: #f0f0f0; 
    height: 30px;
    text-align: center;
    color: #222222;
}
.label
{
        background-color: #dd4b39;
        color: #ffffff;
       border: 0px;
       border-radius: 0px;
        margin-bottom: 20px;
        padding: 8px;
   
}
 
table tbody tr td
{
     border: 1px solid #999999;
     background-color: #ffffff;
} 
.select2-container--default .select2-selection--single
{
        background-color: #ffffff;
}.select2-container
{
        bottom: 5px;
}
.col
{
        border: 1px solid #999999;
}
.col:first-child
{
        border-right: 0px;
}
</style>
     
      </section>
@endsection
