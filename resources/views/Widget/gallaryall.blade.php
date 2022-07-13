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
    width: 31%;
    display: inline-block;
    margin: 5px;
    background: #fff;
    padding: -1px;
    box-sizing: border-box;
  
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
   					
						
<center>        
<main>
@foreach($doc as  $value)
    @if (file_exists(public_path().'/upload/photos/'.$value['name']))
        <div class="thumnails" >
             <a href="/upload/photos/{{$value['name']}}" data-fancybox="images" data-caption="{{$value['name']}}">
            <img  src="/upload/photos/{{$value['name']}}">
            </a>
        </div>
    
    @endif
  
 @endforeach

</main></center>
						
						
			
		<!---End---content----->
	
	<!---End-wrap---->
        
      </section>
@endsection
