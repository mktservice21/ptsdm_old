<?php
session_status();

if ($_GET['module']=="caridataarea") {
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $pcabangidpl=$_POST['ucabang'];
    
    echo "<option value=''>--All--</option>";
    if (!empty($pcabangidpl)) {
        $query_area="SELECT areaid as areaid, nama as nama from MKT.iarea where icabangid='$pcabangidpl' ";
        $query_ak =$query_area." AND IFNULL(aktif,'')='Y' ";
        $query_ak .=" order by nama";
        $tampil= mysqli_query($cnmy, $query_ak);
        while ($row= mysqli_fetch_array($tampil)) {
            $pidarea=$row['areaid'];
            $pnmarea=$row['nama'];
            $pintidarea=(INT)$pidarea;
            echo "<option value='$pidarea'>$pnmarea ($pintidarea)</option>";
        }
        


        $query_non =$query_area." AND IFNULL(aktif,'')<>'Y' ";
        $query_non .=" order by nama";
        $tampil= mysqli_query($cnmy, $query_non);
        $ketemunon= mysqli_num_rows($tampil);
        if ($ketemunon>0) {
            echo "<option value='NONAKTIF'>-- Non Aktif--</option>";
            while ($row= mysqli_fetch_array($tampil)) {
                $pidarea=$row['areaid'];
                $pnmarea=$row['nama'];
                $pintidarea=(INT)$pidarea;
                echo "<option value='$pidarea'>$pnmarea ($pintidarea)</option>";
            }
        }
    }
    
    mysqli_close($cnmy);
    
}elseif ($_GET['module']=="viewdataareacabang") {
    $pidcabang=$_POST['udcab'];
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
    if ((INT)$ketemua==0) echo "<option value='' selected>--Pilih--</option>";
    while ($arow= mysqli_fetch_array($tampila)) {
        $nidarea=$arow['areaid'];
        $nnmarea=$arow['nama'];

        if ($nidarea==$pidarea) 
            echo "<option value='$nidarea' selected>$nnmarea</option>";
        else
            echo "<option value='$nidarea'>$nnmarea</option>";

    }
    
    mysqli_close($cnmy);
}


?>
