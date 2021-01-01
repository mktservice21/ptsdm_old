<?php
session_start();
if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
    echo "<center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href=../index.php><b>LOGIN</b></a></center>";
}else{
    include "../mysdm/config/koneksimysqli.php";
    include "../mysdm/config/fungsi_cekuser.php";
    include "../mysdm/config/fungsi_sql.php";
    include "../mysdm/config/fungsi_combo.php";
    include "templates/template.php";
    /*
    $pilih_template=mysql_query("SELECT folder, TGLSEKARANG FROM templates WHERE aktif='Y'");
    $f=mysql_fetch_array($pilih_template);
    if ($f['TGLSEKARANG']=="Y")
        $_SESSION['TGLSEKARANG']="Y";
    else
        $_SESSION['TGLSEKARANG']="N";

    if(mobile_device_detect(true,true,true,true,false,false)){
        include "templates/m/thems.php";
    }else{
        //include "templates/m/thems.php";
        include "$f[folder]/template.php";
    }
     * 
     */
}
?>