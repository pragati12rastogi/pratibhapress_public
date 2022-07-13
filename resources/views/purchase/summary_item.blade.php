
<link rel="stylesheet" href="/css/common.css">
<div class="summary-outline" >
    @foreach($mode as $item)
  @php
      $mode=$item->category;
  @endphp
 {{-- @if ($mode=="Paper") --}}
  <div class="row">
      <h4>Item Requirement For : {{$item->category}}</h4>
        <div class="col-md-4">
                <label for=""><strong>Item Category :</strong> {{$item->sub_category}}</label>     
        </div>  
        <div class="col-md-4">
                <label for=""><strong>Item Name :</strong> {{$item->item_name}}</label>     
        </div> 
        <div class="col-md-4">
                <label for=""><strong>Item Description :</strong> {{$item->item_desc}}</label>     
        </div>   

</div> 
<div class="row">
          <div class="col-md-4">
                  <label for=""><strong>Length (in inches) :</strong> {{$item->length}}</label>     
          </div>  
          <div class="col-md-4">
                  <label for=""><strong>Breadth (in inches)  :</strong> {{$item->breadth}}</label>     
          </div> 
          <div class="col-md-4">
                  <label for=""><strong>GSM :</strong> {{$item->gsm}}</label>     
          </div>   
  
  </div>
  <div class="row">
        <div class="col-md-4">
                <label for=""><strong>Quantity :</strong> {{$item->qty}}</label>     
        </div>  
        <div class="col-md-4">
                <label for=""><strong>Item Unit  :</strong> {{$item->uom}}</label>     
        </div> 
        <div class="col-md-4">
                <label for=""><strong>Tax :</strong> {{$item->tax}}</label>     
        </div>   

</div>
<div class="row">
        <div class="col-md-4">
                <label for=""><strong>Rate :</strong> {{$item->rate}}</label>     
        </div>  
        <div class="col-md-4">
                <label for=""><strong>Amount  :</strong> {{$item->amount}}</label>     
        </div> 
</div>
 {{-- @endif --}}

 @endforeach

  </div>