@extends($layout)

@section('title', 'Bonus To Be Paid  ')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Bonus To Be Paid </a></li> 
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
    
        if(dataTable){
          dataTable.destroy();
        }
        dataTable = $('#bonus').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url":"/bonus/to/be/paid/api",
            "datatype": "json",
            "data": function (data) {
                var fyear = $('#fyear').val();
                data.fyear = fyear;
            }
          },
          "columns": [
            {"data":"employee_number"},
            {"data":"name"},
            
            {"data": function(data,type,full,meta){
              
  
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
            }},
            {"data":"advance"},
            {"data": function(data,type,full,meta){
        
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
              var bonuss = parseFloat(calc).toFixed(2);
                var advance = parseFloat(data.advance);
                if(isNaN(bonuss)) bonuss=0;
                if(isNaN(advance)) advance=0;
                if(advance >= bonuss)
                  return 0;
                else
                  return bonuss-advance;
            }}
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 5  }
            
            ]
        });
      
      
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
                            <label for="">Financial Year<sup>*</sup></label>
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
                                  <th>Bonus calculated</th>
                                  <th>Advance</th>
                                  <th>Bonus To be paid</th>
                                  
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