@extends($layout)

@section('title', 'Design Work Allotment Status Update')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Design Work Status</a></li>
   
@endsection
@section('js')
<script src="/js/Design/design_work_status.js"></script>
<script>
   
        var item_name = {{$dwa->work_allot_id}};
        var status_pages={{$dwa->pages}};
        $('#ajax_loader_div').css('display','block');
            $.ajax({
                url: "/design/work/details/" + item_name,
                type: "GET",
                success:function(result) {
                   console.log(result);
                   var pages = result.pagess;
                   var tot_pages = result.design.no_pages;
                   pages=tot_pages - pages
                  console.log(pages);
                    $(".no_pages").attr('placeholder',("Max:" + (pages+status_pages)));
                    $(".no_pages").attr('max',(pages+status_pages));
                    $('#ajax_loader_div').css('display','none');
                }
            });

</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                   
                    @yield('content')
            </div>
        <!-- Default box -->
        <form action="/work/status/update/{{$dwa->id}}" method="POST" id="form" autocomplete="off">
        @csrf
        <div class="box-header with-border returnable">
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">Design Work Allotment Status</h2><br><br><br>
                    <div class="container-fluid wdt">
                     
                                <div class="row">
                                        <div class="col-md-6 {{ $errors->has('work_number') ? 'has-error' : ''}}">
                                            <label>Work Allotted number<sup>*</sup></label>
                                            <select name="work_number" id="work_number" class="select2 work_number input-css" style="width:100%" disabled>
                                            <option value="">{{$dwa->work_alloted_number}}</option>
                                                
                                            </select>
                                            {!! $errors->first('work_number', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('status') ? 'has-error' : ''}}">
                                                <label>Work Allotment Status</label>
                                                <select name="status" id="status" class="select2 status input-css"style="width:100%" >
                                                        @foreach ($status as $item)
                                                        <option value="{{$item->id}}" {{$dwa->status==$item->id ? 'selected=selected':''}}>{{$item->value}}</option>
                                                   @endforeach 
                                                </select>
                                                {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div><br><br>
                                <div class="row">
                                        <div class="col-md-12 {{ $errors->has('remark') ? 'has-error' : ''}}">
                                                <label>Any {{__('Utilities/material_inward.remark')}}</label> 
                                                <textarea name="remark" id="remark" class="remark input-css">{{$dwa->remark}}</textarea>
                                                {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div><br><br>
                    </div>
                </div>
        </div>
        <div class="box-header with-border design_process"  {{$dwa['status']==2 || $dwa['status']==4 || $dwa['status']==8 ? "style=display:block" : 'style=display:none'}}>
                <div class='box box-default'>  <br>
                          @php
                           if($dwa['status']==8){
                                echo  "<h2 class='box-title des_print' style='font-size: 28px;margin-left:20px'>Printing Done</h2><br><br><br>";
                           }
                           else{
                            echo  "<h2 class='box-title des' style='font-size: 28px;margin-left:20px'>Design in Process</h2><br><br><br>";
                           }
                       @endphp
                    <div class="container-fluid wdt">
                            <div class="row">
                                    <div class="col-md-6 {{ $errors->has('no_pages') ? 'has-error' : ''}}">
                                        <label>Number Pages/Degree/Creative <sup>*</sup></label>
                                       <input type="number" name="no_pages" min="1" value="{{$dwa['pages']}}" id="no_pages" class="no_pages input-css">
                                       <input type="hidden" name="old_pages" value="{{$dwa['pages']}}">
                                        {!! $errors->first('no_pages', '<p class="help-block">:message</p>') !!}
                                    </div>
                            </div><br><br>
                    </div>
                </div>
        </div>
        <div class="box-header with-border proof_details" {{$dwa['status']==3 ? "style=display:block" : 'style=display:none'}}>
            <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">Proof Details</h2><br><br><br>
                <div class="container-fluid wdt">
                        <div class="row">
                                <div class="col-md-12 {{ $errors->has('proof') ? 'has-error' : ''}}">
                                    <label>Proof number sent to client<sup>*</sup></label> 
                                    <select name="proof" id="proof" class=" select2 proof input-css" style="width:100%">
                                        <option value="">Select Proof Number</option>
                                        <option value="1" {{$dwa->proof==1 ? 'selected=selected':''}}>Proof-1</option>
                                        <option value="2" {{$dwa->proof==2 ? 'selected=selected':''}}>Proof-2</option>
                                        <option value="3" {{$dwa->proof==3 ? 'selected=selected':''}}>Proof-3</option>
                                        <option value="4" {{$dwa->proof==4 ? 'selected=selected':''}}>Proof-4</option>
                                        <option value="5" {{$dwa->proof==5 ? 'selected=selected':''}}>Proof-5</option>
                                        <option value="6" {{$dwa->proof==6 ? 'selected=selected':''}}>Proof-6</option>
                                        <option value="7" {{$dwa->proof==7 ? 'selected=selected':''}}>Proof-7</option>
                                        <option value="8" {{$dwa->proof==8 ? 'selected=selected':''}}>Proof-8</option>
                                        <option value="9" {{$dwa->proof==9 ? 'selected=selected':''}}>Proof-9</option>
                                        <option value="10" {{$dwa->proof==10 ? 'selected=selected':''}}>Proof-10</option>
                                    </select>
                                    {!! $errors->first('proof', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><br><br>
                </div>
            </div>
        </div>
        <div class="box-header with-border approval_details" {{$dwa['status']==5 ? "style=display:block" : 'style=display:none'}}>
            <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">Approval received from client</h2><br><br><br>
                <div class="container-fluid wdt">
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('approval') ? 'has-error' : ''}}">
                                    <label>Approval Type<sup>*</sup></label> 
                                    <select name="approval" id="approval" class=" select2 approval input-css" style="width:100%">
                                        <option value="">Select Approval Type</option>
                                        <option value="Final" {{$dwa->approval=="Final" ? 'selected=selected':''}}>Final</option>
                                        <option value="Further changes required" {{$dwa->approval=="Further changes required" ? 'selected=selected':''}}>Further changes required</option>
                                    </select>
                                    {!! $errors->first('approval', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('approval_on') ? 'has-error' : ''}}">
                                        <label>Approval On<sup>*</sup></label> 
                                        <select name="approval_on" id="approval_on" class=" select2 approval_on input-css" style="width:100%">
                                            <option value="">Select Approval On</option>
                                            @foreach ($approval as $item)
                                                             <option value="{{$item->id}}" {{$dwa->approval_on==$item->id ? 'selected=selected':''}}>{{$item->approval_on}}</option>
                                                        @endforeach
                                        </select>
                                        {!! $errors->first('approval_on', '<p class="help-block">:message</p>') !!}
                                    </div>
                            </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('approval_by') ? 'has-error' : ''}}">
                                    <label>Approval By<sup>*</sup></label> 
                                       
                                <input type="text" name="approval_by" id="approval_by" value="{{$dwa->approval_by}}" class="approval_by input-css">
                                    <p style="font-size:12px;color:green">Please enter name along with designation</p>
                                       
                                    {!! $errors->first('approval_by', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('approval_date') ? 'has-error' : ''}}">
                                        <label>Approval Date<sup>*</sup></label> 
                                           
                                        <input type="text" autocomplete="off" name="approval_date" id="approval_date" value="{{$dwa->approval_date}}" class="approval_date datepicker1 input-css">
                                       
                                        {!! $errors->first('approval_date', '<p class="help-block">:message</p>') !!}
                                </div>
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
