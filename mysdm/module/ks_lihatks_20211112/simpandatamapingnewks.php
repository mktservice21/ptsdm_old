<?php
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_start();

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pidcard=$_SESSION['IDCARD'];
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $_SESSION['LHTKSDAPT']="Y";
    
    $berhasil="tidak ada data yang diproses...";
    
    //$berhasil = "$pmodule, $pact, $pidmenu";
    
    if ($pmodule=="kslihatks" AND $pact=="simpanksnew") {
        
        $pkaryawanid=$_POST['ukryid'];
        $pdokterid=$_POST['udoktid'];
        $papotikid=$_POST['uaptid'];
        $pcabangid=$_POST['udcabid'];
        $pareaid=$_POST['uareaid'];
        $pdsudoktid=$_POST['udsudoktid'];
        $pouteltid=$_POST['uoutletid'];
        
        if (!empty($pkaryawanid) AND !empty($pdokterid) AND !empty($pouteltid) AND !empty($pdsudoktid) AND !empty($papotikid)) {
            
            include "../../config/koneksimysqli_ms.php";
            
            $query = "SELECT dokterid FROM ms2.mapping_ks_dsu WHERE dokterid='$pdokterid' AND karyawanid='$pkaryawanid' AND idapotik='$papotikid'";
            $tampil= mysqli_query($cnms, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ((INT)$ketemu>0) {
                mysqli_close($cnms);
                echo "Sudah Ada Mapping...";
                exit;
            }
            
            $query = "select id as idpraktek from ms2.tempatpraktek WHERE outletid='$pouteltid' AND iddokter='$pdsudoktid'";
            $tampil2= mysqli_query($cnms, $query);
            $row2= mysqli_fetch_array($tampil2);
            $pidpraktek=$row2['idpraktek'];
            
            $query = "DELETE FROM ms2.mapping_ks_dsu WHERE dokterid='$pdokterid' AND karyawanid='$pkaryawanid' AND idapotik='$papotikid' LIMIT 1";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }
            
            $query = "INSERT INTO ms2.mapping_ks_dsu (karyawanid, dokterid, idapotik, iddokter, outletid, userid, icabangid, idpraktek)VALUES"
                    . " ('$pkaryawanid', '$pdokterid', '$papotikid', '$pdsudoktid', '$pouteltid', '$pidcard', '$pcabangid', '$pidpraktek')";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }
            
            
            mysqli_close($cnms);
            $berhasil="berhasil";
        }
    }
    
    echo $berhasil; exit;
?>

