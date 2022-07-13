@extends($layout)

@section('title', 'Update Sub Category')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Sub Category</a></li>
   
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
        
                    @yield('content')
            </div>
        <!-- Default box -->
       <form action="/stock/subcat/update/{{$item['id']}}" method="POST">
        @csrf

        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                <h2 class="box-title" style="font-size: 28px;margin-left:20px">Sub Category</h2><br><br><br>
                <div class="container-fluid wdt">
                        <div class="row">
                                <div class="col-md-6">
                                        <label>Select Master Category<sup>*</sup></label>
                                        <select name="master_cat" id="" class="select2 input-css" required>
                                                <option value="default">Select Master categories</option>
                                                @foreach($master_item_cat as $key => $value)
                                                    <option value="{{$value['id']}}" {{$item['master_cat_id']==$value['id']? 'selected="selected':''}}>{{$value['name']}}</option>
                                                @endforeach
                                            </select>  
                                            {!! $errors->first('master_cat', '<p class="help-block">:message</p>') !!}
                                    </div><!--col-md-4-->
                                    <div class="col-md-6 ">
                                            <label>Sub Category<sup>*</sup></label>
                                            <input name="sub_cat" value="{{$item->name}}" id="" class="input-css" required>
                                                    
                                                {!! $errors->first('sub_cat', '<p class="help-block">:message</p>') !!}
                                        </div><!--col-md-4-->
                        </div><br><br>
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
