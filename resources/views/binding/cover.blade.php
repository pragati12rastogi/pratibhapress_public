@php
    if(!isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Side Wire Stitching'])){ $arr['Side Wire Stitching']="";}
        if(!isset($arr['Centre Wire Stitching'])){ $arr['Centre Wire Stitching']="";}
        if(!isset($arr['Spiral'])){ $arr['Spiral']="";}
        if(!isset($arr['Nipping'])){ $arr['Nipping']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['UV'])){ $arr['UV']="";}
        if(!isset($arr['Hard Case Making'])){ $arr['Hard Case Making']="";}
        if(!isset($arr['Wiro'])){ $arr['Wiro']="";}
        if(!isset($arr['Tin Mounting'])){ $arr['Tin Mounting']="";}
        if(!isset($arr['Wiro Binding'])){ $arr['Wiro Binding']="";}
        if(!isset($arr['Spiral Binding'])){ $arr['Spiral Binding']="";}
        if(!isset($arr['Hanger'])){ $arr['Hanger']="";}
            
    }
       if(isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Centre Wire Stitching'])){ $arr['Centre Wire Stitching']="";}
        if(!isset($arr['Side Wire Stitching'])){ $arr['Side Wire Stitching']="";}
        if(!isset($arr['Spiral'])){ $arr['Spiral']="";}
        if(!isset($arr['Nipping'])){ $arr['Nipping']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['UV'])){ $arr['UV']="";}
        if(!isset($arr['Hard Case Making'])){ $arr['Hard Case Making']="";}
        if(!isset($arr['Wiro'])){ $arr['Wiro']="";}
        if(!isset($arr['Tin Mounting'])){ $arr['Tin Mounting']="";}
        if(!isset($arr['Wiro Binding'])){ $arr['Wiro Binding']="";}
        if(!isset($arr['Spiral Binding'])){ $arr['Spiral Binding']="";}
        if(!isset($arr['Hanger'])){ $arr['Hanger']="";}
       }

    if(!isset($arr1)){
        $arr1['Cutting']="";
        $arr1['Lamination']="";
        $arr1['Side Wire Stitching']="";
        $arr1['Spiral']="";
        $arr1['Packing']="";
        $arr1['UV']="";
        $arr1['Hard Case Making']="";
        $arr1['Centre Wire Stitching']="";
        $arr1['Wiro']="";
        $arr1['Tin Mounting']="";
        $arr1['Wiro Binding']="";
        $arr1['Spiral Binding']="";
        $arr1['Hanger']="";
        if(!isset($arr1['Tin Mounting'])){ $arr1['Tin Mounting']="";}
        if(!isset($arr1['Wiro Binding'])){ $arr1['Wiro Binding']="";}
        if(!isset($arr1['Spiral Binding'])){ $arr1['Spiral Binding']="";}
        if(!isset($arr1['Hanger'])){ $arr1['Hanger']="";}
    }
    if(isset($arr1)){
        if(!isset($arr1['Tin Mounting'])){ $arr1['Tin Mounting']="";}
        if(!isset($arr1['Wiro Binding'])){ $arr1['Wiro Binding']="";}
        if(!isset($arr1['Spiral Binding'])){ $arr1['Spiral Binding']="";}
        if(!isset($arr1['Hanger'])){ $arr1['Hanger']="";}
    }

@endphp

<div class="box">
        <div class="box-header with-border">
                <h2>Cover</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Lamination<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio"  {{$arr['Lamination']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="2[Lamination]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="2[Lamination]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css"  value="{{$arr1['Lamination']}}" placeholder="Remark"  name="2[remark][Lamination]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>UV<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="2[UV]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="2[UV]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css"  value="{{$arr1['UV']}}" placeholder="Remark"  name="2[remark][UV]">
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Cutting<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="Yes" ? 'checked="checked"' : ''}}  class="radio" value="Yes" name="2[Cutting]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="2[Cutting]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Cutting']}}" placeholder="Remark"  name="2[remark][Cutting]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Hard Case Making<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Hard Case Making']=="Yes" ? 'checked="checked"' : ''}}  class="radio" value="Yes" name="2[Hard Case Making]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Hard Case Making']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="2[Hard Case Making]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Hard Case Making']}}" placeholder="Remark"  name="2[remark][Hard Case Making]">
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label>Side Wire Stitching<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Side Wire Stitching']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="2[Side Wire Stitching]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Side Wire Stitching']=="No" ? 'checked="checked"' : ''}}  class="radios" value="No" name="2[Side Wire Stitching]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Side Wire Stitching']}}" placeholder="Remark"  name="2[remark][Side Wire Stitching]">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                    <label>Centre Wire Stitching<span class="span"></span></label>
                                    <div class="col-md-2">
                                    <div class="radio">
                                            <label><input  autocomplete="off" type="radio"  class="radio" {{$arr['Centre Wire Stitching']=="Yes" ? 'checked="checked"' : ''}} value="Yes" name="2[Centre Wire Stitching]">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio"  class="radio" {{$arr['Centre Wire Stitching']=="No" ? 'checked="checked"' : ''}} value="No" name="2[Centre Wire Stitching]">No</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Centre Wire Stitching']}}" placeholder="Remark"  name="2[remark][Centre Wire Stitching]">
                                        </div>
                                    </div>
                                </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Spiral<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio"  class="radio" {{$arr['Spiral']=="Yes" ? 'checked="checked"' : ''}} value="Yes" name="2[Spiral]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio"  class="radio" {{$arr['Spiral']=="No" ? 'checked="checked"' : ''}} value="No" name="2[Spiral]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Spiral']}}" placeholder="Remark"  name="2[remark][Spiral]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Wiro<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio"  class="radios" {{$arr['Wiro']=="Yes" ? 'checked="checked"' : ''}} value="Yes" name="2[Wiro]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio"  class="radios" {{$arr['Wiro']=="No" ? 'checked="checked"' : ''}} value="No" name="2[Wiro]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Wiro']}}" placeholder="Remark"  name="2[remark][Wiro]">
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
                                    <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="2[Packing]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="2[Packing]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Packing']}}" placeholder="Remark"  name="2[remark][Packing]">
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
                @if ($elem==3)
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Tin Mounting<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Tin Mounting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="2[Tin Mounting]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Tin Mounting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="2[Tin Mounting]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark" value="{{$arr1['Tin Mounting']}}" name="2[remark][Tin Mounting]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Wiro Binding<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Wiro Binding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="2[Wiro Binding]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Wiro Binding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="2[Wiro Binding]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Wiro Binding']}}" placeholder="Remark"  name="2[remark][Wiro Binding]">
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Spiral Binding<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Spiral Binding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="2[Spiral Binding]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Spiral Binding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="2[Spiral Binding]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark" value="{{$arr1['Spiral Binding']}}" name="2[remark][Spiral Binding]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Hanger<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Hanger']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="2[Hanger]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Hanger']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="2[Hanger]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Hanger']}}" placeholder="Remark"  name="2[remark][Hanger]">
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                @endif
    </div>  
</div>   