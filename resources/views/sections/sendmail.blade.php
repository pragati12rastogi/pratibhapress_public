@extends($layout)

@section('title', 'Send Mail')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Send Mail</a></li>
   
@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
       <form action="/sendmail" method="POST">
        @csrf

      
 
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
