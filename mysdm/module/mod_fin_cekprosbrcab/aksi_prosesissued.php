<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $puserid=$_SESSION['IDCARD'];
    
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN...!!!";
        exit;
    }
    
    
    
    $f_nobrinput="";
    
    if ($module=="fincekprosesbrcab" AND $act=="prosesissued") {
        
        foreach ($_POST['chkbox_br'] as $nobrinput) {
            $pnobrinput=TRIM($_POST['txtbrid'][$nobrinput]);
            
            $ptglissued="";
            if (isset($_POST['d_tgliss'][$nobrinput])) $ptglissued=$_POST['d_tgliss'][$nobrinput];
            
            if (!empty($ptglissued) AND !empty($pnobrinput)) {
                
                if (strpos($f_nobrinput,$pnobrinput)==0) {
                    $f_nobrinput .="'".$pnobrinput."',";
                    
                    $ptglissued= date("Y-m-d", strtotime($ptglissued));
                    
                    
                    $query = "UPDATE dbmaster.t_br_cab SET tglissued='$ptglissued' WHERE bridinputcab='$pnobrinput' AND IFNULL(validate_date,'')<>''";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan Issued ID : $pnobrinput"; exit; }
                    
                    //echo "$pnobrinput : $ptglissued<br/>";
                    
                }
                
            }
            
            
        }
        
        
    }elseif ($module=="fincekprosesbrcab" AND $act=="removeissued") {
        $pnobrinput=$_GET['id'];
        
        if (!empty($pnobrinput)) {
            
            $query = "UPDATE dbmaster.t_br_cab SET tglissued=NULL WHERE bridinputcab='$pnobrinput' AND IFNULL(validate_date,'')<>''";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error HAPUS ISSUED ID : $pnobrinput"; exit; }
            
        }
        
    }
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
?>

