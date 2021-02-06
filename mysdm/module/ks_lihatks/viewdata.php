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
        if (!empty($pfilterkaryawan)) {
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
}

?>

