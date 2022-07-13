@extends($layout)

@section('title', 'Update Financial Year')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Financial Year</a></li>
   
@endsection
@section('js')
<script>
var dt="{{$finan->to}}";
var date=new Date(dt);
console.log(date);
var currentMonth = date.getMonth();
var currentDate = date.getDate();
var currentYear = date.getFullYear();

  $('.datepickers').datepicker({
      autoclose: true,
      format: 'yyyy-mm',
      startDate: new Date(currentYear, currentMonth, currentDate),
      
  });
 

</script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
       <form action="/financialYear/edit/{{$finan->id}}" method="POST">
        @csrf

        <div class="box-header with-border">
        <div class='box box-default'> <br>
            <div class="container-fluid">
            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Financial Year <sup>*</sup></label>
                                 
                                    <div class="col-md-4">
                                    <label for="">From <sup>*</sup></label>
                                    <input type="text" autocomplete="off" value="{{$finan['from']}}"
                                        class="form-control input-css datepickers"
                                        id="po_number_prefix" disabled value="{{$finan->from}}" placeholder="Financial Year" required>
                                     <input type="hidden"  name="financial_year[]" value="{{$finan->from}}">
                                    </div>
                                    <div class="col-md-4">
                                   
                                   <label for="">To <sup>*</sup></label>
                                   <input type="text" autocomplete="off" value="{{$finan['to']}}"
                                       class="form-control input-css datepickers" name="financial_year[]"
                                       id="po_number_prefix" value="{{$finan->to}}" placeholder="Financial Year" required>
                                   </div>  
                                   <div class="col-md-4">
                                   
                                    <label for="">Bonus % <sup>*</sup></label>
                                    <input autocomplete="off" type="number" step="any" value="{{$finan['bonus_per']}}"
                                        class="form-control input-css" name="bonus"
                                        id="" placeholder="Bonus" required>
                                    </div>  <br><br>
                                    
                                </div>     <br><br><br>        
                            </div>
            </div>
        </div>
        </div>
 
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
