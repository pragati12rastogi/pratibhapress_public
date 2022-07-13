@extends($layout)

@section('title', 'Daily Process Planning')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Daily Process Planning</a></li> 
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
   input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
</style>

@endsection
@section('js')
<script src="/js/Production/platebypress_creation.js"></script>

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
          "ajax": "/production/dailyprocessplanning/list/api",
          "columns": [
            {"data":"job_number"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"creative_name"},
            {"data":"element_name"},
            {"data":"e_plate_size"},
            {"data":"total_plates"},
            {"data":"balance"},
            {
              "targets": [ -1 ],
              "data":"id", "render": function(data,type,full,meta)
              {
                // debugger
                 var tp = full.total_plates;
                 var ac_created =full.created;
                 var hp =full.afterplanned;
                // var today = new Date();
                // var date = today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear();
                var x = "";
                if(tp == ac_created){ //== to !=
                  x= "";
                }
                else{
                  x= "<button class='btn btn-toolbar btn-primary btn-xs' onclick='unusual("+data+","+full.element_id+","+full.afterplanned+","+full.left_plate+")' data-toggle='modal' data-target='#myModal_of'> {{__('Create')}} </button> &nbsp;" 
                ;
                }
                if(ac_created>0){
                  x+="<a class='btn btn-toolbar btn-success btn-xs' href='/production/daily/plate/actual/list/"+data+"/"+full.element_id+"'> View </a> &nbsp;";
                }else{
                  x+= "";
                }
                return x;
              }
              }
            ],
            "columnDefs": [
             { "orderable": false, "targets": 8}
            
            ]
        });

      $( "form" ).submit(function( event ) {
        // debugger;
        var x = check();
        var l = $("#left").val();
        var plate = $("#is_plate").val();
        if(x==0){
          event.preventDefault();
        }else if(plate == 1){
          var no_p = $("#no_plate").val();
          if(no_p <= 0){
            $("#plates_err").text('Value is lesser than 0!').show();
            event.preventDefault();
          }else{
            $("#plates_err").empty().hide();
          }
        }
        // else if(l != 0){
        //   $("#n_err").text('Planning is not completed yet!').show();
        //   event.preventDefault();
          
        // }
        
       });
   var dateToday = new Date(); 
  
      $( "#pNew_date" ).datepicker({
          autoclose: true,
          format: 'd-m-yyyy',
          startDate: new Date(),
      });
  }); 
   
$('#myModal_of').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
});
  
  function check(){
    // debugger;
    var tp = $("#total_p").val();
    var pl= $("#no_plate").val();
    
    if(parseInt(tp) >= parseInt(pl)){
      $("#plates_err").hide();
      return 1;
    }else{
      $("#plates_err").text("increasing the total limit").show();
      return 0;
    }
  }

  $(".is_plate").change(function(){
    var x = $(this).val();
    if(x == 1){
      $("#new_show").show();
    }else{
       $("#new_show").hide();
    }
  })
  function unusual(id,ele,tp,left){
   $("#element").val(ele);
   $("#jc_no").val(id);
   $("#total_p").val(tp);
   $("#lb").text(tp);
   $("#left").val(left);
  }
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
                          <!-- <th>Plate Sets</th> -->
                          <th>Plate Size</th>
                          <!-- <th>Front Color</th> -->
                          <!-- <th>Back Color</th> -->
                          <th>Total Plate Required</th>
                          <th>Balance</th>
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
                       <div id="myModal_of" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Create</h4>
                                    </div>
                                    <div class="modal-body">
                                      
                                        <form action="/Production/dailyprocess/creation" enctype="multipart/form-data" method="POST" id="plate_creationForm" >
                                          @csrf
                                         
                                            <div class="row">
                                              <div class="col-md-6 {{ $errors->has('is_plate') ? 'has-error' : ''}}" style="display: grid;">
                                                  <label>Is plate old or new?<sup>*</sup></label>
                                                  <select name="is_plate" class="select2 is_plate" id="is_plate">
                                                    <option value="">Select</option>
                                                    <option value="0">Old</option>
                                                    <option value="1">New</option>
                                                  </select>
                                                  {!! $errors->first('is_plate', '<p class="help-block">:message</p>') !!}
                                              </div>
                                              <div class="col-md-6">
                                                <input type="text" class="jc_no" name="jc_no" id="jc_no" hidden="hidden">
                                                <input type="text" class="element" name="element" id="element" hidden="hidden">
                                                <input type="text" class="total_p" name="total_p" id="total_p" hidden="hidden">
                                                <input type="text" class="left" name="left" id="left" hidden="hidden">
                                                <label id="n_err" class="error"></label>

                                              </div>
                                            </div>
                                          <br>
                                          <div class="row" id="new_show" style="display:none">
                                              <div class="col-md-6 {{ $errors->has('no_plate') ? 'has-error' : ''}}">
                                                 <label>Number Of Plates Planned <sup>*</sup>(max : <span id="lb"></span> plates)</label><br>
                                                  <input type="number" class="form-control input-css no_plate" value="0" name="no_plate" id="no_plate"
                                                  onKeyPress="if(this.value.length==10) return false;" onchange="check()">
                                                  <label id="plates_err" class="error"></label>
                                                  {!! $errors->first('no_plate', '<p class="help-block">:message</p>') !!}
                                              </div>
                                              <div class="col-md-6 {{ $errors->has('pNew_date') ? 'has-error' : ''}}">
                                                 <label>Plates Date <sup>*</sup></label><br>
                                                  <input type="text" class="form-control input-css pNew_date" name="pNew_date" id="pNew_date">
                                                  {!! $errors->first('pNew_date', '<p class="help-block">:message</p>') !!}
                                              </div>
                                              <div class="col-md-12 {{ $errors->has('machine_name') ? 'has-error' : ''}}" style="margin-top: 20px;display: grid;">
                                                 <label>Machine Name <sup>*</sup></label>
                                                   <select name="machine_name" class="select2 form-control input-css machine_name" id="machine_name">
                                                    <option value="">Select Machine Name</option>
                                                     @foreach($machine as $mac)
                                                      <option value="{{ $mac->id}}">{{$mac->name}}</option>
                                                    @endforeach
                                                  </select>
                                                  {!! $errors->first('machine_name', '<p class="help-block">:message</p>') !!}
                                              </div>
                                          </div><br>
                                         
                                           <div class="row">
                                            <div class="col-md-12">
                                                 <input type="submit" class="btn btn-primary" value="Submit">
                                            </div>
                                        </div><br>
                                      </form>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    
                
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
      </section>
@endsection