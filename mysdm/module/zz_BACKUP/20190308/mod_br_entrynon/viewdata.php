<?php
session_start();
if ($_GET['module']=="viewdatacomboposting"){
    include "../../config/koneksimysqli_it.php";
    $tampil = mysqli_query($cnit, "SELECT distinct kodeid, nama from dbmaster.brkd_otc where ifnull(subpost,'') = '$_POST[ukodesub]'");
    echo "<option value='' selected>-- Pilihan --</option>";
    while ($r=mysqli_fetch_array($tampil)){
        echo "<option value='$r[kodeid]'>$r[nama]</option>";
    }
}elseif ($_GET['module']=="viewmarcabang"){
    include "../../config/koneksimysqli_it.php";
    
    $karyawanId = $_POST['ukaryawan']; 
    $icabangid = $_POST['ucabang']; 
    $query = "select jabatanId from dbmaster.karyawan where karyawanId='$karyawanId'"; 	
    $result = mysqli_query($cnit, $query);
    $records = mysqli_num_rows($result);
    $row = mysqli_fetch_array($result);
    $jabatanid = $row['jabatanId'];
    
    if ($icabangid=="0000000001") { //ho
        $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan order by nama"; 
    } else {
        if (($icabangid=="0000000030") or ($icabangid=='0000000031') or ($icabangid=='0000000032')){ // irian, ambon, ntt
            $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan where iCabangId='$icabangid' order by nama"; 
        } else {
            if (($jabatanid=="18") or ($jabatanid=="10")) { //spv,am
                    $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan where (atasanId='$karyawanId' or atasanId2='$karyawanId') order by nama";
            }
            if ($jabatanid=="08") { //dm
                    $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan where iCabangId='$icabangid' order by nama"; 
            }
            if ($jabatanid=="15") { // mr
                    $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan where karyawanId='$karyawanId'"; 
            }else{
                $query="";
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
    $query = "select kodeid,nama,divprodid from dbmaster.br_kode where "
    . " (divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N') order by nama"; 
    $result = mysqli_query($cnit, $query); 
    $record = mysqli_num_rows($result);
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['kodeid'];
        $nama = $row['nama'];
        echo "<option value=\"$kodeid\">$nama</option>";
    }
}elseif ($_GET['module']=="viewdatacombokodenon"){
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    $kodeidcoa="";
    if (!empty($_POST['ucoa']))
        $kodeidcoa= getfieldcnmy("select kodeid as lcfields from dbmaster.v_coa where COA4='$_POST[ucoa]'");
    
    $divprodid = $_POST['udiv'];
    $query = "select kodeid,nama,divprodid from dbmaster.br_kode where (divprodid='$divprodid' and br = '')  "
            . " and (divprodid='$divprodid' and br<>'N') order by nama";
    $result = mysqli_query($cnit, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['kodeid'];
        $nama = $row['nama'];
        if ($kodeid==$kodeidcoa)
            echo "<option value=\"$kodeid\" selected>$nama</option>";
        else
            echo "<option value=\"$kodeid\">$nama</option>";
    }
}elseif ($_GET['module']=="viewdatacabangkaryawan"){
    include "../../config/koneksimysqli_it.php";
    
    $karyawanId = $_POST['umr']; 
    $query = "select karyawan.iCabangId, cabang.nama from dbmaster.karyawan as karyawan join dbmaster.icabang as cabang on "
            . " karyawan.icabangid=cabang.icabangid where karyawanId='$karyawanId'"; 
    $result = mysqli_query($cnit, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['iCabangId'];
        $nama = $row['nama'];
        echo "<option value=\"$kodeid\" selected>$nama</option>";
    }
}elseif ($_GET['module']=="viewdatacombocoa"){
    include "../../config/koneksimysqli_it.php";
    
    
    
    $sql="select distinct COA4 from dbmaster.v_coa_wewenang where karyawanId='$_SESSION[IDCARD]'";//DCC & DSS
    $tampil=mysqli_query($cnit, $sql);
    $ketemu=mysqli_num_rows($tampil);
    $filcoa="";
    if ($ketemu>0) {
        while ($ar=  mysqli_fetch_array($tampil)) {
            $filcoa .= "'".$ar['COA4']."',";
        }
        if (!empty($filcoa)) {
            $filcoa=" AND COA4 in (".substr($filcoa, 0, -1).")";
        }
    }
    
    
    
    $divprodid = $_POST['udiv'];
    
    $query = "SELECT COA4, NAMA4 FROM dbmaster.v_coa where (DIVISI='$divprodid' or ifnull(DIVISI,'')='') AND "
            . "( ((divprodid='$divprodid' and br = '') and (divprodid='$divprodid' and br<>'N'))   or ifnull(kodeid,'')='') $filcoa order by COA4";
    
    $tampil=mysqli_query($cnit, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
    }
}elseif ($_GET['module']=="x"){
}elseif ($_GET['module']=="xx"){
}

