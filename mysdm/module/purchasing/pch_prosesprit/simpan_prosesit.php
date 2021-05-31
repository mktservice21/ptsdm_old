<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
    include "../../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
    $pnamalogin=$_SESSION['NAMALENGKAP'];
    $pidgroup=$_SESSION['GROUP'];
    
if ($module=="pchprosesprit") {
    
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    
    if (empty($karyawanapv)) $karyawanapv=$_SESSION['IDCARD'];
    
    $noteapv = "tidak ada data yang diproses";
    
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
            
            $fielduntukttd="";
            $fieldtglapprovenya="";
            
            $fielduntukttd=" a.validate1='$karyawanapv', a.tgl_validate1=NOW(), b.gbr_validate1='$gbrapv' ";
            $fieldtglapprovenya= " (IFNULL(a.tgl_validate1,'')='' OR IFNULL(a.tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update dbpurchasing.t_pr_transaksi a JOIN dbttd.t_pr_transaksi_ttd b on a.idpr=b.idpr SET $fielduntukttd WHERE "
                        . " a.idpr IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $noteapv = "data berhasil diproses...";
            }
            
        }elseif ($act=="unproses") {
            $query = "select idpr from dbpurchasing.t_pr_transaksi_po WHERE idpr IN $noidbr AND IFNULL(aktif,'')='Y'";
            $tampilc= mysqli_query($cnmy, $query);
            $ketemuc= mysqli_num_rows($tampilc);
            if ((INT)$ketemuc>0) {
                mysqli_close($cnmy); echo "Salah satu ID PR sudah diisi vendor..., tidak bisa diproses"; exit;
            }
            
            $fielduntukttd=" a.tgl_validate1=NULL, b.gbr_validate1=NULL ";
            $fieldtglapprovenya= " (IFNULL(a.tgl_validate2,'')='' OR IFNULL(a.tgl_validate2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_validate1,'')<>'' AND IFNULL(a.tgl_validate1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";


            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {

                mysqli_query($cnmy, "update dbpurchasing.t_pr_transaksi a JOIN dbttd.t_pr_transaksi_ttd b on a.idpr=b.idpr SET $fielduntukttd WHERE "
                        . " ( IFNULL(a.tgl_validate2,'')='' OR IFNULL(a.tgl_validate2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                        . " a.idpr IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                $noteapv = "data berhasil diunproses...";
            }


        }elseif ($act=="reject") {
            $pkethapus=$_POST['ketrejpen'];

            if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);


            if (!empty($pnamalogin) AND !empty($karyawanapv)) {
                mysqli_query($cnmy, "update dbpurchasing.t_pr_transaksi set stsnonaktif='Y', userid='$karyawanapv', "
                        . " keterangan=CONCAT(IFNULL(keterangan,''),'Ket Reject : $pkethapus', ', user reject : $pnamalogin') WHERE "
                        . " idpr in $noidbr AND "
                        . " ( IFNULL(tgl_validate2,'')='' OR IFNULL(tgl_validate2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil direject...";
            }
        }
        
    
    }
    
    
}

    mysqli_close($cnmy);
    echo $noteapv; exit;
    
    
?>