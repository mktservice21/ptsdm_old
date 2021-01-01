<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="cariareakaryawan") {
    include "../../config/koneksimysqli_ms.php";
    
    $pmyidcard=$_POST['uidkry'];
    
    $ptampil=mysqli_query($cnms, "select jabatanid from ms.karyawan where karyawanid='$pmyidcard'");
    $nro= mysqli_fetch_array($ptampil);
    $pmyjabatanid=$nro['jabatanid'];
    
    
    $query_area="";
    if ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
        $query_area="select distinct a.icabangid, a.areaid, b.nama nama_area, c.nama nama_cabang from sls.ispv0 a JOIN sls.iarea b on a.areaid=b.areaid and a.icabangid=b.icabangid LEFT JOIN sls.icabang c on b.icabangid=c.icabangid WHERE a.karyawanid='$pmyidcard'";
    }elseif ($pmyjabatanid=="08") {
        $query_area="select distinct a.icabangid, a.areaid, b.nama nama_area, c.nama nama_cabang from sls.ispv0 a JOIN sls.iarea b on a.areaid=b.areaid and a.icabangid=b.icabangid LEFT JOIN sls.icabang c on b.icabangid=c.icabangid WHERE "
                . " a.icabangid in (select DISTINCT IFNULL(icabangid,'') from sls.idm0 WHERE karyawanid='$pmyidcard')";
    }elseif ($pmyjabatanid=="15") {
        $query_area="select distinct a.icabangid, a.areaid, b.nama nama_area, c.nama nama_cabang from sls.imr0 a JOIN sls.iarea b on a.areaid=b.areaid and a.icabangid=b.icabangid LEFT JOIN sls.icabang c on b.icabangid=c.icabangid WHERE a.karyawanid='$pmyidcard'";
    }elseif ($pmyjabatanid=="20") {
        $query_area="select distinct a.icabangid, a.areaid, b.nama nama_area, c.nama nama_cabang from sls.ispv0 a JOIN sls.iarea b on a.areaid=b.areaid and a.icabangid=b.icabangid LEFT JOIN sls.icabang c on b.icabangid=c.icabangid WHERE "
                . " a.icabangid in (select DISTINCT IFNULL(icabangid,'') from sls.ism0 WHERE karyawanid='$pmyidcard')";
    }else{
        if ($pmyidcard=="0000000158" OR $pmyidcard=="0000000159") {
            $query_area = "select distinct b.icabangid, b.areaid, b.nama nama_area, c.nama nama_cabang from sls.iarea b JOIN sls.icabang c on b.icabangid=c.icabangid where IFNULL(c.aktif,'')='Y' ";
            //$query_area .=" AND LEFT(b.nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -') ";
            if ($pmyidcard=="0000000158") {
                $query_area .= " AND c.region='B' ";
            }elseif ($pmyidcard=="0000000159") {
                $query_area .= " AND c.region='T' ";
            }
        }
    }

    if (!empty($query_area)) {
        $query_area .=" ORDER BY c.nama, b.nama";

        $tampil = mysqli_query($cnms, $query_area);
        while ($rx= mysqli_fetch_array($tampil)) {
            $nidcabang=$rx['icabangid'];
            $nnmcabang=$rx['nama_cabang'];
            $nidarea=$rx['areaid'];
            $nnmarea=$rx['nama_area'];

            $picabidarea=$nidcabang."".$nidarea;
            //echo $pmyidcard." - ".$pmyjabatanid."<br/>";
            echo "&nbsp; <input type=checkbox name='chkbox_icabarea[]'  id='chkbox_icabarea[]' value='$picabidarea' checked> $nnmcabang - $nnmarea<br/>";
        }

    }
    
}

?>