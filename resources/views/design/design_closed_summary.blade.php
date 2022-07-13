@extends($layout)

@section('title', 'Design Closed Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Design Summary</a></li> 
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
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
  
      function closed(status){
        if(dataTable){
          dataTable.destroy();
        }
        if(status=='close'){
          $('.chal1').removeAttr('style');
          $('.chal').css("background-color","#87CEFA");
        }
        if(status=='misc'){
          $('.chal').removeAttr('style');
          $('.chal1').css("background-color","#87CEFA");
        }
        dataTable = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/design/report/closed/api?status="+status,
          "columns": [
            {"data":"date"},
            {"data":"do_number"},
              {"data":"referencename"},
              { 
                "data":"io_number","render": function(data, type, full, meta){
                  if(data)
                    
                  return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            }, 
              {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var str = data.name; 
                      var idss=data.other_item_desc;
                      console.log(data);
                      if(idss)
                        return str+ " : " +idss;
                      else
                         return str;
                    ;
                    }
                },
              {"data":"no_pages"},
              {"data":"creative"},
              {"data":"creative_party"},
              {"data":"status"},
              {"data":"status_date"},
                   {
                  "targets": [ -1 ],
                  data: function(data,type,full,meta)
                  {
                     var days=data.st_date;
                     
                     return days+" Days";
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 9 }
            
            ]
          
        });
      }

$(document).ready(function(){
  closed('close');
})

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
                  <ul class="nav nav1 nav-pills">
                    <li class="nav-item">
                      <button class="nav-link1 chal"  onclick="closed('close')">Closed</button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link1 chal1" onclick="closed('misc')">Closed For Miscellaneuos work</button>
                    </li>
                  </ul><br><br>
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Created At</th>
                    <th>DO No.</th> 
                      <th>Client</th>
                      <th>IO</th>
                      <th>Item</th>
                      <th>No. Pages</th>
                      <th>Creative Name</th>
                      <th>Creative Received</th>
                      <th>Status</th>
                      <th>Status Date</th>
                      <th>In No of Days Work Completed</th>
                      {{-- <th>Action</th> --}}
                     
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