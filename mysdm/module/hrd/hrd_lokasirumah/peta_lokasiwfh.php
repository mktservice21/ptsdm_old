<?PHP
$platitude=$_POST['ulat'];
$plongitude=$_POST['ulong'];
?>

<iframe width="100%" height="500" src="https://maps.google.com/maps?q=<?php echo $platitude; ?>,<?php echo $plongitude; ?>&output=embed"></iframe>