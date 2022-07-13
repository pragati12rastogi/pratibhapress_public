
<!DOCTYPE html>
<html lang="en">

<head>

        <link rel="stylesheet" href="css/io_templates.css">
        <style>
  

        </style>
</head>
<body>
<br><br><br>
<table class="verticalTableHeader">
                                 <thead>
                                 <tr>
                                <th colspan="5" style="text-align:center;font-size:20px"> {{$mac}} {{"(Date : ".$date.")"}}</th>
                                <th colspan="6"  style="text-align:center;font-size:20px">Raw Material</th>
                                <th></th>
                                <th colspan="4"  style="text-align:center;font-size:20px">Machine Counter</th>
                                 </tr>
                                
                        <tr>
                          <th>JC No.</th>
                          <th>Reference</th>
                          <th>Item</th>
                          <th>Creative</th>
                          <th>Element</th>
                          <th> Size</th>
                            <th> Type</th>
                            <th> GSM</th>
                            <th> Mill</th>
                            <th> Brand</th>
                            <th>No. Of Sheets</th>
                          <th>Planned Imp</th>
                          <th>11 AM</th>
                          <th>2 PM</th>
                          <th>6 PM</th>
                          <th>Remark</th>
                        </tr>

                      </thead>
                      <tbody>
                      @foreach($dailyprocesslog as $key)
                       <tr>
                       <td>{{$key['job_number']}}</td>
                       <td>{{$key['referencename']}}</td>
                       <td>{{$key['item_name']}}</td>
                       <td>{{$key['creative_name']}}</td>
                       <td>{{$key['element_name']}}</td>
                       @if($key['paper_size'])
                             <td>{{$key['paper_size']}}</td>
                        @else
                          <td>{{$key['size']}}</td>
                        @endif
                        
                        @if($key['paper_type'])
                             <td>{{$key['paper_type']}}</td>
                        @else
                          <td>{{$key['paper']}}</td>
                        @endif

                       @if($key['paper_gsm'])
                             <td>{{$key['paper_gsm']}}</td>
                        @else
                          <td>{{$key['gsm']}}</td>
                        @endif

                      @if($key['paper_mill'])
                             <td>{{$key['paper_mill']}}</td>
                        @else
                          <td>{{$key['mills']}}</td>
                        @endif

                      @if($key['paper_brand'])
                             <td>{{$key['paper_brand']}}</td>
                        @else
                          <td>{{$key['brands']}}</td>
                        @endif

                      @if($key['no_of_sheets'])
                             <td>{{$key['no_of_sheets']}}</td>
                        @else
                          <td>{{$key['sheets']}}</td>
                        @endif

                       <td>{{$key['planned_plates']}}</td>
                       <td></td>
                       <td></td>
                       <td></td>
                       <td></td>
                       </tr>
                        @endforeach
                      </tbody>
                                 </table>
  
     <br><br><br>
    
</body>

</html>