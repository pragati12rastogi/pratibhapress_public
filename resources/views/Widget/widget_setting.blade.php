@extends($layout)

@section('title', 'Widget Permission')

@section('breadcrumb')
<li><a href="#"><i class=""></i>Widget Permission</a></li>
@endsection
@section('css')
<style type="text/css">
    .admin.treeview li{
        list-style: none;
    }
    .admin.treeview label{
        width:80%;
    }
</style>
@endsection
@section('script')
@endsection
@section('main_section')
<section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
    <!-- Default box -->
    <div class="box">
            <div class="box-header with-border">
            <h3 class="box-title">Set Widget Permission <span>({{$user->name}})</span></h3>
              </div>
            <div class="box-body">

                <form id="form" action='/widget/setpermission' method='post'>
                    @csrf
                    <div class="row">
                        <div class="col-md-12 form-group">
                        
                   
                            <div id="user_perm">

                            </div>
                                {{-- <ul class="admin treeview">
                                    @each('layouts.menulist',$menudata, 'menu')
                                </ul>  --}}
                        </div>             
                    </div>
                    <div class="form-group"">
                        <input type="submit" name="sub" class="btn btn-primary " value="Submit">
                    </div>
                    <input type="hidden" name="id" value="{{$id}}">
                </form>
            </div><!-- /.box-body -->
    </div><!-- /.box -->

</section>
@endsection

@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('#ajax_loader_div').css('display','block');
    $.ajax({
      url: "/widget/getadminpermission/{{$id}}",
      type: "GET",
      success: function(result) {
        $('#user_perm').append(result);

        // $(document).find($('input[type="checkbox"]')).change(change_check_box);
        
        $('#ajax_loader_div').css('display','none');

      }
    });
  });

 

</script>
@endsection