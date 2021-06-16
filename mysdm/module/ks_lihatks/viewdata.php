<?php
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewdatakaryawan") {
    $fkaryawan=$_SESSION['IDCARD'];
    $fjbtid=$_SESSION['JABATANID'];
    $fgroupid=$_SESSION['GROUP'];
    
    include "../../config/koneksimysqli.php";
    
    $pidcab=$_POST['uidcab'];
    
    if (empty($pidcab)) {
        include "../../config/fungsi_sql.php";
        
        if ($fjbtid=="38" OR $fjbtid=="33" OR $fjbtid=="05" OR $fjbtid=="20" OR $fjbtid=="08" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {

            $pnregion="";
            if ($fkaryawan=="0000000159") $pnregion="T";
            elseif ($fkaryawan=="0000000158") $pnregion="B";
            $pfilterkry=CariDataKaryawanByCabJbt2($fkaryawan, $fjbtid, $pnregion);

            if (!empty($pfilterkry)) {
                $parry_kry= explode(" | ", $pfilterkry);
                if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
                if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
            }

        }
        //echo $pfilterkaryawan; exit;
        $query = "select karyawanId, nama From hrd.karyawan
            WHERE 1=1 ";
        if (!empty($pfilterkaryawan) AND $fgroupid<>"24") {
            $query .= " AND karyawanid IN $pfilterkaryawan ";
        }else{
            $query .= " AND nama NOT IN ('ACCOUNTING')";
            $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
        }

        $query .= " ORDER BY nama";


        $tampil = mysqli_query($cnmy, $query);

        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";

        while ($z= mysqli_fetch_array($tampil)) {
            $pkaryid=$z['karyawanId'];
            $pkarynm=$z['nama'];
            $pkryid=(INT)$pkaryid;
            echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
        }
        
    }else{
        if ($fjbtid=="15") {
            $query = "select distinct a.karyawanid as karyawanid, a.nama as nama "
                    . " from hrd.karyawan as a JOIN MKT.imr0 as b on a.karyawanid=b.karyawanid "
                    . " WHERE b.icabangid='$pidcab' AND b.karyawanid='$fkaryawan' ";
        }else{
            $query = "select distinct a.karyawanid as karyawanid, a.nama as nama "
                    . " from hrd.karyawan as a JOIN MKT.imr0 as b on a.karyawanid=b.karyawanid "
                    . " WHERE b.icabangid='$pidcab' ";
        }
        
        $query .= " ORDER BY a.nama";
        $tampil = mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";

        while ($z= mysqli_fetch_array($tampil)) {
            $pkaryid=$z['karyawanid'];
            $pkarynm=$z['nama'];
            $pkryid=(INT)$pkaryid;
            echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
        }
    
    }
    
    
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdataareacab") {
    include "../../config/koneksimysqli.php";
    
    $pidcab=$_POST['uidcab'];
    
    echo "<option value='' selected>-- All --</option>";
    $query = "select areaid as areaid, nama as nama from mkt.iarea WHERE iCabangId='$pidcab' AND IFNULL(aktif,'')<>'N' ";
    $query .=" order by nama";
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $nareaid=$row['areaid'];
        $nareanm=$row['nama'];
        
        echo "<option value='$nareaid' >$nareanm</option>";
    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdataoutlet") {
    include "../../config/koneksimysqli_ms.php";
    
    $pidcab=$_POST['uidcab'];
    $pidarea=$_POST['uidarea'];
    
    echo "<option value='' selected>-- Pilih --</option>";
    
    $query = "SELECT distinct d.iCabangId as icabangid, g.nama as nama_cabang, d.areaId as areaid, 
        h.nama as nama_area, a.id as idoutlet, a.nama as nama_outelt, a.jenis, b.nama as nama_sektor, 
        a.type, c.Nama as nama_type, a.dispensing, a.alamat,
        e.iddokter, f.namalengkap as nama_dokter  
        FROM ms2.outlet_master as a LEFT JOIN mkt.isektor as b on a.jenis=b.iSektorId 
        LEFT JOIN ms2.outlet_type as c on a.type=c.id 
        LEFT JOIN ms2.outlet_customer as d on a.id=d.outletId 
        LEFT JOIN ms2.tempatpraktek as e on d.outletId=e.outletId
        LEFT JOIN ms2.masterdokter as f on e.iddokter=f.id 
        LEFT JOIN mkt.icabang as g on d.iCabangId=g.iCabangId 
        LEFT JOIN mkt.iarea as h on d.iCabangId=h.iCabangId and d.areaId=h.areaId 
        WHERE d.icabangid='$pidcab' ";
    if (!empty($pidarea)) {
        $query .=" AND d.areaid='$pidarea' ";
    }
    $query .=" ORDER BY a.nama, a.id";
    $tampil= mysqli_query($cnms, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pnareaid=$row['areaid'];
        $pnareanm=$row['nama_area'];
        $pnotlid=$row['idoutlet'];
        $pnotlnm=$row['nama_outelt'];
        $pntypeotl=$row['nama_type'];
        $pndispensing=$row['dispensing'];
        $pnalamatotl=$row['alamat'];
        $pniddokt=$row['iddokter'];
        $pnnmdokt=$row['nama_dokter'];
        
        echo "<option value='$pnotlid' >$pnotlnm  - $pnnmdokt</option>";
    }
    
    mysqli_close($cnms);
    
}

?>

