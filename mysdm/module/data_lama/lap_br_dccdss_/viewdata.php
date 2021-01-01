<?php

if ($_GET['module']=="viewdatacombocoa"){
    include "../../../config/koneksimysqli_it.php";
    
    $divprodid = $_POST['udiv'];
    
    $query = "select kodeid, nama, divprodid from hrd.br_kode where br='Y' and divprodid='$divprodid' order by nama";
    $tampil=mysqli_query($cnit, $query);
    //echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[kodeid]'>$a[nama]</option>";
    }
}

?>

