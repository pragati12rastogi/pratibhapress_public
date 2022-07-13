@extends($layout)

@section('title', $title)

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('party_form.mytitle')}}</i></a></li>
<li><a href="#"><i class="">{{__('party_form.importtitle')}}</i></a></li>
@endsection

@section('css')
<link rel="stylesheet" href="/css/party.css">
@endsection

@section('js')
<script src="/js/views/import_form.js"></script>
<script>
$(document).ready(function() {
      $('.search_in_excel').change(function(e) {
        var table = $(e.target).val().split(" ")[0].split(".")[0];
          
        var search_field = $(e.target).val().split(" ")[0].split(".")[1];
        var id =$(e.target).attr('id');
            id = parseInt(id.split('_')[3]);
       
        var ele = document.getElementsByClassName('search_val_in_excel')[id];
        $('#ajax_loader_div').css('display','block');

          $.ajax({
              url: "/getTableData/"+table+"/" + search_field,
              type: "GET",
              success: function(result) {
                  $(ele).empty();
                  $(ele).append("<option value=' '>Select search value</option>");
                  for (var i = 0; i < result.length; i++) {
                      $(ele).append($('<option value="' + result[i].data + '">' + result[i].data + '</option>'));
                  }
                  $('#ajax_loader_div').css('display','none');
                }

            });
      });
      $('.order_in_excel').change(function(e) {
        var order = $(e.target).val().split(" ")[0];
        var by = $(e.target).val().split(" ")[1];
        
        $("select option:contains('')").attr("disabled","disabled");    });

  });
</script>

@endsection

@section('main_section')
<section class="content">
   <div id="app">
       @include('sections.flash-message')
       @yield('content')
       @php
           print_r(session('importerrors'));
       @endphp
   </div>
<!-- Default box -->
<div class="box-header with-border">
    <div class='box box-default'> 
        <br>
        <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{$title}}</h2><br><br><br>
        <div class="container-fluid">
            <form files="true" enctype="multipart/form-data" action="/export/{{$form}}" method="POST" id="form">
            @csrf
                @for ($i=0;$i<count($columns);$i++)
                <h4>{{$sheet_name[$i]}}</h4>
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('excel') ? 'has-error' : ''}}" >
                            <label for="columns_in_excel">Fields for {{$sheet_name[$i]}} Sheet</label>

                            <select class="form-control select2 columns_in_excel input-css" id="columns_in_excel{{$i}}"
                            data-placeholder="Select Fields (for all keep it blank)" style="width: 100%;" name="columns_in_excel{{$i}}[]" multiple>
                                @foreach($columns[$i] as $key=>$val)
                                    <option value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('excel', '<p class="help-block">:message</p>') !!}

                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="search_in_excel">Search by</label>
                            <select class="form-control select2 search_in_excel input-css" id="search_in_excel_{{$i}}"
                            data-placeholder="" style="width: 100%;" name="search_in_excel{{$i}}" >
                                <option value="  ">Select search criteria</option>
                                @foreach($columns[$i] as $key=>$val)
                                    <option value="{{$key}} =">{{$val}} = </option>
                                @endforeach
                            </select>                              
                        </div>
                        <div class="col-md-4">
                            <label for="search_in_excel">Search by value</label>
                            <select class="form-control select2 search_val_in_excel input-css" id="search_val_in_excel{{$i}}"
                            data-placeholder="" style="width: 100%;" name="search_val_in_excel{{$i}}" >
                                <option value="">Select search value</option>
                            </select>   
                        </div>

               
                    @if($i<1)   
                            <div class="col-md-4">
                                <label for="order_in_excel">Order by</label>
                                <select class="form-control select2 order_in_excel input-css" id="order_in_excel{{$i}}"
                                data-placeholder="Select Order by" style="width: 100%;" name="order_in_excel{{$i}}[]" multiple >
                                    @foreach($columns[$i] as $key=>$val)
                                        <option value="{{$key}} desc">{{$val}} DESC</option>
                                        <option value="{{$key}} asc">{{$val}} ASC</option>
                                    @endforeach
                                </select>
                            </div>
                       
                    @endif
                </div>
                @endfor
           
                <div class="box-footer">
                    <button type="submit" class="btn btn-success">Generate Excel</button>
                </div>       
            </form>
        </div>
        <br>
    </div>
<!--submit button row-->
</div>
<!--end of container-fluid-->
   <!--end of box-header with-border-->
</section>
<!--end of section-->
@endsection