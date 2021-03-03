<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caricabang") {
    $pidkar=$_POST['ukry'];
    
    include "../../../config/koneksimysqli.php";
    
    $query = "select jabatanid from hrd.karyawan where karyawanid='$pidkar'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pjabatanid=$row['jabatanid'];
    if ($pjabatanid=="38") {
        $query = "SELECT distinct a.karyawanid, a.iCabangId, b.nama, '' as icabangkaryawan "
                . " FROM hrd.rsm_auth a JOIN MKT.icabang b on a.icabangid=b.icabangid WHERE a.karyawanid='$pidkar' order by b.nama";//b.aktif='Y'
        $result = mysqli_query($cnmy, $query); 
        $record = mysqli_num_rows($result);
    }else{
    
        //$pnmtablekry = "karyawan";
        $pnmtablekry = "tempkaryawandccdss_inp";

        $belumklik=false;
        $karyawanId = $_POST['umr'];
        $query = "select DISTINCT karyawan.iCabangId, cabang.nama, karyawan.icabangkaryawan from hrd.$pnmtablekry as karyawan join dbmaster.icabang as cabang on "
                . " karyawan.icabangid=cabang.icabangid where karyawanId='$pidkar'  order by cabang.nama"; 
        $result = mysqli_query($cnmy, $query); 
        $record = mysqli_num_rows($result);
        
    }
    
    if ($record==0) {
        $query = "select iCabangId, nama, '' as icabangkaryawan FROM MKT.icabang WHERE AKTIF='Y' order by nama";
        $result = mysqli_query($cnmy, $query); 
        $record = mysqli_num_rows($result);
        $belumklik=true;
    }
    
    
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result);
        $cdicabkry  = $row['icabangkaryawan'];
        $cdkodeid  = $row['iCabangId'];
        $cdnama = $row['nama'];
        if ($cdkodeid==$cdicabkry) {
            echo "<option value=\"$cdkodeid\" selected>$cdnama</option>";
            $belumklik=true;
        }else{
            if ($belumklik==true) 
                echo "<option value=\"$cdkodeid\" >$cdnama</option>";
            else
                echo "<option value=\"$cdkodeid\" selected>$cdnama</option>";
        }
    }
    
    
    mysqli_close($cnmy);
            

}elseif ($pmodule=="cekdatasudahada") {
    $pid=$_POST['uid'];    
    $bolehinput="boleh";

    echo $bolehinput;
    
}elseif ($pmodule=="cekdatasudahada") {
    
    
    
}elseif ($pmodule=="xxxx") {
  
}elseif ($pmodule=="xxx") {
  
}

?>
