<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="cariareacabang") {
    
    include "../../config/koneksimysqli_ms.php";
    $pidareapil="";
    $pidcabang=$_POST['ucabid'];
    $ptxtcab=$_POST['utxtcab'];
    $ptxtcabarea=$_POST['utxtcabarea'];
    $pmyidcard=$_POST['utxtidcard'];
    $pmyjabatanid=$_POST['utxtjbt'];
    
    if (empty($pmyidcard)) $pmyidcard=$_SESSION['IDCARD'];
    if (empty($pmyjabatanid)) $pmyjabatanid=$_SESSION['JABATANID'];
    
    $pfiltercabarea="";
    $ppilihbedaarea=true;
    if ($pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08") {
        if (!empty($ptxtcabarea)) $pfiltercabarea=" (".substr($ptxtcabarea, 0, -1).")";
        
        $ppilihbedaarea=false;
        if (strpos($ptxtcab, $pidcabang)==true) {
            $ppilihbedaarea=true;
        }
    }elseif ($pmyjabatanid=="15") {
        if (!empty($ptxtcabarea)) $pfiltercabarea=" (".substr($ptxtcabarea, 0, -1).")";
    }
    
    
    echo "<option value=''>-- All --</option>";
    
    $query = "select icabangid, areaid, nama, aktif from sls.iarea where 1=1 AND icabangid='$pidcabang' ";
    if ($pmyjabatanid=="15") {
        if (!empty($pfiltercabarea)) $query .=" AND (CONCAT(icabangid, areaid) IN $pfiltercabarea ) ";//AND IFNULL(aktif,'')='Y'
    }elseif ($pmyjabatanid=="08xx") {
        
    }else{
        if ($ppilihbedaarea==false) {
            $query .=" AND IFNULL(aktif,'')<>'Y' ";
        }else{
            if ($pmyjabatanid=="08") {
            }else{
                if (!empty($pfiltercabarea)) $query .=" AND (CONCAT(icabangid, areaid) IN $pfiltercabarea OR IFNULL(aktif,'')<>'Y') ";
            }
        }
    }
    $query .=" order by aktif DESC, nama";
    
    $tampil = mysqli_query($cnms, $query);
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidarea=$rx['areaid'];
        $nnmarea=$rx['nama'];
        $nnmaktif=$rx['aktif'];

        $namaaktif="Aktif";
        if ($nnmaktif!="Y") $namaaktif="Non Aktif";

        if ($pidareapil==$nidarea)
            echo "<option value='$nidarea' selected>$nnmarea ($namaaktif)</option>";
        else
            echo "<option value='$nidarea'>$nnmarea ($namaaktif)</option>";
    }
    
    mysqli_close($cnms);
    
}elseif ($pmodule=="caridataproduk") {
    include "../../config/koneksimysqli_ms.php";
    $pmyjabatanid=$_SESSION['JABATANID'];
    $pmyidcard=$_SESSION['IDCARD'];
    
    $piddivisipil=$_POST['udivi'];
    $fileter_div="";
    $query_cab="";
    
    if (empty($piddivisipil)) {
        
        if ($pmyjabatanid=="15") {
            $query_cab = "select distinct divisiid FROM sls.imr0 WHERE karyawanid='$pmyidcard'";
        }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
            $query_cab = "select distinct divisiid FROM sls.ispv0 WHERE karyawanid='$pmyidcard'";
        }
        
        if (!empty($query_cab)) {
            $tampil= mysqli_query($cnms, $query_cab);
            while ($rs= mysqli_fetch_array($tampil)) {
                $piddivi_=$rs['divisiid'];
                
                if (!empty($piddivi_)) {
                    $piddivisipil=$rs['divisiid'];
                    if (strpos($filiddivisipil, $piddivi_)==false) $filiddivisipil .="'".$piddivi_."',";
                }
                
            }
            
            if (!empty($filiddivisipil)) {
                $filiddivisipil="(".substr($filiddivisipil, 0, -1).")";
                
                $fileter_div=" AND divprodid IN $filiddivisipil ";
            }
            
        }
        
    }else{
        $fileter_div=" AND divprodid='$piddivisipil' ";
    }
        
    
    echo "<option value=''>-- All --</option>";
    $query = "select iprodid, nama from sls.iproduk where divprodid NOT IN ('OTC') $fileter_div order by nama";
    $tampil = mysqli_query($cnms, $query);
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidprod=$rx['iprodid'];
        $nnmprod=$rx['nama'];
        echo "<option value='$nidprod'>$nnmprod</option>";
    }
    
    mysqli_close($cnms);
    
}

?>

