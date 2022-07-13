@extends($layout)

@section('title', 'Machine wise Job Planning')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Machine wise Job Planning</a></li> 
@endsection
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
  var dataTable;
    var hr;
 
    // Data Tables
    function fetchingDataForTable(io_id,machine){
   
    var last_ele = null ;
    var last_tr = null ;
   
      dataTable = $('#asn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/production/press/machineplanning/api/"+io_id+"/"+machine,
          "columns": [
            {"data":"job_number"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"creative_name"},
            {"data":"element_name"},
            {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
                {
                  if(data.paper_size!=null){
                      return data.paper_size;
                  }
                  else{
                    return data.size;
                  }
                }
              },
              {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
                {
                  if(data.paper_type!=null){
                      return data.paper_type;
                  }
                  else{
                    return data.paper;
                  }
                }
              },
              {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
                {
                  if(data.paper_gsm!=null){
                      return data.paper_gsm;
                  }
                  else{
                    return data.gsm;
                  }
                }
              },
              {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
                {
                  if(data.paper_mill!=null){
                      return data.paper_mill;
                  }
                  else{
                    return data.mills;
                  }
                }
              },
              {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
                {
                  if(data.paper_brand!=null){
                      return data.paper_brand;
                  }
                  else{
                    return data.brands;
                  }
                }
              },
              {
              "targets": [ -1 ],
              data: function(data,type,full,meta)
                {
                  if(data.no_of_sheets!=null){
                      return data.no_of_sheets;
                  }
                  else{
                    return data.sheets;
                  }
                }
              },
             
            {"data":"total_plates"},
            // {"data":"planned_plates"},
           
            ],
            "columnDefs": [
            //   { "orderable": false, "targets":  10}
            ]
           
          
        });
      
  }
    $(document).ready(function(ex){
      var last_ele = null ;
    var last_tr = null ;
      $("#asn_table").hide();

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
              url: "/press/planned/date/" + val,
              type: "GET",
              success:function(result) {
                $("#select_wa").empty();
                    $("#select_wa").append('<option disabled selected>Select Date</option>');
                    for(var i=0;i<result.length;i++){
                        $("#select_wa").append('<option value="'+result[i].planned_date+'">'+result[i].planned_date+'</option>');
                    }
                  $('#ajax_loader_div').css('display','none');
              }
          });
      });
     
$("#asn_table").hide();
$('#select_wa').on('select2:select', function (e) {
    var select_data = e.params.data;
    var mach=$('#select_do').val();
    if(select_data.id == -1)
      return;
    if($("#asn_table").is(":hidden"))
      $("#asn_table").show();
    if(dataTable)
      dataTable.destroy();
      $('.div').empty();
    $('.div').append(' <a href="/production/press/machineplanning/print/'+select_data.id+"/"+mach+'" ><button class="btn btn-primary print" >Print</button></a>');
      fetchingDataForTable(select_data.id,mach);
});

    });

 
  </script>
@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
         @include('sections.flash-message')
        <div class="box">
        @section('titlebutton')
                     <div class="div"></div> 
    
                      @endsection
                <div class="box-header">
                  <h3 class="box-title">Select Mchine to List All Plnning Done</h3>
                 
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                  <div class="col-md-6">
                        <select id="select_do" class="input-css select2"  style="width: 100%;" >
                                <option value="-1">Select Machine</option>
                                @foreach ($machine as $key)
                                  <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select id="select_wa" class="form-control select2"  data-placeholder="" style="width: 100%;" >
                                <option value="-1">Select Date</option>
                               
                        </select>
                    </div>
                  </div>
                  <br>
                  <p class="amount"></p>
                  <table id="asn_table" class="table table-bordered table-striped">
                  <thead>
                        <tr>
                          <th>JC No.</th>
                          <th>Reference</th>
                          <th>Item</th>
                          <th>Creative</th>
                          <th>Element</th>
                          <th>Paper Size</th>
                            <th>Paper Type</th>
                            <th>Paper GSM</th>
                            <th>Paper Mill</th>
                            <th>Paper Brand</th>
                            <th>No. Of Sheets</th>
                          <th>Total Impression</th>
                         
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