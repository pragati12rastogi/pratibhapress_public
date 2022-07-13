

@extends($layout)

@section('user', Auth::user()->name)

@section('title', 'Plate By Press Report')

@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
<style>
  .nav1>li>a {
      position: relative;display: block;padding: 10px 34px;background-color: white;margin-left: 10px;
  }
  /* .nav1>li>a:hover {
      background-color:#87CEFA;
  
  
  } */
  </style>
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
  function job(){
  var status="Open";
    dataTable = $('#jobcard_list_table').DataTable({
        
      "processing": true,
        "serverSide": true,
        "ajax" : "/prod/platebypress/report/api",  
        "aaSorting": [],
        "responsive": true,
        "columns": [
             
          {"data": "job"}, 
          { "data": "referencename" }, 
          { "data": "item_name" },
          { "data": "creative_name" }, 
          { "data": "element_type" },
          { "data": "plate_size" },
          { "data": "total_plates" },          
          { "data": "oldplate" },          
          { "data": "plate_planned" },          
          { "data": "wastage" },          
          { "data": "difference" }
        
          ],
          "columnDefs": [
           
          ]
        
      });
}
  $(document).ready(function()  {
    job();
    });
</script>
@endsection


@section('main_section')
    <section class="content">
      <div id="app">

                @include('sections.flash-message')
                @yield('content')
                @section('titlebutton')
          
                {{-- <a href="/import/data/jobcard"><button class="btn btn-sm btn-primary">{{__('jobcard.importtitle')}}</button></a> --}}
                {{-- <a href="/export/data/jobcard"><button class="btn btn-sm btn-primary">{{__('jobcard.exporttitle')}}</button></a> --}}
                @endsection
                      </div>
        <!-- Default box -->
        <div class="box">
         
          <div class="box-body">
            <table id="jobcard_list_table" class="table table-bordered table-striped" >
                <thead>
                <tr>
                  <th>{{__('jobcard.mytitle')}}</th>
                  <th>Reference Name</th>
                  <th>Item Name</th>
                  <th>Creative Name</th>
                  <th>Element Name</th>
                  <th>Plate Size</th>
                  <th>Total Plates </th>
                  <th>Old Plates Sets</th>
                  <th>New Plates Sets</th>
                  <th>Wastage</th>
                  <th>Difference</th>
                 
                  
                </tr>
                </thead>
                <tbody>

                </tbody>
           
            </table>
          </div>
        </div>
        <!-- /.box -->
      </section>
@endsection
