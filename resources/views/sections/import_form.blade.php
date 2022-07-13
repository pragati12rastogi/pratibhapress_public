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
function checkstatus(status_for)
    {
        var id = getstatusid(status_for);
        if(id!='')
            setInterval(getcurrentstatus(id),1000);
    }
    function getstatusid(status_for)
    {
        id='';
        $('#ajax_loader_div').css('display','block');
        $.ajax({
              url: "/get/import/statusid/" + status_for,
              type: "GET",
              success: function(result) {
                  id=result;   
                  $('#ajax_loader_div').css('display','none');

                }
        });
        return id;
    }
    function getcurrentstatus(id)
    {
        $('#ajax_loader_div').css('display','block');

        $.ajax({
              url: "/get/import/status/" + id,
              type: "GET",
              success: function(result) {
                    id='processing on '+result.on + ' record out of '+ result.till;
                    $('#status').html(id);   
                    $('#ajax_loader_div').css('display','none');

                }
        });
    }
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
       <div class='box box-default'> <br>
           <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{$title}}</h2><br><br><br>
           <div class="container-fluid">
               <form files="true" enctype="multipart/form-data" action="{{'/import/'.$form.'/db'}}" method="POST" id="form">
                   @csrf

                   @if($depend!="0")
                   <div class="row">
                       @if($depend=="party")
                        <div id="Party_inp"  >
                            <div class="col-md-4 {{ $errors->has('party') ? 'has-error' : ''}}" >
                                <select value="{{old('party_name')}}" class="form-control select2 party" data-placeholder="" style="width:100%;" name="party">
                                    <option value=" ">Select Client</option>
                                    @foreach($party as $key)
                                        <option value="{{$key->id}}">{{$key->partyname}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('party', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        @endif
                        @if($depend=="task")
                        <div id="Task_inp"  >
                            <div class="col-md-4 {{ $errors->has('emp') ? 'has-error' : ''}}" >
                                <select class="form-control select2 emp" style="width:100%;" name="emp" required=" ">
                                      <option value="">Select Employee</option>
                                    @foreach($task as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('emp', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        @endif
                    </div>
                   @endif
                   <div class="row">
                    <div id="import_excel"  >
                        <div class="col-md-12 {{ $errors->has('excel') ? 'has-error' : ''}}" >
                          <input type="file" name="excel" id="excel_data" />
                          <small class="text-muted">Accepted File Format : xls, xlt, xltm, xltx, xlsm and xlsx </small>
                        {!! $errors->first('excel', '<p class="help-block">:message</p>') !!}
                        
                      </div>
                    </div>
                        <br />
                    
                        
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
                   <!--submit button row-->
               </form>
               <div id="status"></div>
               @if($form == 'task')
                  <p style="color: red;">Data will be inserted from 9th row.</p>
               @endif
               <a href="{{'/download/format/'.$form}}">
                <button class="btn btn-primary" onclick="checkstatus({{$form}})">Download Format</button>
            </a>   
            </div><br>
           <!--end of container-fluid-->
       </div>
       <!------end of box box-default---->
   </div>
   <!--end of box-header with-border-->
</section>
<!--end of section-->
@endsection