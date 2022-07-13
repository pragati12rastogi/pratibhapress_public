
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
        
<h4>Your leave has been 
    <i>
        @if ($status=="Approved")
            <span style='color:green'>{{$status}}</span>
        @else 
            <span style='color:red'>{{$status}}</span>
        @endif
    </i> by <i>{{$by}}</i> Which You Applied On <i>{{$leave_apply_date}}</i>.</h4>
    <u><i><h5>Below is the application you applied.</h5></i></u>
    <div class="row" style="background-color:goldenrod">
            <p>Respected Sir/Mam,</p>
            <p>I am writing to request you for a leave from <b>{{$start_date}}</b> to <b>{{$end_date}}</b> .<p>
            <p><b>Reason : </b> {{$reason}}</p>
            <p>I will be thankful to you for considering my application.</p>
            <br>
            <p>Yours Sincerely,
               {{$employee}}</p>
    </div>
</body>
</html>