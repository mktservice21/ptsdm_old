<?php
session_start();
include "../../config/koneksimysqli.php";

if (!isset($_SESSION['IDCARD'])) {
    echo "ANDA HARUS LOGIN ULANG...!!!";
    exit;
}


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

// Hapus jabatan
if ($module=='supplier' AND $act=='hapus'){
    $query=mysqli_query($cnmy, "select * from dbmaster.t_supplier WHERE KDSUPP   = '$_GET[id]'");
    $r=  mysqli_fetch_array($query);
    if ($r['AKTIF']=="Y")
        mysqli_query($cnmy, "update dbmaster.t_supplier set AKTIF='N' WHERE KDSUPP='$_GET[id]'");
    else
        mysqli_query($cnmy, "update dbmaster.t_supplier set AKTIF='Y' WHERE KDSUPP='$_GET[id]'");
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Input modul
elseif ($module=='supplier' AND $act=='input'){
  
        $sql=  mysqli_query($cnmy, "select MAX(KDSUPP) AS NOURUT from dbmaster.t_supplier");
        $ketemu=  mysqli_num_rows($sql);
        $awal=5; $urut=1; $kodenya=""; $periode=date('Ym');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            if (!empty($o['NOURUT'])) {
                $urut=(int)$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya=str_repeat("0", $awal).$urut;
            }
        }else{
            $kodenya=str_repeat("0", (int)$awal-1)."1";
        }
        
  // Input data jabatan
  mysqli_query($cnmy, "INSERT INTO dbmaster.t_supplier(KDSUPP, NAMA_SUP, ALAMAT, TELP, KEYPERSON)
	                       VALUES('$kodenya', '$_POST[e_nmsupplier]', '$_POST[e_alamat]', '$_POST[e_telp]', '$_POST[e_keyperson]')");
  $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Update modul
elseif ($module=='supplier' AND $act=='update'){
  
  mysqli_query($cnmy, "UPDATE dbmaster.t_supplier SET NAMA_SUP = '$_POST[e_nmsupplier]', NAMA_SUP = '$_POST[e_nmsupplier]', 
        ALAMAT = '$_POST[e_alamat]', TELP = '$_POST[e_telp]', KEYPERSON = '$_POST[e_keyperson]' 
                          WHERE KDSUPP   = '$_POST[id]'");
  $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
