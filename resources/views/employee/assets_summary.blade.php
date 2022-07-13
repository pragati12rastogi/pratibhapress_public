@extends($layout)

@section('title', 'Assets List')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Assets List</a></li> 
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
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
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
          "ajax": "/master/assets/list/api",
          "createdRow": function( row, data, dataIndex){
                if( data.allot_status ==  'Disposed'){
                    $(row).addClass('bg-gray');
                }
            },
          "columns": [
              {"data":"asset_code"},
              {"data":"category_name"},
              {"data":"name"},
              {"data":"brand"},
              {"data":"asset_bill_no"},
              {"data":"model_number"},
              // {"data":"description"},
              {"data":"asset_value"},
              {"data":"allot_status", "render": function(data,type,full,meta)
                  {
                    
                    if(data == "not assign")
                      return "";
                    else if(data == "Assigned") 
                      return data;
                    else if(data == "Disposed")
                      return data;
                  },},
                {
                  "targets": [ -1 ],
                  data : function(data,type,full,meta)
                  {
                    
                    // var x="";
                    // if(full.asset_bill_upload != "" && full.asset_bill_upload != null){
                    //   x= "<a href='/upload/assets/"+full.asset_bill_upload+"' target='_blank'><button class='btn btn-success btn-xs'>Bill</button></a> &nbsp;";
                    // }else{
                    //    x='';
                    // }
                    // if(full.asset_photo_upload != "" && full.asset_photo_upload != null){
                    //   x+= "<a href='/upload/assets/"+full.asset_photo_upload+"' target='_blank'><button class='btn btn-flickr btn-xs'>Image</button></a> &nbsp;";
                    // }else{
                    //    x+='';
                    // }
                    if(data.allot_status!="Disposed"){
                      return "<a href='/master/assets/edit/"+data.asset_id+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;"
                    + "<a href='/master/assets/view/"+data.asset_id+"' target='_blank'><button class='btn btn-flickr btn-xs'>View</button></a> &nbsp;"
                    ;
                    }
                    else{
                      return "<a href='/master/assets/view/"+data.asset_id+"' target='_blank'><button class='btn btn-flickr btn-xs'>View</button></a> &nbsp;"
                    ;
                    }
                  
                  }
              }
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": 8 }
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
                    </div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    <a href="{{url('/master/assets')}}"><button class="btn btn-primary">Create Assets</button></a>
                      @endsection
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Assets Code</th>
                      <th>Assets Category</th>
                      <th>Assets Name</th>
                      <th>Assets Brand</th>
                      <th>Assets Bill Number</th>
                      <th>Assets Model Number</th>
                      <!-- <th>Description</th> -->
                      <th>Asset Value</th>
                      <th>Status</th>
                      <th>Action</th>
                     
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