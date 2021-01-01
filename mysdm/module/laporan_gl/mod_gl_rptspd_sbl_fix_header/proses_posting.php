<?php
    session_start();
    include("../../../config/koneksimysqli.php");
    $puserid=$_SESSION['IDCARD'];
    $pnamalengkap=$_SESSION['NAMALENGKAP'];
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    
    $pidinput=$_POST['uidspd'];
    $pnodivisi=$_POST['unodivisi'];
    
    $query ="DELETE FROM dbmaster.t_suratdana_br_close WHERE idinput='$pidinput'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error DELETE POST : $pnodivisi"; exit; }
    
    if ($module=="glreportspddetail" AND $act=="posting") {
        
        $query ="INSERT INTO dbmaster.t_suratdana_br_close (idinput,userid,nodivisi,namalengkap)VALUES('$pidinput', '$puserid', '$pnodivisi', '$pnamalengkap')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error INSERT POST : $pnodivisi"; exit; }
        
    }
    
    mysqli_close($cnmy);
    
?>

