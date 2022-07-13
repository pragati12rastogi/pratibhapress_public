@extends($layout)

@section('title', 'Binding Bill Levels Authority Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Binding Summary</a></li> 
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
   .nav-pills>li {
    
    border: 1px solid #a9a0a0;
}

</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script>
 $.validator.addMethod("notValidIfSelectFirst", function(value, element, arg) {
        return arg !== value;
    }, "This field is required.");

    $('#infos').validate({ // initialize the plugin
        rules: {

            status: {
                required: true
            },
            
        }
    });
</script>
  <script>
    var dataTable;
    var datatable1;
    var hr;
    $(document).ready(function()  {
   
       
   
    
    $.ajax({
            url: "/binding/bills/setting/list/api",
            type: "GET",
            success: function(result) {
               
                  var level1=result[0].level1.split(',');
                  var level2=result[0].level2.split(',');
               
                  for(var i=0;i<level1.length;i++){
                    $('#level1').append(level1[i]+'<br>');
                  }
                  for(var i=0;i<level2.length;i++){
                    $('#level2').append(level2[i]+'<br>');
                  }
                  
                $('#ajax_loader_div').css('display','none');
    
            }
        });

      });
  </script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
        <!-- Default box -->
        @section('titlebutton')
            <a href="{{url('/binding/setting')}}"><button class="btn btn-primary">Settings</button></a>

            @endsection
            <div class="row">
            <div class="col-md-6">
            <div class="box">
          
            <div class="box-header with-border">
           
            <div class="box-body">
             <div class="row">
             <div class="col-md-6 ">
                <h3>Level 1</h3>
                <table class="table table-striped bordered" id="level1">
               
                </table>
             </div>

             </div>
            </div>
            </div></div>
           
            </div>
            <div class="col-md-6">
            <div class="box">
            <div class="box-header with-border">
          <div class="box-body">
             <div class="row">
            
             <div class="col-md-6 level2">
             <h3>Level 2</h3>
             <table class="table table-striped bordered" id="level2">
               
                </table>
             </div>
             </div>
            </div>
          </div>
          </div>
            </div>
            </div>
        <br><br>
           
        <!-- /.box -->
      </section>
@endsection