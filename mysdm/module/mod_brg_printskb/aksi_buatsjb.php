<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
if ($module=='gimicprintskb' AND $act=="tandaisudahprint")
{
    include "../../config/koneksimysqli.php";
    $pidkeluar=$_GET['id'];
    $pidgroup=$_GET['idg'];
    $pidpenerima=$_GET['idp'];
    
    $pberhasil="Tidak ada data yang diproses";
    
    if (!empty($pidkeluar) AND !empty($pidgroup) AND !empty($pidpenerima)) {
        $query = "UPDATE dbmaster.t_barang_keluar_kirim SET PRINT='Y' WHERE IGROUP='$pidgroup' AND IDPENERIMA='$pidpenerima'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        $pberhasil="";
    }
    
    mysqli_close($cnmy);
    echo $pberhasil;
    exit;
} 
    
if ($module=='gimicprintskb' AND $act=="hapussjb")
{
    include "../../config/koneksimysqli.php";
    $pidkeluar=$_GET['id'];
    $pidgroup=$_GET['idg'];
    $pidpenerima=$_GET['idp'];
    
    $pberhasil="Tidak ada data yang diproses";
    
    if (!empty($pidkeluar) AND !empty($pidgroup) AND !empty($pidpenerima)) {
        $query = "UPDATE dbmaster.t_barang_keluar_kirim SET IGROUP=NULL, "
                . " IDPENERIMA=NULL, NAMA_PENERIMA=NULL, ALAMAT1=NULL, ALAMAT2=NULL, "
                . " KOTA=NULL, PROVINSI=NULL, KODEPOS=NULL, HP=NULL, GRPPRINT=NULL WHERE IDKELUAR='$pidkeluar' AND IGROUP='$pidgroup' AND IDPENERIMA='$pidpenerima'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        $pberhasil="";
    }
    
    mysqli_close($cnmy);
    //header('location:../../eksekusi3.php?module='.$module.'&idmenu='.$idmenu.'&act=hapusberhasil');
    echo $pberhasil;
    exit;
} 


if ($module=='gimicprintskb' AND $act=="suratjalan")
{
    include "../../config/koneksimysqli.php";
    
    $pidpenerima=$_POST['e_idpenerima'];
    $pidwewenang=$_POST['e_wewenangdiv'];
    
    $pnidkeluarpilih="";
    $pfilteridkeluar="";
    foreach ($_POST['chkbox_br'] as $pidkeluarbrg) {
        if (!empty($pidkeluarbrg)) {
            
            if (strpos($pfilteridkeluar, $pidkeluarbrg)==false) $pfilteridkeluar .="'".$pidkeluarbrg."',";
            if (strpos($pnidkeluarpilih, $pidkeluarbrg)==false) $pnidkeluarpilih .=$pidkeluarbrg.",";
            
        }
    }
    
    if (!empty($pfilteridkeluar)) {
        $pfilteridkeluar="(".substr($pfilteridkeluar, 0, -1).")";
        $pnidkeluarpilih=substr($pnidkeluarpilih, 0, -1);
        
        $query = "INSERT INTO dbmaster.t_barang_keluar_kirim (IDKELUAR)"
                . "select DISTINCT IDKELUAR FROM dbmaster.t_barang_keluar WHERE IFNULL(STSNONAKTIF,'')<>'Y' AND IDKELUAR IN $pfilteridkeluar AND "
                . " IDKELUAR NOT IN (select distinct IFNULL(IDKELUAR,'') FROM dbmaster.t_barang_keluar_kirim)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    $query = "select MAX(GRPPRINT) as NURUT FROM dbmaster.t_barang_keluar_kirim";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $nurut=$row['NURUT'];
    if (empty($nurut)) $nurut=0;
    $nurut++;
        
        
    $pfilteridkeluar="";
    foreach ($_POST['chkbox_br'] as $pidkeluarbrg) {
        if (!empty($pidkeluarbrg)) {
            $ptxtigrouptrm=$_POST['txt_igroup'][$pidkeluarbrg];
            $ptxtidtrm=$_POST['txt_idtrm'][$pidkeluarbrg];
            $ptxtnmtrm=$_POST['txt_nmtrm'][$pidkeluarbrg];
            $ptxtalmt1trm=$_POST['txt_almt1trm'][$pidkeluarbrg];
            $ptxtalmt2trm=$_POST['txt_almt2trm'][$pidkeluarbrg];
            $ptxtkotatrm=$_POST['txt_kotatrm'][$pidkeluarbrg];
            $ptxtprovtrm=$_POST['txt_provtrm'][$pidkeluarbrg];
            $ptxtkdpostrm=$_POST['txt_kdpostrm'][$pidkeluarbrg];
            $ptxthptrm=$_POST['txt_hptrm'][$pidkeluarbrg];
            
            
            $query="UPDATE dbmaster.t_barang_keluar_kirim SET IGROUP='$ptxtigrouptrm', IDPENERIMA='$ptxtidtrm', NAMA_PENERIMA='$ptxtnmtrm', "
                    . " ALAMAT1='$ptxtalmt1trm', ALAMAT2='$ptxtalmt2trm', KOTA='$ptxtkotatrm', "
                    . " PROVINSI='$ptxtprovtrm', KODEPOS='$ptxtkdpostrm', HP='$ptxthptrm', GRPPRINT='$nurut' WHERE IDKELUAR='$pidkeluarbrg' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            //echo "$ptxtigrouptrm, $ptxtidtrm, $ptxtnmtrm, $ptxtalmt1trm - $ptxtalmt2trm, $ptxtkotatrm, $ptxtprovtrm, $ptxtkdpostrm, $ptxthptrm<br/>";
        }
    }
    
    mysqli_close($cnmy);
    header('location:../../eksekusi3.php?module='.$module.'&idmenu='.$idmenu.'&act=sjb&inx='.$pnidkeluarpilih);
    exit;
    
    /*
    if (!empty($pfilteridkeluar) AND !empty($pidpenerima)) {
        $pfilteridkeluar="(".substr($pfilteridkeluar, 0, -1).")";
        
        $query = "INSERT INTO dbmaster.t_barang_keluar_kirim (IDKELUAR)"
                . "select DISTINCT IDKELUAR FROM dbmaster.t_barang_keluar WHERE IFNULL(STSNONAKTIF,'')<>'Y' AND IDKELUAR IN $pfilteridkeluar AND "
                . " IDKELUAR NOT IN (select distinct IFNULL(IDKELUAR,'') FROM dbmaster.t_barang_keluar_kirim)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        $query = "select MAX(IGROUP) as NURUT FROM dbmaster.t_barang_keluar_kirim";
        $tampil= mysqli_query($cnmy, $query);
        $row= mysqli_fetch_array($tampil);
        $nurut=$row['NURUT'];
        if (empty($nurut)) $nurut=0;
        $nurut++;
        
        $query = "UPDATE dbmaster.t_barang_keluar_kirim a JOIN "
                . " dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR JOIN dbmaster.t_divisi_gimick c "
                . " ON b.DIVISIID=c.DIVISIID SET "
                . " a.IGROUP='$nurut', a.IDPENERIMA='$pidpenerima' WHERE IFNULL(a.IGROUP,'')='' AND a.IDKELUAR IN $pfilteridkeluar ";
        if ($pidwewenang!="AL") {
            $query .=" AND c.PILIH='$pidwewenang'";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        //echo "$pidwewenang - $pidpenerima : urutan $nurut : $pfilteridkeluar<br/>";   
        mysqli_close($cnmy);
        header('location:../../eksekusi3.php?module='.$module.'&idmenu='.$idmenu.'&act=sjb&inx='.$nurut.'&ip='.$pidpenerima);
    }
     * 
     */
    
}
?>