@extends($layout)

@section('user', Auth::user()->name)

@section('title', __('client_po.list'))
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script src="/js/bootbox.min.js"></script>
<script src="/js/bootbox.locales.min.js"></script>

<script>
  $(document).ready(function()  {
      dataTable = $('#client_po_list_table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/clientpo/api",
          "columns": [
            { "data": "po_date" , "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  },},
            
            { "data": "refer" , "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  },},
            { "data": "pname" , "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  },},
              { "data": "item_name" , "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  },},
                  { data : function(data,type,full,meta)
                  {
                    if(data.is_po_provided==1)
                      if(data.pono){
                        return data.pono.replace(/,/g,'<br>');
                      }
                      else{
                        return "";
                      }
                      
                    else  
                      return "Verbal";
                  },},
           
              { "data": "io", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  },},
              { "data": "qty", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "0";
                  }, },
              { "data": "io_qty", "render": function(data,type,full,meta)
                  {
                    if(data)
                      return data.replace(/,/g,'<br>');
                    else  
                      return "";
                  }, },
                  {"data":function(data, type, full, meta){
                var dt=data.created_at;
                    dt=new Date(dt); 
                      var dd=dt.getDate();
                      var mm=dt.getMonth()+ 1;
                      var yyyy=dt.getFullYear();
                      var hh=dt.getHours();
                      var mi=dt.getMinutes();
                      var ss=dt.getSeconds();
                        var d = "AM";
                        var h = hh;
                        if (h >= 12) {
                          h = hh - 12;
                          d = "PM";
                        }
                        if (h == 0) {h = 12;}
                      var ac=dd+'-'+mm+'-'+yyyy+' '+h+':'+mi+':'+ss+' '+d;
                      return ac;
                  }
                },
              {
                  "targets": [ -1 ],
                  "data":"id1", "render": function(data,type,full,meta)
                  {
                    return "<a href='/clientpo/view/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> View </button></a> &nbsp;" + 
                      '<a href="/clientpo/update/'+data+'" target="_blank"><button class="btn btn-success btn-xs"> Edit </button></a><br>' +
                      '<a onclick="delete_prompt('+data+')" target="_blank"><button class="btn btn-danger btn-xs"> Delete </button></a><br>' ;

                  },
                  "orderable": false
              }
              
            ],
            "columnDefs": [
              
            ]
          
        });
        });
    function delete_prompt(id)
    {
      bootbox.confirm({
                message: "Are You sure to delete the Client PO?",
                buttons: {
                  confirm : {
                        label: 'Yes Delete',
                        className: 'btn-success'
                    },
                  cancel : {
                        label: 'No',
                        className: 'btn-warning'
                    }
                },
                callback: function (result) {
                    if(result){
                      window.location.href='/clientpo/delete/'+id;
                    } 
                }
            });
    }

</script>
@endsection
@section('breadcrumb')
  <li><a href="#"><i class=""> {{__('client_po.list')}}</i></a></li>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                @section('titlebutton')
                <a href="/import/data/clientpo"><button class="btn btn-sm btn-primary">{{__('client_po.importtitle')}}</button></a>
                <a href="/export/data/clientpo"><button class="btn btn-sm btn-primary">{{__('client_po.exporttitle')}}</button></a>
                 
                @endsection
                    @include('sections.flash-message')
                    @yield('content')
            </div>
        <!-- Default box -->
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">{{__('client_po.list')}}</h3>
          </div>
          <div class="box-body">
            <table id="client_po_list_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>PO Date</th>
                    <th>{{__('client_po.Ref Name')}}</th>
                  <th>{{__('client_po.Part Name')}}</th>
                  <th>{{__('client_po.Item Name')}}</th>
                  <th>{{__('client_po.po_num')}}</th>
                  <th>{{__('client_po.Internal Order')}}</th>
                  <th>{{__('client_po.po_qty')}}</th>
                  <th>{{__('client_po.io_qty')}}</th>
                  <th>Created Date</th>
                  <th>{{__('client_po.action')}}</th>

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
