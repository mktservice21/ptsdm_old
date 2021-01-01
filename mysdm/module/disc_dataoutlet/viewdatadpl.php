<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdataareacabang") {
    $pidcabang=$_POST['udcab'];
    $pidpilih=$_POST['upilih'];
    $fjbtid=$_SESSION['JABATANID'];
    $fkaryawan=$_SESSION['IDCARD'];
    
    include "../../config/koneksimysqli.php";
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
    
    $tampila= mysqli_query($cnmy, $query);
    $ketemua= mysqli_num_rows($tampila);
    if ((INT)$ketemua==0 OR $pidpilih=="M") echo "<option value='' selected>--Pilih--</option>";
    while ($arow= mysqli_fetch_array($tampila)) {
        $nidarea=$arow['areaid'];
        $nnmarea=$arow['nama'];

        if ($nidarea==$pidarea) 
            echo "<option value='$nidarea' selected>$nnmarea</option>";
        else
            echo "<option value='$nidarea'>$nnmarea</option>";

    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdatacustomer") {
    $pidcabang=$_POST['udcab'];
    $pidarea=$_POST['udarea'];
    $pidcust="";
    
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    
    $query = "select icabangid as icabangid, areaid as areaid, icustid as icustid, nama as nama from MKT.icust WHERE "
            . " icabangid='$pidcabang' AND areaid='$pidarea' AND ifnull(icabangid,'')<>'' AND ifnull(areaid,'')<>'' ";
    $query .=" AND IFNULL(aktif,'')<>'N' ";
    $query .=" order by CASE WHEN IFNULL(nama,'')='' then 'zzzz' else LTRIM(nama) end";
    $tampila= mysqli_query($cnmy, $query);
    $ketemua= mysqli_num_rows($tampila);
    if ((INT)$ketemua==0) echo "<option value='' selected>--Pilih--</option>";
    while ($arow= mysqli_fetch_array($tampila)) {
        $nidcust=$arow['icustid'];
        $nnmcust=$arow['nama'];

        if ($nidcust==$pidcust) 
            echo "<option value='$nidcust' selected>$nnmcust ($nidcust)</option>";
        else
            echo "<option value='$nidcust'>$nnmcust ($nidcust)</option>";

    }
    
    mysqli_close($cnmy);
    
    
}elseif ($pmodule=="viewdatamulticustomer") {
    $pidcabang=$_POST['udcab'];
    $pidarea=$_POST['udarea'];
    $pidcust="";
    
    $fjbtid=$_SESSION['JABATANID'];
    $fkaryawan=$_SESSION['IDCARD'];
    
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    
    echo "<table id='tblcustomer'>";
    echo "<tbody>";
    
    if ($fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {
        $ptabelnya=" dbmaster.ispv0_ms ";
        if ($fjbtid == "15") $ptabelnya=" dbmaster.imr0_ms ";
        
        $query = "select distinct a.icabangid as icabangid, a.areaid as areaid, a.icustid as icustid, a.nama as nama "
                . " from MKT.icust as a "
                . " JOIN $ptabelnya as b on a.icabangid=b.icabangid AND a.areaid=b.areaid "
                . " WHERE b.karyawanid='$fkaryawan' AND "
                . " a.icabangid='$pidcabang' AND ifnull(a.icabangid,'')<>'' AND ifnull(a.areaid,'')<>'' ";//AND areaid='$pidarea'
        $query .=" AND IFNULL(a.aktif,'')<>'N' ";
        $query .=" order by CASE WHEN IFNULL(a.nama,'')='' then 'zzzz' else LTRIM(a.nama) end";
        
    }else{
        
        $query = "select icabangid as icabangid, areaid as areaid, icustid as icustid, nama as nama from MKT.icust WHERE "
                . " icabangid='$pidcabang' AND ifnull(icabangid,'')<>'' AND ifnull(areaid,'')<>'' ";//AND areaid='$pidarea'
        $query .=" AND IFNULL(aktif,'')<>'N' ";
        $query .=" order by CASE WHEN IFNULL(nama,'')='' then 'zzzz' else LTRIM(nama) end";
    
    }
    
    
    $tampila= mysqli_query($cnmy, $query);
    $ketemua= mysqli_num_rows($tampila);
    while ($arow= mysqli_fetch_array($tampila)) {
        $nidcust=$arow['icustid'];
        $nnmcust=$arow['nama'];

        $nidcabang=$arow['icabangid'];
        $nidarea=$arow['areaid'];
        
        $pidareaidcust=$nidcabang."|".$nidarea."|".$nidcust;
        
        $pchkpilihcust="";
        if ($nidcust==$pidcust) $pchkpilihcust="checked";

        //echo "<input type=checkbox value='$nidcust' name='chkbox_custid[]' $pchkpilihcust> $nnmcust ($nidcust)<br/>";
        
        $pchkcust="<input type=checkbox value='$pidareaidcust' name='chkbox_custid[]' $pchkpilihcust> $nnmcust ($nidcust)";
        
        echo "<tr>";
        echo "<td class='divnone'>$nidcabang</td>";
        echo "<td class='divnone'>$nidarea</td>";
        echo "<td nowrap>$pchkcust</td>";
        echo "</tr>";

    }
    echo "</tbody>";
    echo "</table>";
    
    mysqli_close($cnmy);
    ?>
    <style>
        .divnone {
            display: none;
        }
    </style>
    <?PHP
}

?>