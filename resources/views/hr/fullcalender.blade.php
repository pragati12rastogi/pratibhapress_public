
@extends($layout)

@section('title', 'Events Calendar')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Events Calendar</a></li> 
@endsection

@section('css')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
@endsection

@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script> -->
    {!! $calendar->script() !!}
    <script>
 
 function showModal(title,url){
    $('#modalTitle').html(title);
    $('#ajax_loader_div').css('display','block');
    
     $.ajax({
         type:'get',
         url:"/get/department/"+url,
         success:function(data) {
            debugger;
                if(data.dept != null && data.dept != ''){
                    $('#depart').text(data.dept);
                }else{
                    $('#depart').text("All Departments");
                }
            $('#ajax_loader_div').css('display','none');
            $('#calendarModal').modal();

          }

      });
 }
    </script>
 @endsection


@section('main_section')
<div class="container">
    <div class="row">
        <div class="col-md-11">
            <div class="panel panel-default">

               <center> <div class="panel-body" style="width:100%">
                {!! $calendar->calendar() !!}
   
                </div></center>
            </div>
        </div>
    </div>
</div>
<div id="calendarModal" class="modal fade">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
            <h4 id="modalTitle" class="modal-title"></h4>
        </div>
        <div id="modalBody" class="modal-body"><label style="display: inline;">Departments:</label> <p style="display: inline;" id="depart"></p> </div>
     
        <!-- <center><img src=""  id="eventUrl" alt="" style="height: 200px;width: 300px;"></center> -->
        <p id="event" style="text-align: center;font-size: 20px;color: red;"></p>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div> 
@endsection
