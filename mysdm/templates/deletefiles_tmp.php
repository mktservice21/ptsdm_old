<?php
error_reporting(0);

$nfiles_tmpfl = array();
$nindex_tmpfl = array();
$pflyesterday = strtotime('yesterday');
$pfolter_file="fileupload/temp_file";
 
if ($handle = opendir($pfolter_file)) {
    clearstatcache();
    while (false !== ($nfile = readdir($handle))) {
        if ($nfile != "." && $nfile != "..") {
            $nfiles_tmpfl[] = $nfile;
            $nindex_tmpfl[] = filemtime( $pfolter_file.'/'.$nfile );
        }
    }
    closedir($handle);
}
	
asort( $nindex_tmpfl );
	
foreach($nindex_tmpfl as $iv => $tx) {
	//echo "$nfiles_tmpfl[$iv]<br/>";
    if($tx <= $pflyesterday) {
        if ($nfiles_tmpfl[$iv]=="index.php" OR $nfiles_tmpfl[$iv]=="index") {
            continue;
        }else{
            @unlink($pfolter_file.'/'.$nfiles_tmpfl[$iv]);
        }
    }
	
}

error_reporting(-1);
?>
