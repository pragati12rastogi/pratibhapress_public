@extends($layout)

@section('title','Gallery')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Gallery</a></li>
   
@endsection
@section('css')
<style>

.thumnails{
width:25%;
display: inline-block;
margin:5px;
background:#fff;
padding:20px;
box-sizing:border-box;
  
}
.thumnails img{
width:100%;
height:200px;
  

}
main{
width:100%;
  background-color: aliceblue;

}
img{
    height: 120px;
    width: 163px;margin-bottom: 11px;
}
</style>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
   					
						
<center>        
<main>
<div class="row"><br>
 @foreach($doc as  $value)

   @if (file_exists(public_path().'/images/icon.png'))
        <div class="col-md-3" >
             <a href="/gallary/{{$value['album_id']}}">
            <img  src="/images/icon.png"><br>
            <b>{{$value->album}}</b>
            </a>
        </div>
    
    @endif

  
 @endforeach
 <br><br></div>


</main></center>
						
						
			
		<!---End---content----->
	
	<!---End-wrap---->
        
      </section>
@endsection
