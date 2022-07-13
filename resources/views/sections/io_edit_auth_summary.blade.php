@extends($layout)

@section('title', "Authorized Permission")

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
          "ajax": {url: "/admin/auth/ioedit/req/api",
                    timeout:600000,
                    method:'get'
                },
          "columns": [
            { "data": "operation" },
            {
              "targets": [ -1 ],
              data:function(data,type,full,meta){
                var io=data.io_number;
                var ti=data.invoice_number;
                var tid=data.ti;
				var tie=data.tie;
                var dc=data.challan_number;
				var dce=data.dce;
                if(io)
                  return io;
                else if(dc)
                  return dc;
                else if(tid)
                  return tid;
				else if(tie)
                  return tie;
				else if(dce)
                  return dce;
                else
                  return ti;  
               
                }
                
              },
            { "data": "rea" }, 
            {
              "targets": [ -1 ],
              data:function(data,type,full,meta){
                var io_data=data.changes_data;
                if(io_data){
                  var obj = JSON.parse(io_data);
                  var desc = "";
                  $.each( obj, function( key, value ) {
                    var d = '<b>'+key+'</b>' + ": " + value ;
                    desc = desc+'<br>'+d;
                  });
                  return desc;
                }
                else{
                  return "";
                }
                
              }
            },
            {
              "targets": [ -1 ],
              data:function(data,type,full,meta){
                var io_data=data.original_data;
                if(io_data){
                  var obj = JSON.parse(io_data);
                  var desc = "";
                  $.each( obj, function( key, value ) {
                    var d = '<b>'+key+'</b>' + ": " + value ;
                    desc = desc+'<br>'+d;
                  });
                  return desc;
                }
                else{
                  return "";
                }
                
              }
            },
		      	{ "data": "req" }, 
            {"data" : "stat", },
            {
				"targets": [ -1 ],
				data: function(data,type,full,meta)
				{
					var id=data.id;
          var io=data.io_number;
          var ti=data.invoice_number;
          var dc=data.challan_number;
		  var tie=data.tie;
          var tid=data.ti;
		  var dce=data.dce;

					if(data.stat=='allowed')
                return "allowed";
          else if(data.stat=='updated')
                return "allowed and updated once";
						// return '<a onclick="change_permission_dailog('+id+',\'rejected\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Reject </button></a> &nbsp;' ;
					else if(data.stat=='cancelled')
						    return "";
					else if(data.stat=='pending' && io)
						    return '<a onclick="change_permission_dailog('+id+',\'allowed\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-success btn-xs"> Allow </button></a> &nbsp;' +
								'<a onclick="change_permission_dailog('+id+',\'rejected\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Reject </button></a> &nbsp;' ;
          else if(data.stat=='pending' && ti)
               return '<a onclick="change_permission_dailog1('+id+',\'allowed\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-success btn-xs"> Allow </button></a> &nbsp;' +
								'<a onclick="change_permission_dailog1('+id+',\'rejected\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Reject </button></a> &nbsp;' ;
          else if(data.stat=='pending' && dc)
               return '<a onclick="change_permission_dailog2('+id+',\'allowed\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-success btn-xs"> Allow </button></a> &nbsp;' +
								'<a onclick="change_permission_dailog2('+id+',\'rejected\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Reject </button></a> &nbsp;' ;
          else if(data.stat=='pending' && tid)
               	return '<a onclick="change_permission_dailog3('+id+',\'allowed\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-success btn-xs"> Allow </button></a> &nbsp;' +
								'<a onclick="change_permission_dailog3('+id+',\'rejected\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Reject </button></a> &nbsp;' ;
		 else if(data.stat=='pending' && tie)
               return '<a onclick="change_permission_dailog4('+id+',\'allowed\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-success btn-xs"> Allow </button></a> &nbsp;' +
								'<a onclick="change_permission_dailog4('+id+',\'rejected\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Reject </button></a> &nbsp;' ;
		else if(data.stat=='pending' && dce)
               return '<a onclick="change_permission_dailog5('+id+',\'allowed\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-success btn-xs"> Allow </button></a> &nbsp;' +
								'<a onclick="change_permission_dailog5('+id+',\'rejected\')"  target="_blank"><button style="margin-bottom: 5px;margin-left: 5px;" class="btn btn-danger btn-xs"> Reject </button></a> &nbsp;' ;
					
        	else if(data.stat=='rejected')
						return 'rejected';
					return "expired";
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
							url: "/admin/io/edit/req/grant/"+id+'/'+operation,
						type: "GET",
						success: function(result) {

            var message = result.message;
            var mess=result.mess;
						if(message=='success')
            {
              $('#summary_table').DataTable().ajax.reload();
              alert(mess);
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
  function change_permission_dailog1(id,operation)
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
  function change_permission_dailog2(id,operation)
	{
		bootbox.confirm({
			message: "Do you want to "+operation.slice(0,-2)+" the request?",
			buttons: {
				cancel: {
					label: 'Delete',
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
							url: "/deliverychallan/delete/"+id+'/'+operation,
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
  function change_permission_dailog3(id,operation)
	{
		bootbox.confirm({
			message: "Do you want to "+operation.slice(0,-2)+" the request?",
			buttons: {
				cancel: {
					label: 'Delete',
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
							url: "/taxinvoice/delete/"+id+'/'+operation,
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
	function change_permission_dailog4(id,operation)
	{
		bootbox.confirm({
			message: "Do you want to "+operation.slice(0,-2)+" the request?",
			buttons: {
				cancel: {
					label: 'Delete',
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
						url: "/admin/auth/req/grant/update/"+id+'/'+operation+'/taxinvoiceupdate',
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
	function change_permission_dailog5(id,operation)
	{
		bootbox.confirm({
			message: "Do you want to "+operation.slice(0,-2)+" the request?",
			buttons: {
				cancel: {
					label: 'Delete',
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
						url: "/admin/auth/req/grant/update/"+id+'/'+operation+'/deliverychallanupdate',
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
                      <th>Document No</th>
                        <th style="width:30px">Reason</th>
                        <th>Change Data</th>
                        <th>Original Data</th>
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
