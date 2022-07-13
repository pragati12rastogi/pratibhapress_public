
<link rel="stylesheet" href="/css/common.css">

<div class="summary-outline" >
    @foreach($doc as $item)
        
    @endforeach
  @php
  @endphp
  <div class="row">
      <h4>Document For : {{$item->category}}</h4>
       @if ($item->invoice)
       <div class="col-md-4">
        <label for=""><strong>Invoice Number:</strong> {{$item->invoice}}</label>     
    </div>  
       @endif
       @if ($item->challan)
       <div class="col-md-4">
        <label for=""><strong>Challan Number:</strong> {{$item->challan}}</label>     
    </div>  
       @endif
       @if ($item->bilty)
       <div class="col-md-4">
        <label for=""><strong>Bilty Number:</strong> {{$item->bilty}}</label>     
    </div>  
       @endif
       @if ($item->other)
       <div class="col-md-4">
        <label for=""><strong>Other Number:</strong> {{$item->other}}</label>     
    </div>  
       @endif
</div> 
  </div>