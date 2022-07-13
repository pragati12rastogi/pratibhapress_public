@extends($layout)

@section('title', 'Delegation Task')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Delegation Task</a></li> 
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
  .nav-pills>li {
    
    border: 1px solid #a9a0a0;
}
.select2{
  width: 160px;
}
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script src="/js/Delegate/delegatelist.js"></script>
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable1;
    
   
    function del(){ 
      if(dataTable1){
        dataTable1.destroy();
      }   
      dataTable1 = $('#table_del').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/delegation/employee/summary/api",
          "createdRow": function( row, data, dataIndex){
                var cd =[];
                if(data.completion_date){
                  cd = (data.completion_date).split(',');
                }else{
                  cd =[];
                }
                var ds = [];
                if(data.dele_stat){
                  ds = (data.dele_stat).split(',');
                }else{
                  ds =[];
                }
                var c_cd = cd.length;
                // debugger;
                var final_st = data.final_stat;
                  if(data.final_stat){
                    final_st =(data.final_stat).split(',');
                  }else{
                    final_st =[];
                  }
                if( c_cd ==  1 && ds.includes('completed') && final_st.includes('completed')){
                  $(row).addClass('bg-green');
                }else if(c_cd ==  2 && ds.includes('completed') && final_st.includes('completed')){
                  $(row).addClass('bg-yellow');
                }
                else if(c_cd >=  3 && ds.includes('completed') && final_st.includes('completed')){
                  $(row).addClass('bg-red');
                }
                
            },
          "columns": [
            {"data":"empName"},
              {"data":"task_detail"},
              {"data":"assign_date"},
              {"data":"ass"},
              {"data":"deadline","render": function(data,type,full,meta)
                  {  
                    if(data == "1970-01-01"){
                      return "";
                    }else{
                      return data;
                    }
                   }},
              {"data":"requirements"},
              {"data":"dele_stat","render": function(data,type,full,meta)
                  {   
                      var ds = [];
                      if(data){
                        ds = (data).split(',');
                      }else{
                        ds =[];
                      }
                      var final_st = full.final_stat;
                      if(full.final_stat){
                        final_st =(full.final_stat).split(',');
                      }else{
                        final_st =[];
                      }
                      // debugger;
                      console.log('status');
                      console.log(final_st);
                      console.log(ds);
                      if(ds[0]=='completed' ){
                        if(final_st[0]=='not completed'){
                          return 'Not Completed';
                        }else{
                          return 'Completed';
                        }
                      }else{
                        return 'Not Completed';
                      }
                  }},
              {
                "targets": [ -1 ],
                "data":"id","render": function(data,type,full,meta)
                {
                  console.log("completion dates: "+full.completion_date);
                  // debugger;
                  var cd =full.completion_date;
                  if(full.completion_date){
                    cd = (full.completion_date).split(',');
                  }else{
                    cd =[];
                  }
                  var ds = full.dele_stat;
                  if(full.dele_stat){
                    ds = (full.dele_stat).split(',');
                  }else{
                    ds =[];
                  }
                  var arr_merge = [];
                  for(var i=0;i<cd.length;i++){
                    arr_merge[cd[i]]= ds[i];
                  }
                  var final_st = full.final_stat;
                  if(full.final_stat){
                    final_st =(full.final_stat).split(',');
                  }else{
                    final_st =[];
                  }
                  var now = new Date();
                  var form_date =now.toISOString().substr(0,10);

                  if(full.completion_date == null && new Date(full.assign_date)<=now || final_st[0]=='not completed'){
                    return "<button id="+data+" onClick='getid("+data+")' class='comple btn btn-primary btn-xs' data-toggle='modal' data-target='#myModal_comp_date'>Add Completion Date</button> &nbsp;"+
                    '<button id='+data+' class="job_det btn btn-openid btn-xs">Details</button>&nbsp;'+'<a href="/delegation/status/details/summary/'+data+'" class="btn btn-xs btn-success">Status Detail</a>'
                    ;
                    // return "";
                  }else if(cd[0]==form_date && ds[0]== 'pending'){
                    // arr_merge[form_date] =='pending'
                    console.log("delegate_id "+data);
                    return "<button id="+data+" onClick='getid("+data+")' class='stat btn btn-primary btn-xs'data-toggle='modal' data-target='#myModal_comp_stat'>Add Status</button> &nbsp;"+
                    '<button id='+data+' class="job_det btn btn-openid btn-xs">Details</button>&nbsp;'+
                    '<a href="/delegation/status/details/summary/'+data+'" class="btn btn-xs btn-success">Status Detail</a>'
                    ;
                  // }else if(ds.includes('completed')){
                  //   return '<button id='+data.id+' class="job_det btn btn-openid btn-xs">Details</button>&nbsp;';
                  }else{
                    return '<button id='+data+' class="job_det btn btn-openid btn-xs">Details</button>&nbsp;'+'<a href="/delegation/status/details/summary/'+data+'" class="btn btn-xs btn-success">Status Detail</a>';
                  }
                }
              }
            
            ],
            "columnDefs": [
              { "orderable": false, "targets": 7 }
            
            ]
          
        });
    }
   
   function getid(i){
    // debugger;
      var id = i;
      $("#del_id").val(id);
      $("#stat_del_id").val(id);
   }
    // Data Tables
    $(document).ready(function() {
      del();
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
              del();
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
                     del();
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
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')

            <div class="alert alert-success alert-block goodmsg" style="display: none;">
              <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong id="mesg"></strong>
            </div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="tab-content"> 
                    
                      <table id="table_del" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                        <th>Employee</th> 
                          <th>Task</th>
                          <th>Assign Date</th>
                          <th>Assign By</th>
                          <th>Deadline</th>
                          <th>Requirement</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    
                    
                  </div>   
                  
                </div>
                <!-- /.box-body -->
                <div id="myModal_comp_date" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
        
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add Completion Date</h4>
                            </div>
                            <div class="modal-body">
                                <form action="" method="get" id="first_completion">
                                  <span id="fc_err" style="color:red; display: none;"></span>
                                  <div class="row">
                                    <div class="col-md-6 {{ $errors->has('c_date') ? 'has-error' : ''}}">
                                        <label for="">Completion Date<sup>*</sup></label>
                                        <input type="text" name="c_date" id="c_date" autocomplete="off" 
                                        class="input-css c_date datepickers" required="">
                                        {!! $errors->first('c_date', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <input type="text" name="del_id" id="del_id" hidden="">
                                  </div><br><br>
                                
                                  <!-- return confirm("Are you sure you want to update Completion Date"); -->
                                  <div class="modal-footer">
                                      <input type="submit" value="Update" class="btn btn-primary">&nbsp;&nbsp;
                                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                  </div>
                                </form>
                            </div>
        
                        </div>
                    </div>
                </div>
                <div id="myModal_comp_stat" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                  
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add Status</h4>
                            </div>
                            <div class="modal-body">
                                <form action="" id="completion_st_form" method="post" enctype="multipart/form-data">
                                  @csrf
                                  <span id="fs_err" style="color:red; display: none;"></span>
                                  <input type="text" name="stat_del_id" id="stat_del_id" hidden>
                                  <div class="row">
                                    <div class="col-md-6 {{ $errors->has('status') ? 'has-error' : ''}}">
                                        <label for="">Status<sup>*</sup></label>
                                        <select name="status" id="status" class="input-css select2" onchange="show_as_stat()" required="">
                                          <option value="">Select status</option>
                                          <option value="completed">Completed</option>
                                          <option value="not completed">Not Completed</option>
                                        </select>
                                        {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    
                                  </div><br><br>
                                  <div class="row" id="completed_div" style="display: none;">
                                      <div class="col-md-6 {{ $errors->has('detail') ? 'has-error' : ''}}">
                                         <label for="">Details<sup>*</sup></label>
                                         <textarea id="detail" name="detail" class="detail input-css" ></textarea>
                                      </div>
                                      <div class="col-md-6 {{ $errors->has('img') ? 'has-error' : ''}}">
                                        <label for="">Image of Job Completion<sup>*</sup></label>
                                        <input type="file" accept="image/x-png,image/gif,image/jpeg" name="img" id="img" class="img " >
                                        {!! $errors->first('img', '<p class="help-block">:message</p>') !!}
                                    </div>
                                  </div><br>
                                  <div class="row" id="notcompleted_div" style="display: none;">
                                      <div class="col-md-6 {{ $errors->has('new_c_date') ? 'has-error' : ''}}">
                                         <label for="">New Promised Completion Date<sup>*</sup></label>
                                         <input type="text" name="new_c_date" id="new_c_date" autocomplete="off"
                                        class="input-css new_c_date datepickers" >
                                        {!! $errors->first('new_c_date', '<p class="help-block">:message</p>') !!}
                                      </div>
                                  </div><br>
                                  <div class="modal-footer">
                                      <input type="submit" value="Update" class="btn btn-primary">&nbsp;&nbsp;
                                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                  </div>
                                </form>
                            </div>
        
                        </div>
                    </div>
                </div>
              </div>
        <!-- /.box -->
      </section>
@endsection