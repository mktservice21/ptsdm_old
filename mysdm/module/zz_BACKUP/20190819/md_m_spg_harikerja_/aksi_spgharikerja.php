<?php

session_start();
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$pidspg=$_POST['uidspg'];
$pbulan=$_POST['ubulan'];
$ptglnya= date("Y-m", strtotime($pbulan));
$pcabang=$_POST['ucabang'];

    $query = "select a.id_spg, a.nama, a.jabatid, b.penempatan, a.icabangid, a.alokid, b.areaid from MKT.spg a LEFT JOIN"
            . " dbmaster.t_spg_tempat b on a.id_spg=b.id_spg "
            . " where a.id_spg = '$pidspg'";
    $tampil = mysqli_query($cnmy, $query);
    $sp= mysqli_fetch_array($tampil);
    
    $pareaid=$sp['areaid'];
    $pjabatanid=$sp['jabatid'];
    
    $pidcab_=$pcabang;
    if ($pcabang=="JKT_MT" OR $pcabang=="JKT_RETAIL") $pidcab_="0000000007";
    $query = "select distinct a.id_zona from dbmaster.t_spg_gaji_area_zona a WHERE a.icabangid='$pidcab_' AND a.areaid='$pareaid' AND "
            . " DATE_FORMAT(a.bulan,'%Y-%m') = (select MAX(DATE_FORMAT(b.bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_area_zona b WHERE 
                a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.id_zona=b.id_zona AND b.icabangid='$pidcab_' AND b.areaid='$pareaid')";
    $tampil = mysqli_query($cnmy, $query);
    $sp= mysqli_fetch_array($tampil);
    $pidzona=$sp['id_zona'];
    if ($pidzona=="0") $pidzona="";
    
    if (empty($pjabatanid)) {
        echo "Jabatan kosong...."; exit;
    }
        
    if (empty($pareaid)) {
        echo "Area kosong...."; exit;
    }
        
    if (empty($pidzona)) {
        echo "Zona kosong...."; exit;
    }
    
if ($act=='input') {
    $pharikerja=str_replace(",","", $_POST['uharikerja']);
    $pketerangan=$_POST['uketerangan'];
    
    $pharisakit=str_replace(",","", $_POST['usakit']);
    $phariizin=str_replace(",","", $_POST['uizin']);
    $puc=str_replace(",","", $_POST['uuc']);
    $pharialpa=str_replace(",","", $_POST['ualpa']);
    
    $phksistem=str_replace(",","", $_POST['uhksistem']);
}

$palokid="";
$fcabang =" icabangid='$pcabang' ";
if ($pcabang=="JKT_MT") {
    $pcabang="0000000007";//JAKARTA
    $fcabang = "  IFNULL(icabangid,'') = '$pcabang' AND alokid='001' ";
    $palokid="001";
}elseif ($pcabang=="JKT_RETAIL") {
    $pcabang="0000000007";//JAKARTA
    $fcabang = "  IFNULL(icabangid,'') = '$pcabang' AND alokid='002' ";
    $palokid="002";
}
    
$query = "select id_spg from dbmaster.t_spg_gaji_br0 where DATE_FORMAT(periode,'%Y-%m')='$ptglnya' "
        . " AND id_spg='$pidspg' AND $fcabang "
        . " AND IFNULL(apvtgl1,'')<> '' AND IFNULL(apvtgl1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00'";
$tampilc= mysqli_query($cnmy, $query);
$ketemu= mysqli_num_rows($tampilc);
if ($ketemu>0) {
    mysqli_close($cnmy);
    echo "Data Sudah diproses, tidak bisa diubah";exit;
}

$berhasil="Tidak ada data yang disimpan";
if ( $pbulan=="0000-00-00" OR empty($pbulan) ) exit;
if ($module=='spgharikerja' AND $act=='input') {
    mysqli_query($cnmy, "DELETE FROM $dbname.t_spg_gaji_br0 WHERE DATE_FORMAT(periode,'%Y-%m')='$ptglnya' AND id_spg='$pidspg' AND $fcabang");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    
    $query="INSERT INTO $dbname.t_spg_gaji_br0 (periode, id_spg, icabangid, alokid, jml_harikerja, keterangan, userid, jml_sakit, jml_izin, jml_alpa, jharikerjasistem, jml_uc, areaid, jabatid, id_zona)VALUES"
            . "('$pbulan', '$pidspg' ,'$pcabang', '$palokid', '$pharikerja', '$pketerangan', '$_SESSION[IDCARD]', '$pharisakit', '$phariizin', '$pharialpa', '$phksistem', '$puc', '$pareaid', '$pjabatanid', '$pidzona')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    $berhasil="";
    
    
    mysqli_close($cnmy);
    
}elseif ($module=='spgharikerja' AND $act=='hapus') {
    mysqli_query($cnmy, "DELETE FROM $dbname.t_spg_gaji_br0 WHERE DATE_FORMAT(periode,'%Y-%m')='$ptglnya' AND id_spg='$pidspg' AND $fcabang");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Hapus"; exit; }
    $berhasil="";
    mysqli_close($cnmy);
}
echo $berhasil;
?>

