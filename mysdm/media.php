<?php
//ini_set('display_errors', '0');
session_start();
if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
    echo "<center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href=../index.php><b>LOGIN</b></a></center>";
}else{
    
    
    $pmedia_kriteria=""; $pmedia_session=""; $pmedia_idusr=""; $pmedia_module=""; $pmedia_idmenu="";
    if (isset($_GET['kriteria'])) $pmedia_kriteria=$_GET['kriteria'];
    if (isset($_GET['module'])) $pmedia_module=$_GET['module'];
    if (isset($_GET['idmenu'])) $pmedia_idmenu=$_GET['idmenu'];
    if (isset($_SESSION['IDSESI'])) $pmedia_session=$_SESSION['IDSESI'];
    if (isset($_SESSION['USERID'])) $pmedia_idusr=$_SESSION['USERID'];
    
    if ($pmedia_kriteria=="N") {
        $pcurul=$_SERVER["HTTP_HOST"];
        $pcurul2="http://localhost/ptsdm";
        $pcurul_pl="localhost";
        
        $pcurul2="http://vps.marvis.id";
        $pcurul_pl="vps.marvis.id";
        
        if (!empty($pmedia_session) AND $pmedia_kriteria=="N" AND $pcurul<>$pcurul_pl) {
            header("Location: http://vps.marvis.id/penempatanmarketing?&isesi=$pmedia_session&iuser=$pmedia_idusr&module=$pmedia_module&idmenu=$pmedia_idmenu", TRUE, 301);
            exit;
        }
    }
    
    
    include "config/koneksimysqli.php";
    include "config/fungsi_cekuser.php";
    include "config/fungsi_sql.php";
    include "config/fungsi_combo.php";
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