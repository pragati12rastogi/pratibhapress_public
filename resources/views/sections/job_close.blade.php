

@extends($layout)

@section('user', Auth::user()->name)

@section('title', __('jobcard.list'))

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
    var type="{{$type}}";
    var d_type="";
    if(type=="Production"){
        d_type="prod";
    }
    if(type=="Logistic"){
        d_type="log";
    }
  function job(status){
  var status="{{$status}}";
    dataTable = $('#jobcard_list_table').DataTable({
        
      "processing": true,
        "serverSide": true,
        "ajax" : "/jobcard/status/"+d_type+"/api/",  
        "aaSorting": [],
        "responsive": true,
        "columns": [
             
          {"data": "job"}, 
          {"data": "io"},  
          { "data": "partyname" }, 
          {
                    "targets": [ -1 ],
                    "data" : function(data,type,full,meta)
                    {
                      var str = data.name; 
                      var idss=data.item_name;
                      console.log(data);
                      if(idss)
                        return str+ " : " +idss;
                      else
                         return str;
                    ;
                    }
                },
          { "data": "qty" }, 
          { "data": "io_type" }, 
          { "data": "creative" }, 
          { "data": "open" }, 
          { "data": "close" }, 
         
          { "data": "sample","render": function(data, type, full, meta){
                if(data==1)
                  return "Yes";
                else
                  return "No";
              } },
          { "data": "date", "orderable": true }, 
         
          {
              "targets": [ -1 ],
              "data":"ids", "render": function(data,type,full,meta)
                {
                 
                    return "<a href='/JobCard/view/"+data+"' target='_blank'><button style='margin-bottom: 5px;' class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href="/JobCard/stausupdate/close/'+data+'" target="_blank"><button style="margin-bottom: 5px;" onClick="return confirm('+"'Are you sure to close this Job Card?'"+');" class="btn btn-xs"> Close</button></a>'+
                    "<a href='/templateJC/"+data+"' target='_blank'><button style='margin-bottom: 5px;margin-left: 5px;' class='btn btn-danger btn-xs'> Print </button></a> &nbsp;" ;
                  
              },
              "orderable": false
          }
          ],
          "columnDefs": [
            { "orderable": false, "targets": 1 }
          ]
        
      });
}
  $(document).ready(function()  {
    job('open');
    });
</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class=""> {{__('order_to_collection.title')}}</i></a></li>
  <li><a href="#"><i class=""> {{__('jobcard.list')}}</i></a></li>
@endsection

@section('main_section')
    <section class="content">
      <div id="app">
                @include('sections.flash-message')
                @yield('content')
                @section('titlebutton')
          
                {{-- <a href="/import/data/jobcard"><button class="btn btn-sm btn-primary">{{__('jobcard.importtitle')}}</button></a> --}}
                <a href="/export/data/jobcard"><button class="btn btn-sm btn-primary">{{__('jobcard.exporttitle')}}</button></a>
                @endsection
                      </div>
        <!-- Default box -->
        <div class="box">
          
          <div class="box-header with-border">
            <div class="box-header with-border">
                    <ul class="nav nav1 nav-pills">
                      <li class="nav-item">
                        <a class="nav-link active" {{$titles=='Job Card Close For Production Manager' ? 'style=background-color:#87CEFA' : 'style=background-color:#DCDCDC	'}}  href="{{url('/jobcard/status/prod')}}">Job Card Close For Production Manager</a>
                       
                      </li>
                      <li class="nav-item">
                            <a class="nav-link active" {{$titles=='Job Card Close For Logistic Manager' ? 'style=background-color:#87CEFA' : 'style=background-color:#DCDCDC	'}}  href="{{url('/jobcard/status/log')}}">Job Card Close For Logistic Manager</a>
                        </li>
                     
                    </ul>
                  </div>
          </div>
          <div class="box-body">
            <table id="jobcard_list_table" class="table table-bordered table-striped" >
                <thead>
                <tr>
                  <th>{{__('jobcard.mytitle')}}</th>
                  <th>{{__('jobcard.internalorder')}}</th>
                  <th>Reference Name</th>
                  <th>{{__('jobcard.item')}}</th>
                  <th>{{__('jobcard.qty')}}</th>
                  <th>IO Type</th>
                  <th>{{__('jobcard.creative_name')}}</th>
                  <th>{{__('jobcard.open_size')}}</th>
                  <th>{{__('jobcard.close_size')}}</th>
                  <th>{{__('jobcard.job_sample')}}</th>
                  <th>Job Card Date</th>
                  <th>{{__('internal_order.status')}}</th>
                  
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
