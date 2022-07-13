@php
    if(!isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Folding'])){ $arr['Folding']="";}
        if(!isset($arr['Gathering'])){ $arr['Gathering']="";}
        if(!isset($arr['Thread Swing'])){ $arr['Thread Swing']="";}
        if(!isset($arr['Side Wire Stitching'])){ $arr['Side Wire Stitching']="";}
        if(!isset($arr['Centre Wire Stitching'])){ $arr['Centre Wire Stitching']="";}
        if(!isset($arr['Nipping'])){ $arr['Nipping']="";}
        if(!isset($arr['Glue Filling'])){ $arr['Glue Filling']="";}
        if(!isset($arr['Back Patti Pasting'])){ $arr['Back Patti Pasting']="";}
        if(!isset($arr['Perfect Binding'])){ $arr['Perfect Binding']="";}
        if(!isset($arr['Tin Mounting'])){ $arr['Tin Mounting']="";}
        if(!isset($arr['Wiro Binding'])){ $arr['Wiro Binding']="";}
        if(!isset($arr['Spiral Binding'])){ $arr['Spiral Binding']="";}
        if(!isset($arr['Hanger'])){ $arr['Hanger']="";}

    }
if(isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Folding'])){ $arr['Folding']="";}
        if(!isset($arr['Gathering'])){ $arr['Gathering']="";}
        if(!isset($arr['Thread Swing'])){ $arr['Thread Swing']="";}
        if(!isset($arr['Side Wire Stitching'])){ $arr['Side Wire Stitching']="";}
        if(!isset($arr['Centre Wire Stitching'])){ $arr['Centre Wire Stitching']="";}
        if(!isset($arr['Nipping'])){ $arr['Nipping']="";}
        if(!isset($arr['Glue Filling'])){ $arr['Glue Filling']="";}
        if(!isset($arr['Back Patti Pasting'])){ $arr['Back Patti Pasting']="";}
        if(!isset($arr['Perfect Binding'])){ $arr['Perfect Binding']="";}
        if(!isset($arr['Tin Mounting'])){ $arr['Tin Mounting']="";}
        if(!isset($arr['Wiro Binding'])){ $arr['Wiro Binding']="";}
        if(!isset($arr['Spiral Binding'])){ $arr['Spiral Binding']="";}
        if(!isset($arr['Hanger'])){ $arr['Hanger']="";}
}

    if(!isset($arr1)){
        $arr1['Cutting']="";
        $arr1['Folding']="";
        $arr1['Gathering']="";
        $arr1['Thread Swing']="";
        $arr1['Side Wire Stitching']="";
        $arr1['Centre Wire Stitching']="";
        $arr1['Nipping']="";
        $arr1['Glue Filling']="";
        $arr1['Back Patti Pasting']="";
        $arr1['Perfect Binding']="";
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
            <h2>Text</h2>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <label>Cutting<span class="span"></span></label>
                        <div class="col-md-2">
                        <div class="radio">
                                <label><input  autocomplete="off" type="radio"  {{$arr['Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio Cutting1" value="Yes" name="1[Cutting]">Yes</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="radio">
                                <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="No" ? 'checked="checked"' : ''}}  class="radio Cutting1" value="No" name="1[Cutting]">No</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="radio">
                            <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Cutting']}}" placeholder="Remark"  name="1[remark][Cutting]">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                            <label>Folding<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Folding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Folding]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Folding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Folding]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Folding']}}" placeholder="Remark"  name="1[remark][Folding]">
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Gathering<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Gathering']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Gathering]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Gathering']=="No" ? 'checked="checked"' : ''}}  class="radio" value="No" name="1[Gathering]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css"value="{{$arr1['Gathering']}}" placeholder="Remark"  name="1[remark][Gathering]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Thread Swing<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Thread Swing']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Thread Swing]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Thread Swing']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Thread Swing]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css"value="{{$arr1['Thread Swing']}}" placeholder="Remark"  name="1[remark][Thread Swing]">
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
                                    <label><input  autocomplete="off" type="radio" {{$arr['Side Wire Stitching']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Side Wire Stitching]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Side Wire Stitching']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Side Wire Stitching]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Side Wire Stitching']}}" placeholder="Remark"  name="1[remark][Side Wire Stitching]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Centre Wire Stitching<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Centre Wire Stitching']=="No" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Centre Wire Stitching]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Centre Wire Stitching']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Centre Wire Stitching]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Centre Wire Stitching']}}"  placeholder="Remark"  name="1[remark][Centre Wire Stitching]">
                                    </div>
                                </div>
                            </div>
                    </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <label>Nipping<span class="span"></span></label>
                        <div class="col-md-2">
                        <div class="radio">
                                <label><input  autocomplete="off" type="radio"{{$arr['Nipping']=="Yes" ? 'checked="checked"' : ''}}  class="radio" value="Yes" name="1[Nipping]">Yes</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="radio">
                                <label><input  autocomplete="off" type="radio" {{$arr['Nipping']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Nipping]">No</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="radio">
                                <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Nipping']}}" placeholder="Remark"  name="1[remark][Nipping]">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                            <label>Glue Filling<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Glue Filling']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Glue Filling]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Glue Filling']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Glue Filling]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Glue Filling']}}" placeholder="Remark"  name="1[remark][Glue Filling]">
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <label>Back Patti Pasting<span class="span"></span></label>
                        <div class="col-md-2">
                        <div class="radio">
                                <label><input  autocomplete="off" type="radio" {{$arr['Back Patti Pasting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Back Patti Pasting]">Yes</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="radio">
                                <label><input  autocomplete="off" type="radio" {{$arr['Back Patti Pasting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Back Patti Pasting]">No</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="radio">
                                <input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark" value="{{$arr1['Back Patti Pasting']}}" name="1[remark][Back Patti Pasting]">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                            <label>Perfect Binding<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Perfect Binding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Perfect Binding]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Perfect Binding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Perfect Binding]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Perfect Binding']}}" placeholder="Remark"  name="1[remark][Perfect Binding]">
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
                                <label><input  autocomplete="off" type="radio" {{$arr['Tin Mounting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Tin Mounting]">Yes</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="radio">
                                <label><input  autocomplete="off" type="radio" {{$arr['Tin Mounting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Tin Mounting]">No</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="radio">
                                <input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark" value="{{$arr1['Tin Mounting']}}" name="1[remark][Tin Mounting]">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                            <label>Wiro Binding<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Wiro Binding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Wiro Binding]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Wiro Binding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Wiro Binding]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Wiro Binding']}}" placeholder="Remark"  name="1[remark][Wiro Binding]">
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
                                <label><input  autocomplete="off" type="radio" {{$arr['Spiral Binding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Spiral Binding]">Yes</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="radio">
                                <label><input  autocomplete="off" type="radio" {{$arr['Spiral Binding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Spiral Binding]">No</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="radio">
                                <input  autocomplete="off" type="text" class="form-control input-css" placeholder="Remark" value="{{$arr1['Spiral Binding']}}" name="1[remark][Spiral Binding]">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                            <label>Hanger<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Hanger']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="1[Hanger]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Hanger']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="1[Hanger]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Hanger']}}" placeholder="Remark"  name="1[remark][Hanger]">
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            @endif
    </div>
</div>            