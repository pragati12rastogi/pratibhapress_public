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
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Spiral'])){ $arr['Spiral']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['UV'])){ $arr['UV']="";}
        if(!isset($arr['Hard Case Making'])){ $arr['Hard Case Making']="";}
        if(!isset($arr['Wiro'])){ $arr['Wiro']="";}
        if(!isset($arr['Pasting'])){ $arr['Pasting']="";}
        if(!isset($arr['Form Folding'])){ $arr['Form Folding']="";}
        if(!isset($arr['Half Cutting'])){ $arr['Half Cutting']="";}
        if(!isset($arr['Die Cutting'])){ $arr['Die Cutting']="";}
        if(!isset($arr['Creasing'])){ $arr['Creasing']="";}
        if(!isset($arr['Taping'])){ $arr['Taping']="";}
        if(!isset($arr['Numbering'])){ $arr['Numbering']="";}
        if(!isset($arr['Barcoding'])){ $arr['Barcoding']="";}
        if(!isset($arr['Eyelet'])){ $arr['Eyelet']="";}
        if(!isset($arr['Dorl'])){ $arr['Dorl']="";}
        
       
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
       if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
       if(!isset($arr['Spiral'])){ $arr['Spiral']="";}
       if(!isset($arr['Packing'])){ $arr['Packing']="";}
       if(!isset($arr['UV'])){ $arr['UV']="";}
       if(!isset($arr['Hard Case Making'])){ $arr['Hard Case Making']="";}
       if(!isset($arr['Wiro'])){ $arr['Wiro']="";}
       if(!isset($arr['Pasting'])){ $arr['Pasting']="";}
       if(!isset($arr['Form Folding'])){ $arr['Form Folding']="";}
       if(!isset($arr['Half Cutting'])){ $arr['Half Cutting']="";}
       if(!isset($arr['Die Cutting'])){ $arr['Die Cutting']="";}
       if(!isset($arr['Creasing'])){ $arr['Creasing']="";}
       if(!isset($arr['Taping'])){ $arr['Taping']="";}
       if(!isset($arr['Numbering'])){ $arr['Numbering']="";}
       if(!isset($arr['Barcoding'])){ $arr['Barcoding']="";}
       if(!isset($arr['Eyelet'])){ $arr['Eyelet']="";}
       if(!isset($arr['Dorl'])){ $arr['Dorl']="";}
       
      
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
        $arr1['Lamination']="";
        $arr1['Spiral']="";
        $arr1['Packing']="";
        $arr1['UV']="";
        $arr1['Hard Case Making']="";
        $arr1['Wiro']="";
        $arr1['Pasting']="";
        $arr1['Form Folding']="";
        $arr1['Half Cutting']="";
        $arr1['Die Cutting']="";
        $arr1['Creasing']="";
        $arr1['Taping']="";
        $arr1['Numbering']="";
        $arr1['Barcoding']="";
        $arr1['Eyelet']="";
        $arr1['Dorl']="";
    }
    
@endphp

<div class="box">
        <div class="box-header with-border">
                <h2></h2>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Cutting<span class="span"></span></label>
                            <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Cutting]">Yes</label>
                                    </div>
                                </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Cutting]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Cutting']}}" placeholder="Remark"  name="15[remark][Cutting]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Folding<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Folding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Folding]">Yes</label>
                                        </div>
                                    </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Folding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Folding]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Folding']}}" placeholder="Remark"  name="15[remark][Folding]">
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
                                        <label><input  autocomplete="off" type="radio" {{$arr['Gathering']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Gathering]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Gathering']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Gathering]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Gathering']}}" placeholder="Remark"  name="15[remark][Gathering]">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                    <label>Thread Swing<span class="span"></span></label>
                                    <div class="col-md-2">
                                    <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Thread Swing']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Thread Swing]">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Thread Swing']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Thread  Swing]">No</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Thread Swing']}}" placeholder="Remark"  name="15[remark][Thread Swing]">
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
                                        <label><input  autocomplete="off" type="radio" {{$arr['Side Wire Stitching']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Side Wire Stitching]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Side Wire Stitching']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Side Wire Stitching]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Side Wire Stitching']}}" placeholder="Remark"  name="15[remark][Side Wire Stitching]">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                    <label>Centre Wire Stitching<span class="span"></span></label>
                                    <div class="col-md-2">
                                    <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Centre Wire Stitching']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Centre Wire Stitching]">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Centre Wire Stitching']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Centre Wire Stitching]">No</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Centre Wire Stitching']}}" placeholder="Remark"  name="15[remark][Centre Wire Stitching]">
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
                                    <label><input  autocomplete="off" type="radio" {{$arr['Nipping']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Nipping]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Nipping']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Nipping]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Nipping']}}" placeholder="Remark"  name="15[remark][Nipping]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Glue Filling<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Glue Filling']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Glue Filling]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Glue Filling']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Glue Filling]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Glue Filling']}}" placeholder="Remark"  name="15[remark][Glue Filling]">
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
                                    <label><input  autocomplete="off" type="radio" {{$arr['Back Patti Pasting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Back Patti Pasting]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Back Patti Pasting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Back Patti Pasting]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Back Patti Pasting']}}" placeholder="Remark"  name="15[remark][Back Patti Pasting]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Perfect Binding<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Perfect Binding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Perfect Binding]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Perfect Binding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Perfect Binding]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Perfect Binding']}}" placeholder="Remark"  name="15[remark][Perfect Binding]">
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>UV<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="15[Lamination]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="15[Lamination]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['UV']}}" placeholder="Remark"  name="15[remark][Lamination]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>UV<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="Yes" ? 'checked="checked"' : ''}}  class="radios" value="Yes" name="15[UV]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="15[UV]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['UV']}}" placeholder="Remark"  name="15[remark][UV]">
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
                                    <label><input  autocomplete="off" type="radio" {{$arr['Spiral']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="15[Spiral]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Spiral']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="15[Spiral]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Spiral']}}" placeholder="Remark"  name="15[remark][Spiral]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Wiro<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Wiro']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="15[Wiro]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Wiro']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="15[Wiro]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Wiro']}}" placeholder="Remark"  name="15[remark][Wiro]">
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
                                    <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="15[Packing]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="15[Packing]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Packing']}}" placeholder="Remark"  name="15[remark][Packing]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Pasting<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Pasting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Pasting]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Pasting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Pasting]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Pasting']}}" placeholder="Remark"  name="15[remark][Pasting]">
                                    </div>
                                </div>
                            </div>
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                            <div class="col-md-6">
                                    <label>Form Folding<span class="span"></span></label>
                                    <div class="col-md-2">
                                    <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Form Folding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Form Folding]">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio"{{$arr['Form Folding']=="No" ? 'checked="checked"' : ''}}  class="radio" value="No" name="15[Form Folding]">No</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Form Folding']}}" placeholder="Remark"  name="15[remark][Form Folding]">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                        <label>Hard Case Making<span class="span"></span></label>
                                        <div class="col-md-2">
                                            <div class="radio">
                                                <label><input  autocomplete="off" type="radio" {{$arr['Hard Case Making']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Hard Case Making]">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="radio">
                                                <label><input  autocomplete="off" type="radio" {{$arr['Hard Case Making']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Hard Case Making]">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="radio">
                                                <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Hard Case Making']}}" placeholder="Remark"  name="15[remark][Hard Case Making]">
                                            </div>
                                        </div>
                                    </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    
                        <div class="col-md-6">
                                <label>Half Cutting<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Half Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Half Cutting]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Half Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Half Cutting]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Half Cutting']}}" placeholder="Remark"  name="15[remark][Half Cutting]">
                                    </div>
                                </div>
                        </div>

                        <div class="col-md-6">
                                <label>Die Cutting<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Die Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Die Cutting]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Die Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Die Cutting]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Die Cutting']}}" placeholder="Remark"  name="15[remark][Die Cutting]">
                                    </div>
                                </div>
                        </div>
                    </div>
                            
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Creasing<span class="span"></span></label>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Creasing']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Creasing]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Creasing']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Creasing]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Creasing']}}" placeholder="Remark"  name="15[remark][Creasing]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Taping<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Taping']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Taping]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Taping']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Taping]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Taping']}}" placeholder="Remark"  name="15[remark][Taping]">
                                    </div>
                                </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Numbering<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Numbering']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Numbering]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Numbering']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Numbering]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Numbering']}}" placeholder="Remark"  name="15[remark][Numbering]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Barcoding<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Barcoding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="15[Barcoding]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Barcoding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="15[Barcoding]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Barcoding']}}" placeholder="Remark"  name="15[remark][Barcoding]">
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    
                        <div class="col-md-6">
                                <label>Eyelet<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Eyelet']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="15[Eyelet]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Eyelet']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="15[Eyelet]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Eyelet']}}" placeholder="Remark"  name="15[remark][Eyelet]">
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6">
                                <label>Dorl<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Dorl']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="15[Dorl]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Dorl']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="15[Dorl]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Dorl']}}" placeholder="Remark"  name="15[remark][Dorl]">
                                    </div>
                                </div>
                        </div>

                        
                    </div>
                            
                </div>
     </div>        
</div>            