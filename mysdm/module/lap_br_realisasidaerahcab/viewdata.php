<?php


if ($_GET['module']=="viewprodukfilter"){
    include "../../config/koneksimysqli_it.php";
    $filter= " ifnull(kategori,'')='$_POST[ukategori]' ";
    if (empty($_POST['ukategori'])) $filter= " ifnull(kategori,'')='' ";
    $sql=mysqli_query($cnit, "SELECT distinct iProdId, nama from dbmaster.v_produk where $filter order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[iProdId]' name=chkbox_produk[] checked> $Xt[nama]<br/>";
    }
}elseif ($_GET['module']=="viewregioncab"){
    include "../../config/koneksimysqli_it.php";
    if ($_POST['udata1']=="true" and $_POST['udata2']=="true") {
        $filter =" ('B', 'T') ";
        echo '<input type="checkbox" name="chkbox_cabang[]" id="chkbox_cabang[]" value="tanpa_cabang" checked>_blank <br/>';
    }elseif ($_POST['udata1']=="true" and $_POST['udata2']=="false") {
        $filter =" ('B') ";
    }elseif ($_POST['udata1']=="false" and $_POST['udata2']=="true") {
        $filter =" ('T') ";
    }else{
        $filter =" ('') ";
    }
    $filter= " where region in ".$filter;
    
    $sql=mysqli_query($cnit, "SELECT distinct iCabangId, nama from dbmaster.icabang $filter AND nama NOT like '%OTC -%' AND nama NOT like '%PEA -%' order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[iCabangId]' name=chkbox_cabang[] checked> $Xt[nama]<br/>";
    }
}elseif ($_GET['module']=="viewkodedivisi"){
    include "../../config/koneksimysqli_it.php";
    $myfil=$_POST['udata1'];
    if (!empty($myfil)){
        $myfil="(".substr($myfil, 0, -1).")";
    }
    $filtipe = "";
    if (isset($_POST['upilihtipe'])) {
        if ($_POST['upilihtipe']=="Y") {
            $filtipe = " and kodeid in (select kodeid from dbmaster.br_kode where (br <> '') and (br<>'N')) ";
        }elseif ($_POST['upilihtipe']=="N") {
            $filtipe = " and kodeid not in (select kodeid from dbmaster.br_kode where (br <> '') and (br<>'N')) ";
        }
    }
    $sql=mysqli_query($cnit, "select kodeid,nama,divprodid from dbmaster.br_kode where divprodid in $myfil $filtipe order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[kodeid]' name=chkbox_kode[] checked> $Xt[kodeid] - $Xt[nama]<br/>";
    }
}

?>

