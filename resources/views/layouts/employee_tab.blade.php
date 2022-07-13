<ul class="nav nav1 nav-pills">
    <li class="nav-item">
      <a class="nav-link" href="{{url('/employee/profile/update/'.$id) }}" style="background-color: {{ Request::segment(2)=='profile' ? '#87CEFA' : ''}}">Employee Details</a>
    </li>
    <li class="nav-item">
      <a class="nav-link"  href="{{url('/employee/pfesi/update/'.$id) }}" style="background-color: {{ Request::segment(2)=='pfesi' ? '#87CEFA' : ''}}">PF ESI Details</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{url('/employee/bank/update/'.$id) }}" style="background-color: {{ Request::segment(2)=='bank' ? '#87CEFA' : ''}}">Bank Details</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{url('/employee/document/update/'.$id) }}" style="background-color: {{ Request::segment(2)=='document' ? '#87CEFA' : ''}}">Documents Upload</a>
    </li>
     <li class="nav-item">
        <a class="nav-link"  href="{{url('/employee/relieving/update/'.$id) }}" style="background-color: {{ Request::segment(2)=='relieving' ? '#87CEFA' : ''}}">Relieving Details</a>
      </li>
    <li class="nav-item">
       <a class="nav-link" href="{{url('/employee/category/update/'.$id) }}" style="background-color: {{ Request::segment(2)=='category' ? '#87CEFA' : ''}}">Employee Category</a>
    </li>
</ul>
<br>