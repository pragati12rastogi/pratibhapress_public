@extends($layout)

@section('title', 'Recruitment Not Interviewed Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Recruitment Summary</a></li> 
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

    function f_check(){
    debugger
    var x =$("#round_f").is(':checked');
    if(x){
      $("#f_form").show();
    }else{
       $("#f_form").hide();
    }
  }

    var dataTable;
    var hr;
    $(document).ready(function()  {
   
       dataTable = $('#delivery_challan_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/hr/recruitment/not/list/api",
          "columns": [
                {"data":'name'},
                {"data":'contact'},
                {"data":'email'},
                {"data":'reference_from'},
                {"data":'interview_date'},
                {"data":'position_for'},
                {"data":'remark'},
                {
                  "targets": [ -1 ],
                  data: function(data,type,full,meta)
                  {
                    if(data.resume == null){
                    return '<a href="/hr/interview/assess/print/'+data.id+'" ><button class="btn btn-success btn-xs"> Print </button></a>&nbsp;'+
                    '<a onclick="cancel_alert_dailog('+data.id+')"><button class="btn btn-warning btn-xs"> Assessment Details </button></a>' 
                    ;
                  }else{
                    return  '<a href="/upload/recruitment_resume/'+data.resume+'" target="_blank"><u>View File</u></a>&nbsp;' +
                    // '<a href="/purchase/indent/update/'+data+'" target="_blank"><button class="btn btn-primary btn-xs"> Edit </button></a>&nbsp;' +
                    '<a href="/hr/interview/assess/print/'+data.id+'" ><button class="btn btn-success btn-xs"> Print </button></a>&nbsp;'+
                    '<a onclick="cancel_alert_dailog('+data.id+')"><button class="btn btn-warning btn-xs"> Assessment Details </button></a>' 
                    ;
                  }
                  }
              }
            ],
            "columnDefs": [
              
               
              { "orderable": false, "targets": 7 },
          
          ]
          
        });
    });

    function cancel_alert_dailog(id)
    {
      $.ajax({
               type:'get',
               url:"/hr/interview/assess/log/"+id,
               timeout:600000,
                   
               success:function(data) {
                
                 var rec=data[0];
                
                $('#modal_div').empty();
      $('.select2').select2('destroy');
      $('#modal_div').append(
            '<div id="myModal" class="modal fade" role="dialog">'+
              '<div class="modal-dialog modal-lg">'+
                '<!-- Modal content-->'+
                '<div class="modal-content">'+
                  '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                    '<h4 class="modal-title">Interview Assessment Details</h4>'+
                  '</div>'+
                  '<form id="infos" method="POST" action="/hr/recruitment/assess/create">'+
                    '@csrf'+
                    '<div class="modal-body">'+
                      '<input type="hidden" name="id" value="'+id+'">'+
                      '<br><label>Please Fill Below OPTIONS for Recruitment Assessment</label>'+
                      '<div class="row">'+
                          '<div class="col-md-12">'+
                              '<label class="checkbox-inline col-md-3" ><input type="checkbox"  value="Preliminary" id="round_p" name="round[]" onchange="p_check()" required>Preliminary Round</label>'+
                              '<label class="checkbox-inline col-md-3" ><input type="checkbox" onchange="f_check()" id="round_f" name="round[]" value="Final" required>Final Round</label>'+
                          '</div>'+
                      '</div><br><br>'+
                      '<div id="p_form" style="display:none">'+
                      '<h4>Preliminary Round</h4><br>'+
                      '<div class="row">'+
                        '<div class="col-md-6">'+
                            ' <label for="">Preliminary Round Conducted by :<sup>*</sup></label>'+
                                '<select name="prelim_round_by" id="" class="select2 input-css" style="width:100%" required>'+
                                  '<option value="">Select Name</option>'+
                                  @foreach ($emp as $item)
                                '<option value="{{$item->id}}">{{$item->name}}</option>'+
                                  @endforeach
                                '</select>'+
                                '<label id="prelim_round_by-error" class="error" for="prelim_round_by"></label>'+
                        '</div>'+
                        '<div class="col-md-6">'+
                            ' <label for="">Preliminary Interview Remarks :<sup>*</sup></label>'+
                            '<input type="text" name="remark" id="" class="remark input-css" required>'+
                        '</div>'+
                      '</div>'+
                      '<div class="row">'+
                        '<div class="col-md-6">'+
                            '<label for="">Post Suited : <sup>*</sup></label>'+
                            '<input type="text" name="post_suited" id="" class="post_suited input-css" required>'+
                        '</div>'+
                        '<div class="col-md-6">'+
                            '<label for="">Proposed Department :<sup>*</sup></label>'+
                              '<select name="dept" id="" class="select2 input-css" style="width:100%" required>'+
                                '<option value="">Select Department</option>'+
                                @foreach ($dep as $item)
                                '<option value="{{$item->id}}">{{$item->department}}</option>'+
                                @endforeach
                              '</select>'+
                              '<label id="dept-error" class="error" for="dept"></label>'+
                        '</div>'+
                      '</div><br>'+
                      // '<div class="row">'+
                      //   '<div class="col-md-6">'+
                      //       '<label for="">Salary Expected : <sup></sup></label>'+
                      //       '<input type="number" step="none" name="salary" id="" class="salary input-css" >'+
                      //   '</div>'+
                      // '</div><br>'+
                    '</div>'+
                    '<div id="f_form" style="display:none">'+
                      '<h4>Final Round</h4><br>'+
                      '<div class="row">'+
                        '<div class="col-md-6">'+
                            ' <label for="">Final Round Conducted by :<sup>*</sup></label>'+
                                '<select name="final_round_by" id="final_round_by" class="select2 input-css" style="width:100%" required>'+
                                  '<option value="">Select Name</option>'+
                                  @foreach ($emp as $item)
                                '<option value="{{$item->id}}">{{$item->name}}</option>'+
                                  @endforeach
                                '</select>'+
                                '<label id="final_round_by-error" class="error" for="final_round_by"></label>'+
                        '</div>'+
                        '<div class="col-md-6">'+
                            ' <label for="">Final Interview Remarks :<sup>*</sup></label>'+
                            '<input type="text" name="finalremark" id="finalremark" class="finalremark input-css" required>'+
                        '</div>'+
                      '</div>'+
                      '<div class="row">'+
                        '<div class="col-md-6">'+
                            '<label for="">Post Suited : <sup>*</sup></label>'+
                            '<input type="text" name="final_post_suited" id="final_post_suited" class="final_post_suited input-css" required>'+
                        '</div>'+
                        '<div class="col-md-6">'+
                            '<label for="">Proposed Department :<sup>*</sup></label>'+
                              '<select name="final_dept" id="final_dept" class="final_dept select2 input-css" style="width:100%" required>'+
                                '<option value="">Select Department</option>'+
                                @foreach ($dep as $item)
                                '<option value="{{$item->id}}">{{$item->department}}</option>'+
                                @endforeach
                              '</select>'+
                              '<label id="final_dept-error" class="error" for="final_dept"></label>'+
                        '</div>'+
                      '</div><br>'+
                      // '<div class="row">'+
                      //   '<div class="col-md-6">'+
                      //       '<label for="">Salary Expected : <sup></sup></label>'+
                      //       '<input type="number" step="none" name="finalsalary" id="finalsalary" class="finalsalary input-css" >'+
                      //   '</div>'+
                      //   '<div class="col-md-6">'+
                      //     '<label for="">Expected Joining Date : <sup></sup></label>'+
                      //     '<input type="text" name="finaljoining_date"  class="finaljoining_date input-css datepicker" >'+
                      //   '</div>'+
                      // '</div>'+
                       '<div class="row">'+
                       '<div class="col-md-12"><label for="">Final Interview Remarks by G.M. / President : <sup>*</sup></label>'+
                      '<textarea name="final_remark" id="" cols="30" rows="5" class="input-css" required></textarea></div>'+
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
         
          $('#round_p').prop('checked',true);
          p_check();
          $('select[name=prelim_round_by]').val(rec['round_by']).select2().trigger('change');
          $('input[name=remark]').val(rec['remarks']);
          $('input[name=post_suited]').val(rec['post_suited']);
          $('select[name=dept]').val(rec['proposed_dept']).select2().trigger('change');
          $('input[name=salary]').val(rec['salary_expect']);
          $('.datepicker').datepicker({
              autoclose: true,
              format: 'd-m-yyyy'
          });
               }
        });     

  
         
  }
  function p_check(){
    var x =$("#round_p").is(':checked');
    if(x){
      $("#p_form").show();
    }else{
       $("#p_form").hide();
    }
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
            <div id="modal_div"></div>
            @section('titlebutton')
            {{-- <a href="{{url('/hr/setting')}}"><button class="btn btn-primary">Settings</button></a> --}}

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