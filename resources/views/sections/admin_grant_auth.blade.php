@extends($layout)

@section('title', "Authorise Permission")

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)
@section('css')
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/bootbox.min.js"></script>
<script src="/js/bootbox.locales.min.js"></script>
<script src="/js/dataTables.responsive.js"></script>
<script>
  $(document).ready(function()  {
    var last_ele = null ;
    var last_tr = null ;
    $('.loader').hide()
      dataTable = $('#summary_table').DataTable({
          "processing": true,
          "scrollX":true,
          "serverSide": true,
          "aaSorting": [],
          "responsive": true,
          "ajax": {url: "/admin/auth/req/api",
                    timeout:600000,
                    method:'get'
                },
          "columns": [
            { "data": "op" }, 
            { "data": "rea" }, 
		      	{ "data": "req" }, 
            {"data" : "stat", },
            {
				"targets": [ -1 ],
				data: function(data,type,full,meta)
				{
					var id = data.id1.toString().split('-')[0];
          var stat = data.id1.toString().split('-')[1];
          
					if(stat=='allowed')
              return "allowed";
          else if(stat=='updated')
              return "allowed and updated once";
						// return '<a onclick="change_permission_dailog('+id+',\'rejected\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Reject </button></a> &nbsp;' ;
					else if(stat=='cancelled')
						return "";
					else if(stat=='pending')
						return '<a onclick="change_permission_dailog('+id+',\'allowed\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-success btn-xs"> Allow </button></a> &nbsp;' +
								'<a onclick="change_permission_dailog('+id+',\'rejected\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Reject </button></a> &nbsp;' ;
					else if(stat=='rejected')
						return '';
					return "error";
			  }
            }
          ],
            "columnDefs": [
              { "orderable": false, "targets": 4 }
            ]
        });
        
    });
	function change_permission_dailog(id,operation)
	{
		bootbox.confirm({
			message: "Do you want to "+operation.slice(0,-2)+" the request?",
			buttons: {
				cancel: {
					label: 'cancel',
					className: 'btn-success'
				},
				confirm: {
					label: 'ok',
					className: 'btn-warning'
				}
			},
			callback: function (result) {
				if(result){
					$('#ajax_loader_div').css('display','block');

					$.ajax({
							url: "/admin/auth/req/grant/"+id+'/'+operation,
						type: "GET",
						success: function(result) {
						console.log(result);
						var message = result.message;
						if(message=='success')
            {
              $('#summary_table').DataTable().ajax.reload();
            }
						$('#ajax_loader_div').css('display','none');
						
					}
					});
				// 
					// bootbox.prompt({
					// 	title: "Reason for Request to Cancel Tax Invoice",
					// 	inputType: 'text',
					// 	placeholder:"Reason tp cancel",
					// 	callback: function (result) {
					// 		if(result== null)
					// 		{
					// 		}
					// 		else
					// 		{
					// 		if(result.length>0)
					// 			var patt = new RegExp('/','g');
					// 			window.location.href = "/addreqiredpermission/taxinvoice/"+ele+"/"+result.replace(patt,' - - - ');
					// 		}
					// 	}
					// });
				// 
				} 
			}
		});
	}
 </script>
@endsection
@section('main_section')

    @if(in_array(1, Request::get('userAlloweds')['section']))
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            </div>
        <!-- Default box -->
        <div class="box">
          <div class="box-header with-border">
            {{-- <h3 class="box-title">Summary</h3> --}}
          </div>
          <div class="box-body">
            <table id="summary_table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Operation</th>
                        <th>Reason</th>
                        <th>Requested By</th><!-- 6-->
                        <th>Current Status</th><!-- 6-->
                        <th>Action</th><!-- 6-->
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>


            </div>
        </div>
        <div id="datatab"></div>
        <!-- /.box -->
                
      </section>
      @else
        <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            </div>
        <!-- Default box -->
        <div class="box">
          <div class="box-header with-border">
            {{-- <h3 class="box-title">Summary</h3> --}}
          </div>
          <div class="box-body">
            You do not have permission to access this section.

          </div>
        </div>
        <div id="datatab"></div>
        <!-- /.box -->
                
      </section>
      @endif
@endsection
