@php
    use \App\Http\Controllers\Template;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    .page-break {
            page-break-inside: avoid;       
    }
    @page {
        margin-top:180px;
	    footer: page-footer;
}
    
    </style>
     <script>
            
            </script>
       
</head>
<body>
     

<?php echo $format;?>

      
        <htmlpagefooter name="page-footer">
            Page {PAGENO} of {nbpg}
        </htmlpagefooter>
        
   
</body>

</html>