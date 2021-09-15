<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdatamridkary") {
    include "../../config/koneksimysqli_ms.php";

    $pidkaryawan=$_SESSION['IDCARD'];
    $pidjabatan=$_SESSION['JABATANID'];
    $pidgroup=$_SESSION['GROUP'];


    $pbln=$_POST['ubulan'];
    $pbulan = date('Y-m', strtotime($pbln));

    if ($pidjabatan=="15") {
        $query_data = "select b.karyawanid as karyawanid, b.nama as nama from hrd.karyawan as b WHERE b.karyawanid='$pidkaryawan' ";
    }else{
        if ($pidgroup=="1" OR $pidgroup=="24") {
            $query_data = "SELECT DISTINCT a.mr AS karyawanid, b.nama AS nama FROM ms.`penempatan_marketing` AS a 
                    JOIN ms.`karyawan` AS b ON a.mr=b.`karyawanId` 
                    WHERE LEFT(a.bulan,7)='$pbulan' ";
        }else{
            $query_data = "SELECT DISTINCT a.mr AS karyawanid, b.nama AS nama FROM ms.`penempatan_marketing` AS a 
                JOIN ms.`karyawan` AS b ON a.mr=b.`karyawanId` 
                WHERE LEFT(a.bulan,7)='$pbulan' ";
            if ($pidjabatan=="18" OR $pidjabatan=="10") {
                $query_data .=" AND a.am='$pidkaryawan'";
            }elseif ($pidjabatan=="08") {
                $query_data .=" AND a.dm='$pidkaryawan'";
            }elseif ($pidjabatan=="20") {
                $query_data .=" AND a.sm='$pidkaryawan'";
            }else{
                if ($pidkaryawan=="0000000158" OR $pidkaryawan=="0000000159" OR $pidjabatan=="05") {
                    $query_data .=" AND a.gsm='$pidkaryawan'";
                }else{
                    $query_data .=" AND a.mr='$pidkaryawan'";
                }
            }


        }
        
    }
    if (!empty($query_data)) {
        
        $query =$query_data." ORDER BY b.nama";
        $tampil = mysqli_query($cnms, $query);
        $ketemu=mysqli_num_rows($tampil);
        
        if ((INT)$ketemu<=0) echo "<option value=''>_blank</option>";
        
        while ($rx= mysqli_fetch_array($tampil)) {
            $nidkry=$rx['karyawanid'];
            $nnmkry=$rx['nama'];
            if ($nidkry==$pidkaryawan)
                echo "<option value='$nidkry' selected>$nnmkry</option>";
            else
                echo "<option value='$nidkry'>$nnmkry</option>";
        }
        
    }

    mysqli_close($cnms);
}elseif ($pmodule=="viewdataamidkary") {
    include "../../config/koneksimysqli_ms.php";

    $pidkaryawan=$_SESSION['IDCARD'];
    $pidjabatan=$_SESSION['JABATANID'];
    $pidgroup=$_SESSION['GROUP'];


    $pbln=$_POST['ubulan'];
    $pbulan = date('Y-m', strtotime($pbln));

    if ($pidjabatan=="18" OR $pidjabatan=="10") {
        $query_data = "select b.karyawanid as karyawanid, b.nama as nama from hrd.karyawan as b WHERE b.karyawanid='$pidkaryawan' ";
    }else{
        if ($pidgroup=="1" OR $pidgroup=="24") {
            $query_data = "SELECT DISTINCT a.am AS karyawanid, b.nama AS nama FROM ms.`penempatan_marketing` AS a 
                    JOIN ms.`karyawan` AS b ON a.am=b.`karyawanId` 
                    WHERE LEFT(a.bulan,7)='$pbulan' ";
        }else{
            $query_data = "SELECT DISTINCT a.am AS karyawanid, b.nama AS nama FROM ms.`penempatan_marketing` AS a 
                JOIN ms.`karyawan` AS b ON a.am=b.`karyawanId` 
                WHERE LEFT(a.bulan,7)='$pbulan' ";
            if ($pidjabatan=="08") {
                $query_data .=" AND a.dm='$pidkaryawan'";
            }elseif ($pidjabatan=="20") {
                $query_data .=" AND a.sm='$pidkaryawan'";
            }else{
                if ($pidkaryawan=="0000000158" OR $pidkaryawan=="0000000159" OR $pidjabatan=="05") {
                    $query_data .=" AND a.gsm='$pidkaryawan'";
                }else{
                    $query_data .=" AND a.am='$pidkaryawan'";
                }
            }


        }
        
    }
    if (!empty($query_data)) {
        
        $query =$query_data." ORDER BY b.nama";
        $tampil = mysqli_query($cnms, $query);
        $ketemu=mysqli_num_rows($tampil);
        
        if ((INT)$ketemu<=0) echo "<option value=''>_blank</option>";
        
        while ($rx= mysqli_fetch_array($tampil)) {
            $nidkry=$rx['karyawanid'];
            $nnmkry=$rx['nama'];
            if ($nidkry==$pidkaryawan)
                echo "<option value='$nidkry' selected>$nnmkry</option>";
            else
                echo "<option value='$nidkry'>$nnmkry</option>";
        }
        
    }

    mysqli_close($cnms);
}elseif ($pmodule=="viewdatadmidkary") {
    include "../../config/koneksimysqli_ms.php";

    $pidkaryawan=$_SESSION['IDCARD'];
    $pidjabatan=$_SESSION['JABATANID'];
    $pidgroup=$_SESSION['GROUP'];


    $pbln=$_POST['ubulan'];
    $pbulan = date('Y-m', strtotime($pbln));

    if ($pidjabatan=="08") {
        $query_data = "select b.karyawanid as karyawanid, b.nama as nama from hrd.karyawan as b WHERE b.karyawanid='$pidkaryawan' ";
    }else{
        if ($pidgroup=="1" OR $pidgroup=="24") {
            $query_data = "SELECT DISTINCT a.dm AS karyawanid, b.nama AS nama FROM ms.`penempatan_marketing` AS a 
                    JOIN ms.`karyawan` AS b ON a.dm=b.`karyawanId` 
                    WHERE LEFT(a.bulan,7)='$pbulan' ";
        }else{
            $query_data = "SELECT DISTINCT a.dm AS karyawanid, b.nama AS nama FROM ms.`penempatan_marketing` AS a 
                JOIN ms.`karyawan` AS b ON a.dm=b.`karyawanId` 
                WHERE LEFT(a.bulan,7)='$pbulan' ";
            if ($pidjabatan=="08") {
                $query_data .=" AND a.dm='$pidkaryawan'";
            }elseif ($pidjabatan=="20") {
                $query_data .=" AND a.sm='$pidkaryawan'";
            }else{
                if ($pidkaryawan=="0000000158" OR $pidkaryawan=="0000000159" OR $pidjabatan=="05") {
                    $query_data .=" AND a.gsm='$pidkaryawan'";
                }else{
                    $query_data .=" AND a.dm='$pidkaryawan'";
                }
            }


        }
        
    }
    if (!empty($query_data)) {
        
        $query =$query_data." ORDER BY b.nama";
        $tampil = mysqli_query($cnms, $query);
        $ketemu=mysqli_num_rows($tampil);
        
        if ((INT)$ketemu<=0) echo "<option value=''>_blank</option>";
        
        while ($rx= mysqli_fetch_array($tampil)) {
            $nidkry=$rx['karyawanid'];
            $nnmkry=$rx['nama'];
            if ($nidkry==$pidkaryawan)
                echo "<option value='$nidkry' selected>$nnmkry</option>";
            else
                echo "<option value='$nidkry'>$nnmkry</option>";
        }
        
    }

    mysqli_close($cnms);
}

?>