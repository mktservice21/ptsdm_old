<?php
include "../config/koneksimysqli.php";
if ($_GET['module']=="viewtipecoa"){
    $tampil = mysqli_query($cnmy, "SELECT TIPE FROM v_coa where COA_KODE='$_POST[coakode]'");
    $r=mysqli_fetch_array($tampil);
    echo "$r[TIPE]";
}elseif ($_GET['module']=="viewpro"){

}

?>
