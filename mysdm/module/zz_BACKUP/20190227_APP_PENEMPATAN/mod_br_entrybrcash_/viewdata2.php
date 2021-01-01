<?php

if ($_GET['module']=="getperiode"){
    $bulan = "01-".str_replace('/', '-', $_POST['ubulan']);
    if ($_POST['ukode']==1) {
        $periode1= date("Y-m-d", strtotime($bulan));
        $periode2= date("Y-m-15", strtotime($bulan));
    }elseif ($_POST['ukode']==2) {
        $periode1= date("Y-m-16", strtotime($bulan));
        $periode2= date("Y-m-t", strtotime($bulan));
    }
    $bln1=""; $bln2="";
    if (!empty($_POST['ukode'])) {
        $bln1= date("d/m/Y", strtotime($periode1));
        $bln2= date("d/m/Y", strtotime($periode2));
    }
    echo "$bln1, $bln2";
}elseif ($_GET['module']=="viewdatajabatankaryawan"){
    include "../../config/fungsi_sql.php";
    $pkaryawan=$_POST['umr'];
    $pjabatanid = getfieldcnit("select jabatanId as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
    $rank = getfieldcnit("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
    $lvlpos = getfieldcnit("select LEVELPOSISI as lcfields from dbmaster.jabatan_level where jabatanId='$pjabatanid'");
    $lvlpos=trim($lvlpos);
    echo "$pjabatanid, $rank, $lvlpos";
}elseif ($_GET['module']=="viewdatadivisi"){
    include "../../config/koneksimysqli_it.php";
    $rank=$_POST['urank'];
    $kry=$_POST['umr'];
    if ($rank=="05" OR $rank==5)
        $sql= mysqli_query($cnit, "select distinct divisiid from MKT.imr0 where karyawanid='$kry'");
    elseif ($rank=="04" OR $rank==4)
        $sql= mysqli_query($cnit, "select distinct divisiid from MKT.ispv0 where karyawanid='$kry'");
    else
        $sql=mysqli_query($cnit, "SELECT DivProdId divisiid FROM dbmaster.divprod where br='Y' order by nama");
    
    $ketemu= mysqli_num_rows($sql);
    if ($ketemu==0) {
        $sql=mysqli_query($cnit, "SELECT DivProdId divisiid FROM dbmaster.divprod where br='Y' order by nama");
        $ketemu= mysqli_num_rows($sql);
    }
    
    
    echo "<option value=''>-- Pilihan --</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        if ($ketemu==1)
            echo "<option value='$Xt[divisiid]' selected>$Xt[divisiid]</option>";
        else
            echo "<option value='$Xt[divisiid]'>$Xt[divisiid]</option>";
    }
}elseif ($_GET['module']=="viewdataatasan"){
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    
    $karyawan=$_POST['umr'];
    $zjabatan=(int)$_POST['ujbt'];
    $prank=(int)$_POST['urank'];
    $area=$_POST['uarea'];
    
    $patasan = getfieldcnit("select atasanId as lcfields from hrd.karyawan where karyawanId='$karyawan'");
    
    $query="SELECT DISTINCT karyawanId, nama FROM hrd.karyawan WHERE (ifnull(tglkeluar,'0000-00-00') = '0000-00-00' OR tglkeluar = '0000-00-00') "
            . " AND nama not like '%NN - %' AND nama not like '%NN AM %' AND nama not like '%DR - %' AND nama not like '%NN DM %' AND nama not like '%DM - %' ";
    if ((int)$prank==4)//SPV or Area Manager
        $query .=" AND jabatanId in (select distinct jabatanId from hrd.jabatan where rank='03')";
    elseif ((int)$prank==5)//MR
        $query .=" AND jabatanId in (select distinct jabatanId from hrd.jabatan where rank='04')";
    elseif ((int)$prank==3 AND (int)$zjabatan==8)
        $query .=" AND jabatanId in ('20', '31')";
    if ((int)$prank==2 OR $zjabatan==20)
        $query .=" AND jabatanId in (select distinct jabatanId from hrd.jabatan where rank='02')";
    
    
    
    $filemp="";
    if ((int)$zjabatan==15) { //MR
        $query ="select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatanspv as a WHERE CONCAT(a.areaid, a.divisiid, a.icabangid) in 
                (select CONCAT(b.areaid, b.divisiid, b.icabangid) 
                from MKT.imr0 b where b.karyawanid='$karyawan' and b.areaid='$area')";
    }elseif ((int)$zjabatan==10 OR (int)$zjabatan==18) {
        $query ="select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatandm as a WHERE a.icabangid in 
            (select b.icabangid from MKT.ispv0 b where b.karyawanid='$karyawan')";
    }elseif ((int)$zjabatan==8) {
        $query ="select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatansm as a WHERE a.icabangid in 
            (select b.icabangid from MKT.idm0 b where b.karyawanid='$karyawan')";
    }
        
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnit, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){ 
        if ($a['karyawanId']==$patasan)
            echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
        else{
            if ($ketemu==1) echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
            else echo "<option value='$a[karyawanId]'>$a[nama]</option>";
        }
    }
}elseif ($_GET['module']=="viewdataatasanspv"){
    include "../../config/koneksimysqli_it.php";
    $karyawan=$_POST['umr'];
    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatanspv as a WHERE CONCAT(a.divisiid, a.icabangid, a.areaid) in 
        (select CONCAT(b.divisiid, b.icabangid, b.areaid) 
        from MKT.imr0 b where b.karyawanid='$karyawan')";
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnit, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){ 
        $sel="";
        if ($ketemu==1) $sel="selected";
        echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdataatasandm"){
    include "../../config/koneksimysqli_it.php";
    $karyawan=$_POST['umr'];
    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatandm as a WHERE CONCAT(a.icabangid) in 
        (select CONCAT(b.icabangid) 
        from MKT.ispv0 b where b.karyawanid='$karyawan')";
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnit, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        $sel="";
        if ($ketemu==1) $sel="selected";
        echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdataatasansm"){
    include "../../config/koneksimysqli_it.php";
    $karyawan=$_POST['umr'];
    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatansm as a WHERE CONCAT(a.icabangid) in 
        (select CONCAT(b.icabangid) 
        from MKT.idm0 b where b.karyawanid='$karyawan')";
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnit, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        $sel="";
        if ($ketemu==1) $sel="selected";
        echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatalevel"){
    include "../../config/fungsi_sql.php";
    $zkry=$_POST['umr'];
    $zjabatan = (int)getfieldcnit("select jabatanId as lcfields from hrd.karyawan where karyawanId=$zkry");
    $lvlpengajuan = getfieldcnit("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId=$zjabatan");
    echo $lvlpengajuan;
}elseif ($_GET['module']=="zzz"){
}elseif ($_GET['module']=="zzz"){
}elseif ($_GET['module']=="zzz"){
}

?>

