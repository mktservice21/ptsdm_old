<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdatacabang") {
    $piddivisi=$_POST['udivid'];
    $pidcab=$_POST['ucabangid'];
    $pnjabatanid=$_POST['ujbtid'];
    
    
    
    $ppilihotc=false;
    if ($pnjabatanid=="06" OR $pnjabatanid=="07" OR $pnjabatanid=="09" OR $pnjabatanid=="11" OR $pnjabatanid=="12" OR $pnjabatanid=="13" OR $pnjabatanid=="14" OR $pnjabatanid=="16" OR $pnjabatanid=="17" OR $pnjabatanid=="37") {    
    }else{
        if ($piddivisi=="OTC") $ppilihotc=true;
    }
    
    include "../../config/koneksimysqli.php";
    
    echo "<option value='' selected>--Pilihan--</option>";
    
    if ($ppilihotc==true) {
        $query = "select icabangid_o icabangid, nama from MKT.icabang_o WHERE aktif='Y' ";
    }else{
        $query = "select icabangid, nama from MKT.icabang WHERE aktif='Y' ";
    }
    $query .=" ORDER BY nama";
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $npidcab=$row['icabangid'];
        $npnmcab=$row['nama'];
        if ($npidcab==$pidcab)
            echo "<option value='$npidcab' selected>$npnmcab</option>";
        else
            echo "<option value='$npidcab'>$npnmcab</option>";
    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdataarea") {
    $piddivisi=$_POST['udivid'];
    $pidcab=$_POST['ucabangid'];
    $pidareas=$_POST['uareaids'];
    $pnjabatanid=$_POST['ujbtid'];
    
    
    $ppilihotc=false;
    if ($pnjabatanid=="06" OR $pnjabatanid=="07" OR $pnjabatanid=="09" OR $pnjabatanid=="11" OR $pnjabatanid=="12" OR $pnjabatanid=="13" OR $pnjabatanid=="14" OR $pnjabatanid=="16" OR $pnjabatanid=="17" OR $pnjabatanid=="37") {    
    }else{
        if ($piddivisi=="OTC") $ppilihotc=true;
    }
    
    
    include "../../config/koneksimysqli.php";
    
    
    echo "<option value='' selected>--Pilihan--</option>";
    
    if ($ppilihotc==true) {
        $query = "select icabangid_o icabangid, areaid_o areaid, nama from mkt.iarea_o WHERE aktif='Y' and icabangid_o='$pidcab' ";
    }else{
        $query = "select icabangid, areaid, nama from mkt.iarea WHERE aktif='Y' and icabangid='$pidcab' ";
    }
    $query .=" ORDER BY nama";
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $npidcab=$row['icabangid'];
        $npidarea=$row['areaid'];
        $npnmarea=$row['nama'];
        if ($npidarea==$pidareas AND $npidcab==$pidcab)
            echo "<option value='$pidareas' selected>$npnmarea</option>";
        else
            echo "<option value='$npidarea'>$npnmarea</option>";
    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdatadmnya") {
    
    $pidnta=$_POST['uspv'];
    include "../../config/koneksimysqli.php";
    
    $query = "select distinct dm from dbmaster.t_karyawan_posisi where karyawanid='$pidnta'";
    $tampil=mysqli_query($cnmy, $query);
    $rs= mysqli_fetch_array($tampil);
    $atasaniddm=$rs['dm'];
    
    
    $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasaniddm') ";                                            
    $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
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
    $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
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
    $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
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
}elseif ($pmodule=="viewdatadmnyaotc") {
    $pidnta=$_POST['uspv'];
    include "../../config/koneksimysqli.php";
    
    $query = "select distinct dm from dbmaster.t_karyawan_posisi where karyawanid='$pidnta'";
    $tampil=mysqli_query($cnmy, $query);
    $rs= mysqli_fetch_array($tampil);
    $atasaniddm=$rs['dm'];
    
    
    $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasaniddm') ";                                            
    $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
    $query .=" AND divisiid ='OTC' ";
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
}elseif ($pmodule=="viewdatasmnyaotc") {
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
    $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
    $query .=" AND divisiid ='OTC' ";
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
}elseif ($pmodule=="viewdatagsmnyaotc") {
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
    $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
    $query .=" AND divisiid ='OTC' ";
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
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
    
}

?>