@php
    if(!isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Numbering'])){ $arr['Numbering']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['Barcoding'])){ $arr['Barcoding']="";} 
    }
    if(isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Numbering'])){ $arr['Numbering']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['Barcoding'])){ $arr['Barcoding']="";}   
    }
    if(!isset($arr1)){
        $arr1['Cutting']="";
        $arr1['Lamination']="";
        $arr1['Numbering']="";
        $arr1['Barcoding']="";
        $arr1['Packing']="";
    }
@endphp

<div class="box">
        <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Numbering<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Numbering']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="6[Numbering]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Numbering']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="6[Numbering]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Numbering']}}" placeholder="Remark"  name="6[remark][Numbering]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Barcoding<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Barcoding']=="Yes" ? 'checked="checked"' : ''}}  class="radio" value="Yes" name="6[Barcoding]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Barcoding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="6[Barcoding]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Barcoding']}}" placeholder="Remark"  name="6[remark][Barcoding]">
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
                                    <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="Yes" ? 'checked="checked"' : ''}}  class="radio" value="Yes" name="6[Cutting]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio"  {{$arr['Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="6[Cutting]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Cutting']}}" placeholder="Remark"  name="6[remark][Cutting]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Lamination<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="6[Lamination]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="6[Lamination]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Lamination']}}" placeholder="Remark"  name="6[remark][Lamination]">
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
                                        <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="6[Packing]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="6[Packing]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Packing']}}" placeholder="Remark"  name="6[remark][Packing]">
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
        </div>
</div> 