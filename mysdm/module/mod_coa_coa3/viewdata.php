<?php

if ($_GET['module']=="viewdatalevel2"){
    include "../../config/koneksimysqli.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnmy, "SELECT COA2, NAMA2 FROM dbmaster.coa_level2 where COA1='$_POST[ulevel1]' order by COA2");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA2]'>$a[COA2] - $a[NAMA2]</option>";
    }
}


?>