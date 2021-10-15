<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

$berhasil="Tidak ada yang diproses....";
if ($pmodule=="simpandataproses") {
    $nbln1=$_POST['ubln1'];
    $nbln2=$_POST['ubln2'];
    $nbln3=$_POST['ubln3'];
    $nbln4=$_POST['ubln4'];
    
    
    $ptgl1 = date('Y-m-01', strtotime($nbln1));
    $ptgl2 = date('Y-m-01', strtotime($nbln2));
    $ptgl3 = date('Y-m-t', strtotime($nbln3));
    $ptgl4 = date('Y-m-01', strtotime($nbln4));
    
    $pthnproses = date('Y', strtotime($nbln1));
    
    //$berhasil="$ptgl1, $ptgl2, $ptgl3, $ptgl4";
    
    
    
    include "../../config/koneksimysqli_ms.php";
    
    $query = "CALL sls.updatejkthospital('$ptgl1','$ptgl2','$ptgl3','$ptgl4')";
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }
    
    
    //PROSES BUDGET VS EXPEN
    
        $query = "CALL dbproses.0_proses_all_gl('$pthnproses')";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }

        $query = "CALL dbproses.proses_budget('$pthnproses')";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }

        $query = "CALL dbproses.proses_budget_expensis('$pthnproses')";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }

        $query = "CALL dbproses.proses_cabang('$pthnproses')";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }

        $query = "CALL dbproses.proses_coa('$pthnproses')";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }
    
    
    mysqli_close($cnms);
    $berhasil="berhasil....";
}
echo $berhasil;

?>