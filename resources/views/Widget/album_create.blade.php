@extends($layout)

@section('title', 'Album')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Album</a></li>
   
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
        <form action="/dashboard/album/create" method="post" id="form" enctype="multipart/form-data">
        @csrf 
        <div class="box box-default">
                <div class="box-header with-border">
                        
                    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                    <label>Album Name<sup>*</sup></label>
                    <input type="text" name="album"  id="supp_challan" required  class="input-css">
            </div>
        </div><br><br> 
        </div>
        </div>
        </div> 
        
        <div class="row">
                            <div class="col-md-12">
                                <input type="submit" style="float:left" class="btn btn-primary" value="Submit">
                            </div>
                    </div> 
       
       
        </form>
      
      </section>
@endsection
