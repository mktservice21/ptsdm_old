<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caricabangregion") {
    include "../../config/koneksimysqli.php";
    include "../../config/koneksimysqli_ms.php";
    
    $pmyidcard=$_SESSION['IDCARD'];
    $pmyjabatanid=$_SESSION['JABATANID'];
                
    $pidregion=$_POST['uregion'];
    $filregion="";
    if (!empty($pidregion)) $filregion=" AND a.region='$pidregion' ";

    
        $pidcabangpil="";
        $piddivisipil="EAGLE";
        $filiddivisipil="";
        $filtercabangbyadmin="";
        $query = "select distinct icabangid from hrd.rsm_auth WHERE karyawanid='$pmyidcard'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($rs= mysqli_fetch_array($tampil)) {
                $picabid_=$rs['icabangid'];
                $filtercabangbyadmin .="'".$picabid_."',";
            }
            if (!empty($filtercabangbyadmin)) {
                $filtercabangbyadmin="(".substr($filtercabangbyadmin, 0, -1).")";
            }
        }
        
        $ilewat=false;
        if ($pmyidcard=="0000002297") {
            
        }else{
            
            if ($pmyjabatanid=="15") {
                $query_cab = "select distinct icabangid, divisiid FROM sls.imr0 WHERE karyawanid='$pmyidcard'";
                $ilewat=true;
            }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
                $query_cab = "select distinct icabangid, divisiid FROM sls.ispv0 WHERE karyawanid='$pmyidcard'";
                $ilewat=true;
            }elseif ($pmyjabatanid=="08") {
                $query_cab = "select distinct icabangid, '' as divisiid FROM sls.idm0 WHERE karyawanid='$pmyidcard'";
                $ilewat=true;
            }elseif ($pmyjabatanid=="20") {
                $query_cab = "select distinct icabangid, '' as divisiid FROM sls.ism0 WHERE karyawanid='$pmyidcard'";
                $ilewat=true;
            }
        }
        
        if ($ilewat==true) {
            $filtercabangbyadmin="";
            
            $tampil= mysqli_query($cnms, $query_cab);
            while ($rs= mysqli_fetch_array($tampil)) {
                $picabid_=$rs['icabangid'];
                $pidcabangpil=$rs['icabangid'];
                $piddivi_=$rs['divisiid'];
                
                if (strpos($filtercabangbyadmin, $picabid_)==false) $filtercabangbyadmin .="'".$picabid_."',";
                
                if (!empty($piddivi_)) {
                    $piddivisipil=$rs['divisiid'];
                    
                    if (strpos($filiddivisipil, $piddivi_)==false) $filiddivisipil .="'".$piddivi_."',";
                }
                
            }
            
            
            if (!empty($filtercabangbyadmin)) {
                $filtercabangbyadmin="(".substr($filtercabangbyadmin, 0, -1).")";
            }
            
            if (!empty($filiddivisipil)) {
                $filiddivisipil="(".substr($filiddivisipil, 0, -1).")";
            }
            
        }
        
        
        if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="24") {
            $filtercabangbyadmin="";
        }else{
            if (!empty($filtercabangbyadmin)) $filtercabangbyadmin = " AND a.iCabangId IN $filtercabangbyadmin ";
            if (!empty($filiddivisipil)) $filiddivisipil = " AND DivProdId IN $filiddivisipil ";

        }
    
    if ($pmyjabatanid!="15" AND $pmyjabatanid!="10" AND $pmyjabatanid!="18")  echo "<option value=''>-- Pilih --</option>";

    
    
        $query = "select a.iCabangId, a.nama from sls.icabang as a where "
                . " a.aktif='Y' $filregion $filtercabangbyadmin ";
        $query .=" order by a.nama";
    
    $query = "select a.iCabangId, a.nama, c.nama as nama_karyawan from sls.icabang as a "
            . " LEFT JOIN (select DISTINCT karyawanid, icabangid from sls.idm0 WHERE ifnull(aktif,'')<>'N') as b "
            . " on a.icabangid=b.icabangid "
            . " LEFT JOIN hrd.karyawan as c on b.karyawanid=c.karyawanid "
            . " where a.aktif='Y' $filregion $filtercabangbyadmin ";
    $query .=" order by a.nama";
    
    
    $tampil = mysqli_query($cnms, $query);
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidcab=$rx['iCabangId'];
        $nnmcab=$rx['nama'];
        $nnmkaryawan=$rx['nama_karyawan'];

        $pcabnama=$nnmcab;
        if (!empty($nnmkaryawan)) $pcabnama="$nnmcab ($nnmkaryawan)";
                                                                
        if ($pidcabangpil==$nidcab)
            echo "<option value='$nidcab' selected>$pcabnama</option>";
        else
            echo "<option value='$nidcab'>$pcabnama</option>";
    }
    
	
        $query = "select a.iCabangId, a.nama from sls.icabang as a where "
            . " IFNULL(a.aktif,'')<>'Y' $filregion ";
        $query .=" AND LEFT(a.nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
        $query .=" order by a.nama";

    $query = "select a.iCabangId, a.nama, c.nama as nama_karyawan from sls.icabang as a "
            . " LEFT JOIN (select DISTINCT karyawanid, icabangid from sls.idm0 WHERE ifnull(aktif,'')<>'N') as b "
            . " on a.icabangid=b.icabangid "
            . " LEFT JOIN hrd.karyawan as c on b.karyawanid=c.karyawanid "
            . " where a.aktif<>'Y' $filregion ";
    $query .=" AND LEFT(a.nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
    $query .=" order by a.nama";
    
    $tampil = mysqli_query($cnms, $query);
    $ketemunon=mysqli_num_rows($tampil);
    if ($ketemunon>0) {
        echo "<option value='NONAKTIFPL'></option>";
        echo "<option value='NONAKTIFPL'>-- Non Aktif --</option>";
        while ($rx= mysqli_fetch_array($tampil)) {
            $nidcab=$rx['iCabangId'];
            $nnmcab=$rx['nama'];
            $nnmkaryawan=$rx['nama_karyawan'];

            $pcabnama=$nnmcab;
            if (!empty($nnmkaryawan)) $pcabnama="$nnmcab ($nnmkaryawan)";
            
            if ($pidcabangpil==$nidcab)
                echo "<option value='$nidcab' selected>$pcabnama</option>";
            else
                echo "<option value='$nidcab'>$pcabnama</option>";
        }
    }
	
    
    mysqli_close($cnms);
    mysqli_close($cnmy);
    
}elseif ($pmodule=="xxxx") {
    
    
}

?>

