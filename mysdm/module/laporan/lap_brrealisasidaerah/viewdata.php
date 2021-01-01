<?php
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewregioncab"){
    include "../../../config/koneksimysqli.php";
    if ($_POST['udata1']=="true" and $_POST['udata2']=="true") {
        $filter =" ('B', 'T') ";
        echo '<input type="checkbox" name="chkbox_cabangdaerah[]" id="chkbox_cabangdaerah[]" value="tanpa_cabang" checked>_blank <br/>';
    }elseif ($_POST['udata1']=="true" and $_POST['udata2']=="false") {
        $filter =" ('B') ";
    }elseif ($_POST['udata1']=="false" and $_POST['udata2']=="true") {
        $filter =" ('T') ";
    }else{
        $filter =" ('') ";
    }
    $filter= " where region in ".$filter;
    
    $sql=mysqli_query($cnmy, "SELECT distinct idcabang iCabangId, nama from MKT.cbgytd $filter order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[iCabangId]' name=chkbox_cabangdaerah[] checked> $Xt[nama]<br/>";
    }
}

?>
