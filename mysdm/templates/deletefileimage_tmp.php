<?php
error_reporting(0);

$nfiles_tmpimg = array();
$nindex_tmpimg = array();
$pimyesterday = strtotime('yesterday');
$pfolter_img="images/tanda_tangan_base64";
 
if ($handle = opendir($pfolter_img)) {
    clearstatcache();
    while (false !== ($nfile = readdir($handle))) {
        if ($nfile != "." && $nfile != "..") {
            $nfiles_tmpimg[] = $nfile;
            $nindex_tmpimg[] = filemtime( $pfolter_img.'/'.$nfile );
        }
    }
    closedir($handle);
}
	
asort( $nindex_tmpimg );
	
foreach($nindex_tmpimg as $iv => $tx) {
	//echo "$nfiles_tmpimg[$iv]<br/>";
    if($tx <= $pimyesterday) {
        if ($nfiles_tmpimg[$iv]=="index.php" OR $nfiles_tmpimg[$iv]=="index") {
            continue;
        }else{
            @unlink($pfolter_img.'/'.$nfiles_tmpimg[$iv]);
        }
    }
	
}

error_reporting(-1);
?>
