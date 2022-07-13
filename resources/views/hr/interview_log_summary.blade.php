@extends($layout)

@section('title', 'Interview Log Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Interview Log</a></li> 
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

</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
 $.validator.addMethod("notValidIfSelectFirst", function(value, element, arg) {
        return arg !== value;
    }, "This field is required.");

    $('#infos').validate({ // initialize the plugin
        rules: {

            status: {
                required: true
            },
            
        }
    });
</script>
  <script>
    var dataTable;
    var hr;
    $(document).ready(function()  {
      var last_ele = null ;
      var last_tr = null ;
       dataTable = $('#delivery_challan_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/hr/recruitment/interview/log/api",
          "columns": [
                {"data":'name'},
                {"data":'contact'},
                {"data":'email'},
                {"data":'reference_from'},
                {"data":'interview_date'},
                {"data":'position_for'},
                {"data":'remark'},
                {"data":'status',"render": function(data,type,full,meta){
                    return data +"<br><button id="+full.id+" class='job_det btn btn-warning btn-xs'>Assessment</button> &nbsp;";
                }},
                {
                  "targets": [ -1 ],
                  data: function(data,type,full,meta)
                  {
                    var btns ='<a href="/hr/recruitment/data/update/'+data.id+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit </button></a>&nbsp;'+
                      '<a onclick="alert_status('+data.id+')"><button class="btn btn-foursquare btn-xs"> Status </button></a>&nbsp;';
                    if(data.resume != null){
                      btns =btns+'<a href="/upload/recruitment_resume/'+data.resume+'" target="_blank"><u>View File</u></a>&nbsp;';
                    }
                    if(data.status == 'hired' || data.status == 'Hired but not joined'){
                      btns = btns+'<a href="/recruitment/template/pdf/'+data.id+'/Offer Letter" class="btn btn-xs btn-microsoft" target="_blank">Offer Letter</a>&nbsp;';
                        if(data.signed_document == null){
                          btns = btns+"<button onClick='btn_upload_signed("+data.id+")' class='btn btn-success btn-xs'data-toggle='modal' data-target='#upload_modal'>Upload Signed Letter</button> &nbsp;";
                        }else{
                          btns = btns+'<a href="/upload/signed_offer_letter/'+data.signed_document+'" class="btn-link" target="_blank"><u>Signed Offer Letter</u></a>&nbsp;';
                        }
                      
                    }
                    return btns;
                  }
              }
            ],
            "columnDefs": [
              
               
              { "orderable": false, "targets": 8 },
          
          ]
          
        });
        $('#delivery_challan_list_table tbody').on('click', 'button.job_det', function () {
          
        var tr = $(this).parents('tr');
        var row = dataTable.row( tr );
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
    } );

  $('#offer').submit(function(e){     
    debugger;
    e.preventDefault();
    
    var formvalidation=$("#offer").valid();
        var formData = new FormData(this);
        
          if(formvalidation==true)
          {
              $('#ajax_loader_div').css('display','block');
               $.ajax({
                type:'POST',
                url: "/hr/signed/offer/letter",
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(result) {
                debugger;
                  $('#ajax_loader_div').css('display','none');
                  if((result.error).length > 0){
                    $("#fs_err").text(result.error).show();
                    setTimeout(function() { 
                        $('#fs_err').fadeOut('fast'); 
                    }, 8000);
                  }else if((result.msg).length > 0){
                     $('#upload_modal').modal('hide');
                     dataTable.draw();
                     $(".goodmsg").show();
                     $("#mesg").text(result.msg);
                }
              }
            });

          }
      });

    });
    function btn_upload_signed(id){
     $("#offer_emp_id").val(id);
    }
    $('#upload_modal').on('hidden.bs.modal', function(){
      $(this).find('form')[0].reset();
    });
  
    function alert_status(id){
      // debugger;
      $('#modal_div').empty();
      $('.select2').select2('destroy');
      $('#modal_div').append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Interview Status</h4>'+
                  '</div>'+
                  '<form id="infos" method="GET" action="/hr/recruitment/assess/update/">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      '<input type="hidden" name="id" value="'+id+'">'+
                      '<div class="row">'+
                          '<div class="col-md-6">'+
                              '<label for="">Status :<sup>*</sup></label>'+
                              '<select name="status" id="status" class="select2 input-css" style="width:100%" required>'+
                                '<option value="">Select Status</option>'+
                                '<option value="hired">Hired</option>'+
                                '<option value="Hired but not joined">Hired but not joined</option>'+
                                '<option value="not hired">Not Hired</option>'+
                                '<option value="pending">Pending</option>'+
                                
                              '</select>'+
                              '<label id="status-error" class="error" for="status"></label>'+
                          '</div>'+
                      '</div>'+
                      '<div class="row pikabo" style="display:none">'+
                          '<div class="col-md-6">'+
                            '<label for="">Salary Expected : <sup></sup></label>'+
                            '<input type="number" step="none" name="salary" id="" class="salary input-css" required>'+
                          '</div>'+
                          '<div class="col-md-6">'+
                          '<label for="">Expected Joining Date : <sup></sup></label>'+
                          '<input type="text" name="finaljoining_date"  class="finaljoining_date input-css datepicker" required autocomplete="off">'+
                        '</div>'+
                      '</div>'+
                    '</div>'+
                    '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>&nbsp;&nbsp;'+
                      '<a target="_blank"><button type="submit" class="btn btn-success"  onclick="$(\'#infos\').validate();">Submit</button></a>'+
                    '</div>'+
                    '</form>'+
                    '</div>'+
                '</div>'+
              '</div>'+
            '</div>'
      );
       $(document).find('#myModal').modal("show");
        $('.select2').select2();
        $('.datepicker').datepicker({
              autoclose: true,
              format: 'd-m-yyyy'
        });
        $('#status').change(function(){
          if($(this).val() == "hired"){
            $(".pikabo").show();
          }else{
            $(".pikabo").hide();
          }
        })
    }
    function getdata1(data,ele,button)  {  
      var out;
      $('#ajax_loader_div').css('display','block');

      $.ajax({
               type:'get',
               url:"/hr/interview/assess/data/"+data,
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
  </script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        <div class="alert alert-success alert-block goodmsg" style="display: none;">
              <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong id="mesg"></strong>
            </div>
        <div class="box">
            <div id="modal_div"></div>
            <div id="upload_offer"></div>
            @section('titlebutton')
            <!-- <a href="{{url('/hr/setting')}}"><button class="btn btn-primary">Settings</button></a> -->

            @endsection
            <div class="box-header with-border">
            </div>
            <div class="box-body">
              <table id="delivery_challan_list_table" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Candidate Name</th>
                    <th>Contact Number</th>
                    <th>Email Id</th>
                    <th>Reference From</th>
                    <th>Interview Date</th>
                    <th>Positioned For</th>
                    <th>Remark</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
  
                  </tbody>
             
              </table>
            </div>
            <div id="upload_modal" class="modal fade" role="dialog">
              <div class="modal-dialog modal-lg">
                
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Upload Signed Offer Letter</h4>
                  </div>
                  <form id="offer" method="POST" action="" enctype="multipart/form-data">
                    @csrf            
                    <span id="fs_err" style="color:red; display: none;"></span>
                    <div class="modal-body">
                      <input type="hidden" name="offer_emp_id" id="offer_emp_id">
                        <div class="row">
                          <div class="col-md-12">
                              <label for="">Upload Signed File:<sup>*</sup></label>
                              <input type="file" accept="application/msword, application/pdf" name="signed_file" id="signed_file" class="signed_file " required="">
                              <label id="signed_file-error" class="error" for="signed_file"></label>
                          </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>&nbsp;&nbsp;
                      <a target="_blank"><button type="submit" class="btn btn-success">Submit</button></a>
                    </div>
                    </form>
                    </div>
                </div>
              </div>
          </div>
        <!-- /.box -->
      </section>
@endsection