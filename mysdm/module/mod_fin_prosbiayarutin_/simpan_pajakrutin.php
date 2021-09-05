<?php

session_start();
include "../../config/koneksimysqli.php";
$dbname = "dbmaster";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
$puserid=$_SESSION['IDCARD'];
$pkaryawanid=$puserid;
    
if (empty($puserid)) {
    mysqli_close($cnmy);
    echo "ANDA HARUS LOGIN ULANG...!!!";
    exit;
}

$berhasil="Tidak ada data yang disimpan...";

if ( ($module=="finprosbiayarutin" OR $module=="entrybrrutin") AND $act=="inputdatapajak" OR $act=="editdata") {
    
        $pbrid=$_POST['uidbr'];
        $pidinput=$_POST['uidinput'];
        if ($pidinput=="0") $pidinput="";
        
        $pcbpajak=$_POST['cbpajak'];
        
        $pjenis_dpp="";
        
        $pekenapajak=$_POST['ekenapajak'];
        $penoserifp=$_POST['enoserifp'];
        $petglfp=$_POST['etglfp'];
        $perpjmljasa=$_POST['erpjmljasa'];
        $pejmldpp=$_POST['ejmldpp'];
        $pejmlppn=$_POST['ejmlppn'];
        $pejmlrpppn=$_POST['ejmlrpppn'];
        $pcbpph=$_POST['cbpph'];
        $pejmlpph=$_POST['ejmlpph'];
        $pejmlrppph=$_POST['ejmlrppph'];
        $pejmlbulat=$_POST['ejmlbulat'];
        $pejmlmaterai=$_POST['ejmlmaterai'];
        $pejmlusulan=$_POST['ejmlusulan'];
        
        if (!empty($pekenapajak)) $pekenapajak = str_replace("'", " ", $pekenapajak);
        if (!empty($penoserifp)) $penoserifp = str_replace("'", " ", $penoserifp);
        
        if (empty($perpjmljasa)) $perpjmljasa=0;
        if (empty($pejmldpp)) $pejmldpp=0;
        if (empty($pejmlppn)) $pejmlppn=0;
        if (empty($pejmlrpppn)) $pejmlrpppn=0;
        if (empty($pejmlpph)) $pejmlpph=0;
        if (empty($pejmlrppph)) $pejmlrppph=0;
        if (empty($pejmlbulat)) $pejmlbulat=0;
        if (empty($pejmlmaterai)) $pejmlmaterai=0;
        if (empty($pejmlusulan)) $pejmlusulan=0;
        
        $petglfp = str_replace('/', '-', $petglfp);
        $petglfp = date("Y-m-d", strtotime($petglfp));
        
        $perpjmljasa=str_replace(",","", $perpjmljasa);
        $pejmldpp=str_replace(",","", $pejmldpp);
        $pejmlppn=str_replace(",","", $pejmlppn);
        $pejmlrpppn=str_replace(",","", $pejmlrpppn);
        $pejmlpph=str_replace(",","", $pejmlpph);
        $pejmlrppph=str_replace(",","", $pejmlrppph);
        $pejmlbulat=str_replace(",","", $pejmlbulat);
        $pejmlmaterai=str_replace(",","", $pejmlmaterai);
        $pejmlusulan=str_replace(",","", $pejmlusulan);
        
    
    
        if (!empty($pbrid) AND !empty($pbrid)) {
            
            $query = "UPDATE dbmaster.t_brrutin1 SET pajak='Y', nama_pengusaha='$pekenapajak', noseri='$penoserifp', tgl_fp='$petglfp', "
                    . " dpp='$pejmldpp', ppn='$pejmlppn', ppn_rp='$pejmlrpppn', "
                    . " pph_jns='$pcbpph', pph='$pejmlpph', pph_rp='$pejmlrppph', "
                    . " jumlah='$pejmlusulan', pembulatan='$pejmlbulat', materai_rp='$pejmlmaterai' WHERE nourut='$pidinput' AND idrutin='$pbrid'";

            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : rutin pajak"; exit; }
            
            $berhasil="";
        }
}elseif ( ($module=="finprosbiayarutin" OR $module=="entrybrrutin") AND $act=="hapus") {
    
        $pidinput=$_POST['uidbr'];
        $pbrid=$_POST['uidrutin'];
        if ($pidinput=="0") $pidinput="";
        
        if ($pidinput=="0" OR (double)$pidinput==0) {
            
        }else{
            $query = "UPDATE dbmaster.t_brrutin1 SET pajak='N' WHERE nourut='$pidinput' AND idrutin='$pbrid'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : hapus br otc"; exit; }
        }
        $berhasil="data berhasil dihapus...";
}


mysqli_close($cnmy);
echo $berhasil;
?>

