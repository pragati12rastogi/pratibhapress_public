@extends($layout)

@section('title', __('layout.dashboard'))

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class="fa "></i> DashBoard</a></li>
@endsection

@section('css')
  <link rel="stylesheet" href="/css/widget.css">
  <style>

.thumnails{
width:20%;
display: inline-block;
margin:2px;
background:#fff;
padding:10px;
box-sizing:border-box;
  
}
.thumnails img{
width:100%;
height:100px;
  =

}
main{
width:100%;
  background-color: aliceblue;

}
.fc-center h2{
  font-size:10px;
}
.fc-event{
  display: none !important;
}
.fc button {
  padding: 0 .1em !important;
  font-size: smaller !important;
}
.fc-right{
  width: 20% !important;
}
.fc-left{
  width: 20% !important;
}
.fc-center{
  width: 20% !important;
}
.fc table{
  font-size: 11px !important;
}
.fc-day-number {
    font-size: 13px !important;
}
.fc-rigid{ height: 12px !important; }
.fc-icon {
  font-size: smaller !important;
  line-height: .5em !important;
  height: 1.3em !important;
}.fc-button-group{
  width: 100% !important;
}
.fc-toolbar{
  padding: 0px !important;

}
.panel-body {
    padding: 8px !important;
}
.panel{
  margin-bottom: 0px !important;
}
.fc-basic-view tbody .fc-row {
    min-height: 2em !important;
}
.img {
  font-size: 27px;
    /* object-fit: cover; */
    text-align: center;
    color: #dc2a14;
    margin-top: 4px;
    margin-bottom: 4px;
      }
.divs{
  margin-top: 5px;
    /* margin-left: 5px; */
}
</style>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
@endsection
@section('js')
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script> 
    {!! $calendar->script() !!}
    <script>
    // external js: masonry.pkgd.js, imagesloaded.pkgd.js

// init Masonry
// var $grid = $('.grid').masonry();
// layout Masonry after each image loads

    </script>
<script>

function cancel_alert_dailog(id,leave_id)
    {
      $('#modal_div').empty().append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Approval/Rejection</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/hr/leave/approve/'+id+'">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      '<input type="hidden" name="leave_id" value="'+leave_id+'">'+
                      '<br><label>Please select Below OPTIONS for Leave Application</label>'+
                      '<label> <input name="status" type="radio" value="Approved" required> Approved.</label>'+
                      '<label> <input name="status" type="radio" value="Rejected" required> Rejected.</label>'+
                      '<label id="status-error" class="error" for="status"></label>'+
                      '<br><br><input type="text" name="remark" class="input-css" placeholder="Please Enter Remark" required>'+
                    '</div>'+
                    '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>'+
                      '<a target="_blank"><button type="submit" class="btn btn-success"  onclick="$(\'#infos\').validate();">Submit</button></a>'+
                    '</div>'+
                    '</form>'+
                '</div>'+
              '</div>'+
            '</div>'
      );
          $(document).find('#myModal').modal("show"); 
  }

     function cancel_alert_dailog1(id,leave_id,repoting)
    {
      $('#modal_div').empty().append(
            '<div id="myModal1" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Approval/Rejection</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/hr/leave/approve/'+leave_id+'">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      '<input type="hidden" name="leave_id" value="'+leave_id+'">'+
                      '<input type="hidden" name="repoting" value="'+repoting+'">'+
                      '<br><label>Please select Below OPTIONS for Leave Application</label>'+
                      '<label> <input name="status1" type="radio" value="Approved" required> Approved.</label>'+
                      '<label> <input name="status1" type="radio" value="Rejected" required> Rejected.</label>'+
                      '<label id="status-error" class="error" for="status"></label>'+
                      '<br><br><input type="text" name="remark" class="input-css" placeholder="Please Enter Remark" required>'+
                    '</div>'+
                    '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>'+
                      '<a target="_blank"><button type="submit" class="btn btn-success"  onclick="$(\'#infos\').validate();">Submit</button></a>'+
                    '</div>'+
                    '</form>'+
                '</div>'+
              '</div>'+
            '</div>'
      );
          $(document).find('#myModal1').modal("show"); 
  }

function fetchdata(i){
  // debugger;
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        type:"GET",
        url:"/events/basis/date/select",
        data:{'selecteddate':i},
        success: function(result){
          
          $('#eventTable').empty();
            if(result){
              if((result.Birthday).length >0){
                $('#eventTable').append("<thead style='position: sticky;top: 0px;'class='bg-primary'><tr><th class='text-center'>Birthday</th><th class='text-center'>Date</th></tr></thead><tbody><tr id='jqbday'></tr></tbody>");
                $.each(result.Birthday, function( i, val ) {
                  $("#jqbday").after("<tr><td>"+val.empcode+"</td><td><span style='color:green'>"+val.original+"</span></span></td></tr>");
                });

              }
              if((result.Anniversary).length>0){
                $('#eventTable').append("<thead style='position: sticky;top: 0px;'class='bg-danger'><tr><th class='text-center'>Service Anniversary</th><th class='text-center'>Date</th></tr></thead><tbody><tr id='jqanni'></tr></tbody>");
                $.each(result.Anniversary, function( i, val ) {
                  $("#jqanni").after("<tr><td>"+val.empcode+"</td><td><span style='color:green'>"+val.original+"</span></span></td></tr>");
                });
              }
              if((result.Event).length>0){
                $('#eventTable').append("<thead style='position: sticky;top: 0px;'class='bg-success'><tr><th class='text-center'>Event Name</th><th class='text-center'>Date</th></tr></thead><tbody><tr id='jqeven'></tr></tbody>");
                $.each(result.Event, function( i, val ) {
                  $("#jqeven").after("<tr><td>"+val.events+"</td><td><span style='color:green'>"+val.date+"</span></span></td></tr>");
                });
              }
              $('#ajax_loader_div').css('display','none');
            }
        }
    })
}

$(document).ready(function(){
  $('.fc-day-grid-container').removeClass("fc-scroller").removeAttr("style");
  $('.fc-row').removeAttr("style");
  $('.fc-today-button').hide();
  $('.fc-agendaWeek-button').hide();
  $('.fc-agendaDay-button').hide();
})
</script>

<script>
    function getid(i){
    // debugger;
      var id = i;
      $("#del_id").val(id);
      $("#stat_del_id").val(id);
   }
    // Data Tables
    $(document).ready(function() {
     
      var last_ele = null ;
      var last_tr = null ;
      $('#table_del tbody').on('click', 'button.job_det', function () {
        var tr = $(this).parents('tr');
        var row = dataTable1.row( tr );
        var data=$(this).attr("id");
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
          if(last_ele)
          {
            //  last_ele.child.hide();     
          }
          $(this).parents('li').children('div').remove();
                
          $(this).parents('li').append('<center><div class="card" ><h5> Processing...</h5></div></center>');
              
          row.child('<center><div class="card" ><h5> Processing...</h5></div></center>').show();
          getdata1(data,row,this)

          last_ele=row;
          last_tr=tr;
          tr.addClass('shown');
        }
      });
      
    });

    function getdata1(data,ele,button)  {  
      var out;
      $('#ajax_loader_div').css('display','block');

      $.ajax({
               type:'get',
               url:"/all/completion/date/"+data,
               timeout:600000,
                   
               success:function(data) {
                $(button).parents('li').children('div').remove();
                $(button).parents('li').children('center').remove();
                
                $(button).parents('li').append(data);
                  ele.child(data).show();
                  $('#ajax_loader_div').css('display','none');

                }

            });

            return out;
    }

$(".c_date").datepicker({
    startDate:'today',
    format: 'd-m-yyyy'
});
$(".new_c_date").datepicker({
    startDate:'+1d',
    format: 'd-m-yyyy'
});
function show_as_stat()
{ 
  var status = $("#status").val();
  if(status == 'completed'){
    $("#completed_div").show();
    $("#notcompleted_div").hide();
  }else if(status == 'not completed'){
    $("#completed_div").hide();
    $("#notcompleted_div").show();
  }else{
    $("#completed_div").hide();
    $("#notcompleted_div").hide();
  }

}
$('#myModal_comp_date').on('hidden.bs.modal', function(){
  $(this).find('form')[0].reset();
});
$('#myModal_comp_stat').on('hidden.bs.modal', function(){
  $(this).find('form')[0].reset();
  $(".select2").val('').trigger("change");
});
$('#first_completion').submit(function(e){     
// debugger;
    e.preventDefault();
    var $form = $(this);
  // check if the input is valid
    if(! $form.valid()){
      return false; 
    }

    var c_date = $("#c_date").val();
    var del_id = $("#del_id").val();
  $('#ajax_loader_div').css('display','block');
  $.ajax({
      type:'get',
      url:"/add/completion",
      data:{'c_date':c_date,'del_id':del_id},
      contentType: "application/json",
      dataType: "json",
      success:function(result) {
          $('#ajax_loader_div').css('display','none');
          // debugger;
          if((result.error).length > 0){
            $("#fc_err").text(result.error).show();
            setTimeout(function() { 
                $('#fc_err').fadeOut('fast'); 
            }, 8000);
          }else if((result.msg).length > 0){
              $('#myModal_comp_date').modal('hide');
             
              $(".goodmsg").show();
              $("#mesg").text(result.msg);
          }
        }

    });
});

$('#completion_st_form').submit(function(e){     

    e.preventDefault();
    
    var formvalidation=$("#completion_st_form").valid();
        var formData = new FormData(this);
        
          if(formvalidation==true)
          {
              $('#ajax_loader_div').css('display','block');
               $.ajax({
                type:'POST',
                url: "/add/status/completion",
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(result) {
                // debugger;
                  $('#ajax_loader_div').css('display','none');
                  if((result.error).length > 0){
                    $("#fs_err").text(result.error).show();
                    setTimeout(function() { 
                        $('#fs_err').fadeOut('fast'); 
                    }, 8000);
                  }else if((result.msg).length > 0){
                     $('#myModal_comp_stat').modal('hide');
                    
                     $(".goodmsg").show();
                     $("#mesg").text(result.msg);
                }
              }
            });

          }
});
</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="modal_div"></div>
            <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
            @if ($errors->any())
              <div class="alert alert-danger">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li style="list-style:none">{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
             @endif
        <div class="box">
          <div class="box-header with-border">  
         
            <div class="col-md-6">
              <div class="card">
                <div class="card-header border-transparent" style="background-color:#dc2a14" id="wrapper">
              
                  <div class="card-tools" id="first">
                    <p class="col_head">Quick Links</p>
                  </div>
                  <div class="card-tools" id="second" >
                    <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#multiCollapseExample2" style="background: transparent;color: whitesmoke;">
                      <i class="fa fa-minus"></i>
                    </button>
               
                  </div>
                </div>
              
                <!-- /.card-header -->
                <div class="card-body p-0 scrr" id="multiCollapseExample2" style="height:240px;">
                  <div class="table-responsive">
                    <div class="col-md-3 divs">
                        <div style="border:1px solid red">
                        <center> <a href="/hr/leave/create"><i class="fa fa-pencil img"  ></i></a></center>
                      
                        </div>
                        <center>Leave Apply</center>
                    </div>
                  
                    <div class="col-md-3 divs">
                        <div style="border:1px solid red">
                        <center> <a href="/staff/directory"><i class="fa fa-users img"  ></i></a></center>
                      
                        </div>
                        <center>Staff Directory</center>
                    </div>
                
                    <div class="col-md-3 divs">
                        <div style="border:1px solid red">
                        <center> <a href="/hr/attendance/summary"><i class="fa fa-address-book img"  ></i></a></center>
                      
                        </div>
                        <center>Attendance List</center>
                    
                    </div>
                  </div>
                  <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix " style="background-color:#dc2a14;border-radius: 0px 0px 3px 3px;    height: 20px;">
                  <!-- <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a>
                  <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a> -->
                </div>
                <!-- /.card-footer -->
              </div>
            </div>
              
                      
                      @foreach($setting as $key)
                              @include('Widget.'.$key['template_name'])
                        
                      @endforeach

                      
                     
                 
             
         
          
         
          </div>
        </div >

      
      </section>
@endsection