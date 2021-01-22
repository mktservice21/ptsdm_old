<?php
session_status();

if ($_GET['module']=="caridataecust") {
    include "../../config/koneksimysqli_ms.php";
    $pdistidpl=$_POST['ucabang'];
    
    echo "<option value=''>--All--</option>";
    if (!empty($pdistidpl)) {
        $query="SELECT distid, ecabangid, nama from MKT.ecabang where distid='$pdistidpl' ";
        $query .=" order by nama";
        $tampil= mysqli_query($cnms, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pidecab=$row['ecabangid'];
            $pnmecab=$row['nama'];
            echo "<option value='$pidecab'>$pnmecab ($pidecab)</option>";
        }
       
    }
    
    mysqli_close($cnms);
    
}elseif ($_GET['module']=="viewdataareacabang") {
    $pidcabang=$_POST['udcab'];
    $pidarea="";
    $fjbtid=$_SESSION['JABATANID'];
    $fkaryawan=$_SESSION['IDCARD'];
    
    include "../../config/koneksimysqli_ms.php";
    if ($fjbtid=="10" OR $fjbtid=="18") {
        
        $query = "select DISTINCT a.icabangid as icabangid, a.areaid as areaid, a.nama as nama "
                . " from MKT.iarea as a "
                . " JOIN MKT.ispv0 as b on a.icabangid=b.icabangid AND a.areaid=b.areaid "
                . " WHERE a.icabangid='$pidcabang' AND b.karyawanid='$fkaryawan'";
        
        $query .=" AND IFNULL(a.aktif,'')<>'N' ";
        $query .=" order by a.nama";
        
    }elseif ($fjbtid=="15") {
        
        $query = "select DISTINCT a.icabangid as icabangid, a.areaid as areaid, a.nama as nama "
                . " from MKT.iarea as a "
                . " JOIN MKT.imr0 as b on a.icabangid=b.icabangid AND a.areaid=b.areaid "
                . " WHERE a.icabangid='$pidcabang' AND b.karyawanid='$fkaryawan'";
        
        $query .=" AND IFNULL(a.aktif,'')<>'N' ";
        $query .=" order by a.nama";
        
    }else{
        $query = "select icabangid as icabangid, areaid as areaid, nama as nama from MKT.iarea WHERE icabangid='$pidcabang' ";
        $query .=" AND IFNULL(aktif,'')<>'N' ";
        $query .=" order by nama";
    }
    
    $tampila= mysqli_query($cnms, $query);
    $ketemua= mysqli_num_rows($tampila);
    if ((INT)$ketemua==0) echo "<option value='' selected>--Pilih--</option>";
    while ($arow= mysqli_fetch_array($tampila)) {
        $nidarea=$arow['areaid'];
        $nnmarea=$arow['nama'];

        if ($nidarea==$pidarea) 
            echo "<option value='$nidarea' selected>$nnmarea ($nidarea)</option>";
        else
            echo "<option value='$nidarea'>$nnmarea ($nidarea)</option>";

    }
    
    mysqli_close($cnms);
}elseif ($_GET['module']=="viewdatacustomer") {
    $pidcabang=$_POST['udcab'];
    $pidarea=$_POST['udarea'];
    
    include "../../config/koneksimysqli_ms.php";
    
    $query = "select icustid, nama from MKT.icust WHERE IFNULL(aktif,'')<>'N' AND icabangid='$pidcabang' and areaid='$pidarea' order by nama";
    $tampila= mysqli_query($cnms, $query);
    $ketemua= mysqli_num_rows($tampila);
    if ((INT)$ketemua==0) echo "<option value='' selected>--Pilih--</option>";
    while ($arow= mysqli_fetch_array($tampila)) {
        $nidcust=$arow['icustid'];
        $nnmcust=$arow['nama'];

        echo "<option value='$nidcust'>$nnmcust ($nidcust)</option>";

    }
    
    mysqli_close($cnms);
}


?>
