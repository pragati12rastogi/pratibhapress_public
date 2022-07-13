@extends($layout)

@section('title', 'Bonus Calculator  ')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Bonus Calculator </a></li> 
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
.md_label{
  display: inline;
}
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;
   
    // Data Tables
    $(document).ready(function() {
      getbonus();
    });

    function finance(bonus){
    // alert(bonus);
        if(dataTable){
          dataTable.destroy();
        }
        dataTable = $('#bonus').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url":"/bonus/calculator/api",
            "datatype": "json",
            "data": function (data) {
                var fyear = $('#fyear').val();
                data.fyear = fyear;
            }
          },
          "columns": [
            {"data":"employee_number"},
            {"data":"name"},
            {"data":"doj"},
            {"data":"total_sal_a"},
            {"data":"bonus"},
            {"data":"checkdiff"},
            {"data": function(data,type,full,meta){
                return "<br><button id="+data.emp_id+" class='all_month btn btn-warning btn-xs'>Details</button> &nbsp;";
            }},
            {"data": function(data,type,full,meta){
                // var da;
                // if(data.payroll_apr != null){
                //    da = (data.payroll_apr).split(',');
                // }else{
                //   da=0;
                // }
                // var add = 0;
                // if(da){
                //   for(var i=0;i<da.length;i++){
                //     add= parseFloat(add)+parseFloat(da[i]);
                //   }
                // }
                if(data.tot_sal_pre_apr==0){
                  data.tot_sal_pre_apr=data.total_sal_a;
                }
             
                var total = (parseFloat(data.tot_sal_pre_apr)+parseFloat(data.payroll_apr))*parseFloat(data.apr_sep);
                if(isNaN(total)) total=0;
                return total.toFixed(2);
            }},
            {"data": function(data,type,full,meta){
              // var da;
              // if(data.payroll_oct != null){
              //    da = (data.payroll_oct).split(',');
              // }else{
              //   da=0;
              // }
                  
              //   var add = 0;
              //   if(da){
              //     for(var i=0;i<da.length;i++){
              //       add= parseFloat(add)+parseFloat(da[i]);
              //     }
              //   }
              if(data.tot_sal_pre_oct==0){
                  data.tot_sal_pre_oct=data.total_sal_a;
                }
                var total = (parseFloat(data.tot_sal_pre_oct)+parseFloat(data.payroll_oct))*parseFloat(data.oct_mar);
                if(isNaN(total)) total=0;
                return total.toFixed(2);
            }},
            {"data": function(data,type,full,meta){
              
              //apr-sep
                // var da_apr;
                // if(data.payroll_apr != null){
                //    da_apr = (data.payroll_apr).split(',');
                // }else{
                //   da_apr=0;
                // }
                // var add_apr = 0;
                // if(da_apr){
                //   for(var i=0;i<da_apr.length;i++){
                //     add_apr= parseFloat(add_apr)+parseFloat(da_apr[i]);
                //   }
                // }
              var total_apr = (parseFloat(data.tot_sal_pre_apr)+parseFloat(data.payroll_apr))*parseFloat(data.apr_sep);
              if(isNaN(total_apr)) total_apr=0;
     
              var total_oct = (parseFloat(data.tot_sal_pre_oct)+parseFloat(data.payroll_oct))*parseFloat(data.oct_mar);
              if(isNaN(total_oct)) total_oct=0;
              var tot=total_apr+total_oct;
              var tot_sal_pre_apr=data.tot_sal_pre_apr;
              var tot_sal_pre_oct=data.tot_sal_pre_oct;
              var total_sal_a=data.total_sal_a;

              if(tot_sal_pre_oct==0 && tot_sal_pre_apr==0){
                var calc = (parseFloat(data.bonus) * parseFloat(data.checkdiff)*parseFloat(bonus))*parseFloat(total_sal_a)/(parseFloat(total_apr)+parseFloat(total_oct));
                
              }
              else if(tot_sal_pre_oct!=0 && tot_sal_pre_apr==0){
                var calc = (parseFloat(data.bonus) * parseFloat(data.checkdiff)*parseFloat(bonus))*parseFloat(tot_sal_pre_oct)/(parseFloat(total_apr)+parseFloat(total_oct));
                
              }
              else if(tot_sal_pre_oct!=0 && tot_sal_pre_apr!=0){
                var calc = (parseFloat(data.bonus) * parseFloat(data.checkdiff)*parseFloat(bonus))*parseFloat(tot_sal_pre_oct)/(parseFloat(total_apr)+parseFloat(total_oct));
                
              }
              else{
                var calc = (parseFloat(data.bonus) * parseFloat(data.checkdiff)*parseFloat(bonus))*parseFloat(tot_sal_pre_apr)/(parseFloat(total_apr)+parseFloat(total_oct));
                
              }
              if(tot==0){
                calc=0;
              }
                if(isNaN(calc)) calc=0;
                return parseFloat(calc).toFixed(2);
            }}
            ],
            "columnDefs": [
              { "orderable": false, "targets": 6  }
            
            ]
        });
      
      var last_ele = null ;
      var last_tr = null ;
      $('#bonus tbody').on('click', 'button.all_month', function () {
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
    function getdata1(data,ele,button)  {  
      var out;
      $('#ajax_loader_div').css('display','block');
      var fyear = $('#fyear').val();
      $.ajax({
               type:'get',
               url:"/totalsal/a/twelve/month/"+data,
               data:{fyear:fyear},
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

    function getbonus(){
      var fyear = $('#fyear').val();
      $.ajax({
               type:'get',
               url:"/getbonus/"+fyear,
               timeout:600000,
                   
               success:function(data) {
                  var bonus=data;
                 
                  $('#ajax_loader_div').css('display','none');
                  finance(bonus);

                }

            });
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
                    @section('titlebutton')
                    
                      @endsection

                        <div class="row">
                          <div class="col-md-6">
                            <label for="">Financial Year <sup>*</sup></label>
                                <select name="fyear" class="select2 input-css fyear" id="fyear" onchange="getbonus()">
                                    
                                    @foreach($finan as $fy)
                                        <option value="{{$fy->id}}" 
                                          <?php $d=date("Y-m");
                                          echo (($fy->from <= $d && $d <= $fy->to)?"Selected=selected":"");
                                          ?> >{{$fy->financial_year}}</option>
                                    @endforeach
                                </select>
                              </div>
                          </div><br><br>
                    
                         <div class="row" >
                           <table id="bonus" class="table table-bordered table-striped" >
                              <thead>
                                <tr>
                                 
                                 <th>Employee Code</th> 
                                  <th>Employee Name</th>
                                  <th>Date of Joining</th>
                                  <th>Total Salary A</th>
                                  <th>Bonus on Salary A</th>
                                  <th>No. of months bonus is applicable</th>
                                  <th>Total Salary A paid to the employee</th>
                                  <th>Salary payable from Apr-Sep</th>
                                  <th>Salary payable from Oct-Mar </th>
                                  <th>Bonus calculated </th>
                                  
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                         
                          </table>
                         </div>
                    
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection