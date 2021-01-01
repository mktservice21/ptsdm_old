<?php

    session_start();
    if (!isset($_SESSION['IDCARD'])) {
        echo "Anda harus login ulang...";
        exit;
    }
    
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
if ($module=='bgtlimitkaskecilcab' AND ($act=='simpanum' OR $act=='hapusum') )
{
    $puserid=$_SESSION['IDCARD'];
    $pjumlah = $_POST['txtrp_um'];
    $pid_input = $_POST['txt_idbr'];
    $jumlah_tag=(double)count($pjumlah)-1;
    for ($x=0; $x<=$jumlah_tag; $x++){
        $nobrinput="";
        if (isset($pid_input[$x]) AND isset($pjumlah[$x])) {
            $pn_id=$pid_input[$x];
            $prp_um=$pjumlah[$x];
            if (empty($prp_um)) $prp_um="0";
            $prp_um=str_replace(",","", $prp_um);
            
            
            $query = "delete from dbmaster.t_uangmuka_kascabang WHERE icabangid='$pn_id'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            if ($act=='simpanum') {
                $query = "INSERT INTO dbmaster.t_uangmuka_kascabang (icabangid, jumlah, userid)VALUES"
                        . "('$pn_id', '$prp_um', '$puserid')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
            
            
        }
    }
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
}

?>