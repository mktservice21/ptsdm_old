<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridataarea") {
    include "../../config/koneksimysqli.php";
    
    $pidcabang=$_POST['uidcab'];
    $pidarea="";
    
    echo "<option value='' selected>_blank</option>";
    
    $query = "select areaid_o, nama from MKT.iarea_o where icabangid_o='$pidcabang' AND IFNULL(aktif,'')='Y' ";
    $query .= " order by nama";
    $tampil=mysqli_query($cnmy, $query);
    while ($rt= mysqli_fetch_array($tampil)) {
        $pareaid=$rt['areaid_o'];
        $pnmarea=$rt['nama'];

        if ($pareaid==$pidarea)
            echo "<option value='$pareaid' selected>$pnmarea</option>";
        else
            echo "<option value='$pareaid'>$pnmarea</option>";
    }
    
    mysqli_close($cnmy);
}elseif ($_GET['module']=="viewdatakendaraan"){
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $karyawan=trim($_POST['umr']);
    $filnopol="";
    $adakendaraan = getfieldcnit("select nopol as lcfields from dbmaster.t_kendaraan_pemakai where karyawanid='$karyawan' and stsnonaktif <> 'Y'");
    //if (!empty($adakendaraan))
        $filnopol=" AND nopol in (select distinct nopol from dbmaster.t_kendaraan_pemakai where karyawanid='$karyawan' and stsnonaktif <> 'Y')";
    
    $query = "select * from dbmaster.t_kendaraan WHERE 1=1 $filnopol ";
    $query .=" order by merk, tipe, nopol";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value=''>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        if ($ketemu<=1)
            echo "<option value='$a[nopol]' selected>$a[nopol] - $a[merk] $a[tipe]</option>";
        else
            echo "<option value='$a[nopol]'>$a[nopol] - $a[merk] $a[tipe]</option>";
    }
    mysqli_close($cnmy);
    
}elseif ($_GET['module']=="getkodeperiode"){
    include "../../config/fungsi_sql.php";
    $mytglini="";
    $mytglini = getfield("select CURRENT_DATE as lcfields");
    if ($mytglini==0) $mytglini="";
    if (empty($mytglini)) $mytglini = date("Y-m-d");
    $tglini=date("Y-m", strtotime($mytglini));
    $hariiniserver=date("d", strtotime($mytglini));
    
    $periodeini=trim($_POST['ubulan']);
    $pbulan =  date("Y-m", strtotime($periodeini));
    if (empty($pbulan)) $Pbulan = date("Y-m");
    
    echo "<option value='' selected>-- Pilihan --</option>";
    if ($pbulan<$tglini){
        echo "<option value='2'>Periode 2</option>";
    }else{
        if ($pbulan==$tglini){
            if ((int)$hariiniserver > 20) {
                echo "<option value='2'>Periode 2</option>";
            }else{
                echo "<option value='1'>Periode 1</option>";
                echo "<option value='2'>Periode 2</option>";
            }
        }else{
            echo "<option value='1'>Periode 1</option>";
            echo "<option value='2'>Periode 2</option>";
        }
    }
}elseif ($_GET['module']=="getperiode"){
    $bulan = "01-".str_replace('/', '-', $_POST['ubulan']);
    if ($_POST['ukode']==1) {
        $periode1= date("Y-m-d", strtotime($bulan));
        $periode2= date("Y-m-15", strtotime($bulan));
    }elseif ($_POST['ukode']==2) {
        $periode1= date("Y-m-16", strtotime($bulan));
        $periode2= date("Y-m-t", strtotime($bulan));
    }
    $bln1=""; $bln2="";
    if (!empty($_POST['ukode'])) {
        $bln1= date("d/m/Y", strtotime($periode1));
        $bln2= date("d/m/Y", strtotime($periode2));
    }
    
    if ($_SESSION['DIVISI']=="OTC") {
        $bln1= date("01/m/Y", strtotime($periode1));
        $bln2= date("t/m/Y", strtotime($periode2));
    }
    
    echo "$bln1, $bln2";
}elseif ($pmodule=="xxxx") {
    
}

?>