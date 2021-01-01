<?php
    session_start();
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_combo.php";
    $kodeinput = " AND kode=3 ";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnit;
    $dbname = "dbmaster";
 
if ($module=="finprosca") {
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
        
        mysqli_query($cnmy, "update $dbname.t_ca0 set ppn='$ppn' WHERE idca in $noidbr");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$act);
        
    }elseif ($act=="B") {
        $filterid=('');
        if (!empty($_POST['chkbox_br'])){
            $filterid=$_POST['chkbox_br'];
            $filterid=PilCekBox($filterid);
        }
        $noidbr=" $filterid ";

        if (empty($filterid)) {
            echo "Tidak ada data yang akan diupdate"; exit;
        }
        
        if (!empty($_POST['e_tgltrans'])) {
            $date1 =  date("Y-m-d", strtotime($_POST['e_tgltrans']));
            $fieldnya=" tgltrans='$date1' ";
        }else{
            $fieldnya=" tgltrans=null ";
        }
        
        mysqli_query($cnmy, "update $dbname.t_ca0 set $fieldnya WHERE idca in $noidbr");
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

                mysqli_query($cnmy, "update $dbname.t_ca0 set fin='$karyawanapv', tgl_fin=NOW(), gbr_fin='$img' WHERE idca in $noidbr $stsnonaktifnya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil diapprove...";

            }elseif ($act=="unapprove") {

                mysqli_query($cnmy, "update $dbname.t_ca0 set fin=null, tgl_fin=null, gbr_fin=null WHERE idca in $noidbr $stsnonaktifnya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                $noteapv = "data berhasil diunapprove...";
            }elseif ($act=="reject") {
                $kethapus = $_POST['ketrejpen'];
                if ($kethapus=="null") $kethapus="";
                if (!empty($kethapus)) $kethapus =", Ket Reject : ".$kethapus;
				if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);

                mysqli_query($cnmy, "update $dbname.t_ca0 set stsnonaktif='Y', userid='$karyawanapv', "
                        . " keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idca in $noidbr");

                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil direject...";
            }

        }
        
        echo $noteapv;
        
    }
    

}

?>

