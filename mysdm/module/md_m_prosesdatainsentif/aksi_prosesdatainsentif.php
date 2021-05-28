<?php

    session_start();
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $berhasil="Tidak ada data yang diproses...";
    
// Hapus 
if ($module=='mstprosesinsentif' AND $act=='hapus')
{
    $ptgl=$_POST['utgl'];
    $ptgl1= date("Y-m-01", strtotime($ptgl));
    
    $pdivprod=$_POST['udivisi'];
    $fildivisi="";
    if (!empty($pdivprod)) $fildivisi=" AND IFNULL(divisi,'')='$pdivprod'";
    if ($pdivprod=="blank") $fildivisi=" AND IFNULL(divisi,'')=''";

    $pincfrom=$_POST['uincfm'];
    $pfilterincfrom=" AND IFNULL(jenis2,'')='$pincfrom' ";
    if ($pincfrom=="PM") $pfilterincfrom=" AND IFNULL(jenis2,'') NOT IN ('GSM', '') ";

    
    $query="DELETE FROM ms.incentiveperdivisi WHERE bulan='$ptgl1' $fildivisi $pfilterincfrom";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_close($cnmy);
    
    $berhasil="proses insentif berhasil dihapus...";
    echo $berhasil; exit;
}
elseif ($module=='mstprosesinsentif')
{
    
    $ptgl=$_POST['utgl'];
    $ptgl1= date("Y-m-01", strtotime($ptgl));
    
    $pdivprod=$_POST['udivisi'];
    $fildivisi="";
    if (!empty($pdivprod)) $fildivisi=" AND IFNULL(divisi,'')='$pdivprod'";
    if ($pdivprod=="blank") $fildivisi=" AND IFNULL(divisi,'')=''";
    
    $pincfrom=$_POST['uincfm'];
    $pfilterincfrom=" AND IFNULL(jenis2,'')='$pincfrom' ";
    if ($pincfrom=="PM") $pfilterincfrom=" AND IFNULL(jenis2,'') NOT IN ('GSM', '') ";

    include "prosesdatainc.php";
    
    $now=date("mdYhis");
    $tmp01 =caridatainsentif_query($cnmy, "", $ptgl1, "", $pdivprod, $pincfrom);
    
    $query = "SELECT table_name FROM information_schema.tables WHERE table_name='$tmp01'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu==0) {
        $berhasil="gagal proses....";
        goto hapusdata;
    }
    $tmp01="dbtemp.".$tmp01;
    
    
        //$query = "delete from $tmp01 where jabatan='MR' AND karyawanid='0000001503'";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query="DELETE FROM ms.incentiveperdivisi WHERE bulan='$ptgl1' $fildivisi";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query="INSERT INTO ms.incentiveperdivisi (bulan, divisi, cabang, jabatan, karyawanid, nama, region, jumlah, jenis2)"
            . "select bulan, divisi, icabangid, jabatan, karyawanid, nama, region, jumlah, '$pincfrom' as jenis2 FROM $tmp01";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $berhasil="berhasil...";
    
    mysqli_query($cnmy, "DROP TABLE $tmp01");
    mysqli_close($cnmy);
    echo $berhasil;
    
    
    exit;
hapusdata:
    
    $query="DELETE FROM ms.incentiveperdivisi WHERE bulan='$ptgl1' $fildivisi $pfilterincfrom";
    mysqli_query($cnmy, $query);
    
    mysqli_query($cnmy, "DROP TABLE $tmp01");
    mysqli_close($cnmy);
    echo "gagal proses....";
}
  
    
?>
