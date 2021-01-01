<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdataaptdr") {
    $pidkar=$_POST['uidkry'];
    $piddokt=$_POST['uiddr'];
    $piddokt2=$_POST['uidapt2'];
    
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $query = "select aptid as aptid, nama as nama, apttype as apttype from hrd.mr_apt where srid='$pidkar' and IFNULL(aktif,'')<>'N' order by nama";
    $result = mysqli_query($cnit, $query);
    $record = mysqli_num_rows($result);
    
    if ((DOUBLE)$record<=0) echo "<option value='' selected>--Pilih--</option>";
    
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result);
        
        $aptid  = $row['aptid'];
        $nama = $row['nama'];
        if ($nama<>"") {
            if ($aptid==$piddokt2)
                echo "<option value=\"$aptid\" selected>$nama - $aptid</option>";
            else
                echo "<option value=\"$aptid\">$nama - $aptid</option>";
        }
    }
    
    mysqli_close($cnit);
}elseif ($pmodule=="viewdatacndr") {
    $pidkar=$_POST['uidkry'];
    $piddokt=$_POST['uiddr'];
    $pbulan=$_POST['ubln'];
    $pbulan = date('Y-m-d', strtotime($pbulan));
    
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $cn_cn="";
    $query_sa = "select cn as cn from hrd.cn where karyawanid='$pidkar' and dokterid='$piddokt' and tgl<='$pbulan' order by tgl desc"; //echo"$query_sa";
    $result_sa = mysqli_query($cnit, $query_sa);
    $num_results_sa = mysqli_num_rows($result_sa);
    if ($num_results_sa) {
        $row_sa = mysqli_fetch_array($result_sa);
        $cn_cn = $row_sa['cn']; 
        if ((DOUBLE)$cn_cn==0) $cn_cn="";
    }
        
    
    $cn_dk="";
    $query_dk = "select cn as cn from hrd.mr_dokt where karyawanid='$pidkar' and dokterid='$piddokt'"; 
    $result_dk = mysqli_query($cnit, $query_dk);
    $num_results_dk = mysqli_num_rows($result_dk);
    if ($num_results_dk) {
        $row_dk = mysqli_fetch_array($result_dk);
        $cn_dk = $row_dk['cn'];
        if ((DOUBLE)$cn_dk==0) $cn_dk="";
    }	
	
	
    if ($cn_cn == '') {
        $cn = $cn_dk;
    } else {
        $cn = $cn_cn;
    }
    if (empty($cn)) $cn=0;
    
    mysqli_close($cnit);
    
    echo $cn;
    
}elseif ($pmodule=="viewdatadrpilih") {
    $pidkar=$_POST['uidkry'];
    
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $piddoktpilih="";
    if (!empty($_SESSION['KSDTKSDOK'])) $piddoktpilih = $_SESSION['KSDTKSDOK'];
    
    $query ="select distinct a.dokterid as dokterid, a.nama as nama, a.alamat1 as alamat1, a.alamat2 as alamat2 "
            . " from hrd.dokter as a JOIN hrd.mr_dokt as b on a.dokterid=b.dokterid WHERE b.karyawanid='$pidkar' ORDER BY a.nama";
    
    $result = mysqli_query($cnit, $query);
    $record = mysqli_num_rows($result);
    
    if ((DOUBLE)$record<=0) echo "<option value='' selected>--Pilih--</option>";
    
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result);
        
        $doktid  = $row['dokterid'];
        $nama = $row['nama'];
        if ($nama<>"") {
            if ($doktid==$piddoktpilih)
                echo "<option value=\"$doktid\" selected>$nama - $doktid</option>";
            else
                echo "<option value=\"$doktid\">$nama - $doktid</option>";
        }
    }
    
    mysqli_close($cnit);
    
}elseif ($pmodule=="cekdatasudahada") {
    $pidkar=$_POST['ukry'];
    $piddokt=$_POST['udoktid'];
    $pidapt=$_POST['uaptid'];
    $pbulan=$_POST['ubln'];
    $pbulan = date('Y-m', strtotime($pbulan));
    
    $pidgrpuser="";
    if (isset($_SESSION['GROUP'])) $pidgrpuser=$_SESSION['GROUP'];
    
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    
    $query  = "select distinct dokterid FROM hrd.ks1 WHERE bulan='$pbulan' AND srid='$pidkar' AND dokterid='$piddokt' AND aptid='$pidapt'";
    $result = mysqli_query($cnit, $query);
    $record = mysqli_num_rows($result);
    
    $bolehinput="boleh";
    if ((DOUBLE)$record>0) $bolehinput="Sudah ada data... Tidak bisa diubah / hapus";
    if ($pidgrpuser=="1" OR $pidgrpuser=="24") $bolehinput="boleh";
    
    
    mysqli_close($cnit);
    
    echo $bolehinput;
    
}

?>