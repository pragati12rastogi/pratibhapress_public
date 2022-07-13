@extends($layout)

@section('title', __('taxinvoice.cancel taxinvoice'))

{{-- TODO: fetch from auth --}}
@section('user',Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Cancel Tax Invoice</a></li>
@endsection
@section('js')
	<script>
		$(document).ready(function () {
			$('#taxform').validate({ // initialize the plugin
				rules: {
					reason: {
						required: true
					},
					cancellation:{
						required: true  
					}				
				}
			});
		});

	</script>
@endsection
@section('css')
{{-- for All Css --}}
{{-- <link rel="stylesheet" href="css/all.css"> --}}
<style>
#delivery{
    border:none;
    border-bottom: 2px solid #D1C4E9;
    width: 100%;
}
</style>
@endsection

@section('js')
@endsection

@section('main_section')
   <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
        </div>
        
         @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            @if(in_array(1, Request::get('userAlloweds')['section']))
            {{-- <p>Hello</p> --}}
            @endif

        <form id="taxform" action="/taxinvoice/cancel/{{$id}}" method="POST">
            @csrf
            <div class="box box-default">
                <div class="box-header with-border">
					<h3>{{__('taxinvoice.cancel taxinvoice')}}</h3>
                    <div class="box-body">
						<div class="row">
							<div class="col-md-4">
								<h4>
									<label for="">Tax Invoice No:{{$tax_invoice_details['invoice_number']}}</label>
								</h4>
								</div>
							<div class="col-md-4">
								<label for="">Reason for Cancellation</label>
								<input class="form-control input-css reason" type="text" name="reason" id="reason">
							</div>
							<div class="col-md-4">
								<label for="">Cancellation Advised by</label>
								<input class="form-control input-css cancellation" type="text" name="cancellation" id="cancellation">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12">
								<button type="submit" style="float:right" class="btn btn-primary ">Submit</button>  
							</div>
						</div>
        	        </div> <!-- /.box-body -->
                </div>  
                <!-- /.box --> 
            </div> 
            
        </form>
    </section>
@endsection

