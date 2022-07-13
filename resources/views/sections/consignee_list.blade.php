@extends($layout)

@section('title', __('consginee_list.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>{{__('consginee_list.title')}}</a></li>
@endsection

@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    function fetchingDataForTable(party_cons_id) {
      dataTable = $('#cosng_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting": [],
          "responsive": true,
          "ajax": "/consignee/list/api?id="+party_cons_id,
          "columns": [
              { "data": "id" }, 
              { "data": "partyname" }, 
              { "data": "consignee_name" }, 
              { "data": "address" }, 
              { "data": "city" }, 
              { "data": "gst" }, 
              { "data": "pan" }, 
             
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/consignee/view/"+data+"'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                    '<a href=/consignee/update?id='+data+'><button class="btn btn-success btn-xs"> Edit </button></a>' ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 7 },
              { "orderable": false, "targets": 5 },
              { "orderable": false, "targets": 6 }
            ]
          
        });
    };

    $(document).ready(function(ex){

      $("#cosng_table").hide();

      //Initialize Select2 Elements
      $('.select2').select2();

      @if($pid)
        if($("#cosng_table").is(":hidden"))
            $("#cosng_table").show();
        fetchingDataForTable({{$pid}});
      @endif

      $('#select_party').on('select2:select', function (e) {
          var select_data = e.params.data;
          if(select_data.id == -1)
            return;
          if($("#cosng_table").is(":hidden"))
            $("#cosng_table").show();
          if(dataTable)
            dataTable.destroy();
            fetchingDataForTable(select_data.id);
      });

    });

  </script>
@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
         @include('sections.flash-message')
        <div class="box">
                <div class="box-header">
                  <h3 class="box-title">{{__('consginee_list.sel_party')}}</h3>
                  @section('titlebutton')
                  <a href="/import/data/consignee"><button class="btn btn-sm btn-primary">{{__('consginee_list.importtitle')}}</button></a>
                  <a href="/export/data/consignee"><button class="btn btn-sm btn-primary">{{__('consginee_list.exporttitle')}}</button></a>

                  @endsection
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                        <select id="select_party" class="form-control select2"  data-placeholder="" style="width: 100%;" >
                                <option value="-1">Select Client for Summary consignee</option>
                                @foreach ($party as $key)
                                  <option value="{{$key->id}}" @if($pid==$key->id)selected @endif>{{$key->partyname}}</option>
                                @endforeach
                        </select>
                    </div>
                  </div>
                  <br>
                  <table id="cosng_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Consignee Id</th>
                      <th>Client Name</th>
                      <th>Consignee Name</th>
                      <th>Shipping Adress</th>
                      <th>City</th>
                      <th>GST/IN</th>
                      <th>PAN/IT</th>
                      <!-- <th>Created Date</th> -->
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