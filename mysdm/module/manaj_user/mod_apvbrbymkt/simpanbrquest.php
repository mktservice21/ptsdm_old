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
    
    
    $berhasil="tidak ada data budget request yang disimpan...";
    
    //$berhasil = "$pmodule, $pact, $pidmenu";
    if ($pmodule=="approvebrquestbymkt" AND $pact=="simpanbrequestnorek") {
        
        $piddokt=$_POST['uiduser'];
        $pidbr=$_POST['ubrid'];
        $pidrekening=$_POST['uidrekening'];
        
        if (!empty($piddokt) AND !empty($pidbr) AND !empty($pidrekening)) {
            include "../../../config/koneksimysqli.php";
            
            $query = "UPDATE hrd.br0 SET id_rekening='$pidrekening' WHERE brid='$pidbr' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "error update norekening br"; mysqli_close($cnmy); exit; }
            mysqli_close($cnmy);
            
            //$berhasil="$piddokt, $pnobrid<br/>$pdataimage";
            $berhasil="berhasil";
        }else{
            $berhasil="ID KOSONG...";
        }
        
        
    }
    
    echo $berhasil;
    
?>