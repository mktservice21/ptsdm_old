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
    
}elseif ($pmodule=="viewdatadokter") {
    include "../../config/koneksimysqli_ms.php";
    
    $pidcab=$_POST['uidcab'];
    $pidarea=$_POST['uidarea'];
    
    echo "<option value='' selected>-- Pilih --</option>";
    //d.iCabangId as icabangid, e.nama as nama_cabang, d.areaId as areaid, f.Nama as nama_area, 
    $query = "SELECT DISTINCT 
        a.iddokter, g.namalengkap as nama_dokter, g.spesialis, h.nama as nama_spesialis  
        FROM ms2.tempatpraktek as a 
        JOIN ms2.outlet_master as b on a.outletId=b.id 
        LEFT JOIN ms2.outlet_type as c on b.type=c.id 
        JOIN ms2.outlet_customer as d on a.outletId=d.outletId 
        LEFT JOIN mkt.icabang as e on d.iCabangId=e.iCabangId 
        LEFT JOIN mkt.iarea as f on d.iCabangId=f.iCabangId and d.areaId=f.areaId 
        JOIN ms2.masterdokter as g on a.iddokter=g.id 
        LEFT JOIN ms2.lookup as h on g.spesialis=h.id 
        WHERE d.icabangid='$pidcab' ";
    if (!empty($pidarea)) {
        $query .=" AND d.areaid='$pidarea' ";
    }
    $query .=" ORDER BY g.namalengkap, a.iddokter";
    $tampil= mysqli_query($cnms, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pniddokt=$row['iddokter'];
        $pnnmdokt=$row['nama_dokter'];

        echo "<option value='$pniddokt' >$pnnmdokt - ($pniddokt)</option>";
    }    
    
    mysqli_close($cnms);
    
}elseif ($pmodule=="viewdataoutlet") {
    include "../../config/koneksimysqli_ms.php";
    
    $pidcab=$_POST['uidcab'];
    $pidarea=$_POST['uidarea'];
    $piddokt=$_POST['uiddokt'];
    
    echo "<option value='' selected>-- Pilih --</option>";
    
    //d.iCustId as icustid, 
    $query = "SELECT distinct a.approve as approvepraktek, a.id as idpraktek, a.outletId as idoutlet, b.nama as nama_outlet, b.alamat,  
        b.jenis, b.type, c.Nama as nama_type, b.dispensing, 
        d.iCabangId as icabangid, e.nama as nama_cabang, d.areaId as areaid, f.Nama as nama_area, 
        a.iddokter, g.namalengkap as nama_dokter, g.spesialis, h.nama as nama_spesialis  
        FROM ms2.tempatpraktek as a 
        JOIN ms2.outlet_master as b on a.outletId=b.id 
        LEFT JOIN ms2.outlet_type as c on b.type=c.id 
        JOIN ms2.outlet_customer as d on a.outletId=d.outletId 
        LEFT JOIN mkt.icabang as e on d.iCabangId=e.iCabangId 
        LEFT JOIN mkt.iarea as f on d.iCabangId=f.iCabangId and d.areaId=f.areaId 
        JOIN ms2.masterdokter as g on a.iddokter=g.id 
        LEFT JOIN ms2.lookup as h on g.spesialis=h.id 
        WHERE d.icabangid='$pidcab' ";
    if (!empty($pidarea)) {
        $query .=" AND d.areaid='$pidarea' ";
    }
    $query .=" AND a.iddokter='$piddokt' ";
    $query .=" ORDER BY b.nama, a.id";
    $tampil= mysqli_query($cnms, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pnidpraktek=$row['idpraktek'];
        $pnareaid=$row['areaid'];
        $pnareanm=$row['nama_area'];
        $pnotlid=$row['idoutlet'];
        $pnotlnm=$row['nama_outlet'];
        $pntypeotl=$row['nama_type'];
        $pndispensing=$row['dispensing'];
        $pnalamatotl=$row['alamat'];
        $pniddokt=$row['iddokter'];
        $pnnmdokt=$row['nama_dokter'];
        $pnnamatype=$row['nama_type'];
        
        echo "<option value='$pnotlid' >$pnotlnm - $pnotlid ($pnnamatype)</option>";
    }
    
    mysqli_close($cnms);
    
}

?>

