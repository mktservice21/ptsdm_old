<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
//$ptgl = str_replace('/', '-', $_POST['utgl']);
//$ptglpengajuan= date("Y-m-d", strtotime($ptgl));
//$pjumlah=str_replace(",","", $pjumlah);

if ($pmodule=="caridatacabang") {
    $pidreg=$_POST['uidreg'];
    
    include "../../config/koneksimysqli_ms.php";
    
    $pnmpil="";
    if ($pidreg=="B") $pnmpil="Barat ";
    if ($pidreg=="T") $pnmpil="Timur ";
    
    $filregion = "";
    if (!empty($pidreg)) $filregion = " AND region='$pidreg' ";
    
    echo "<option value=''>~ All $pnmpil~</option>";
    $query = "select icabangid, nama from sls.icabang WHERE IFNULL(aktif,'')='Y' AND "
            . " LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -', 'OTH -') $filregion order by nama";
    $tampil=mysqli_query($cnms, $query);
    while ($nr1= mysqli_fetch_array($tampil)) {
        $nidcab=$nr1['icabangid'];
        $nnmcab=$nr1['nama'];

        echo "<option value='$nidcab'>$nnmcab</option>";
    }
    echo "<option value='ZAAZZA'>&nbsp;</option>";
    echo "<option value='NON'>--Non Aktif--</option>";
    echo "<option value='ZAAZZA'>&nbsp;</option>";
    $query = "select icabangid, nama from sls.icabang WHERE IFNULL(aktif,'')<>'Y' AND "
            . " LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -', 'OTH -') $filregion order by nama";
    $tampil=mysqli_query($cnms, $query);
    while ($nr1= mysqli_fetch_array($tampil)) {
        $nidcab=$nr1['icabangid'];
        $nnmcab=$nr1['nama'];

        echo "<option value='$nidcab'>$nnmcab</option>";
    }
    mysqli_close($cnms);
    
}elseif ($pmodule=="caridataarea") {
    $pidcab=$_POST['uidcab'];
    
    include "../../config/koneksimysqli_ms.php";
    
    
    $filcabang = " AND icabangid='$pidcab' ";
    if (!empty($pidreg)) $filregion = " AND icabangid='$pidcab' ";
    
    echo "<option value=''>~ Pilih ~</option>";
    $query = "select icabangid, areaid, nama from sls.iarea WHERE IFNULL(aktif,'')='Y' $filcabang order by nama";
    $tampil=mysqli_query($cnms, $query);
    while ($nr1= mysqli_fetch_array($tampil)) {
        $nidcab=$nr1['icabangid'];
        $nidarea=$nr1['areaid'];
        $nnmarea=$nr1['nama'];

        echo "<option value='$nidarea'>$nnmarea</option>";
    }
    
    $query = "select icabangid, areaid, nama from sls.iarea WHERE IFNULL(aktif,'')<>'Y' $filcabang order by nama";
    $tampil=mysqli_query($cnms, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        echo "<option value='ZAAZZA'>&nbsp;</option>";
        echo "<option value='NON'>--Non Aktif--</option>";
        echo "<option value='ZAAZZA'>&nbsp;</option>";
        while ($nr1= mysqli_fetch_array($tampil)) {
            $nidcab=$nr1['icabangid'];
            $nidarea=$nr1['areaid'];
            $nnmarea=$nr1['nama'];

            echo "<option value='$nidarea'>$nnmarea</option>";
        }
    }
    mysqli_close($cnms);
}elseif ($pmodule=="caridataproduk") {
    $piddivisi=$_POST['uiddivisi'];
    
    include "../../config/koneksimysqli_ms.php";
    echo "<option value=''>~ All ~</option>";
    $query = "select iprodid, nama from sls.iproduk WHERE IFNULL(aktif,'')='Y' AND divprodid='$piddivisi' order by nama";
    $tampil=mysqli_query($cnms, $query);
    while ($nr1= mysqli_fetch_array($tampil)) {
        $nidprod=$nr1['iprodid'];
        $nnmprod=$nr1['nama'];

        echo "<option value='$nidprod'>$nnmprod</option>";
    }
    
    if (!empty($piddivisi)) {
        $query = "select iprodid, nama from sls.iproduk WHERE IFNULL(aktif,'')<>'Y' AND divprodid='$piddivisi' order by nama";
        $tampil=mysqli_query($cnms, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            echo "<option value='ZAAZZA'>&nbsp;</option>";
            echo "<option value='NON'>--Non Aktif--</option>";
            echo "<option value='ZAAZZA'>&nbsp;</option>";
            while ($nr1= mysqli_fetch_array($tampil)) {
                $nidprod=$nr1['iprodid'];
                $nnmprod=$nr1['nama'];

                echo "<option value='$nidprod'>$nnmprod</option>";
            }
        }
    }
    
    
    mysqli_close($cnms);
    
}