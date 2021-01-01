<?php

if ($_GET['module']=="viewdataarea"){
    include "../../config/koneksimysqli.php";
    $pcabangpilih=trim($_POST['ucab']);
    $query = "select areaId, Nama from MKT.iarea WHERE iCabangId='$pcabangpilih'";
    $query .=" order by nama";
    $tampil = mysqli_query($cnmy, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        $piarea=$z['areaId'];
        $pnmarea=$z['Nama'];
        echo "<option value='$piarea'>$piarea - $pnmarea</option>";
    }
}

?>
