

@extends($layout)

@section('user', Auth::user()->name)

@section('title', 'Plate By Party Not Generated')

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
        "ajax" : "/production/PlateByParty/notGenerated/api/",  
        "aaSorting": [],
        "responsive": true,
        "columns": [
             
          {"data": "job"}, 
          { "data": "partyname" }, 
          {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var str = data.item_name; 
                         return str;
                    ;
                    }
                },
          { "data": "creative" }, 
        //   { "data": "element_type" }, 
          { "data": "element_type" , "render": function(data,type,full,meta)
            {
            if(data)
                return data.replace(/,/g,'<br>');
            else  
                return "";
            },},
            { "data": "plate_sets" , "render": function(data,type,full,meta)
            {
            if(data)
                return data.replace(/,/g,'<br>');
            else  
                return "";
            },},
            { "data": "plate_size" , "render": function(data,type,full,meta)
            {
            if(data)
                return data.replace(/,/g,'<br>');
            else  
                return "";
            },},
          { "data": "front_color" },
          { "data": "back_color" },
          {
                  "targets": [ -1 ],
                  "data":"ids", "render": function(data,type,full,meta)
                  {
                    return "<a href='/production/platebyparty/create/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> Plate Process </button></a> &nbsp;" ;

                  },
                  "orderable": false
              }
          ],
          "columnDefs": [
            { "orderable": false, "targets": 9 }
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
                  <th>{{__('jobcard.item')}}</th>
                  <th>{{__('jobcard.creative_name')}}</th>
                  <th>Element Name</th>
                  <th>Plate Sets</th>
                  <th>Plate Size</th>
                  <th>Front Color</th>
                  <th>Back Color</th>
                  <th>Action</th>
                  
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
