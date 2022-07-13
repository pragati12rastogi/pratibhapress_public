@php
    if(!isset($arr)){
        if(!isset($arr['UV'])){ $arr['UV']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Hard Case Making'])){ $arr['Hard Case Making']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
    }
    if(isset($arr)){
        if(!isset($arr['UV'])){ $arr['UV']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Hard Case Making'])){ $arr['Hard Case Making']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
         
    }
    if(!isset($arr1)){
        $arr1['UV']="";
        $arr1['Lamination']="";
        $arr1['Packing']="";
        $arr1['Hard Case Making']="";
    }  
@endphp
      <div class="box">
            <div class="box-header with-border">
                    <h2>Hard Case Stand</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label>Lamination<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="5[Lamination]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="5[Lamination]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Lamination']}}" placeholder="Remark"  name="5[remark][Lamination]">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>UV<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="5[UV]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="5[UV]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['UV']}}" placeholder="Remark"  name="5[remark][UV]">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label>Packing<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="5[Packing]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="5[Packing]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Packing']}}" placeholder="Remark"  name="5[remark][Packing]">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Hard Case Making<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Hard Case Making']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="5[Hard Case Making]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Hard Case Making']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="5[Hard Case Making]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Hard Case Making']}}" placeholder="Remark"  name="5[remark][Hard Case Making]">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>      
    </div> 