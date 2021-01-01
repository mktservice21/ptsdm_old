<?php
    session_start();
    include "../../config/koneksimysqli.php";
    $dbname = "dbmaster";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    $puserid="";
    
    if (isset($_SESSION['IDCARD'])) $puserid=$_SESSION['IDCARD'];
    
    if (empty($puserid)) {
        mysqli_close($cnmy);
        echo "Anda harus login ulang...!!!";
        exit;
    }
    $pkaryawanid=$puserid;
    
    
    $berhasil="Tidak ada data yang disimpan";
    
    if ( ($module=="importdataspg" OR $module=="spgproses") AND $act=="simpanbpjskerja") {
        $pidspg=$_POST['uidspg'];
        $pbln=$_POST['ubulan'];
        $pnobpjs=$_POST['unpbpjs'];
        $pbulan="";
        
        if (!empty($pbln)) $pbulan = date('Y-m-d', strtotime($pbln));
        if (!empty($pnobpjs)) $pnobpjs = str_replace("'", " ", $pnobpjs);
        
        $query = "DELETE FROM dbmaster.t_spg_bpjs WHERE id_spg='$pidspg' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : br eth"; exit; }
        
        $query = "INSERT INTO dbmaster.t_spg_bpjs (id_spg, nobpjs_kerja)values('$pidspg', '$pnobpjs')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : br eth"; exit; }
        
        if (!empty($pbulan)) {
            $query = "UPDATE dbmaster.t_spg_bpjs SET bulan='$pbulan' WHERE id_spg='$pidspg' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : br eth"; exit; }
        }
        
        $berhasil="berhasil...";
    }
    
    mysqli_close($cnmy);
    echo $berhasil;
?>