<?php
session_status();

if ($_GET['module']=="viewdatacabang") {
    include "../../config/koneksimysqli_ms.php";
    $pidregion=$_POST['uregion'];
    $ffilterregion=" AND region='$pidregion' ";
    if (empty($pidregion)) $ffilterregion="";
    
    $query = "select distinct iCabangId, nama from ms.icabang where ifnull(aktif,'')<>'N' $ffilterregion order by nama";
    $tampil=mysqli_query($cnms, $query);
    echo "<option value=''>--Pilih--</option>";
    while ($r=  mysqli_fetch_array($tampil)) {
        $picabangid=$r['iCabangId'];
        $pnmcabang=$r['nama'];
        echo "<option value='$picabangid'>$pnmcabang</option>";
    }
    
}elseif ($_GET['module']=="viewdatacabangmarketing") {
    include "../../config/koneksimysqli_ms.php";
    $ptgl=$_POST['utgl'];
    $pperiode= date("Y-m", strtotime($ptgl));
    $pcabawal=$_POST['ucabawal'];
    $pidregion=$_POST['uregion'];
    $ffilterregion=" AND a.region='$pidregion' ";
    if (empty($pidregion)) $ffilterregion="";
    
    $query = "select DISTINCT a.region, a.icabangid iCabangId, b.nama  
        from ms.penempatan_marketing a JOIN ms.icabang b on a.icabangid=b.iCabangId where 
            date_Format(a.bulan,'%Y-%m')='$pperiode' $ffilterregion order by b.nama";
    $tampil=mysqli_query($cnms, $query);
    echo "<option value=''>--Pilih--</option>";
    while ($r=  mysqli_fetch_array($tampil)) {
        $picabangid=$r['iCabangId'];
        $pnmcabang=$r['nama'];
        if ($picabangid==$pcabawal)
            echo "<option value='$picabangid' selected>$pnmcabang</option>";
        else
            echo "<option value='$picabangid'>$pnmcabang</option>";
    }
}elseif ($_GET['module']=="viewdataarea") {
    include "../../config/koneksimysqli_ms.php";
    $pidcabang=$_POST['ucabang'];
    
    $ffiltercabang=" AND iCabangId='$pidcabang' ";
    
    $query = "select distinct areaId, Nama nama from ms.iarea where ifnull(aktif,'')<>'N' $ffiltercabang order by Nama";
    $tampil=mysqli_query($cnms, $query);
    echo "<option value=''>--Pilih--</option>";
    while ($r=  mysqli_fetch_array($tampil)) {
        $piareaid=$r['areaId'];
        $pnmarea=$r['nama'];
        echo "<option value='$piareaid'>$pnmarea</option>";
    }
}elseif ($_GET['module']=="viewdataareamarketing") {
    include "../../config/koneksimysqli_ms.php";
    $pidcabang=$_POST['ucabang'];
    $ptgl=$_POST['utgl'];
    $pperiode= date("Y-m", strtotime($ptgl));
    $pareaawal=$_POST['uareaawal'];
    $ffiltercabang=" AND a.iCabangId='$pidcabang' ";
    
    $query = "select DISTINCT a.region, a.icabangid, a.areaid areaId, b.nama   
        from ms.penempatan_marketing a JOIN ms.iarea b on a.icabangid=b.iCabangId AND a.areaid=b.areaid
        WHERE date_Format(a.bulan,'%Y-%m')='$pperiode' $ffiltercabang order by b.nama";
    $tampil=mysqli_query($cnms, $query);
    echo "<option value=''>--Pilih--</option>";
    while ($r=  mysqli_fetch_array($tampil)) {
        $piareaid=$r['areaId'];
        $pnmarea=$r['nama'];
        if ($piareaid==$pareaawal)
            echo "<option value='$piareaid' selected>$pnmarea</option>";
        else
            echo "<option value='$piareaid'>$pnmarea</option>";
    }
}elseif ($_GET['module']=="viewdatakaryawan") {
    include "../../config/koneksimysqli_ms.php";
    $pkry=$_POST['ukry'];
    $psts=$_POST['usts'];
    $filtersts=" AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='')  AND jabatanId='15' ";
    if ($psts=="T") $filtersts=" AND (IFNULL(tglkeluar,'0000-00-00')<>'0000-00-00'  AND jabatanId='15') "
            . " OR ((IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='')  AND jabatanId<>'15')";
    
    if ($pkry=="AM") {
        $filtersts=" AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='')  AND jabatanId IN ('10', '18') ";
        if ($psts=="T") $filtersts=" AND (IFNULL(tglkeluar,'0000-00-00')<>'0000-00-00'  AND jabatanId IN ('10', '18')) "
                . " OR ((IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='')  AND jabatanId NOT IN ('10', '18'))";
    }elseif ($pkry=="DM") {
        $filtersts=" AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='')  AND jabatanId='08' ";
        if ($psts=="T") $filtersts=" AND (IFNULL(tglkeluar,'0000-00-00')<>'0000-00-00'  AND jabatanId='08') "
                . " OR ((IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='')  AND jabatanId<>'08')";
    }
    
    $query="select karyawanId, nama from ms.karyawan WHERE 1=1 $filtersts order by nama, karyawanId";

    $tampil=mysqli_query($cnms, $query);
    echo "<option value=''>--Pilih--</option>";
    while ($r=  mysqli_fetch_array($tampil)) {
        $pkaryawaid=$r['karyawanId'];
        $pnmkaryawan=$r['nama'];
        echo "<option value='$pkaryawaid'>$pkaryawaid - $pnmkaryawan</option>";
    }
}elseif ($_GET['module']=="axxxxx") {
    
}
?>

