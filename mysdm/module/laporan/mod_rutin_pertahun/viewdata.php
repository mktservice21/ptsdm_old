<?php
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
    
if ($pmodule=="viewdatajabatan") {
    include "../../../config/koneksimysqli.php";
    $ppilposisi=$_POST['upilposisi'];
    
    if ($ppilposisi=="HO" OR $ppilposisi=="CAB") {
        $sql=mysqli_query($cnmy, "select a.jabatanId, a.nama from hrd.jabatan a "
                . " JOIN dbmaster.jabatan_level b on a.jabatanId=b.jabatanId "
                . " WHERE IFNULL(b.M_GROUP,'')='$ppilposisi' "
                . " order by a.jabatanId");
    }else{
        $sql=mysqli_query($cnmy, "select jabatanId, nama from hrd.jabatan order by jabatanId");
    }
    
    if (Empty($ppilposisi)) {
        echo "<input type=checkbox value='emptypilih' name='chkbox_posisi[]' id='chkbox_posisi[]' checked> 00 - empty<br/>";
    }
    
    while ($Xt=mysqli_fetch_array($sql)){
        $npkdjab=$Xt['jabatanId'];
        $npnmjab=$Xt['nama'];
        echo "<input type=checkbox value='$npkdjab' name='chkbox_posisi[]' id='chkbox_posisi[]' checked> $npkdjab - $npnmjab<br/>";
    }
    mysqli_close($cnmy);
    
}


?>
