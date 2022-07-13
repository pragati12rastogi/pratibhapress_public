@extends($layout)

@section('title', 'Salary C Summary')

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Salary C Summary</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
@media (max-width: 768px){
    .content-header>h1 {
      display: inline-block;
    }
  }
  @media (max-width: 425px){   
    .content-header>h1 {
      display: inline-block;
    }
  }
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>

 $("#error").hide();
$("#js-msg-success").hide();
$("#js-msg-error").hide();
    var dataTable;

    function create_datatable(){
       if(dataTable){
        dataTable.destroy();
      }
      dataTable = $('#att_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/employee/salary/list/c/api",
            "datatype": "json",
                "data": function (data) {
                    var emp_name = $('#emp_name').val();
                    data.emp_name = emp_name;
                    var month = $('#month').val();
                    data.month = month;
                    var year = $(".datepicker-switch").html();
                    data.year = year;
                },
            },
          "columns": [
              {"data":"name"},
              {"data":"acc_number"},
              {"data":"acc_ifsc"},
              {"data":"net_salary"},
              {"data":"paid"},
              {"data":"net_salary",
                "render": function(data,type,full,meta) {
                      return data-full.paid;
                  }
              },
              {"data":"id",
                 "render": function(data,type,full,meta) {
                     var str = '';
                      str = str+'<a href="#"><button class="btn btn-info btn-xs "  onclick="SalaryPayment(this)"><i class="'+data+'"></i>Pay</button></a> ';
                      str = str+"<button id="+data+" class='all_month btn btn-warning btn-xs'>Details</button> &nbsp; ";
                      return str;
                  }
              }
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": 6 }
            ]
           
      });
      var last_ele = null ;
      var last_tr = null ;
      $('#att_table tbody').on('click', 'button.all_month', function () {
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
      });
    }


    // Data Tables
    $(document).ready(function() {
      create_datatable();
    });

    $('#emp_name').on( 'change', function () {
      dataTable.draw();
    });

    $('#month').datepicker({
      format: "M",
      weekStart: 1,
      orientation: "bottom",
      keyboardNavigation: false,
      viewMode: "months",
      minViewMode: "months",
      autoclose: true
    });
    $('#month').on('change', function () {
      create_datatable();
    });

       $("#export_anchor").click(function(){
         geturl();
    });
    
    function geturl(){
        var emp = $('#emp_name').val();
        var month = $('#month').val();
       $("#export_anchor").attr("href", '/export/data/salary/c?emp='+emp+'&month='+month);
   
    }


     function SalaryPayment(el){
      var type = 'SalaryC';
      $("#utr").hide();
      var id = $(el).children('i').attr('class');
      $('#ajax_loader_div').css('display','block');
      $.ajax({  
                url:"/employee/salary/list/c/payment/details/"+id+'/'+type,  
                method:"get",  
                success:function(data){
                  $('#ajax_loader_div').css('display','none');
                  if (data.msg == 'error') {
                      $("#js-msg-error").html('Record not found.');
                      $("#js-msg-error").show();
                       setTimeout(function() {
                         $('#js-msg-error').fadeOut('fast');
                        }, 4000);
                  }else{
                      if(data) {
                        $("#id").val(id);
                        var balance = data.salary.net_salary-data.payment;
                        $("#net_salary").html('Net Salary : '+data.salary.net_salary);
                        $("#paid").html('Paid : '+data.payment);
                        $("#balance").html('Balance : '+balance);
                        $('#exampleModalCenter').modal("show"); 
                      }
                    }   
                }
           });  
    }

     function BtnSubmit(){
      var type = 'SalaryC';
      var payment_mode = $("#payment_mode").val();
      var amount = $("#amount").val();
      var utr_no = $("#utr_no").val();
      var id = $("#id").val();
        $('#ajax_loader_div').css('display','block');
        $.ajax({
          url: "/employee/salary/list/c/payment",
          data: {
            'id': id,
            'payment_mode'  : payment_mode,
            'amount' : amount,
            'utr_no' : utr_no,
            'type' : type
          },
          method: 'GET',
          success: function (result) {
            var original_result = result;
            if(result == 'success') {
              $('#exampleModalCenter').modal("hide"); 
              $('#att_table').dataTable().api().ajax.reload();
              $("#js-msg-success").html('Payment added successfully.');
              $("#js-msg-success").show();
              setTimeout(function() {
                 $('#js-msg-success').fadeOut('fast');
                }, 4000);
            $('#form')[0].reset(); 
            }else {
              $("#error").html(result);
              $("#error").show();
              setTimeout(function() {
                 $('#error').fadeOut('fast');
                }, 4000);
            }  
             
              
          
          },
          error: function(ex){
            $('#exampleModalCenter').modal("hide"); 
            $('#ajax_loader_div').css('display','none');
            alert('Some Error Occured.');
          }
        }).done(function(){
         $('#ajax_loader_div').css('display','none');
        });
     
    }

     $("#payment_mode").change(function(){
        var mode = $("#payment_mode").val();
        if (mode == 'Cheque') {
            $("#utr").show();
        }else{
            $("#utr").hide();
        }
    });
    function getdata1(data,ele,button)  {  
      var out;
      var type = 'SalaryC';
      $('#ajax_loader_div').css('display','block');
      
      $.ajax({
               type:'get',
               url:"/salary/payment/details/"+data+"/"+type,
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
                    <div class="row">
                      <div class="alert alert-success" id="js-msg-success"></div>
                      <div class="alert alert-danger" id="js-msg-error"></div>
                    </div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    
                    @endsection
                    <div class="row">
                      <div class="col-md-3">
                         <label>Employee Name</label>
                         
                            <select name="emp_name" class="input-css select2 selectValidation" id="emp_name">
                               <option value="">Select Employee</option>
                               @foreach($employee as $key => $val)
                               <option value="{{$val['id']}}"> {{$val['name']}}({{$val['employee_number']}})</option>
                               @endforeach
                            </select>
                         
                         {!! $errors->first('emp_name', '
                         <p class="help-block">:message</p>
                         ') !!}
                      </div>
                      <div class="col-md-3" >
                        <label>Select Month</label>
                        <input type="text" name="month" id="month" class="input-css" autocomplete="off">
                      </div>
                      <div class="col-md-1 pull-right">
                        <a href="/export/data/salary/c" id="export_anchor">
                          <button class="btn btn-info">Export</button>
                        </a>
                      </div>
                    </div><br>
                    <table id="att_table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>Name</th>
                          <th>Account Number</th>
                          <th>IFSC</th>
                          <th>Net Salary</th>
                          <th>Paid</th>
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
         <div id="exampleModalCenter" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Salary Payment</h4>
                    </div>
                    <div class="modal-body">
                      <form id="form" method="post">
                        @csrf
                        <div class="alert alert-danger" id="error"></div>
                        <div class="row">
                          <div class="col-md-12 margin-bottom">
                           <div class="col-md-4">
                              <label id ="net_salary"></label>
                           </div>
                            <div class="col-md-4">
                              <label id ="paid"></label>
                           </div>
                            <div class="col-md-4">
                              <label id ="balance"></label>
                           </div>
                          </div>
                          <input type="hidden" name="id" id="id">
                          <div class="col-md-12">
                          <div  class="col-md-6">
                            <label class="md_label"> Mode of Payment <sup>*</sup></label>
                            <select name="payment_mode" id="payment_mode" class="input-css select" style="width:100%" tabindex="-1" aria-hidden="true">
                                  <option value="">Select Payment Mode</option>
                                  <option value="BankDraft">Bank Draft</option>
                                  <option value="CardPayment">Card Payment</option>
                                  <option value="Cash">Cash</option>
                                  <option value="CashDepositAtBank">Cash Deposit At Bank</option>
                                  <option value="Cheque">Cheque</option>
                                  <option value="NEFT/RTGS/IMPS">NEFT/RTGS/IMPS</option>
                                  <option value="Paytm">Paytm</option>
                                  <option value="UPI">UPI</option>
                          </select>
                          </div>
                          <div  class="col-md-6">
                            <label class="md_label"> Amount <sup>*</sup></label>
                            <input type="number" name="amount" class="input-css amount"  id="amount" min="0">
                          </div>
                          
                          </div>
                        </div><br>
                        <div class="row">
                          <div class="col-md-12">
                            <div  class="col-md-6" id="utr">
                            <label class="md_label"> UTR Number <sup>*</sup></label>
                            <input type="text" name="utr_no" class="input-css utr_no" id="utr_no" >
                          </div>
                          </div>
                        </div>
                       <div class="row">
                      <div class="col-md-12">
                           <input type="button" style="float:right" class="btn btn-primary" value="Submit" onclick="BtnSubmit()">
                      </div>
                    </form>
                  </div>
                    </div>
                  
                </div>
            </div>
      </div>      </section>
@endsection