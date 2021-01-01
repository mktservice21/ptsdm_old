<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdatadmnya") {
    
    $pidnta=$_POST['uspv'];
    include "../../config/koneksimysqli.php";
    
    $query = "select distinct dm from dbmaster.t_karyawan_posisi where karyawanid='$pidnta'";
    $tampil=mysqli_query($cnmy, $query);
    $rs= mysqli_fetch_array($tampil);
    $atasaniddm=$rs['dm'];
    
    
    $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasaniddm') ";                                            
    if ($_SESSION['DIVISI']=="OTC"){
        $query .=" AND divisiid ='OTC' ";
        $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
    }else{
        $query .=" AND jabatanid in ('08')";
    }
    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
    $query .=" ORDER BY nama";

    $sql=mysqli_query($cnmy, $query);
    echo "<option value=''>-- Pilihan --</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        $xid=$Xt['karyawanid'];
        $xnama=$Xt['nama'];

        if ($xid==$atasaniddm)
            echo "<option value='$xid' selected>$xnama</option>";
        else
            echo "<option value='$xid'>$xnama</option>";
    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdatasmnya") {
    
    $piddm=$_POST['udm'];
    $pidspv=$_POST['uspv'];
    
    $pidnta=$piddm;
    if (empty($piddm)) $pidnta=$pidspv;
    
    include "../../config/koneksimysqli.php";
    
    $query = "select distinct sm from dbmaster.t_karyawan_posisi where karyawanid='$pidnta'";
    $tampil=mysqli_query($cnmy, $query);
    $rs= mysqli_fetch_array($tampil);
    $atasanidsm=$rs['sm'];
    
    
    $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasanidsm')";
    if ($_SESSION['DIVISI']=="OTC"){
        $query .=" AND divisiid ='OTC' ";
        $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
        //$query .=" And jabatanId in (select distinct jabatanId from hrd.jabatan WHERE rank='03')";
    }else{
        $query .=" AND jabatanid in ('20')";
    }
    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
    $query .=" ORDER BY nama";
    
    $sql=mysqli_query($cnmy, $query);
    echo "<option value=''>-- Pilihan --</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        $xid=$Xt['karyawanid'];
        $xnama=$Xt['nama'];

        if ($xid==$atasanidsm)
            echo "<option value='$xid' selected>$xnama</option>";
        else
            echo "<option value='$xid'>$xnama</option>";
    }
    
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatagsmnya") {
    $pidsm=$_POST['usm'];
    $piddm=$_POST['udm'];
    $pidspv=$_POST['uspv'];
    
    $pidnta=$pidsm;
    if (empty($pidsm)) $pidnta=$piddm;
    if (empty($pidsm) AND empty($piddm)) $pidnta=$pidspv;
    
    
    
    include "../../config/koneksimysqli.php";
    
    $query = "select distinct gsm from dbmaster.t_karyawan_posisi where karyawanid='$pidnta'";
    $tampil=mysqli_query($cnmy, $query);
    $rs= mysqli_fetch_array($tampil);
    $atasanidgsm=$rs['gsm'];
    
    $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasanidgsm')";
    if ($_SESSION['DIVISI']=="OTC"){
        $query .=" AND divisiid ='OTC' ";
        $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
        $query .=" And jabatanId in (select distinct jabatanId from hrd.jabatan WHERE rank='02')";
    }else{
        $query .=" AND jabatanid in ('05')";
    }
    $query .=" ORDER BY nama";
    
    $sql=mysqli_query($cnmy, $query);
    echo "<option value=''>-- Pilihan --</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        $xid=$Xt['karyawanid'];
        $xnama=$Xt['nama'];

        if ($xid==$atasanidgsm)
            echo "<option value='$xid' selected>$xnama</option>";
        else
            echo "<option value='$xid'>$xnama</option>";
    }
    
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdata2divisi") {
    $pdivisi1=$_POST['udivisi1'];
    include "../../config/koneksimysqli.php";
    
    $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' and DivProdId NOT IN ('HO', 'OTC', 'CAN', 'OTHER', '$pdivisi1') order by nama");
    $ketemu= mysqli_num_rows($sql);
    echo "<option value=''>-- Pilihan --</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<option value='$Xt[divisiid]'>$Xt[divisiid]</option>";
    }
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdata3divisi") {
    $pdivisi1=$_POST['udivisi1'];
    $pdivisi2=$_POST['udivisi2'];
    include "../../config/koneksimysqli.php";
    
    $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' and DivProdId NOT IN ('HO', 'OTC', 'CAN', 'OTHER', '$pdivisi1', '$pdivisi2') order by nama");
    $ketemu= mysqli_num_rows($sql);
    echo "<option value=''>-- Pilihan --</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<option value='$Xt[divisiid]'>$Xt[divisiid]</option>";
    }
    
    mysqli_close($cnmy);
}elseif ($pmodule=="xxx") {
    
}

?>