<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    $kodeinput = " AND kode=5 ";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
    $dbname = "dbmaster";
 
if ($module=="finprosservice") {
    $noidbr="";
    if ($act=="A") {
        
        $filterid=('');
        if (!empty($_POST['chkbox_br'])){
            $filterid=$_POST['chkbox_br'];
            $filterid=PilCekBox($filterid);
        }
        $noidbr=" $filterid ";

        if (empty($filterid)) {
            echo "Tidak ada data yang akan diupdate"; exit;
        }
        
        $ppn = str_replace(",","", $_POST['e_ppn']);
        
        mysqli_query($cnmy, "update $dbname.t_service_kendaraan set ppn='$ppn' WHERE idservice in $noidbr");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$act);
        
    }else{
    
        $noidbr=$_POST['unobr'];
        if ($noidbr=="()") $noidbr = "";
        $stsspv=$_POST['ket'];
        $karyawanapv=$_POST['ukaryawan'];
        $noteapv = "tidak ada data yang diapprove";
        
        if (!empty($noidbr) AND !empty($karyawanapv)) {

            $stsnonaktifnya = " AND stsnonaktif <> 'Y' ";

            if ($act=="simpan_ttdallfin") {
                $gbrapv=$_POST['uttd'];
                $img = $gbrapv;

                mysqli_query($cnmy, "update $dbname.t_service_kendaraan set fin='$karyawanapv', tgl_fin=NOW(), gbr_fin='$img' WHERE idservice in $noidbr $stsnonaktifnya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil diapprove...";

            }elseif ($act=="unapprove") {

                mysqli_query($cnmy, "update $dbname.t_service_kendaraan set fin=null, tgl_fin=null, gbr_fin=null WHERE idservice in $noidbr $stsnonaktifnya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                $noteapv = "data berhasil diunapprove...";
            }elseif ($act=="reject") {
                $kethapus = $_POST['ketrejpen'];
                if ($kethapus=="null") $kethapus="";
                if (!empty($kethapus)) $kethapus =", Ket Reject : ".$kethapus;

                mysqli_query($cnmy, "update $dbname.t_service_kendaraan set stsnonaktif='Y', userid='$karyawanapv', "
                        . " keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[IDCARD], ', NOW()) WHERE idservice in $noidbr");

                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil direject...";
            }

        }
        
        echo $noteapv;
        
    }
    

}

?>

