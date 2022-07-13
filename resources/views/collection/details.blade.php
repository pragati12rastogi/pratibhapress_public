@extends($layout)

@section('user', Auth::user()->name)

@section('title','Details')
@section('css')
<style>
table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting {
    padding-right: 272px !important;
}
.CB{
  background-color:yellow;
 
 
}
.FUD{
  background-color:green;
 
  
}
.DSPT{
  background-color:red;
  
  
}
.NC{
  background-color:aqua;
 

}
.PTP{
  background-color:#CC6600;
 

}
.NR{
  background-color:#A0A0A0;

 
}
.DNC{
  background-color:#E0E0E0;

 
}
</style>
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>

@endsection

@section('main_section')
<section class="content">
            <div id="app">

        <!-- Default box -->
        
        <div class="box box-primary">
            <!-- /.box-header -->
            <div class="box-header with-border">
<!--                    <h3 class="box-title">{{__('customer.mytitle')}} </h3>-->
                </div>  
                <div class="box-body">
                  <h4><b>{{$users['name']}}</b></h4>
               
                 @foreach($data_new as $key)
                 <div class="table-responsive" style="height:140px;">
                  <table id="admin_table" class="table table-bordered table-striped tableBodyScroll"  style="overflow-x:auto;overflow-y:auto;">
                    <thead style="position: sticky;top: 0px;">
                    <tr>
                      <th class="first" style="width:20%">Date</th>
                      <th class="first">Status</th>   
                      <th class="first">Date</th>
                      <th class="first">Time</th>
                      <th class="first">Reason</th>
                      <th class="first">View Screenshot Uploaded</th> 
                      <th class="first">Remark</th>            
                    </tr>
                   
                    </thead>
                    <tbody>
                 
                     @foreach($key as $j=>$value)
                     <tr>
                        @php 
                          $x=explode('@',$j);
                          $j=$x[0];
                        @endphp
                        <td>{{$j}}</td>
                        <td class="{{$value['status']}}">{{$value['status']}}</td>
                      
                        <td class="">{{$value['cb_date']}}</td>
                        <td class="">{{$value['cb_time']}}</td>
                        <td class="">{{$value['reason']}}</td>
                        <td class="">
                        @if($value['call_log_upload']=="-")
                          {{$value['call_log_upload']}}
                        @else
                        <a href="/upload/Recievable/{{$value['call_log_upload']}}" target="_blank"><u>View File</u></a></td>
                        @endif
                       
                        <td class="">{{$value['remark']}}</td>
                     </tr>
                      @endforeach
                    </tbody>
               
                  </table>
                  </div>
                 <br><br>
                 @endforeach
           
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection
