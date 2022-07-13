<link rel="stylesheet" href="/css/common.css">
   <style>
   table, th, td {
  border: 1px solid black;
}
   </style> 

      
        <div class="row" >
          <table id="" class="" style="width:100%">
             <thead>
             <tr>
             <th>Payment Date</th>
             <th>Payment Mode</th>
             <th>Amount</th>
             <th>UTR No</th>
             </tr>
             </thead>
             <tbody>
             @foreach($pp as $key)
                <tr>
                <td>{{$key['payment_date']}}</td>
                <td>{{$key['payment_mode']}}</td>
                <td>{{$key['amount']}}</td>
                <td>{{$key['utr_no']}}</td>
                </tr>
             @endforeach
             </tbody>
          </table>
         </div>
      