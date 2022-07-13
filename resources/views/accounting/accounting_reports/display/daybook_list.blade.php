@extends($layout)

@section('title', __('accounting/report/daybook.daybook list'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Daybook List</a></li> 
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
  
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
      dataTable = $('#asn_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/accounting/display/daybook/api",
          "columns": [
              { "data": "a" }, 
              { "data": "b" }, 
              { "data": "c" }, 
              { "data": "d" }, 
              {
                  "targets": [ -1 ],
                  "data":"e", "render": function(data,type,full,meta)
                  {
                    var name=data.split("---")['0'].toLowerCase();
                    var amt=data.split("---")['1'];
                        if(name=="payment" || name=="sales" || name=="journal" || name=="debit note")
                                return (amt);
                        else if(name=="reversing journal")
                                return (full.amount * (-1));
                        else
                                return '';
                  }
              },
              {
                  "targets": [ -1 ],
                  "data":"e", "render": function(data,type,full,meta)
                  {
                    var name=data.split("---")['0'].toLowerCase();
                    var amt=data.split("---")['1'];
                        if(name=='contra' ||name== 'receipt'||name=='purchase' ||name=="credit note")
                                return amt;
                        else
                                return '';
                  }
              },

              {
                  "targets": [ -1 ],
                  "data":"g", "render": function(data,type,full,meta)
                  {
                    return "<a href='/accounting/voucher/update/"+data+"'><button class='btn btn-primary btn-xs'> {{__('accounting/group.update')}} </button></a> &nbsp;" + 
                    '<a href="/accounting/voucher/entry/view/'+data+'"><button class="btn btn-success btn-xs"> {{__("accounting/group.view")}} </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 4 }
            ]
           
          
        });
    });


  </script>
@endsection

@section('main_section')
    <section class="content">
        <div class="row">
            <div style="text-align:center" class="col-md-12">
                <h3>
                    <label for="company_name">{{AccountingCustomHelper::getCompanyName()}}</label>
                </h3>    
            </div>
        </div>
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                      {{-- <a href="{{url('/hsn/create')}}"><button class="btn btn-primary">{{__('hsn.hsn_create_btn')}}</button></a>
                      <a href="/import/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_import_btn')}}</button></a>
                      <a href="/export/data/hsn" ><button class="btn btn-primary "  >{{__('hsn.hsn_export_btn')}}</button></a> --}}
    
                      @endsection
                    <table id="asn_table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>{{__('accounting/report/daybook.date')}}</th>
                        <th>{{__('accounting/report/daybook.particular')}}</th>
                        <th>{{__('accounting/report/daybook.vch type')}}</th>
                        <th>{{__('accounting/report/daybook.vch no')}}</th>
                        <th>{{__('accounting/report/daybook.debit amt')}}</th>
                        <th>{{__('accounting/report/daybook.credit amt')}}</th>
                        <th>{{__('accounting/report/daybook.action')}}</th>
                      {{-- <th>{{__('hsn.hsn_list_Action')}}</th> --}}
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
               
                  </table>
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection