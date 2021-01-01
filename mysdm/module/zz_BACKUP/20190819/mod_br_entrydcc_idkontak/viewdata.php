<?php
if ($_GET['module']=="viewdatacomboposting"){
    include "../../config/koneksimysqli_it.php";
    $tampil = mysqli_query($cnit, "SELECT distinct kodeid, nama from hrd.brkd_otc where ifnull(subpost,'') = '$_POST[ukodesub]'");
    echo "<option value='' selected>-- Pilihan --</option>";
    while ($r=mysqli_fetch_array($tampil)){
        echo "<option value='$r[kodeid]'>$r[nama]</option>";
    }
}elseif ($_GET['module']=="viewmarcabang"){
    include "../../config/koneksimysqli_it.php";
    
    $karyawanId = $_POST['ukaryawan']; 
    $icabangid = $_POST['ucabang']; 
    $query = "select jabatanId from hrd.karyawan where karyawanId='$karyawanId'"; 	
    $result = mysqli_query($cnit, $query);
    $records = mysqli_num_rows($result);
    $row = mysqli_fetch_array($result);
    $jabatanid = $row['jabatanId'];
    
    if ($icabangid=="0000000001") { //ho
        $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan order by nama"; 
    } else {
        if (($icabangid=="0000000030") or ($icabangid=='0000000031') or ($icabangid=='0000000032')){ // irian, ambon, ntt
            $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where iCabangId='$icabangid' order by nama";
        } else {
            if (($jabatanid=="18") or ($jabatanid=="10")) { //spv,am
                    $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where (atasanId='$karyawanId' or atasanId2='$karyawanId') order by nama";
            }
            if ($jabatanid=="08") { //dm
                    $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where iCabangId='$icabangid' order by nama"; 
            }
            if ($jabatanid=="15") { // mr
                    $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where karyawanId='$karyawanId'"; 
            }
        }
    }
    
    if ($query=="") {
        echo "<option value='' selected>-- Pilihan --</option>";
    }else{
        $tampil = mysqli_query($cnit, $query);
        echo "<option value='' selected>-- Pilihan --</option>";
        while ($r=mysqli_fetch_array($tampil)){
            echo "<option value='$r[mr_id]'>$r[nama]</option>";
        }
    }
}elseif ($_GET['module']=="viewdatacombokode"){
    include "../../config/koneksimysqli_it.php";

    $divprodid = $_POST['udiv']; 
    $query = "select kodeid,nama,divprodid from hrd.br_kode where "
    . " (divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N') order by nama"; 
    $result = mysqli_query($cnit, $query); 
    $record = mysqli_num_rows($result);
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['kodeid'];
        $nama = $row['nama'];
        echo "<option value=\"$kodeid\">$nama</option>";
    }
}elseif ($_GET['module']=="viewdatalevel2"){
    include "../../config/koneksimysqli_it.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnit, "SELECT COA2, NAMA2 FROM dbmaster.coa_level2 where COA1='$_POST[ulevel1]' order by COA2");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA2]'>$a[NAMA2]</option>";
    }
}elseif ($_GET['module']=="viewdatalevel3"){
    include "../../config/koneksimysqli_it.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnit, "SELECT COA3, NAMA3 FROM dbmaster.coa_level3 where COA2='$_POST[ulevel2]' order by COA3");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA3]'>$a[NAMA3]</option>";
    }
}elseif ($_GET['module']=="viewdatalevel4"){
    include "../../config/koneksimysqli_it.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnit, "SELECT COA4, NAMA4 FROM dbmaster.coa_level4 where COA3='$_POST[ulevel3]' order by COA4");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
    }
}elseif ($_GET['module']=="viewdatalevel5"){
    include "../../config/koneksimysqli_it.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnit, "SELECT COA_KODE, COA_NAMA FROM dbmaster.coa where COA4='$_POST[ulevel4]' order by COA_KODE");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA_KODE]'>$a[COA_NAMA]</option>";
    }
}elseif ($_GET['module']=="viewdatakaryawancabang"){
    include "../../config/koneksimysqli_it.php";
    $icabangid = $_POST['uicabang']; 
    if (($icabangid=='30') or ($icabangid=='31') or ($icabangid=='0000000032')) {
            $query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where (karyawanId='0000000154' or karyawanId='0000000159') AND aktif = 'Y' order by nama"; 
    } else {
            $query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where icabangid='$icabangid' AND aktif = 'Y' order by nama"; 
    }
    $tampil=mysqli_query($cnit, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[karyawanId]'>$a[nama]</option>";
    }
    
    
}elseif ($_GET['module']=="viewdoktermr"){
    include "../../config/koneksimysqli_it.php";
    
    $mr_id = $_POST['umr']; 
    $icabangid = $_POST['ucab']; 

    $query = "select iCabangId from hrd.karyawan where iCabangId='$icabangid'"; 
    $result = mysqli_query($cnit, $query); 
    $record = mysqli_num_rows($result); 
    if ($icabangid=="0000000001") {
        $query = "select distinct (mr_dokt.dokterId),CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                          from hrd.mr_dokt as mr_dokt 
                          join hrd.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
                          where mr_dokt.aktif <> 'N' and dokter.nama<>''
                          order by nama"; 
    } else {
        $query = "select dokter.dokterId, CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                          FROM hrd.mr_dokt as mr_dokt 
                          join hrd.karyawan as karyawan on mr_dokt.karyawanId=karyawan.karyawanId
                          join hrd.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
                          where mr_dokt.aktif <> 'N' and karyawan.karyawanId='$mr_id' and dokter.nama <> ''
                          order by dokter.nama";
    }
    $tampil=mysqli_query($cnit, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[dokterId]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatacombocoa"){
    include "../../config/koneksimysqli_it.php";
    
    $divprodid = $_POST['udiv'];
    $queryx = " AND ifnull(kodeid,'') (select distinct ifnull(kodeid,'') as kodeid from hrd.br_kode where "
        . " (divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N'))";
    
    $query = "SELECT COA4, NAMA4 FROM dbmaster.v_coa where DIVISI='$divprodid' AND "
            . "(divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N') order by COA4";
    $tampil=mysqli_query($cnit, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
    }
}elseif ($_GET['module']=="viewdatalevel2xxx"){
}

