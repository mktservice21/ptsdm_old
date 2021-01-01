<?php


if ($_GET['module']=="viewprodukfilter"){
    include "../../config/koneksimysqli.php";
    $filter= " ifnull(kategori,'')='$_POST[ukategori]' ";
    if (empty($_POST['ukategori'])) $filter= " ifnull(kategori,'')='' ";
    $sql=mysqli_query($cnmy, "SELECT distinct iProdId, nama from dbmaster.v_produk where $filter order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[iProdId]' name=chkbox_produk[] checked> $Xt[nama]<br/>";
    }
}elseif ($_GET['module']=="viewregioncab"){
    include "../../config/koneksimysqli.php";
    if ($_POST['udata1']=="true" and $_POST['udata2']=="true") {
        $filter =" ('B', 'T') ";
    }elseif ($_POST['udata1']=="true" and $_POST['udata2']=="false") {
        $filter =" ('B') ";
    }elseif ($_POST['udata1']=="false" and $_POST['udata2']=="true") {
        $filter =" ('T') ";
    }else{
        $filter =" ('') ";
    }
    $filter= " where region in ".$filter;
    
    $sql=mysqli_query($cnmy, "SELECT distinct icabangid, nama from ms.icabang $filter order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[icabangid]' name=chkbox_cabang[] checked> $Xt[nama]<br/>";
    }
}

?>

