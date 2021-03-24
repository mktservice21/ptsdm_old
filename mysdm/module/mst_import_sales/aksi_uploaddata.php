<?php
session_start();
$puser=$_SESSION['IDCARD'];
if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}
    include "ceknamadist.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];

    $target_dir = "../../fileupload/";
    $pdist=$_POST['cb_distid'];
    $ptgl=$_POST['e_periode01'];
    $pdbkonekpilih=$_POST['cb_pildb'];
    
    
    
    $_SESSION['MSTIMPPERTPIL']=$ptgl;
    $_SESSION['MSTIMPDISTPIL']=$pdist;
    $_SESSION['MSTIMPFILEPIL']="";
    $_SESSION['MSTIMPKONEPIL']=$pdbkonekpilih;
    
    

if (($_FILES['fileToUpload']['name']!="")){
    
    $pfile = $_FILES['fileToUpload']['name'];
    
    if (empty($pfile)) {
        echo "File yang diupload kosong...!!!";
        exit;
    }
    
    $pname_foder_dist=CekNamaDist($pdist);
    
    $pbulan =  date("Ym", strtotime($ptgl));
    
    if (!file_exists($target_dir.$pbulan)) {
        mkdir($target_dir.$pbulan, 0777, true);
    }
    $target_dir .=$pbulan."/";
    
    if (!file_exists($target_dir.$pname_foder_dist)) {
        mkdir($target_dir.$pname_foder_dist, 0777, true);
    }
    
    $target_dir .=$pname_foder_dist."/";
    
    //echo $target_dir; exit;
    
    $path = pathinfo($pfile);
    $filename = $path['filename'];
    $ext = $path['extension'];
    $temp_name = $_FILES['fileToUpload']['tmp_name'];
    $path_filename_ext = $target_dir.$filename.".".$ext;
    
    
    // Check if file already exists
    /*
    if (file_exists($path_filename_ext)) {
        echo "Sorry, file already exists.";
    }else{
        move_uploaded_file($temp_name,$path_filename_ext);
        echo "Congratulations! File Uploaded Successfully.";
    }
      */
    
    move_uploaded_file($temp_name,$path_filename_ext);
    
    
    $ppath=$target_dir;
    $pnamezip=$filename.".".$ext;

    $filename = basename($ppath.'/'.$pnamezip);
    $filenameWX = preg_replace("/\.[^.]+$/", "", $filename);

    //echo $ppath." dan ".$filenameWX; exit;
    
    if ($ext=="xls" OR $ext=="XLS" OR $ext=="xlsx" OR $ext=="XLSX" OR $ext=="csv" OR $ext=="CSV") {
        $filenameWX=$pnamezip;
    }else{
        if ($ext=="rar" OR $ext=="RAR") {
            $archive = RarArchive::open($ppath.$pnamezip);
            $entries = $archive->getEntries();
            foreach ($entries as $entry) {
                $entry->extract($ppath.$filenameWX);
            }
            $archive->close();
        }else{
            $unzip = new ZipArchive;
            $out = $unzip->open($ppath.$pnamezip);
            if ($out === TRUE) {
              $unzip->extractTo(getcwd()."/$ppath/$filenameWX/");
              $unzip->close();
            } else {
              echo 'Error';exit;
            }
        }
    }
    
    if ($pdist=="2" OR $pdist=="0000000002") {
        
        $pnmdir = $target_dir.$filenameWX."/";
        
        if (!empty($filenameWX)) {
            if (is_dir($pnmdir)){
                if ($dh = opendir($pnmdir)){
                    while (($pfilerar = readdir($dh)) !== false){
                        if (!empty($pfilerar) && $pfilerar!="." && $pfilerar!="..") {
                            $path = pathinfo($filenameWX.'/'.$pfilerar);
                            $ext = "";
                            if (isset($path['extension'])) $ext = $path['extension'];
                            
                            if (!empty($ext)) {
                                
                                $filename_rar = basename($filenameWX.'/'.$pfilerar);
                                $filenameWX_rar = preg_replace("/\.[^.]+$/", "", $filename_rar);
                                if ($ext=="rar" OR $ext=="RAR") {
                                    
                                    $archive = RarArchive::open($pnmdir.$pfilerar);
                                    $entries = $archive->getEntries();
                                    foreach ($entries as $entry) {
                                        $entry->extract($pnmdir.$filenameWX_rar);
                                    }
                                    $archive->close();
                                    
                                }elseif ($ext=="zip" OR $ext=="ZIP") {
                                    
                                    $unzip = new ZipArchive;
                                    $out = $unzip->open($pnmdir.$filenameWX_rar);
                                    if ($out === TRUE) {
                                      $unzip->extractTo(getcwd()."$pnmdir/$filenameWX_rar/");
                                      $unzip->close();
                                    } else {
                                      echo 'Error'; exit;
                                    }
                                    
                                }
                                
                            }
                            
                        }
                    }
                    closedir($dh);
                }
            }
        }
            
    }
    
    $_SESSION['MSTIMPFOLDPIL']=$filenameWX;
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahpilihdist'.$pname_foder_dist);
    
}

?>

