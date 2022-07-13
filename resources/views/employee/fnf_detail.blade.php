
    <table border="1" style="width:100%">
        <h5><b> F&F calculation details</b></h5>
        <thead>
            <tr>
                <th>Total Amount</th>
                <th>Full & Final Settlement Date</th>
                <th>Leave Encashment</th>
                <th>Bonus Calculated</th>
                <th>Gratuity Calculated</th>
                <th>Balance Salary A</th>
                <th>Balance Salary B</th>
                <th>Advance Deducted</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{$details['total_amount']}}</td>
                <td>{{date('d-m-Y',strtotime($details['fnf_date']))}}</td>
                <td>{{$details['leaves_encashment']}}</td>
                <td>{{$details['bonus_ctc']}}</td>
                <td>{{$details['gratuity']}}</td>
                <td>{{$details['bal_SalA']}}</td>
                <td>{{$details['bal_SalB']}}</td>
                <td>{{$details['advance']}}</td>
            </tr>
        </tbody>
        
    </table>
        
    
