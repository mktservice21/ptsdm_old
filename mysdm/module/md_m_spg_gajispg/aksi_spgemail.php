<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    $puserid=$_SESSION['IDCARD'];
    $pnamalengkap=$_SESSION['NAMALENGKAP'];
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...!!!";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $pidn="3";
    
    $psubject=$_POST['txt_subject'];
    $pnmpengirim=$_POST['txt_nmpengirim'];
    $pemailpengirim=$_POST['txt_emailpengirim'];
    $pemailcc1=$_POST['txt_emailcc1'];
    $pemailcc2=$_POST['txt_emailcc2'];
    $pemailcc3=$_POST['txt_emailcc3'];
    $pemailcc4=$_POST['txt_emailcc4'];
    $pemailcc5=$_POST['txt_emailcc5'];
    
    
    $query = "UPDATE dbmaster.t_email SET tsubject='$psubject', nama_from='$pnmpengirim', "
            . " email_from='$pemailpengirim', cc1='$pemailcc1', cc2='$pemailcc2',"
            . " cc3='$pemailcc3', cc4='$pemailcc4', cc5='$pemailcc5' WHERE id='$pidn'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
    
    foreach ($_POST['chkbox_cab'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $pemail=$_POST['txt_email'][$nobrinput];
            
            $query = "UPDATE dbmaster.t_email_cabang_otc SET ckirim1='$pemail' WHERE icabangid_o='$nobrinput' AND id='$pidn'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
    }
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
?>
