@extends($layout)

@section('title', 'Work Allotment Status Report')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Work Report</a></li>
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
          "ajax": "/work/status/all/"+party_cons_id,
          "columns": [
              { "data": "value" }, 
              { "data": "pages" }, 
              { "data": "proof" }, 
              { "data": "approval_type" }, 
              { "data": "approval_on" }, 
              { "data": "approval_by" }, 
              { "data": "approval_date" }, 
              { "data": "date" }, 
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/work/status/update/"+data+"'><button class='btn btn-success btn-xs'> Edit </button></a>" ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 8 },
              { "orderable": false, "targets": 6 },
              { "orderable": false, "targets": 7 }
            ]
          
        });
    };

    $(document).ready(function(ex){

      $("#cosng_table").hide();

      //Initialize Select2 Elements
      $('.select2').select2();

      $('#select_do').on('select2:select', function (e) {
            // $("#cosng_table").hide()
          var select_data = e.params.data;
          var val=$(e.target).val();
          if(select_data.id == -1)
            return;
          $('#ajax_loader_div').css('display','block');
          $.ajax({
              url: "/design/work/all/" + val,
              type: "GET",
              success:function(result) {
                $("#select_wa").empty();
                    $("#select_wa").append('<option disabled selected>Select Work Alloted Number</option>');
                    for(var i=0;i<result.length;i++){
                        $("#select_wa").append('<option value="'+result[i].id+'">'+result[i].work_alloted_number+'</option>');
                    }
                  $('#ajax_loader_div').css('display','none');
              }
          });
      });
      $('#select_wa').on('select2:select', function (e) {
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
                  <h3 class="box-title">Select Design Order to List Work</h3>
                  @section('titlebutton')
                  {{-- <a href="/import/data/consignee"><button class="btn btn-sm btn-primary">{{__('consginee_list.importtitle')}}</button></a>
                  <a href="/export/data/consignee"><button class="btn btn-sm btn-primary">{{__('consginee_list.exporttitle')}}</button></a> --}}

                  @endsection
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6">
                        <select id="select_do" class="form-control select2"  data-placeholder="" style="width: 100%;" >
                                <option value="-1">Select Design Order</option>
                                @foreach ($design as $key)
                                  <option value="{{$key->id}}">{{$key->do_number}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select id="select_wa" class="form-control select2"  data-placeholder="" style="width: 100%;" >
                                <option value="-1">Select Work Allotment</option>
                               
                        </select>
                    </div>
                  </div>
                  <br>
                  <table id="cosng_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Status</th>
                      <th>Pages</th>
                      <th>Proof</th>
                      <th>Approval Type</th>
                      <th>Approval On</th>
                      <th>Approval By</th>
                      <th>Approval Date</th>
                      <th>Created Date</th>
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