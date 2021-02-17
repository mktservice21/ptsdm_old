<?php

session_start();

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
    
$puserid="";
$pnamalengkap="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['NAMALENGKAP'])) $pnamalengkap=$_SESSION['NAMALENGKAP'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}
    
if ($module=="gantiprofile" AND $module="updateprofile") {
    include "../../config/koneksimysqli.php";
    $kodenya=$_POST['e_id'];
    
    if (!empty($kodenya)) {
        $pgambarnya=$_POST['e_imgconv'];
        
        if (!empty($pgambarnya)) {
            $file_name = $_FILES['image1']['name'];
            $file_tmp_name = $_FILES['image1']['tmp_name'];
            $file_target = '../../img/users/';
            $file_size = $_FILES['image1']['size'];
            $f_type=$_FILES['image1']['type'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            
            $extensions= array("jpeg","jpg","png");

            if(in_array($file_ext,$extensions)=== false){
               $errors[]="extension not allowed, please choose a JPEG or PNG file.";
               exit;
            }
            //echo "$file_ext"; exit;
            
            list($width, $height) = getimagesize($file_tmp_name);
            // Resize
            $ratio = $width/$height;
            if($ratio > 1) {
                $new_width = 300;
                $new_height = 400/$ratio;
            }
            else {
                $new_width = 300*$ratio;
                $new_height = 400;
            }

            // Rename file
            $temp = explode('.', $file_name);
            $newfilename = $puserid.'.'.end($temp);
            

            $_SESSION['FOTOKU']="";
            mysqli_query($cnmy, "DELETE FROM dbimages.img_foto_karyawan WHERE karyawanid='$kodenya' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            mysqli_query($cnmy, "ALTER TABLE dbimages.img_foto_karyawan AUTO_INCREMENT = 1");
            
            // Upload image
            if(move_uploaded_file($file_tmp_name , $file_target.$newfilename)) {
                $src = imagecreatefromstring(file_get_contents($file_target.$newfilename));
                $dst = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagedestroy($src);
                imagepng($dst, $file_target.$newfilename);
                imagedestroy($dst);
                
                $_SESSION['FOTOKU']=$file_ext;
                mysqli_query($cnmy, "insert into dbimages.img_foto_karyawan (karyawanid, itipe) values ('$kodenya', '$file_ext')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            }
            
        }
        
        mysqli_close($cnmy);
    }
    
}
header('location:../../media.php?module=gantiprofile'.'&idmenu='.$idmenu.'&act=complt');
    
?>