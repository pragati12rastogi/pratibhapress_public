@extends($layout)

@section('title', 'Plate By Press Summary')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Plate By Press Summary </a></li> 
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

@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
      dataTable = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/production/platebypress/summary/api",
          "columns": [
            {"data":"job_number"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"creative_name"},
            {"data":"element_name","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            {"data":"e_plate_set","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            {"data":"e_plate_size","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            {"data":"e_front_color","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            {"data":"e_back_color","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            },
            // { data:function(data, type, full, meta){
            //   var plate_set = data.e_plate_set;
            //   var split_plate_set = plate_set.split(',');
            //   var front_col = data.e_front_color;
            //   var split_front_col = front_col.split(',');
            //   var back_col = data.e_back_color;
            //   var split_back_col = back_col.split(',');
            //   var len = split_plate_set.length;
            //   var total= [];
            //   for(var i=0;i<len;i++){
            //        total[i] =  ((split_plate_set[i]*split_front_col[i])+(split_plate_set[i]*split_back_col[i]));
            //     }
            //       if(total){
            //         var x = total.toString();
            //         return x.replace(/,/g,'<br>');
            //       }
            //       else{
            //         return "";
            //       }
                 
            //     } 
            // },
            {"data":"total_plates","render": function(data, type, full, meta){
                  if(data)
                    return data.replace(/,/g,'<br>');
                  else
                    return "";
                } 
            }
            // ,
            // {
            //   "targets": [ -1 ],
            //   "data":"id", "render": function(data,type,full,meta)
            //   {
                
            //     return "<a href='/prod/platebypress/creation/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('Create')}} </button></a> &nbsp;" 
            //     ;
            //   }
            //   }
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 10 }
            
            ]
          
        });
    });


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
                   
                    <table id="table" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Job Card Number</th>
                          <th>Reference Name</th>
                          <th>Item Name</th>
                          <th>Creative Name</th>
                          <th>Element Name</th>
                          <th>Plate Sets</th>
                          <th>Plate Size</th>
                          <th>Front Color</th>
                          <th>Back Color</th>
                          <th>Total Plate Required</th>
                          <!-- <th>Action</th> -->
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