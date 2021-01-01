<?php
    session_start();
    include "../../config/koneksimysqli.php";
    
    
    $pbln=$_POST['ubln'];
    $piddisti=$_POST['uiddist'];
    $pnmfolderfile=$_POST['unmfile'];
    
    
    
    $ppath = dirname($pnmfolderfile);
    
    $filename = basename($pnmfolderfile);
    $filenameWX = preg_replace("/\.[^.]+$/", "", $filename);
    $pnmEXT = pathinfo($pnmfolderfile, PATHINFO_EXTENSION);
    
    $berhasil="ADA : $pbln, $piddisti, $pnmfolderfile<br/>$ppath, $filename, $filenameWX dan $pnmEXT";
    
    
    if ($pnmEXT=="zip") {
        
        $pnamezip=$filename;
        
        $unzip = new ZipArchive;
        //$out = $unzip->open($ppath.'/'.$pnamezip);
        $out = $unzip->open($pnmfolderfile);
        if ($out === TRUE) {
          $unzip->extractTo(getcwd()."/$filenameWX/");
          $unzip->close();
        } else {
          echo 'Error';
        }
        
    }
    
    //echo $berhasil;
    mysqli_close($cnmy);
?>
    

